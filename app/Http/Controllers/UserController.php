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
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
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
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $user = User::find($id);
        return New UserResource($user);
    }
    /**
     * Update the specified resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        $user = User::find($id);
        /*
        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->rol = $request->input('rol');
        $user->api_token = hash('sha256', Str::random(60));
        $user->save();
        */
        $user->fill([
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'rol' => $request->input('rol'),
            'api_token' => hash('sha256', Str::random(60))
        ])->save();
        return New UserResource($user);
    }
    /**
     * Remove the specified resource from storage.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        $user = $this->show($id);
        $user->delete();
        return New UserResource($user);
    }
}
