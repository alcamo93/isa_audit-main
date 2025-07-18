<?php

use App\Http\Controllers\V2\AddressController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verify.ownership.corporate.address.api'], 'prefix' => 'v2'], function () {
  Route::controller(AddressController::class)->group(function () {
    Route::get('customer/{idCustomer}/corporate/{idCorporate}/address', 'index')->name('v2.address.index');
    Route::post('customer/{idCustomer}/corporate/{idCorporate}/address', 'store')->name('v2.address.store');
    Route::get('customer/{idCustomer}/corporate/{idCorporate}/address/{idAddress}', 'show')->name('v2.address.show');
    Route::put('customer/{idCustomer}/corporate/{idCorporate}/address/{idAddress}', 'update')->name('v2.address.update');
    Route::delete('customer/{idCustomer}/corporate/{idCorporate}/address/{idAddress}', 'destroy')->name('v2.address.destroy');
  });
});