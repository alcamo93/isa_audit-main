<?php

use App\Http\Controllers\V2\ContractController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2'], function () {
  Route::controller(ContractController::class)->group(function () {
    Route::get('contract/view', 'view')->name('v2.contract.view');
    Route::get('contract', 'index')->name('v2.contract.index');
    Route::post('contract', 'store')->name('v2.contract.store');
    Route::get('contract/{id}', 'show')->name('v2.contract.show');
    Route::put('contract/{id}', 'update')->name('v2.contract.update');
    Route::delete('contract/{id}', 'destroy')->name('v2.contract.destroy');
    Route::put('contract/{id}/change-status', 'changeStatus')->name('v2.contract.change-status');
    Route::post('contract/{idCorporate}/contact', 'contact')->name('v2.contract.contact');
  });
});