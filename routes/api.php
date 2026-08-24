<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['guest','web', 'throttle:6,1'], 'prefix' => 'v1'], function () {
    // Public routes go here
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [RegisterController::class, 'store']);
});


Route::group(['middleware' => ['auth:sanctum', 'web'], 'prefix' => 'v1'], function () {
    // Protected routes go here

    // Users
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('users/{user}', [UserController::class, 'update']);
    Route::delete('users/{user}', [UserController::class, 'destroy']);
    Route::put('users/{user}/avatar', [UserController::class, 'updateAvatar']);


});

