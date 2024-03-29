<?php

namespace App\Http\Controllers;

use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SaleItem as ItemResources;
use App\Http\Requests\SaleItem as ItemRequests;

class SaleItemController extends Controller
{
    protected $item;

    public function __construct(SaleItem $item)
    {
        $this->middleware('can:saleItem.list')->only('index');
        $this->middleware('can:saleItem.show')->only('show');
        $this->middleware('can:saleItem.add')->only('store');
        $this->middleware('can:saleItem.edit')->only('update');
        $this->middleware('can:saleItem.delete')->only('destroy');
        $this->item = $item;
    }
    /**
     * Muestra una lista de ventas
     * @return Json api rest
     */
    public function index(Request $request)
    {
        $orderby = $request->orderby ? $request->orderby : "sales_items.created_at";
        $order = $request->order == "desc" ? "desc" : "asc";
        $page = $request->itemsPage ? $request->itemsPage : 50;

        $item = $this->item
            ->orderBy($orderby, $order)
            ->Stock($request->stock)
            ->SaleDay($request->date)
            ->paginate($page);

        if ($request->stock) {
            return $item;
        } else {
            return ItemResources::collection($item);
        }
    }

    /**
     * Almacena una venta nueva
     * @param  $request datos de la venta por body en json
     * @return json api rest
     */
    public function store(ItemRequests $request)
    {
        $request['user_id'] = Auth::user()->id;
        $item = $this->item->create($request->all());
        return new ItemResources($item);
    }

    /**
     * Muestra una venta en espesifico
     * @param  $sale identificador de la venta
     * @return Json api rest
     */
    public function show($id)
    {
        $item = $this->item::find($id);
        return new ItemResources($item);
    }

    /**
     * Actualiza una venta en espesifico
     * @param  $request datos de la venta desde body en json
     * @param  $sale identificador de la venta
     * @return Json api rest
     */
    public function update(ItemRequests $request, $id)
    {
        $item = $this->item::find($id);
        $request['user_id'] = $item->user_id;
        $item->update($request->all());
        return new ItemResources($item);
    }

    /**
     * Elimina una venta en espesifico
     * @param  $sale identificador de la venta
     * @return null 204
     */
    public function destroy($id)
    {
        $item = $this->item::find($id);
        $item->delete();
        return response()->json(null, 204);
    }
}
