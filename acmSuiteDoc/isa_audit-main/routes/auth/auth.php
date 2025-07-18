<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

/**
 * Handler login and logout
 */
Route::get('/', [LoginController::class,  'index']);
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout']);
/**
 * Handler access recovery
 */
Route::post('login/reset', [ResetPasswordController::class, 'resetToken']);
Route::get('login/reset/{token}', [ResetPasswordController::class, 'index']);
Route::post('login/setReset', [ResetPasswordController::class, 'setReset']);
