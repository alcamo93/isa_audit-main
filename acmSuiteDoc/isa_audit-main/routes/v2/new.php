<?php

use App\Http\Controllers\V2\NewController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2'], function () {
	Route::controller(NewController::class)->group(function () {
		Route::get('new/view', 'view')->name('v2.new.view');
		Route::get('new', 'index')->name('v2.new.index');
		Route::post('new', 'store')->name('v2.new.store');
		Route::get('new/{id}', 'show')->name('v2.new.show');
		Route::post('new/{id}', 'update')->name('v2.new.update');
		Route::delete('new/{id}', 'destroy')->name('v2.new.destroy');
		Route::put('new/{id_status}/{id}/status', 'status')->name('v2.new.status');
		Route::get('new/{id_new}/view', 'newView')->name('v2.new.newView');
	});
});