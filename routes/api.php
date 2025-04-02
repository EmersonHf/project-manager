<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V2\ProjectController;
use App\Http\Controllers\Api\V2\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication Routes
Route::post('/v2/register', [AuthController::class, 'register']);
Route::post('/v2/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/v2/logout', [AuthController::class, 'logout']);
    Route::get('/v2/user', function (Request $request) {
        return $request->user();
    });

    // Project routes
    Route::apiResource('v2/projects', ProjectController::class);
});
