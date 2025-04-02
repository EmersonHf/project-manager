<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V2\ProjectController;
use App\Http\Controllers\Api\V2\AuthController;
use App\Http\Controllers\Api\V2\PropertyController;
use App\Http\Controllers\Api\V2\LeaseController;
use App\Http\Controllers\Api\V2\MaintenanceRequestController;
use App\Http\Controllers\Api\V2\PaymentController;
use Illuminate\Http\Request;

// Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Property Routes (protected by auth and role middleware)
Route::middleware(['auth:sanctum', 'role:admin,manager'])->group(function () {
    Route::apiResource('properties', PropertyController::class);
});

// Lease Routes (protected by auth and role middleware)
Route::middleware(['auth:sanctum', 'role:admin,manager'])->group(function () {
    Route::apiResource('leases', LeaseController::class);
});

// Maintenance Request Routes (protected by auth and role middleware)
Route::middleware(['auth:sanctum', 'role:admin,manager'])->group(function () {
    Route::apiResource('maintenance-requests', MaintenanceRequestController::class);
});

// Payment Routes (protected by auth and role middleware)
Route::middleware(['auth:sanctum', 'role:admin,manager'])->group(function () {
    Route::apiResource('payments', PaymentController::class);
});

// Project Routes (protected by auth and role middleware)
Route::middleware(['auth:sanctum', 'role:admin,manager'])->group(function () {
    Route::apiResource('projects', ProjectController::class);
    
});
