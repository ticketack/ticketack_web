<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\EquipmentController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\NotificationController;
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
    // Auth
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);
    
    // Notifications - uniformisé pour éviter les doublons
    Route::get('notifications/count', [NotificationController::class, 'getUnreadCount'])->name('api.notifications.count');
    Route::get('notifications/recent', [NotificationController::class, 'getRecentNotifications'])->name('api.notifications.recent');
    
    // Equipment management
    Route::get('equipment/search', [EquipmentController::class, 'search'])->name('api.equipment.search');
    Route::apiResource('equipment', EquipmentController::class, ['as' => 'api']);
    
    // Tickets management
    Route::apiResource('tickets', TicketController::class, ['as' => 'api']);
    
    // Roles management (avec permission)
    Route::middleware('permission:manage roles')->group(function () {
        Route::apiResource('roles', RoleController::class, ['as' => 'api']);
    });
    
    // Permissions management (avec permission)
    Route::middleware('permission:manage permissions')->group(function () {
        Route::apiResource('permissions', PermissionController::class, ['as' => 'api']);
    });
});
