<?php

use Illuminate\Support\Facades\Route;
use Modules\Orders\Http\Controllers\OrdersController;

Route::group(['middleware' => ['auth:api']], function () {
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrdersController::class, 'index']);
        Route::post('/', [OrdersController::class, 'store']);
        Route::put('/{id}', [OrdersController::class, 'update']);
        Route::get('/{id}', [OrdersController::class, 'show']);
        Route::delete('/{id}', [OrdersController::class, 'destroy']);
    });
});