<?php

namespace App\Http\Controllers;

use App\Models\StoreItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\StoreItem as StoreResources;
use App\Http\Resources\StoreItemShow as StoreResourceShow;
use App\Http\Requests\StoreItem as StoreRequests;
use Carbon\Carbon;

class StoreItemController extends Controller
{
    protected $store;

    public function __construct(StoreItem $store)
    {
        $this->store = $store;
    }
    /**
     * Muestra una lista de los productos en almacen
     * @return Json api rest
     */
    public function index(Request $request)
    {
        $orderby = $request->orderby ? $request->orderby : "created_at";
        $order = $request->order == "asc" ? "asc" : "desc";
        $page = $request->itemsPage ? $request->itemsPage : 50;


        $items = $this->store
            ->withoutRelations()
            ->orderBy($orderby, $order)
            ->searchItem((string) $request->search)
            ->searchCode((string) $request->code)
            ->searchCodeBar((string) $request->codebar)
            ->zero($request->zero)
            ->category(intval($request->cat))
            ->searchSupplier($request->supplier)
            ->publish()
            ->searchBrand($request->brand);

        return StoreResources::collection($items->paginate($page));
    }

    /**
     * Almacena un nuevo producto
     * @param  $request datos a almacenar a traves del body en formato json
     * @return Json api rest
     */
    public function store(StoreRequests $request)
    {
        $request['user_id'] = Auth::user()->id;

        $store = $this->store->create($request->all());
        return new StoreResources($store);
    }

    public static function getEloquentSqlWithBindings($query)
    {
        return vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function ($binding) {
            $binding = addslashes($binding);
            return is_numeric($binding) ? $binding : "'{$binding}'";
        })->toArray());
    }

    /**
     * Muestra un producto en espesifico
     * @param  $store identificador del producto
     * @return Json api rest
     */
    public function show(int $id)
    {
        $store = $this->store
            ->where('id', $id)
            ->with('lote', 'categoria.parent.parent', 'supplier', 'brand', 'inBranch')
            ->first();

        return new StoreResourceShow($store);
    }

    /**
     * Actualiza un producto en espesifico
     * @param  $request datos del producto a actualizar
     * @param  $store identificador del producto a actualizar
     * @return Json api rest
     */
    public function update(StoreRequests $request, StoreItem $store)
    {
        $request['user_id'] = $store->user_id;
        $store->update($request->all());
        return new StoreResources($store);
    }

    /**
     * Elimina un producto en espesifico
     * @param  $storeItem identificador del producto
     * @return null 204
     */
    public function destroy(StoreItem $store)
    {
        $store->deleted_at = Carbon::now();
        $store->updated_id = Auth::user()->id;
        $store->save();
        //$store->delete();
        return response()->json(null, 204);
    }
}