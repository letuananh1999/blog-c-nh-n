<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CommentController;

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
        Route::get('/', function () {
          return view('admin.dashboard');
        })->name('dashboard');

        Route::resource('categories', CategoryController::class);
        Route::resource('posts', PostController::class);
        Route::resource('users', UserController::class);
        Route::resource('comments', CommentController::class);
      });
  });
