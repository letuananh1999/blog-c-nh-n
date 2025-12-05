<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;

/**
 * Public API Routes
 */
Route::get('/test', function () {
    return response()->json(['message' => 'API hoạt động!']);
});

// Auth endpoints
Route::post('/login', [AuthController::class, 'login']);

// Public post endpoints
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post}', [PostController::class, 'show']);
Route::get('/posts/search', [PostController::class, 'search']);

/**
 * Protected API Routes (require authentication)
 */
Route::middleware('auth:sanctum')->group(function () {
    // Post management (Admin only)
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
});
