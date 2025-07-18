<?php

use App\Http\Controllers\Catalogues\MattersController;
use App\Http\Controllers\Catalogues\AspectsController;
use Illuminate\Support\Facades\Route;

// Matters
Route::get('catalogs/matters', [MattersController::class, 'index'])->middleware('profileSubmodule');
Route::post('catalogs/matters', [MattersController::class, 'getMattersDT']);
Route::post('catalogs/matters/set', [MattersController::class, 'setMatter']);
Route::post('catalogs/matters/update', [MattersController::class, 'updateMatter']);
Route::post('catalogs/matters/delete', [MattersController::class, 'deleteMatter']);

// Aspects
Route::post('catalogs/aspects', [AspectsController::class , 'getaspectsDT']);
Route::post('catalogs/aspects/set', [AspectsController::class , 'setAspect']);
Route::post('catalogs/aspects/update', [AspectsController::class , 'updateAspect']);
Route::post('catalogs/aspects/delete', [AspectsController::class , 'deleteAspect']);

Route::post('catalogs/matters-aspects', [AspectsController::class , 'getAspectsByIdMatter']);

// Versión 2
Route::get('catalogs/matters/records', [MattersController::class, 'getMatters']);

Route::get('catalogs/aspects/{id}', [AspectsController::class , 'getAspectsByMatterId']);
