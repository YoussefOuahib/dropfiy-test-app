<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');