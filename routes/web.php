<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $package = json_decode(file_get_contents(base_path('composer.json')), true);

    return response()->json([
        'status' => 'healthy',
        'name' => $package['name'] ?? 'unknown',
        'description' => $package['description'] ?? 'unknown',
        'version' => $package['version'] ?? '1.0.0',
    ]);
});
