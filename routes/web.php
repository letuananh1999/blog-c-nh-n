<?php

// use App\Http\Controllers\HomeControllers;

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\web\HomeControllers;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeControllers::class, 'index'])->name('home');
Route::get('/post/{slug}', [HomeControllers::class, 'show'])->name('post.show');

//group dành cho admin
Route::prefix('admin')
  ->group(function () {
    Route::resource('category', CategoryController::class);
    Route::resource('comments', CommentController::class);
    Route::resource('posts', PostController::class);
  });
