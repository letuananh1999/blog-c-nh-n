<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
// use Illuminate\Routing\Router;

Route::get('/test', function () {
  return response()->json(['message' => 'API hoạt động!']);
});
Route::post('/login', [AuthController::class, 'login']);
