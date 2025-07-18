<?php

use App\Http\Controllers\V2\Catalogs\MatterController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2/catalogs'], function () {
  Route::controller(MatterController::class)->group(function () {
    Route::get('matter/view', 'view')->name('v2.matter.view');
    Route::get('matter', 'index')->name('v2.matter.index');
    Route::post('matter', 'store')->name('v2.matter.store');
    Route::get('matter/{id}', 'show')->name('v2.matter.show');
    Route::put('matter/{id}', 'update')->name('v2.matter.update');
    Route::delete('matter/{id}', 'destroy')->name('v2.matter.destroy');
    Route::post('matter/{id}', 'image')->name('v2.matter.image');
  });
});