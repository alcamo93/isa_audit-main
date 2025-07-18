<?php

use App\Http\Controllers\V2\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v2'], function () {
  Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login')->name('v2.auth.login');
    Route::post('refresh-token', 'setRefreshToken')->name('v2.auth.refresh');
  });
});