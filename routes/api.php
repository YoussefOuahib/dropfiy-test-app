<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserSettingController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Sanctum authenticated routes
Route::middleware('auth:sanctum')->group(function () {

    // Authenticated user route
    Route::get('/user', [AuthController::class, 'user']);

    // Logout route
    Route::post('/logout', [AuthController::class, 'logout']);

    
    // Feed routes
    Route::apiResource('feeds', FeedController::class);
    Route::get('/feeds-report', [FeedController::class, 'getReport']);
    Route::post('feeds/{feed}/sync', [FeedController::class, 'sync'])
        ->name('feeds.sync');
    Route::post('/feeds/{feed}/detach-product', [FeedController::class, 'detachProduct'])->name('feeds.detach-product');


    //Product  routes
    Route::apiResource('products', ProductController::class);
    Route::post('products/{product}/sync', [ProductController::class, 'sync']);
    Route::post('products/{product}/detach-feed', [ProductController::class, 'detachFeed']);


    //User Setting Routes
    Route::get('/user-settings', [UserSettingController::class, 'index']);
    Route::put('/user-settings', [UserSettingController::class, 'update']);
    Route::post('/user-settings/reset', [UserSettingController::class, 'resetToDefault']);




});
