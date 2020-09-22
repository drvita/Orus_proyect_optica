<?php

namespace App\Http\Controllers;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SaleItem as ItemResources;
use App\Http\Requests\SaleItem as ItemRequests;

class SaleItemController extends Controller{
    protected $item;

    public function __construct(SaleItem $item){
        $this->item = $item;
    }
    /**
     * Muestra una lista de ventas
     * @return Json api rest
     */
    public function index(Request $request){
        $orderby = $request->orderby? $request->orderby : "created_at";
        $order = $request->order=="desc"? "desc" : "asc";

        $item = $this->item
                ->orderBy($orderby, $order)
                ->paginate(10);
        return ItemResources::collection($item);
    }

    /**
     * Almacena una venta nueva
     * @param  $request datos de la venta por body en json
     * @return json api rest
     */
    public function store(ItemRequests $request){
        $request['user_id'] = Auth::id();
        $item = $this->item->create( $request->all() );
        return new ItemResources($item);
    }

    /**
     * Muestra una venta en espesifico
     * @param  $sale identificador de la venta
     * @return Json api rest
     */
    public function show($id){
        $item = $this->item::find($id);
        return new ItemResources($item);
    }

    /**
     * Actualiza una venta en espesifico
     * @param  $request datos de la venta desde body en json
     * @param  $sale identificador de la venta
     * @return Json api rest
     */
    public function update(ItemRequests $request, $id){
        $item = $this->item::find($id);
        $request['user_id']=$item->user_id;
        $item->update( $request->all() );
        return New ItemResources($item);
    }

    /**
     * Elimina una venta en espesifico
     * @param  $sale identificador de la venta
     * @return null 204
     */
    public function destroy($id){
        $item = $this->item::find($id);
        $item->delete();
        return response()->json(null, 204);
    }
}
