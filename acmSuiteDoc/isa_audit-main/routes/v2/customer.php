<?php

use App\Http\Controllers\V2\CustomerController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2'], function () {
	Route::controller(CustomerController::class)->group(function () {
		Route::get('customer/view', 'view')->name('v2.customer.view');
		Route::get('customer', 'index')->name('v2.customer.index');
		Route::post('customer', 'store')->name('v2.customer.store');
		Route::get('customer/{id}', 'show')->name('v2.customer.show');
		Route::put('customer/{id}', 'update')->name('v2.customer.update');
		Route::delete('customer/{id}', 'destroy')->name('v2.customer.destroy');
		Route::post('customer/{id}/image/{usage}', 'image')->name('v2.customer.image');
	});
});