<?php

use App\Http\Controllers\V2\CorporateController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verify.ownership.corporate.api'], 'prefix' => 'v2'], function () {
	Route::controller(CorporateController::class)->group(function () {
		Route::get('customer/{idCustomer}/corporate/view', 'view')->name('v2.corporate.view');
		Route::get('customer/{idCustomer}/corporate', 'index')->name('v2.corporate.index');
		Route::post('customer/{idCustomer}/corporate', 'store')->name('v2.corporate.store');
		Route::get('customer/{idCustomer}/corporate/{idCorporate}', 'show')->name('v2.corporate.show');
		Route::put('customer/{idCustomer}/corporate/{idCorporate}', 'update')->name('v2.corporate.update');
		Route::delete('customer/{idCustomer}/corporate/{idCorporate}', 'destroy')->name('v2.corporate.destroy');
		Route::post('customer/{idCustomer}/corporate/{idCorporate}/image', 'image')->name('v2.corporate.image');
	});
});