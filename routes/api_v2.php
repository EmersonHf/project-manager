<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V2\ProjectController;
use App\Http\Controllers\Api\V2\AuthController;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});
Route::middleware(['auth:sanctum', 'role:admin,project_manager'])->group(function () {
    Route::apiResource('projects', ProjectController::class);
});
