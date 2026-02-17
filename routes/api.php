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

// Auth & Session
Route::post('user/login', [AuthController::class, 'login'])->name('user.login');
Route::post('auth/validate-password-token', [AuthController::class, 'validateResetToken'])->name('auth.validateToken');
Route::post('auth/reset-password', [AuthController::class, 'resetPassword'])->name('auth.resetPassword');
Route::post('auth/request-reset-by-email', [AuthController::class, 'publicRequestPasswordReset'])->name('auth.requestResetEmail');

Route::middleware('auth:sanctum')->group(function () {
    // Current User Actions
    Route::post('/user/logout', [AuthController::class, 'logout'])->name('users.logout');
    Route::get('/user', [AuthController::class, 'userData'])->name('users.data');
    Route::get('/user/stats', [UserController::class, 'stats'])->name('users.stats');
    Route::post('/user/readAllNotifications', [AuthController::class, 'userReadNotify'])->name('users.readNotify');
    Route::get('/user/subscriptionNotify', [AuthController::class, 'userSubscriptionNotify'])->name('users.subscribtionNotify');
    Route::post('/user/change-branch', [UserController::class, 'changeBranch'])->name('users.changeBranch');
    Route::put('/user/profile', [UserController::class, 'updateProfile'])->name('users.updateProfile');
    Route::post('/user/social/code', [UserController::class, 'generateSocialCode'])->name('users.socialCode');
    Route::post('/user/social/unlink', [UserController::class, 'deleteSocialCode'])->name('users.socialUnlink');
    Route::post('/auth/request-password-reset', [AuthController::class, 'requestPasswordReset'])->name('auth.requestReset');

    // Contacts
    Route::get('contacts/{id}/stats', [ContactController::class, 'stats']);
    Route::apiResource('contacts', ContactController::class);

    // Categories
    Route::get('/categories/lens', [CategoryController::class, 'getLensCategories']);
    Route::post('/categories/setprice/{category}', [CategoryController::class, 'setPriceByCategory']);
    Route::apiResource('categories', CategoryController::class);

    // Config & Branches
    Route::get('/config/branches', [ConfigController::class, 'branches']);
    Route::get('/config/banks', [ConfigController::class, 'banks']);
    Route::apiResource('config', ConfigController::class);

    // Store & Items
    Route::post('/store/bylist', [StoreItemController::class, 'storeList']);
    Route::post('/store/setcant/{item}', [StoreItemController::class, 'setCantItem']);
    Route::apiResource('store', StoreItemController::class);

    // Branches (Store Management)
    Route::apiResource('branches', StoreBranchController::class)->except("show", "destroy");

    // Users
    Route::post('/users/clearToken/{user}', [UserController::class, 'clearToken'])->name('users.clearToken');
    Route::apiResource('users', UserController::class);

    // Other Resources
    Route::apiResource('exams', ExamController::class);
    Route::apiResource('items', StoreLotController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('sales', SaleController::class);
    Route::apiResource('salesItems', SaleItemController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('atms', AtmController::class);
    Route::apiResource('brands', BrandController::class);
    Route::apiResource('messengers', MessengerController::class);
    Route::apiResource('messages', MessengerController::class);
});
