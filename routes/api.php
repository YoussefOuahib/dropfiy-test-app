<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeedController;
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

    // Custom route for generating and submitting a feed
    Route::post('feeds/{feed}/generate', [FeedController::class, 'generateAndSubmit'])
        ->name('feeds.generate');

});
