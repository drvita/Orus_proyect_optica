<?php

namespace App\Http\Controllers;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Sale as SaleResources;
use App\Http\Requests\Sale as SaleRequests;
use App\Events\SaleSave;

class SaleController extends Controller{
    protected $sale;

    public function __construct(Sale $sale){
        $this->sale = $sale;
    }
    /**
     * Muestra una lista de ventas
     * @return Json api rest
     */
    public function index(Request $request){
        $orderby = $request->orderby? $request->orderby : "created_at";
        $order = $request->order=="desc"? "desc" : "asc";

        $sale = $this->sale
                ->orderBy($orderby, $order)
                ->Cliente($request->search)
                ->SearchId($request->search)
                ->Type($request->type)
                ->Date($request->date)
                ->paginate(25);
        return SaleResources::collection($sale);
    }

    /**
     * Almacena una venta nueva
     * @param  $request datos de la venta por body en json
     * @return json api rest
     */
    public function store(SaleRequests $request){
        $request['user_id'] = Auth::user()->id;
        $sale = $this->sale->create( $request->all() );
        $sale['items'] = $request->items;
        event(new SaleSave($sale));
        return new SaleResources($sale);
    }

    /**
     * Muestra una venta en espesifico
     * @param  $sale identificador de la venta
     * @return Json api rest
     */
    public function show(Sale $sale){
        return new SaleResources($sale);
    }

    /**
     * Actualiza una venta en espesifico
     * @param  $request datos de la venta desde body en json
     * @param  $sale identificador de la venta
     * @return Json api rest
     */
    public function update(SaleRequests $request, Sale $sale){
        $request['user_id']= $sale->user_id;
        $sale->update( $request->all() );
        $sale['items'] = $request->items;
        event(new SaleSave($sale));
        return New SaleResources($sale);
    }

    /**
     * Elimina una venta en espesifico
     * @param  $sale identificador de la venta
     * @return null 204
     */
    public function destroy(Sale $sale){
        $sale->delete();
        return response()->json(null, 204);
    }
}
