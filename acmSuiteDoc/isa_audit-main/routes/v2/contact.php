<?php

use App\Http\Controllers\V2\ContactController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verify.ownership.corporate.contact.api'], 'prefix' => 'v2'], function () {
  Route::controller(ContactController::class)->group(function () {
    Route::get('customer/{idCustomer}/corporate/{idCorporate}/contact', 'index')->name('v2.contact.index');
    Route::post('customer/{idCustomer}/corporate/{idCorporate}/contact', 'store')->name('v2.contact.store');
    Route::get('customer/{idCustomer}/corporate/{idCorporate}/contact/{idContact}', 'show')->name('v2.contact.show');
    Route::put('customer/{idCustomer}/corporate/{idCorporate}/contact/{idContact}', 'update')->name('v2.contact.update');
    Route::delete('customer/{idCustomer}/corporate/{idCorporate}/contact/{idContact}', 'destroy')->name('v2.contact.destroy');
  });
});