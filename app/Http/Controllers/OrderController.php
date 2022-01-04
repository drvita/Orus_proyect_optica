<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Order as OrderResources;
use App\Http\Requests\Order as OrderRequests;
use App\Events\OrderUpdated;
use Carbon\Carbon;

class OrderController extends Controller
{
    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
    /**
     * Muestra lista de ordenes
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

        $orderdb = $this->order
            ->withRelation()
            ->Estado($request->status)
            ->orderBy($orderby, $order)
            ->Paciente($request->search)
            ->SearchId($request->search)
            ->publish()
            ->branch($branch)
            ->paginate($page);

        return OrderResources::collection($orderdb);
    }

    /**
     * Almacena una nueva orden de pedido
     * @param  $request de body en Json
     * @return Json api rest
     */
    public function store(OrderRequests $request)
    {
        $auth = Auth::user();
        $currentUser = User::find($auth->id);
        $request['user_id'] = $currentUser->id;
        $request['status'] = 0;
        $rolUserAdmin = $currentUser->hasRole("admin");

        //Only admin can save in differents branches
        if (!$rolUserAdmin || !isset($request['branch_id'])) {
            $request['branch_id'] = $currentUser->branch_id;
        }

        $order = $this->order->create($request->all());

        if (isset($request->items)) {
            $order['items'] = $this->getItemsRequest($request->items, $request['branch_id']);
            if (count($order['items'])) event(new OrderUpdated($order, false));
        }

        return new OrderResources($order);
    }

    /**
     * Muestra una orden en espesifico
     * @param  $order identificador de la orden
     * @return Json api rest
     */
    public function show(Order $order)
    {
        $order->withRelation();
        return new OrderResources($order);
    }

    /**
     * Actualiza una orden espesifica
     * @param  $request datos a actualizar por medio de body en Json
     * @param  $order identificador de la orden a actualizar
     * @return Json api rest
     */
    public function update(Request $request, Order $order)
    {
        $auth = Auth::user();
        $currentUser = User::find($auth->id);
        $request['updated_id'] = $currentUser->id;
        $udStatus = $order->status != $request->status ? true : false;
        $rolUserAdmin = $currentUser->hasRole("admin");
        //Only admin can modify branches
        if (isset($request->branch_id) && !$rolUserAdmin) {
            unset($request['branch_id']);
        }

        if ($order) {
            $order->update($request->all());

            if (isset($request->items)) {
                $order['items'] = $this->getItemsRequest($request->items);

                if (count($order['items'])) event(new OrderUpdated($order, $udStatus));
            }
        }

        return new OrderResources($order);
    }

    /**
     * Elimina una orden
     * @param  $order identificador de la orden
     * @return null 404
     */
    public function destroy($id)
    {
        $order = $this->order::where('id', $id)
            ->with('nota')
            ->first();
        $enUso = 0;

        if ($order) {
            $enUso = count($order->nota ?? []);

            if ($enUso) {
                $order->deleted_at = Carbon::now();
                $order->updated_id = Auth::user()->id;
                $order->save();
            } else {
                $order->delete();
            }
        }

        return response()->json(null, 204);
    }

    private function getItemsRequest($items, $branch_id = null)
    {
        if ($items) {
            $itemsArray = is_string($items) ? json_decode($items, true) : $items;

            if (is_array($itemsArray)) {
                if ($branch_id) {
                    foreach ($itemsArray as $key => $item) {
                        if (!isset($item['branch_id'])) {
                            $itemsArray[$key]['branch_id'] = $branch_id;
                        }
                    }
                }

                return $itemsArray;
            }
        }

        return [];
    }
}