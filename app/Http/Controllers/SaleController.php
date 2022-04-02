<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Sale as SaleResources;
use App\Http\Requests\Sale as SaleRequests;
use App\Events\SaleSave;
use Carbon\Carbon;

class SaleController extends Controller
{
    protected $sale;

    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }
    /**
     * Muestra una lista de ventas
     * @return Json api rest
     */
    public function index(Request $request)
    {
        $orderby = $request->orderby ? $request->orderby : "created_at";
        $order = $request->order == "desc" ? "desc" : "asc";
        $page = $request->itemsPage ? $request->itemsPage : 20;
        $currentUser = Auth::user();
        $branchUser = $currentUser->branch_id;
        $branch = $branchUser;

        // If branches var is not present, use the same branch of user
        // only admin can see all branches
        if (isset($request->branch)) {
            if ($request->branch === "all") {
                $branch = null;
            } else {
                $branch = $request->branch;
            }
        }

        $sale = $this->sale
            ->relations()
            ->orderBy($orderby, $order)
            ->cliente($request->search)
            ->searchId($request->search)
            ->type($request->type)
            ->date($request->date)
            ->publish()
            ->branch($branch)
            ->paginate($page);
        return SaleResources::collection($sale);
    }

    /**
     * Almacena una venta nueva
     * @param  $request datos de la venta por body en json
     * @return json api rest
     */
    public function store(SaleRequests $request)
    {
        $currentUser = Auth::user();
        $request['user_id'] = $currentUser->id;
        $request['branch_id'] = $currentUser->branch_id;
        $request['status'] = 0;
        $rolUser = $currentUser->rol;

        //Only admin can save in differents branches
        if (!$rolUser) {
            if (isset($request->branch_id)) $request['branch_id'] = $request->branch_id;
        }


        $sale = $this->sale->create($request->all());
        $sale['items'] = getItemsRequest($request->items, $currentUser->branch_id);
        $sale['payments'] = getPaymentsRequest($request->payments, $currentUser->branch_id);

        event(new SaleSave($sale));
        //Get sale with new data
        $sale = $sale::where('id', $sale->id)->relations()->first();
        return new SaleResources($sale);
    }

    /**
     * Muestra una venta en espesifico
     * @param  $sale identificador de la venta
     * @return Json api rest
     */
    public function show(Sale $sale)
    {
        return new SaleResources($sale);
    }

    /**
     * Actualiza una venta en espesifico
     * @param  $request datos de la venta desde body en json
     * @param  $sale identificador de la venta
     * @return Json api rest
     */
    public function update(Request $request, Sale $sale)
    {
        $currentUser = Auth::user();
        $request['updated_id'] = $currentUser->id;
        $userIsAdmin = $this->isAdmin($currentUser);

        //Only admin can modify branches
        if (!isset($request->branch_id) || $userIsAdmin) {
            $request['branch_id'] = $currentUser->branch_id;
        }

        $sale->update($request->all());
        $sale['items'] = getItemsRequest($request->items, $currentUser->branch_id);
        $sale['payments'] = getPaymentsRequest($request->payments, $currentUser->branch_id);
        event(new SaleSave($sale));
        $sale = $sale::where('id', $sale->id)->relations()->first();

        return new SaleResources($sale);
    }

    /**
     * Elimina una venta en espesifico
     * @param  $sale identificador de la venta
     * @return null 204
     */
    public function destroy($id)
    {
        $sale = $this->sale::where('id', $id)->first();

        $sale->deleted_at = Carbon::now();
        $sale->updated_id = Auth::user()->id;
        $sale->save();
        //$sale->delete();
        return response()->json(null, 204);
    }

    function isAdmin($user)
    {

        return $user->hasRole("admin");
    }
}
