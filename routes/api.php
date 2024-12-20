<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\UserController;

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
Route::post('/refresh-token-login', [AuthController::class, 'refreshLoginToken']);
Route::post('/register', [AuthController::class, 'register']); // Opcional
Route::post('/register-hashed', [UserController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/devices-info/{id}', [DeviceController::class, 'getDeviceInfo']);
});

Route::middleware('auth:sanctum')->get('/user-info', [AuthController::class, 'getUserInfo']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/devices-accesed', [DeviceController::class, 'getDevicesAccesed']); // Get all devices assigned to the user (Detailed)
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/devices-accesed-detailed', [DeviceController::class, 'getDevicesAccesedDetailed']); // Get all devices assigned to the user (Detailed)
});

Route::post('/devices/assign', [DeviceController::class, 'assignDevice']); // Assign a device to a user
