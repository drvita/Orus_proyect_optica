<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Brand as BrandResources;
use App\Http\Requests\Brand as BrandRequests;

class BrandController extends Controller
{
    protected $brand;

    public function __construct(Brand $brand)
    {
        $this->middleware('can:brand.list')->only('index');
        $this->middleware('can:brand.show')->only('show');
        $this->middleware('can:brand.add')->only('store');
        $this->middleware('can:brand.edit')->only('update');
        $this->middleware('can:brand.delete')->only('destroy');
        $this->brand = $brand;
    }
    /**
     * Muestra una lista de marcas
     * @return Json api rest
     */
    public function index(Request $request)
    {
        $orderby = $request->orderby ? $request->orderby : "name";
        $order = $request->order == "desc" ? "desc" : "asc";
        $page = $request->itemsPage ? $request->itemsPage : 50;

        $brand = $this->brand
            ->orderBy($orderby, $order)
            ->Supplier($request->supplier)
            ->paginate($page);

        return BrandResources::collection($brand);
    }

    /**
     * Almacena una marca nueva
     * @param  $request datos de la marca por boy en json
     * @return json api rest
     */
    public function store(BrandRequests $request)
    {
        $request['user_id'] = Auth::user()->id;
        $brand = $this->brand->create($request->all());
        return new BrandResources($brand);
    }

    /**
     * Muestra una marca en espesifico
     * @param  $brand identificador de la marca
     * @return json api rest
     */
    public function show(Brand $brand)
    {
        return new BrandResources($brand);
    }

    /**
     * Actualiza una marca en espesifico
     * @param  $request datos a actualizar
     * @param  $brand identificador de la marca
     * @return Json api rest
     */
    public function update(BrandRequests $request, Brand $brand)
    {
        $request['user_id'] = $brand->user_id;
        $brand->update($request->all());
        return new BrandResources($brand);
    }

    /**
     * Elimina una marca en espesifico
     * @param  $brand identificador de la marca
     * @return Json api rest
     */
    public function destroy(Brand $brand)
    {
        $brand->delete();
        return response()->json(null, 204);
    }
}
