<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Messenger as MessengerResource;
Use App\Models\Messenger;

class MessengerController extends Controller{
    public function __construct(Messenger $messenger){
        $this->messenger = $messenger;
    }
    /**
     * Muestra la lista de usuarios en sistema
     * @return Json api rest
     */
    public function index(Request $request){
        $orderby = $request->orderby? $request->orderby : "created_at";
        $order = $request->order=="desc"? "desc" : "asc";

        $users = $this->messenger
                ->orderBy($orderby, $order)
                ->Table($request->table)
                ->IdRow($request->idRow)
                ->paginate(10);
        return MessengerResource::collection($users);
    }
    /**
     * Registra un nuevo usuario en la base de datos.
     * @param  $request que se traen de post body json
     * @return Json api rest
     */
    public function store(Request $request){
        $user = Messenger::create([
            'message' => $request->input('message')
        ]);
        return New MessengerResource($user);
    }
    /**
     * Muestra unj usuario espesifico
     * @param  int  $id
     * @return Json api rest
     */
    public function show(Messenger $messanger){
        return New MessengerResource($messanger);
    }
    /**
     * Actualiza el registro de un susuario
     * @param  $request que se traen del body json
     * @param  int  $id
     * @return Json api rest
     */
    public function update(Request $request, Messenger $messanger){
        $request['user_id']=$messanger->user_id;
        $messanger->update( $request->all() );
        return New MessengerResource($messanger);
    }
    /**
     * Elimina un usuario en espesifico.
     * @param  int  $id
     * @return Json api rest
     */
    public function destroy(Messenger $messanger){
        $messanger->delete();
        return response()->json(null, 204);
    }
}
