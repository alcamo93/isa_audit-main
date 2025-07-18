<?php

use App\Http\Controllers\V2\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2'], function () {
	Route::controller(UserController::class)->group(function () {
		Route::get('user/view', 'view')->name('v2.user.view');
		Route::get('user', 'index')->name('v2.user.index');
		Route::post('user', 'store')->name('v2.user.store');
		Route::get('user/{id}', 'show')->name('v2.user.show');
		Route::put('user/{id}', 'update')->name('v2.user.update');
		Route::delete('user/{id}', 'destroy')->name('v2.user.destroy');
		Route::put('user/{id}/password', 'password')->name('v2.user.update.password');
	});
});