<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TikTokController;

// Home route
Route::get('/', function () {
    return view('welcome');
});

// TikTok routes
Route::get('/search', [TikTokController::class, 'search']);
Route::get('/username/{username}', [TikTokController::class, 'getUserProfile']);
Route::get('/video/{videoId}', [TikTokController::class, 'getVideo']);
Route::get('/api/user-posts/{userId}', [TikTokController::class, 'getUserPosts']);
Route::get('/api/popular-posts/{userId}', [TikTokController::class, 'getPopularPosts']);