<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return response()->json([
        'message' => 'AutoCatalogue API',
        'version' => '1.0.0',
        'admin' => url('/admin'),
        'api' => url('/api/v1'),
        'docs' => 'https://github.com/autostyle/catalogue-backend#api-endpoints',
    ]);
});

// Admin routes are handled by Filament