<?php

use App\Http\Controllers\V2\LicenseController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2'], function () {
  Route::controller(LicenseController::class)->group(function () {
    Route::get('license/view', 'view')->name('v2.license.view');
    Route::get('license', 'index')->name('v2.license.index');
    Route::post('license', 'store')->name('v2.license.store');
    Route::get('license/{id}', 'show')->name('v2.license.show');
    Route::put('license/{id}', 'update')->name('v2.license.update');
    Route::delete('license/{id}', 'destroy')->name('v2.license.destroy');
    Route::put('license/{id}/change-status', 'changeStatus')->name('v2.license.change-status');
  });
});