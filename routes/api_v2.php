<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V2\ProjectController;
use App\Http\Controllers\Api\V2\AuthController;

Route::prefix('v2')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::apiResource('projects', ProjectController::class);
    });
});
