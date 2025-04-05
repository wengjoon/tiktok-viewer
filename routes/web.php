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

// API routes for fetching videos
Route::get('/api/user/{userId}/videos', [TikTokController::class, 'getUserPosts']);
Route::get('/api/user/{userId}/popular', [TikTokController::class, 'getPopularPosts']);