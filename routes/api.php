<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:api')->apiResource('contacts','ContactController');
Route::middleware('auth:api')->apiResource('categories','CategoryController');
Route::middleware('auth:api')->apiResource('exams','ExamController');
Route::middleware('auth:api')->apiResource('store','StoreItemController');
Route::middleware('auth:api')->apiResource('items','StoreLotController');
Route::middleware('auth:api')->apiResource('orders','OrderController');
Route::middleware('auth:api')->apiResource('sales','SaleController');
Route::middleware('auth:api')->apiResource('salesItems','SaleItemController');
Route::middleware('auth:api')->apiResource('payments','PaymentController');
Route::middleware('auth:api')->apiResource('atms','AtmController');
Route::middleware('auth:api')->apiResource('brands','BrandController');
Route::middleware('auth:api')->apiResource('messengers','MessengerController');
Route::middleware('auth:api')->apiResource('users','UserController');
Route::post('users/login','Auth@login')->name('users.login');
Route::middleware('auth:api')->post('users/logout','Auth@logout')->name('users.logout');

//Route::get('/', 'UserController@index');
//Route::middleware('auth:api')->apiResource('users','UserController');
//Route::get('users','UserController@index');
//Route::get('users/{id}','UserController@show');
//Route::post('users','UserController@store');
//Route::put('users/{id}','UserController@update');
//Route::delete('users/{id}','UserController@destroy');


