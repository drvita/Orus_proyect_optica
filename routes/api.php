<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:api')->apiResource('users','UserController');
Route::middleware('auth:api')->apiResource('contacts','ContactController');
Route::middleware('auth:api')->apiResource('categories','CategoryController');
Route::post('users/login','Auth@login')->name('users.login');
Route::middleware('auth:api')->post('users/logout','Auth@logout')->name('users.logout');

//Route::get('/', 'UserController@index');
//Route::middleware('auth:api')->apiResource('users','UserController');
//Route::get('users','UserController@index');
//Route::get('users/{id}','UserController@show');
//Route::post('users','UserController@store');
//Route::put('users/{id}','UserController@update');
//Route::delete('users/{id}','UserController@destroy');


