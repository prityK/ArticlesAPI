<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordResetController;


Route::get('/', function () {
    return view('welcome');
});

Route::post("/register",[AuthController::class,"register"]);
Route::post("/login",[AuthController::class,"login"]);
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::get('password/reset', [PasswordResetController::class, 'request'])->name('password.request');
Route::post('password/email', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('password/reset/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
Route::post('password/reset', [PasswordResetController::class, 'update'])->name('password.update');
