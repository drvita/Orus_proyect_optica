<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Messenger as MessengerResource;
use App\Http\Requests\Messenger as MessengerRequests;
use App\Models\Messenger;
use Illuminate\Support\Facades\Auth;

class MessengerController extends Controller
{
    public function __construct(Messenger $messenger)
    {
        $this->middleware('can:messenger.list')->only('index');
        $this->middleware('can:messenger.show')->only('show');
        $this->middleware('can:messenger.add')->only('store');
        $this->middleware('can:messenger.edit')->only('update');
        $this->middleware('can:messenger.delete')->only('destroy');
        $this->messenger = $messenger;
    }
    /**
     * Muestra la lista de usuarios en sistema
     * @return Json api rest
     */
    public function index(Request $request)
    {
        $orderby = $request->orderby ? $request->orderby : "created_at";
        $order = $request->order === "asc" ? "asc" : "desc";
        $page = $request->itemsPage ? $request->itemsPage : 50;

        $messenger = $this->messenger
            ->orderBy($orderby, $order)
            ->Table($request->table)
            ->IdRow($request->idRow)
            ->paginate(50);
        return MessengerResource::collection($messenger);
    }
    /**
     * Registra un nuevo usuario en la base de datos.
     * @param  $request que se traen de post body json
     * @return Json api rest
     */
    public function store(MessengerRequests $request)
    {
        $request['user_id'] = Auth::user()->id;
        $messenger = $this->messenger->create($request->all());
        return new MessengerResource($messenger);
    }
    /**
     * Muestra unj usuario espesifico
     * @param  int  $id
     * @return Json api rest
     */
    public function show(Messenger $messanger)
    {
        return new MessengerResource($messanger);
    }
    /**
     * Actualiza el registro de un susuario
     * @param  $request que se traen del body json
     * @param  int  $id
     * @return Json api rest
     */
    public function update(MessengerRequests $request, Messenger $messanger)
    {
        $request['user_id'] = $messanger->user_id;
        $messanger->update($request->all());
        return new MessengerResource($messanger);
    }
    /**
     * Elimina un usuario en espesifico.
     * @param  int  $id
     * @return Json api rest
     */
    public function destroy(Messenger $messanger)
    {
        $messanger->delete();
        return response()->json(null, 204);
    }
}
