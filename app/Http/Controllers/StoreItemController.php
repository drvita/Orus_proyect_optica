<?php

namespace App\Http\Controllers;

use App\Models\StoreItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\StoreItem as StoreResources;
use App\Http\Resources\StoreItemShow as StoreResourceShow;
use App\Http\Requests\StoreItem as StoreRequests;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StoreItemController extends Controller
{
    protected $store;

    public function __construct(StoreItem $store)
    {
        $this->store = $store;
    }
    /**
     * @OA\Get(
     *  path="/api/store",
     *  summary="List of store items",
     *  description="GET list of store items in DB",
     *  operationId="index",
     *  tags={"Store"},
     *  security={ {"bearer": {} }},
     *  @OA\Parameter(name="orderby",in="query",required=false,@OA\Schema(type="string")),
     *  @OA\Parameter(name="order",in="query",required=false,@OA\Schema(type="string")),
     *  @OA\Parameter(name="itemsPage",in="query",required=false,@OA\Schema(type="string")),
     *  @OA\Parameter( name="search",in="query",required=false,@OA\Schema(type="string")),
     *  @OA\Parameter(name="code",in="query",required=false,@OA\Schema(type="string")),
     *  @OA\Parameter(name="codebar",in="query",required=false,@OA\Schema(type="string")),
     *  @OA\Parameter(name="zero",in="query",required=false,@OA\Schema(type="boolean")),
     *  @OA\Parameter( name="cat",in="query",required=false,@OA\Schema(type="string")),
     *  @OA\Parameter(name="supplier",in="query",required=false,@OA\Schema(type="string")),
     *  @OA\Parameter(name="brand",in="query",required=false,@OA\Schema(type="string")),
     *  @OA\Parameter(name="branch",in="query",required=false,@OA\Schema(type="string")),
     *  @OA\Parameter( name="responseType",in="query",required=false,@OA\Schema(type="string")),
     * 
     *  @OA\Response(response=401,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry. Please try again")
     *    )
     *  ),
     *  @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="data", type="object"),
     *     )
     *  ),
     *  @OA\Response(
     *     response=201,
     *     description="Success generate file csv",
     *     @OA\JsonContent()
     *  ),
     * )
     *
     * @param "" $request
     * @return JsonResponse
     */



    public function index(Request $request)
    {
        $orderby = $request->orderby ? $request->orderby : "created_at";
        $order = $request->order == "asc" ? "asc" : "desc";
        $responseType = $request->responseType ? $request->responseType : "json";
        $page = $request->itemsPage ? $request->itemsPage : 50;

        $store = $this->store
            ->withoutRelations()
            ->orderBy($orderby, $order)
            ->searchItem((string) $request->search)
            ->searchCode((string) $request->code)
            ->searchCodeBar((string) $request->codebar)
            ->zero($request->zero)
            ->category(intval($request->cat))
            ->searchSupplier($request->supplier)
            ->publish()
            ->searchBrand($request->brand)
            ->filterBranch($request->branch)
            ->updateDate($request->update);

        if ($responseType === "csv") {
            $fileName = 'storeItems.csv';
            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );
            $items = $store->limit(1000)->get();
            $callback = $this->handleDonloadCSV($items);

            return response()->stream($callback, 201, $headers);
        }

        return StoreResources::collection($store->paginate($page));
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

    /**
     * make a data to download
     * @param class of item model
     * @return bob
     */
    private function handleDonloadCSV($items)
    {
        $columns = array('code', 'codebar', 'supplier', 'brand', 'name', 'category', "cant", "price");

        // foreach ($items as $item) {
        //     $brand = $item->categoria;
        //     dd($brand->toArray());
        // }

        $callback = function () use ($items, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($items as $item) {
                $branches = $item->inBranch;

                $row['code']  = $item->code;
                $row['codebar']    = $item->codebar;
                $row['supplier']    = $item->supplier ? $item->supplier->name : $item->contact_id;
                $row['brand']    = $item->brand ? $item->brand->name : $item->brand_id;
                $row['name']  = $item->name;
                $row['category']  = $item->categoria ? $item->categoria->name : $item->category_id;
                $row['cant'] = 0;
                $row['price'] = 0;

                foreach ($branches as $branch) {
                    $row['cant']  += $branch->cant;
                    $row['price']  = $branch->price;
                }

                fputcsv($file, array($row['code'], $row['codebar'], $row['supplier'], $row['brand'], $row['name'], $row['category'], $row['cant'], $row['price']));
            }

            fclose($file);
        };

        return $callback;
    }
}
