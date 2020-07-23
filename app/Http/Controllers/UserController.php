<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Resources\User as UserResource;
Use App\User;

class UserController extends Controller
{
    /**
     * Muestra la lista de usuarios en sistema
     * @return Json api rest
     */
    public function index(){
        $users = User::all();
        //dd($users);
        if(! $users){
            abort(404);
        }
        //$return['data'] = $users;
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
            'password' => Hash::make($request->input('password')),
            'api_token' => hash('sha256', Str::random(60))
        ]);
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
        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->rol = $request->input('rol');
        $user->api_token = hash('sha256', Str::random(60));
        $user->save();
        /*
        $user->fill([
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'rol' => $request->input('rol'),
            'api_token' => hash('sha256', Str::random(60))
        ])->save();
        */
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
