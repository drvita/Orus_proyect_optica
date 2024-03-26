<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\v2\UserController;
use App\Http\Controllers\AtmController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\MessengerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleItemController;
use App\Http\Controllers\StoreBranchController;
use App\Http\Controllers\StoreItemController;
use App\Http\Controllers\StoreLotController;
use Illuminate\Support\Facades\Route;

Route::post('user/login', [AuthController::class, 'login'])->name('users.login');

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/store/bylist', [StoreItemController::class, 'storeList']);
    Route::apiResource('contacts', ContactController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('config', ConfigController::class);
    Route::apiResource('exams', ExamController::class);
    Route::apiResource('store', StoreItemController::class);
    Route::apiResource('items', StoreLotController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('sales', SaleController::class);
    Route::apiResource('salesItems', SaleItemController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('atms', AtmController::class);
    Route::apiResource('brands', BrandController::class);
    Route::apiResource('messengers', MessengerController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('branches', StoreBranchController::class)->except(['index', 'show', 'destroy']);

    // Rutas especiales
    Route::post('/users/clearToken/{id}', [UserController::class, 'clearToken'])->name('users.clearToken');
    Route::post('/user/logout', [AuthController::class, 'logout'])->name('users.logout');
    Route::get('/user', [AuthController::class, 'userData'])->name('users.data');
    Route::post('/user/readAllNotifications', [AuthController::class, 'userReadNotify'])->name('users.readNotify');
    Route::get('/user/subscriptionNotify', [AuthController::class, 'userSubscriptionNotify'])->name('users.subscribtionNotify');

    Route::post('/categories/setprice/{category}', [CategoryController::class, 'setPriceByCategory']);
    Route::post('/store/setcant/{item}', [StoreItemController::class, 'setCantItem']);
});
