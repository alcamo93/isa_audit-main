<?php

use App\Http\Controllers\V2\Catalogs\IndustryController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2/catalogs'], function () {
  Route::controller(IndustryController::class)->group(function () {
    Route::get('industry/view', 'view')->name('v2.industry.view');
    Route::get('industry', 'index')->name('v2.industry.index');
    Route::post('industry', 'store')->name('v2.industry.store');
    Route::get('industry/{id}', 'show')->name('v2.industry.show');
    Route::put('industry/{id}', 'update')->name('v2.industry.update');
    Route::delete('industry/{id}', 'destroy')->name('v2.industry.destroy');
  });
});