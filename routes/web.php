<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\HomeControllers;
// Root route - redirect tới login
Route::redirect('/', '/admin/login');

Route::prefix('admin')
  ->name('admin.')
  ->group(function () {

    // 👉 Các route đăng nhập, đăng xuất (không cần đăng nhập mới vào được)
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // 👉 Các route chỉ cho admin sau khi đăng nhập + có quyền admin
    Route::middleware(['auth', 'checkrole:admin'])
      ->group(function () {
        Route::get('/', [HomeControllers::class, 'index'])->name('index');

        Route::get('/categories/search', [CategoryController::class, 'search'])->name('categories.search');
        Route::get('/categories/{category}/version', [CategoryController::class, 'getVersion'])->name('categories.getVersion');
        Route::resource('categories', CategoryController::class);
        Route::resource('posts', PostController::class);
        Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
        Route::resource('users', UserController::class);
        Route::patch('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])
          ->name('users.toggleStatus');
        Route::resource('comments', CommentController::class);

        // Comment custom routes
        Route::patch('/comments/{id}/approve', [CommentController::class, 'approve'])
          ->name('comments.approve');
        Route::patch('/comments/{id}/unapprove', [CommentController::class, 'unapprove'])
          ->name('comments.unapprove');
        Route::post('/comments/{id}/reply', [CommentController::class, 'reply'])
          ->name('comments.reply');
      });
  });
