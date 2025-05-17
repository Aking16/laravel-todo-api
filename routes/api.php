<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected
// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/user', fn(Request $r) => $r->user());
//     // Your task routes
// });
