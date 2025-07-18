<?php

use App\Http\Controllers\V2\ProfileController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2'], function () {
	Route::controller(ProfileController::class)->group(function () {
		Route::get('what/profile', 'whatProfile')->name('process.what.profile');
		Route::get('profile/view', 'view')->name('profile.view');
		Route::get('profile', 'index')->name('profile.index');
		Route::post('profile', 'store')->name('profile.store');
		Route::get('profile/{id}', 'show')->name('profile.show');
		Route::put('profile/{id}', 'update')->name('profile.update');
		Route::delete('profile/{id}', 'destroy')->name('profile.destroy');
	});
});