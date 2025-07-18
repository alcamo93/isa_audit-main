<?php

use App\Http\Controllers\Catalogues\PeriodsController;
use Illuminate\Support\Facades\Route;

// Periods
Route::get('catalogs/periods', [PeriodsController::class, 'index'])->middleware('profileSubmodule');
Route::post('catalogs/periods', [PeriodsController::class, 'getPeriodsDT']);
Route::post('catalogs/periods/set', [PeriodsController::class, 'setPeriod']);
Route::post('catalogs/periods/update', [PeriodsController::class, 'updatePeriod']);
Route::post('catalogs/periods/delete', [PeriodsController::class, 'deletePeriod']);