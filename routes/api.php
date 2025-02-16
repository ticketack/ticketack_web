<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\EquipementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Test route
Route::get('ping', function () {
    return response()->json(['message' => 'pong']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);
    
    // Equipements management
    Route::apiResource('equipements', EquipementController::class);
    
    // Roles management
    Route::middleware('permission:manage roles')->group(function () {
        Route::apiResource('roles', RoleController::class);
    });

    // Permissions management
    Route::middleware('permission:manage permissions')->group(function () {
        Route::apiResource('permissions', PermissionController::class);
    });
});
