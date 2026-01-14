<?php

use App\Http\Controllers\Api\Auth;
use App\Http\Controllers\Api\User;
use Illuminate\Support\Facades\Route;

Route::post('/auth/send-code', [Auth::class, 'sendCode'])
    ->name('send-code')
    ->middleware('throttle:send-code');
Route::post('/auth/login', [Auth::class, 'loginWithCode'])
    ->name('login')
    ->middleware('throttle:login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/me', [User::class, 'me']);
});

