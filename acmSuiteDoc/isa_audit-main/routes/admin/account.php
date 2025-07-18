<?php

use App\Http\Controllers\Admin\AccountController;
use Illuminate\Support\Facades\Route;

// view
Route::get('account', [AccountController::class, 'index']);
Route::post('account/set', [AccountController::class, 'setUser']);