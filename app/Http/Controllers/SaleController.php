<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Sale as SaleResources;
use App\Http\Requests\Sale as SaleRequests;
use App\Events\SaleSave;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SaleController extends Controller
{
    protected $sale;

    public function __construct(Sale $sale)
    {
        $this->middleware('can:sale.list')->only('index');
        $this->middleware('can:sale.show')->only('show');
        $this->middleware('can:sale.add')->only('store');
        $this->middleware('can:sale.edit')->only('update');
        $this->middleware('can:sale.delete')->only('destroy');
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

        return SaleResources::collection(
            $sale->load('items.item')
        );
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
        $request['subtotal'] = 0;
        $request['descuento'] = $request->discount ?? 0;

        foreach ($request['items'] as $item) {
            $request['subtotal'] += $item['cant'] * $item['price'];
        }

        // Total calculation removed, handled by SaleObserver

        $sale = $this->sale->create($request->all());
        $sale->items = getItemsRequest($request->items, $sale->branch_id);
        $sale->addPayments = false;
        if (isset($request["payments"])) {
            $sale->paymentsRequest = getPaymentsRequest($request->payments, $request['branch_id']);
            $sale->addPayments = true;
        }
        $sale->method = "create";

        event(new SaleSave($sale));
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
    public function update(SaleRequests $request, Sale $sale)
    {
        $currentUser = Auth::user();
        $request['user_id'] = $sale->user_id;
        $request['updated_id'] = $currentUser->id;
        $request['branch_id'] = $sale->branch_id;
        $request['descuento'] = $request->discount ?? 0;

        if (isset($request['items'])) {
            $request['subtotal'] = 0;
            foreach ($request['items'] as $item) {
                $request['subtotal'] += $item['cant'] * $item['price'];
            }

            // Total calculation removed, handled by SaleObserver
        }

        $sale->update($request->all());
        $sale->items = getItemsRequest($request->items, $sale->branch_id);
        $sale->addPayments = false;
        if (isset($request["payments"])) {
            Log::info("[Sales.update] Payments request", [
                "payments" => $request->payments
            ]);
            $sale->paymentsRequest = getPaymentsRequest($request->payments, $request['branch_id']);
            $sale->addPayments = true;
        }
        $sale->method = "update";

        event(new SaleSave($sale));
        $sale = $sale::where('id', $sale->id)->relations()->first();

        return new SaleResources($sale);
    }

    /**
     * Elimina una venta en espesifico
     * @param  $sale identificador de la venta
     * @return null 204
     */
    public function destroy(Sale $sale)
    {
        $sale->delete();
        return response()->json(null, 204);
    }
}
