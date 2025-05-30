<?php

use App\Http\Controllers\v1\OrderController;
use App\Http\Controllers\v1\ProductController;
use App\Http\Controllers\v1\SalesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\UserController;

Route::prefix('v1')->group(function () {

    Route::post('/login', [UserController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [UserController::class, 'logout']);
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        
        Route::apiResource('products', ProductController::class)->only(['index', 'show'])->parameters(['products' => 'pizza_id']);
        
        Route::apiResource('orders', OrderController::class)->only(['index', 'show']);

        Route::get('/sales/daily', [SalesController::class, 'getDailyOrdersWithDetails']);
        Route::get('/sales/monthly', [SalesController::class, 'getMonthlySales']);
    });

});