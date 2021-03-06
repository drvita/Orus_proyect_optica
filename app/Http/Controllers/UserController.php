<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\User as UserResource;
Use App\User;

class UserController extends Controller{
    public function __construct(User $user){
        $this->user = $user;
    }
    /**
     * Muestra la lista de usuarios en sistema
     * @return Json api rest
     */
    public function index(Request $request){
        $orderby = $request->orderby? $request->orderby : "created_at";
        $order = $request->order=="desc"? "desc" : "asc";
        $page = $request->itemsPage ? $request->itemsPage : 50;

        $users = $this->user
                ->orderBy($orderby, $order)
                ->Search($request->search)
                ->UserName($request->username)
                ->UserEmail($request->email)
                ->Rol($request->rol)
                ->Bot()
                ->paginate($page);
        return UserResource::collection($users);
    }
    /**
     * Registra un nuevo usuario en la base de datos.
     * @param  $request que se traen de post body json
     * @return Json api rest
     */
    public function store(Request $request){
        $user = User::create([
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'rol' => $request->input('rol'),
            'password' => Hash::make($request->input('password'))
        ]);
        //$user->createToken('AppName')->accessToken;
        return New UserResource($user);
    }
    /**
     * Muestra unj usuario espesifico
     * @param  int  $id
     * @return Json api rest
     */
    public function show(User $user){
        return New UserResource($user);
    }
    /**
     * Actualiza el registro de un susuario
     * @param  $request que se traen del body json
     * @param  int  $id
     * @return Json api rest
     */
    public function update(Request $request, User $user){
        if($request['password']) $request['password'] = Hash::make($request->input('password'));
        $user->update( $request->all() );
        return New UserResource($user);
    }
    /**
     * Elimina un usuario en espesifico.
     * @param  int  $id
     * @return Json api rest
     */
    public function destroy(User $user){
        $user->delete();
        return New UserResource($user);
    }
}
