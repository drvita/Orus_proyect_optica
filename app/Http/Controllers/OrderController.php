<?php

namespace App\Http\Controllers;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Order as OrderResources;
use App\Http\Requests\Order as OrderRequests;
use App\Events\OrderUpdated;
use Carbon\Carbon;

class OrderController extends Controller{
    protected $order;

    public function __construct(Order $order){
        $this->order = $order;
    }
    /**
     * Muestra lista de ordenes
     * @return Json api rest
     */
    public function index(Request $request){
        $orderby = $request->orderby? $request->orderby : "created_at";
        $order = $request->order=="desc"? "desc" : "asc";
        $page = $request->itemsPage ? $request->itemsPage : 20;
        $rol = Auth::user()->rol;
        
        //Validation for branchs to admins
        if(!$rol){
            if(!isset($request->branch)) $branch = Auth::user()->branch_id;
            else {
                if($request->branch === "all") $branch = null;
                else $branch = $request->branch;
            }
        }else {
            $branch = Auth::user()->branch_id;
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
    public function store(OrderRequests $request){
        $request['user_id']= Auth::user()->id;
        $order = $this->order->create($request->all());
        $rol = Auth::user()->rol;
        //Validation for branchs to admins
        if(!$rol){
            if(!isset($request['branch_id'])) $request['branch_id'] = Auth::user()->branch_id; 
        }else {
            $request['branch_id'] = Auth::user()->branch_id; 
        } 

        if(isset($request->items)){
            $order['items'] = $this->getItemsRequest($request->items);
            if(count($order['items'])) event(new OrderUpdated($order, false));
        }

        return new OrderResources($order);
    }

    /**
     * Muestra una orden en espesifico
     * @param  $order identificador de la orden
     * @return Json api rest
     */
    public function show(Order $order){
        $order->withRelation();
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
        $udStatus = $order->status != $request->status ? true : false;
        
        $order->update( $request->all() );
        if(isset($request->items)){
            $order['items'] = $this->getItemsRequest($request->items);
            
            if(count($order['items'])) event(new OrderUpdated($order, $udStatus));
        }
    
        return New OrderResources($order);
    }

    /**
     * Elimina una orden
     * @param  $order identificador de la orden
     * @return null 404
     */
    public function destroy($id){
        $order = $this->order::where('id', $id)
                ->with('nota')
                ->first();

        $enUso = count($order->nota);

        if($enUso){
            $order->deleted_at = Carbon::now();
            $order->updated_id = Auth::user()->id;
            $order->save();
        } else {
            $order->delete();
        }
        
        //$order->delete();
        return response()->json(null, 204);
    }

    private function getItemsRequest($items){
        if($items) return is_string($items) ? json_decode($items, true) : $items;

        return null;
    }
}