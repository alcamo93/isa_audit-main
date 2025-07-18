<?php

use App\Http\Controllers\V2\AccountController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2'], function () {
  Route::controller(AccountController::class)->group(function () {
    Route::get('account/view', 'view')->name('v2.account.view');
    Route::get('account', 'index')->name('v2.account.index');
    Route::put('account', 'update')->name('v2.account.update');
    Route::post('account/image', 'image')->name('v2.account.image');
  });
});