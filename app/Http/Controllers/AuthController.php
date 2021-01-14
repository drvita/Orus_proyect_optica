<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
Use App\User;
Use App\Models\Session;


class AuthController extends Controller{
    public function __construct(User $user){
        $this->user = $user;
    }
    public function login(){
        $credenciales = $this->validate(request(),[
            'email' => 'email|required|string',
            'password' => 'required|string'
        ]);
        $user = $this->user
            ->UserEmail($credenciales['email'])
            ->first();
            
        if($user && Hash::check($credenciales['password'],$user->password)){
            $user->api_token = hash('sha256', Str::random(60));
            $user->save();
            $session = Session::where("session_id","LIKE",$user->id)->first();
            if($session){
                $session->ip_address = getenv("REMOTE_ADDR");
                $session->user_agent = getenv("HTTP_USER_AGENT");
                $session->last_activity = date('Y-m-d H:i:s');
                $session->user_data = $user->api_token;
                $session->save();
            } else {
                $session = Session::create([
                    'session_id' => $user->id,
                    'ip_address' => getenv("REMOTE_ADDR"),
                    'user_agent' => getenv("HTTP_USER_AGENT"),
                    'last_activity' => date('Y-m-d H:i:s'),
                    'user_data' => $user->api_token
                ]);
            }

            $respons['data'] = $user;
            $respons['message'] = "Bienvenido al sistema";
            $respons['token'] = $user->api_token;
            return response()->json($respons, 200);
        } else {
            $respons['errors'] = $user && $user->id ? "La contrasena es incorrecta" : "Las credenciales del usuario no son correctas";
            $respons['errnum'] = $user && $user->id ? $user->id : 0;
            return response()->json($respons, 401);
        }

        
    }
    public function logout(){
        $user = $this->user;
        $user->api_token = null;
        $user->save();
        return response()->json(['message' => 'Ha salido del sistema correctamente'], 200);
    }
}
