<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->middleware("throttle:auth");
    Route::post('login', [AuthController::class, 'login'])->middleware("throttle:auth");
});

Route::group(['middleware' => ['auth:api']], function () {
    Route::prefix('auth')->group(function () {
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});