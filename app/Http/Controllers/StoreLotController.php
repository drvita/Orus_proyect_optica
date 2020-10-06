<?php

namespace App\Http\Controllers;

use App\Models\StoreLot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\StoreLot as StoreResources;
use App\Http\Requests\StoreLot as StoreRequests;

class StoreLotController extends Controller{
    protected $item;

    public function __construct(StoreLot $item){
        $this->item = $item;
    }
    /**
     * Muestra una lista de ingreso de lotes de producto
     * @return Json api
     */
    public function index(Request $request){
        if($request->store_items_id){
            $items = $this->item
                    ->where('store_items_id',$request->store_items_id)
                    ->paginate(10);
        } else {
            $items = $this->item::paginate(10);
        }
        
        return StoreResources::collection($items);
    }

    /**
     * Almacena un lote relacionado con un producto
     * @param  $request campos del lote a traves del body en Json
     * @return Json api rest
     */
    public function store(StoreRequests $request){
        $request['user_id']= Auth::user()->id;
        $item = $this->item->create($request->all());
        return new StoreResources($item);
    }

    /**
     * Muestra un lote en espesifico
     * @param  $storeLot identificador del lote
     * @return Json api rest
     */
    public function show(StoreLot $item){
        return new StoreResources($item);
    }

    /**
     * Actualiza un lote en espesifico
     * @param  $request datos a traves del body en json
     * @param  $storeLot identificador del lote
     * @return json api rest
     */
    public function update(StoreRequests $request, StoreLot $item){
        $request['user_id']=$item->user_id;
        $item->update( $request->all() );
        return New StoreResources($item);
    }

    /**
     * Eliminamos un lote en espesifico
     * @param  $storeLot identificador del lote
     * @return null 204
     */
    public function destroy(StoreLot $item){
        $item->delete();
        return response()->json(null, 204);
    }
}
