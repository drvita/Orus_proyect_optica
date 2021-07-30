<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\User as UserResource;
Use App\User;
Use App\Models\Session;
use Carbon\Carbon;


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
        $search = $request->search;
        $userId = $request->userId;
        $username = $request->username;
        $email = $request->email;
        $rol = $request->rol;
        $deleted = true;

        if(isset($request->deleted)){
            $deleted = (int) $request->deleted;
            $deleted = (boolean) $deleted;
        }

        $users = $this->user
                ->with('session')
                ->orderBy($orderby, $order)
                ->search($search)
                ->userName($username, $userId)
                ->userEmail($email, $userId)
                ->rol($rol)
                ->bot()
                ->notDelete($deleted)
                ->paginate($page);

        return UserResource::collection($users);
    }
    /**
     * Registra un nuevo usuario en la base de datos.
     * @param  $request que se traen de post body json
     * @return Json api rest
     */
    public function store(Request $request){
        $auth = Auth::user();

        if($auth->rol){
            return response()->json([
                "data" => [],
                "message" => "No tiene permisos administrativos"
            ], 401);
        }

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
        $auth = Auth::user();

        if($auth->rol){
            return response()->json([
                "data" => [],
                "message" => "No tiene permisos administrativos"
            ], 401);
        }

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
        try {
            $auth = Auth::user();

            if($auth->rol){
                return response()->json([
                    "data" => [],
                    "message" => "No tiene permisos administrativos"
                ], 401);
            }

            $user->delete();
            return response()->json(null, 204);
        } catch (\Throwable $th) {
            $user->deleted_at = Carbon::now();
            $user->api_token = null;
            $user->save();
            return response()->json(null, 204);
        }
    }
    /**
     * Limpia el token de un usuario
     */
    public function clearToken($id){
        $user = $this->user::find($id);
        $auth = Auth::user();

        if($user && !$auth->rol){
            $session = Session::where('session_id',$id);
            $session->delete();
            $user->api_token = null;
            $user->save();

            return response()->json([
                "success" => true
            ], 202);
        }

        return response()->json([
            "success" => false,
        ], 401);
    }
}
