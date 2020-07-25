<?php

namespace App\Http\Controllers;
use App\Models\StoreItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\StoreItem as StoreResources;
use App\Http\Requests\StoreItem as StoreRequests;

class StoreItemController extends Controller{
    protected $store;

    public function __construct(StoreItem $store){
        $this->store = $store;
    }
    /**
     * Muestra una lista de los productos en almacen
     * @return Json api rest
     */
    public function index(){
        return StoreResources::collection(
            $this->store::all()
        );
    }

    /**
     * Almacena un nuevo producto
     * @param  $request datos a almacenar a traves del body en formato json
     * @return Json api rest
     */
    public function store(StoreRequests $request){
        $request['user_id']=Auth::id();
        $store = $this->store->create($request->all());
        return new StoreResources($store);
    }

    /**
     * Muestra un producto en espesifico
     * @param  $store identificador del producto
     * @return Json api rest
     */
    public function show(StoreItem $store){
        return new StoreResources($store);
    }

    /**
     * Actualiza un producto en espesifico
     * @param  $request datos del producto a actualizar
     * @param  $store identificador del producto a actualizar
     * @return Json api rest
     */
    public function update(StoreRequests $request, StoreItem $store){
        $request['user_id']=$store->user_id;
        $store->update( $request->all() );
        return New StoreResources($store);
    }

    /**
     * Elimina un producto en espesifico
     * @param  $storeItem identificador del producto
     * @return null 204
     */
    public function destroy(StoreItem $store){
        $store->delete();
        return response()->json(null, 204);
    }
}
