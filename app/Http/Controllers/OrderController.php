<?php

namespace App\Http\Controllers;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Order as OrderResources;
use App\Http\Requests\Order as OrderRequests;

class OrderController extends Controller{
    protected $order;

    public function __construct(Order $order){
        $this->order = $order;
    }
    /**
     * Muestra lista de ordenes
     * @return Json api rest
     */
    public function index(){
        return OrderResources::collection(
            $this->order::all()
        );
    }

    /**
     * Almacena una nueva orden de pedido
     * @param  $request de body en Json
     * @return Json api rest
     */
    public function store(OrderRequests $request){
        $request['user_id']=Auth::id();
        $order = $this->order->create($request->all());
        return new OrderResources($order);
    }

    /**
     * Muestra una orden en espesifico
     * @param  $order identificador de la orden
     * @return Json api rest
     */
    public function show(Order $order){
        return new OrderResources($order);
    }

    /**
     * Actualiza una orden espesifica
     * @param  $request datos a actualizar por medio de body en Json
     * @param  $order identificador de la orden a actualizar
     * @return Json api rest
     */
    public function update(Request $request, Order $order){
        $request['user_id']=$order->user_id;
        $order->update( $request->all() );
        return New OrderResources($order);
    }

    /**
     * Elimina una orden
     * @param  $order identificador de la orden
     * @return null 404
     */
    public function destroy(Order $order){
        $order->delete();
        return response()->json(null, 204);
    }
}
