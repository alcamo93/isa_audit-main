<?php

use App\Http\Controllers\V2\Catalogs\Risk\RiskCategoryController;
use App\Http\Controllers\V2\Catalogs\Risk\RiskIntepretationController;
use App\Http\Controllers\V2\Catalogs\Risk\RiskHelpController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2/catalogs'], function () {
  Route::controller(RiskCategoryController::class)->group(function () {
    Route::get('risk/view', 'view')->name('v2.risk.view');
    Route::get('risk', 'index')->name('v2.risk.index');
    Route::post('risk', 'store')->name('v2.risk.store');
    Route::get('risk/{id}', 'show')->name('v2.risk.show');
    Route::put('risk/{id}', 'update')->name('v2.risk.update');
    Route::delete('risk/{id}', 'destroy')->name('v2.risk.destroy');
  });
});

Route::group(['middleware' => ['auth'], 'prefix' => 'v2/catalogs'], function () {
  Route::controller(RiskIntepretationController::class)->group(function () {
    Route::get('risk/{idRiskCategory}/interpretation', 'index')->name('v2.risk.interpretation.index');
    Route::post('risk/{idRiskCategory}/interpretation', 'store')->name('v2.risk.interpretation.store');
  });
});

Route::group(['middleware' => ['auth'], 'prefix' => 'v2/catalogs'], function () {
  Route::controller(RiskHelpController::class)->group(function () {
    Route::get('risk/{idRiskCategory}/help', 'index')->name('v2.risk.help.index');
    Route::post('risk/{idRiskCategory}/help', 'store')->name('v2.risk.help.store');
    Route::get('risk/{idRiskCategory}/help/{id}', 'show')->name('v2.risk.help.show');
    Route::put('risk/{idRiskCategory}/help/{id}', 'update')->name('v2.risk.help.update');
  });
});