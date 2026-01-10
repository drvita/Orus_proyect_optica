<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $package = json_decode(file_get_contents(base_path('composer.json')), true);

    return response()->json([
        'status' => 'healthy',
        'name' => $package['name'] ?? 'unknown',
        'description' => $package['description'] ?? 'unknown',
        'version' => $package['version'] ?? '1.0.0',
    ]);
});
