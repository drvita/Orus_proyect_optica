<?php

use App\Http\Controllers\AtmController;
use App\Http\Controllers\AuthController;
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
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->apiResource('contacts', ContactController::class);
Route::middleware('auth:sanctum')->apiResource('categories', CategoryController::class);
Route::middleware('auth:sanctum')->apiResource('config', ConfigController::class);
Route::middleware('auth:sanctum')->apiResource('exams', ExamController::class);
Route::middleware('auth:sanctum')->apiResource('store', StoreItemController::class);
Route::middleware('auth:sanctum')->apiResource('items', StoreLotController::class);
Route::middleware('auth:sanctum')->apiResource('orders', OrderController::class);
Route::middleware('auth:sanctum')->apiResource('sales', SaleController::class);
Route::middleware('auth:sanctum')->apiResource('salesItems', SaleItemController::class);
Route::middleware('auth:sanctum')->apiResource('payments', PaymentController::class);
Route::middleware('auth:sanctum')->apiResource('atms', AtmController::class);
Route::middleware('auth:sanctum')->apiResource('brands', BrandController::class);
Route::middleware('auth:sanctum')->apiResource('messengers', MessengerController::class);
Route::middleware('auth:sanctum')->apiResource('users', UserController::class);
Route::middleware('auth:sanctum')->apiResource('branches', StoreBranchController::class)->except("index", "show", "destroy");

// Rutas especiales
Route::post('user/login', [AuthController::class, 'login'])->name('user.login');
Route::post('users/login', [AuthController::class, 'login'])->name('users.login');
Route::middleware('auth:sanctum')->post('/users/clearToken/{id}', [UserController::class, 'clearToken'])->name('users.clearToken');
Route::middleware('auth:sanctum')->post('/user/logout', [AuthController::class, 'logout'])->name('users.logout');
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'userData'])->name('users.data');
Route::middleware('auth:sanctum')->post('/user/readAllNotifications', [AuthController::class, 'userReadNotify'])->name('users.readNotify');
Route::middleware('auth:sanctum')->get('/user/subscriptionNotify', [AuthController::class, 'userSubscriptionNotify'])->name('users.subscribtionNotify');

Route::middleware('auth:sanctum')->post('/categories/setprice/{category}', [CategoryController::class, 'setPriceByCategory']);
Route::middleware('auth:sanctum')->post('/store/bylist', [StoreItemController::class, 'storeList']);
Route::middleware('auth:sanctum')->post('/store/setcant/{item}', [StoreItemController::class, 'setCantItem']);
