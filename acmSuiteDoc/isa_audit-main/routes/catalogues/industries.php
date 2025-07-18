<?php

use App\Http\Controllers\Catalogues\IndustriesController;
use Illuminate\Support\Facades\Route;

//industries
Route::get('catalogs/industries', [IndustriesController::class, 'index'])->middleware('profileSubmodule');
Route::post('catalogs/industries', [IndustriesController::class, 'getIndustriesDT']);
Route::post('catalogs/industries/set', [IndustriesController::class, 'setIndustry']);
Route::post('catalogs/industries/update', [IndustriesController::class, 'updateIndustry']);
Route::post('catalogs/industries/delete', [IndustriesController::class, 'deleteIndustry']);
