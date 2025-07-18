<?php

use App\Http\Controllers\Admin\LicensesController;
use Illuminate\Support\Facades\Route;

// view
Route::get('licenses', [LicensesController::class, 'index'])->middleware('profile');
Route::post('licenses', [LicensesController::class, 'getLicensesDT']);
// crud
Route::get('licenses/{idLicense}', [LicensesController::class, 'getLicense']);
Route::post('licenses/set', [LicensesController::class, 'setLicense']);
Route::post('licenses/update', [LicensesController::class, 'updateLicense']);
Route::post('licenses/delete', [LicensesController::class, 'deleteLicense']);
// source
Route::get('licences/filter', [LicensesController::class, 'getFilterLicenses']);