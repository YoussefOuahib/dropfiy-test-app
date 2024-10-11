<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\ProductController;
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

    // Feed resource routes
    Route::apiResource('feeds', FeedController::class);

    //Custom Route for Data reporting 
    Route::get('/feeds-report', [FeedController::class, 'getReport']);
    // Custom route for syncing and submitting a feed
    Route::post('feeds/{feed}/sync', [FeedController::class, 'sync'])
        ->name('feeds.sync');
    // Custom route for detaching a product
    Route::post('/feeds/{feed}/detach-product', [FeedController::class, 'detachProduct'])->name('feeds.detach-product');



    //Product resource routes
    Route::apiResource('products', ProductController::class);




});
