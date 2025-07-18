<?php

use App\Http\Controllers\V2\Catalogs\AspectController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verify.ownership.aspect.matter.api'], 'prefix' => 'v2/catalogs'], function () {
  Route::controller(AspectController::class)->group(function () {
    Route::get('matter/{idMatter}/aspect', 'index')->name('v2.matter.aspect.index');
    Route::post('matter/{idMatter}/aspect', 'store')->name('v2.matter.aspect.store');
    Route::get('matter/{idMatter}/aspect/{idAspect}', 'show')->name('v2.matter.aspect.show');
    Route::put('matter/{idMatter}/aspect/{idAspect}', 'update')->name('v2.matter.aspect.update');
    Route::delete('matter/{idMatter}/aspect/{idAspect}', 'destroy')->name('v2.matter.aspect.destroy');
  });
});