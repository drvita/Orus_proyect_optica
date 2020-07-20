<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
Use App\User;


class Auth extends Controller{

    public function login(){
        $credenciales = $this->validate(request(),[
            'email' => 'email|required|string',
            'password' => 'required|string'
        ]);
        $user = User::where('email',$credenciales['email'])->first(); //$credenciales['email']
        
        if($user && Hash::check($credenciales['password'],$user->password)){
            $user->api_token = hash('sha256', Str::random(60));
            $user->save();
            $respons['data'] = $user;
            $respons['message'] = "Bienvenido al sistema";
            $respons['token'] = $user->api_token;
        } else {
            $respons['errors'] = "El usuario no se encuentra registrado";
        }

        return $respons;
    }
    public function logout(){
        $user = auth()->user();
        $user->api_token = null;
        $user->save();
        return ['message' => 'Ha salido del sistema correctamente'];
    }
}
