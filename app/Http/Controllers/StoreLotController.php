<?php

namespace App\Http\Controllers;

use App\Models\StoreLot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\StoreLot as StoreResources;
use App\Http\Requests\StoreLot as StoreRequests;
use App\Models\Config;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StoreLotController extends Controller
{
    protected $item;

    public function __construct(StoreLot $item)
    {
        $this->middleware('can:storeLot.list')->only('index');
        $this->middleware('can:storeLot.show')->only('show');
        $this->middleware('can:storeLot.add')->only('store');
        $this->middleware('can:storeLot.edit')->only('update');
        $this->middleware('can:storeLot.delete')->only('destroy');
        $this->item = $item;
    }
    /**
     * Muestra una lista de ingreso de lotes de producto
     * @return Json api
     */
    public function index(Request $request)
    {
        $page = $request->itemsPage ? $request->itemsPage : 50;
        $branch_id = $request->input("branch_id", null);
        $item_id = $request->input("store_items_id", null) ?? $request->input("item_id", null) ?? $request->input("id", null);

        if ($branch_id === "all" || !is_numeric($branch_id)) {
            $branch_id = null;
        }

        if ($branch_id) {
            $branchExists = Config::where('name', "branches")
                ->where('id', $branch_id)
                ->exists();

            if (!$branchExists) {
                return response()->json([
                    'message' => 'Branch not found',
                ], 404);
            }
            Log::info("[StoreLotController] Filter store lots by branch: " . $branch_id);
        }

        $items = $this->item;

        if ($item_id) {
            $items = $items->where('store_items_id', $item_id);
        }

        return StoreResources::collection(
            $items->branch($branch_id)
                ->paginate($page)
        );
    }

    /**
     * Almacena un lote relacionado con un producto
     * @param  $request campos del lote a traves del body en Json
     * @return Json api rest
     */
    public function store(StoreRequests $request)
    {
        $request['user_id'] = Auth::user()->id;
        $rol = Auth::user()->rol;
        //Validation for branchs to admins
        if (!$rol) {
            if (!isset($request['branch_id'])) $request['branch_id'] = Auth::user()->branch_id;
        } else {
            $request['branch_id'] = Auth::user()->branch_id;
        }

        $item = $this->item->create($request->all());
        return new StoreResources($item);
    }

    /**
     * Muestra un lote en espesifico
     * @param  $storeLot identificador del lote
     * @return Json api rest
     */
    public function show(StoreLot $item)
    {
        return new StoreResources($item);
    }

    /**
     * Actualiza un lote en espesifico
     * @param  $request datos a traves del body en json
     * @param  $storeLot identificador del lote
     * @return json api rest
     */
    public function update(StoreRequests $request, StoreLot $item)
    {
        $request['user_id'] = $item->user_id;
        $item->update($request->all());
        return new StoreResources($item);
    }

    /**
     * Eliminamos un lote en espesifico
     * @param  $storeLot identificador del lote
     * @return null 204
     */
    public function destroy(StoreLot $item)
    {
        $item->delete();
        return response()->json(null, 204);
    }
}
