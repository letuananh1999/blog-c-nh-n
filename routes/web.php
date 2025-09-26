<?php

// use App\Http\Controllers\HomeControllers;

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Admin\HomeControllers;
use Illuminate\Support\Facades\Route;

// Route::get('/post/{slug}', [HomeControllers::class, 'show'])->name('post.show');
// Route::post('/post/{slug}/comment', [CommentController::class, 'store']);

//group dành cho admin
Route::prefix('admin')
  ->group(function () {
    Route::get('/', [HomeControllers::class, 'index'])->name('admin.index');
    Route::resource('category', CategoryController::class);
    Route::resource('comments', CommentController::class);
    Route::resource('posts', PostController::class);
  });
