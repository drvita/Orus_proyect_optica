<?php

namespace App\Http\Controllers;

use App\Models\StoreItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\StoreItem as StoreResources;
use App\Http\Resources\StoreItemActivity;
use App\Http\Requests\StoreItem as StoreRequests;
use App\Http\Requests\StoreItemByList;
use App\Http\Requests\StoreItemSetCant;
use Carbon\Carbon;

class StoreItemController extends Controller
{
    protected $store;

    public function __construct(StoreItem $store)
    {
        $this->middleware('can:store.list')->only('index');
        $this->middleware('can:store.list')->only('handleDonloadCSV');
        $this->middleware('can:store.show')->only('show');
        $this->middleware('can:store.add')->only('store');
        $this->middleware('can:store.edit')->only('update');
        $this->middleware('can:store.delete')->only('destroy');
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
            ->searchCode((string) $request->code, $request->id ?? 0)
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
            $callback = $this->handleDonloadCSV($items, $request->zero);

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

        if (isset($request['supplier_id'])) {
            $request['contact_id'] = $request->supplier_id;
            unset($request['supplier_id']);
        }

        $store = $this->store->create($request->all());
        return new StoreItemActivity($store);
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

        return new StoreItemActivity($store);
    }

    /**
     * Actualiza un producto en espesifico
     * @param  $request datos del producto a actualizar
     * @param  $store identificador del producto a actualizar
     * @return Json api rest
     */
    public function update(StoreRequests $request, StoreItem $store)
    {
        if (!count($request->all())) {
            return new StoreItemActivity($store);
        }

        if (isset($request['supplier_id'])) {
            $request['contact_id'] = $request->supplier_id;
            unset($request['supplier_id']);
        }

        $request['user_id'] = $store->user_id;
        $request['updated_id'] = Auth::user()->id;
        $store->update($request->all());

        return new StoreItemActivity($store);
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
     * 
     */
    public function setCantItem(StoreItem $item, StoreItemSetCant $request)
    {
        if (!$item->branch_default) {
            return ["status" => "failer", "message" => "The item is not item valid to set cant by default branch"];
        }

        $item->inBranch()->get()->each(function ($i) use ($item, $request) {
            if ($item->branch_default === $i->branch_id) {
                $i->cant = $request->cant;
                $i->save();
            } else {
                // $i->deleted_at = carbon::now();
                $i->delete();
            }
        });

        $item->cant = $request->cant;

        return [
            "status" => "ok",
            "data" => $item,
        ];
    }
    /**
     * Save items by array
     * @param $request into items
     * @return result about operation
     */
    public function storeList(StoreItemByList $request)
    {
        foreach ($request->items as $row) {
            $item = StoreItem::find($row['id']);
            $branch_id = $item->branch_default ? $item->branch_default : $row['branch_id'];
            $branch = $item->inBranch()->where("branch_id", $branch_id)->first();

            if ($branch->cant < 0) {
                $branch->cant = 0;
            }

            if (isset($row['price'])) {
                $branch->price = (float) $row['price'];
            }

            $branch->cant += (int) $row['cant'];
            $branch->updated_id = Auth::user()->id;
            $branch->save();

            //TODO: save in Store lot
        }

        return ["status" => "ok"];
    }

    /**
     * make a data to download
     * @param class of item model
     * @return bob
     */
    private function handleDonloadCSV($items, $zero)
    {
        $columns = array('code', 'codebar', 'supplier', 'brand', 'name', 'category', "cant", "price");
        $callback = function () use ($items, $columns, $zero) {
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

                if (!$row['cant'] && $zero == "false") {
                    continue;
                }

                fputcsv($file, array($row['code'], $row['codebar'], $row['supplier'], $row['brand'], $row['name'], $row['category'], $row['cant'], $row['price']));
            }

            fclose($file);
        };

        return $callback;
    }
}
