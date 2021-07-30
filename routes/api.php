<?php
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->apiResource('contacts','ContactController');
Route::middleware('auth:api')->apiResource('categories','CategoryController');
Route::middleware('auth:api')->apiResource('config','ConfigController');
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
Route::middleware('auth:api')->post('/users/clearToken/{id}','UserController@clearToken')->name('users.clearToken');
Route::middleware('auth:api')->post('/user/logout','AuthController@logout')->name('users.logout');
Route::middleware('auth:api')->get('/user','AuthController@userData')->name('users.data');
Route::middleware('auth:api')->post('/user/readAllNotifications', 'AuthController@userReadNotify')->name('users.readNotify');
Route::middleware('auth:api')->get('/user/subscriptionNotify','AuthController@userSubscriptionNotify')->name('users.subscribtionNotify');
//DELETE in the future
Route::middleware('auth:api')->get('saleday','PaymentController@saleday')->name('payments.saleday');
Route::middleware('auth:api')->get('bankdetails','PaymentController@bankdetails')->name('payments.bankdetails');


