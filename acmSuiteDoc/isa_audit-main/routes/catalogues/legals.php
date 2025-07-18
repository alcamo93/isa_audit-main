<?php

use App\Http\Controllers\Catalogues\LegalsController;
use Illuminate\Support\Facades\Route;

//legals
Route::get('catalogs/legals', [LegalsController::class, 'index'])->middleware('profileSubmodule');

// Guide lines
Route::post('catalogs/legals/guidelines', [LegalsController::class, 'getGuidelinesDT']);
Route::post('catalogs/legals/guidelines/selection', [LegalsController::class, 'getGuidelinesSelection']);
Route::post('catalogs/legals/guidelines/set', [LegalsController::class, 'setGuideline']);
Route::post('catalogs/legals/guidelines/update', [LegalsController::class, 'updateGuideline']);
Route::post('catalogs/legals/guidelines/delete', [LegalsController::class, 'deleteGuideline']);

// Basis
Route::post('catalogs/legals/basis', [LegalsController::class, 'getBasisDT']);
Route::get('catalogs/legals/basis', [LegalsController::class, 'getBasis']);
Route::post('catalogs/legals/basis/set', [LegalsController::class, 'setBasis']);
Route::post('catalogs/legals/basis/update', [LegalsController::class, 'updateBasis']);
Route::post('catalogs/legals/basis/delete', [LegalsController::class, 'deleteBasis']);

// Basis images
Route::get('catalogs/legals/images', [LegalsController::class, 'images']);