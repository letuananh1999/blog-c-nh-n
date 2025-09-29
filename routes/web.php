<?php

// use App\Http\Controllers\HomeControllers;

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Admin\HomeControllers;
use Illuminate\Support\Facades\Route;


Route::get('/', [HomeControllers::class, 'index'])->name('admin.index');
// Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
// Route::get('/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');
// Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
// Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
// Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('admin.categories.update');
// Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
//group dành cho admin
Route::prefix('admin')
  ->as('admin.')
  ->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('comments', CommentController::class);
    Route::resource('posts', PostController::class);
  });
