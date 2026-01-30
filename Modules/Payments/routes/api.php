<?php

use Illuminate\Support\Facades\Route;
use Modules\Payments\Http\Controllers\PaymentsController;



Route::group(['middleware' => ['auth:api']], function () {
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentsController::class, 'index']);
        Route::post('/orders/{id}/pay', [PaymentsController::class, 'pay']);
        Route::get('/orders/{id}', [PaymentsController::class, 'orderPayments']);
        Route::get('orders/{id}/list', [PaymentsController::class, 'getOrderPayments']);

    });
});

