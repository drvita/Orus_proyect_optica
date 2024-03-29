<?php

namespace App\Http\Controllers;

use App\Models\StoreBranch;
use App\Models\StoreItem;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreBranch as StoreRequests;
use App\Http\Resources\StoreBranch as BranchResource;

class StoreBranchController extends Controller
{
    protected $branch;

    public function __construct(StoreBranch $branch)
    {
        $this->middleware('can:storeBranch.add')->only('store');
        $this->middleware('can:storeBranch.edit')->only('update');
        $this->branch = $branch;
    }

    /**
     * Almacena un nuevo producto
     * @param  $request datos a almacenar a traves del body en formato json
     * @return Json api rest
     */
    public function store(StoreRequests $request)
    {
        $user_default = Auth::user();
        $item = StoreItem::find($request->store_item_id);

        if ($item) {
            if ($item->branch_default) {
                $request['branch_id'] = $item->branch_default;
            }
        } else {
            return [
                "message" => "El producto al que quiere agregar articulo no existe",
                "error" => [$item]
            ];
        }

        $branch = $this->branch
            ->where("store_item_id", $request->store_item_id)
            ->where("branch_id", $request->branch_id)
            ->first();

        if ($branch) {
            $request['updated_id'] = $user_default->id;
            $branch->update($request->all());
            return new BranchResource($branch);
        }

        $request['user_id'] = $user_default->id;
        $branch = $this->branch->create($request->all());

        return new BranchResource($branch);
    }


    /**
     * Actualiza un producto en espesifico
     * @param  $request datos del producto a actualizar
     * @param  $store identificador del producto a actualizar
     * @return Json api rest
     */
    public function update(StoreRequests $request, StoreBranch $branch)
    {
        $request['updated_id'] = Auth::user()->id;

        if ($branch) {
            $branch->update($request->all());
        }

        return $branch;
    }
}
