<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FoodController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\OrderController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('foods', FoodController::class);
    Route::get('/tables', [TableController::class, 'index']);

    Route::post('/orders/open', [OrderController::class, 'openOrder']);
    Route::post('/orders/{order}/add-item', [OrderController::class, 'addItem']);
    Route::post('/orders/{order}/close', [OrderController::class, 'closeOrder']);
});
