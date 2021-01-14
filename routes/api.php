<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\UserNoty;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return new UserNoty($request->user());
});
Route::middleware('auth:api')->post('/user/readAllNotifications', function (Request $request) {
    $res = ["success" => false,"id" => $request->id];
    $code = 402;
    foreach ($request->user()->unreadNotifications as $notification) {
        if($request->id === -1){
            $notification->markAsRead();
            $res =["success" => true,"id" => $request->id];
            $code = 200;
        } else if($request->id === $notification->id){
            $notification->markAsRead();
            $res =["success" => true,"id" => $request->id];
            $code = 200;
            break;
        }
    }
    return response()->json($res, $code);
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
// Rutas especiales
Route::post('users/login','AuthController@login')->name('users.login');
Route::middleware('auth:api')->post('users/logout','AuthController@logout')->name('users.logout');
Route::middleware('auth:api')->get('saleday','PaymentController@saleday')->name('payments.saleday');


