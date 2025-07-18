<?php

use App\Http\Controllers\Catalogues\RisksController;
use Illuminate\Support\Facades\Route;

// Categories
Route::get('catalogs/risks', [RisksController::class, 'index'])->middleware('profileSubmodule');
Route::post('catalogs/risks/categories', [RisksController::class, 'getCategoriesDT']);
Route::post('catalogs/risks/categories/set', [RisksController::class, 'setCategory']);
Route::post('catalogs/risks/categories/update', [RisksController::class, 'updateCategory']);
Route::post('catalogs/risks/categories/delete', [RisksController::class, 'deleteCategory']);

// Consequences
Route::post('catalogs/risks/consequences', [RisksController::class, 'getConsequencesDT']);
Route::post('catalogs/risks/consequences/set', [RisksController::class, 'setConsequence']);
Route::post('catalogs/risks/consequences/update', [RisksController::class, 'updateConsequence']);
Route::post('catalogs/risks/consequences/delete', [RisksController::class, 'deleteConsequence']);

// Exhibitions
Route::post('catalogs/risks/exhibitions', [RisksController::class, 'getExhibitionsDT']);
Route::post('catalogs/risks/exhibitions/set', [RisksController::class, 'setExhibition']);
Route::post('catalogs/risks/exhibitions/update', [RisksController::class, 'updateExhibition']);
Route::post('catalogs/risks/exhibitions/delete', [RisksController::class, 'deleteExhibition']);

// Probabilities
Route::post('catalogs/risks/probabilities', [RisksController::class, 'getProbabilitiesDT']);
Route::post('catalogs/risks/probabilities/set', [RisksController::class, 'setProbability']);
Route::post('catalogs/risks/probabilities/update', [RisksController::class, 'updateProbability']);
Route::post('catalogs/risks/probabilities/delete', [RisksController::class, 'deleteProbability']);

// Specifications
Route::post('catalogs/risks/specifications', [RisksController::class, 'getSpecificationsDT']);
Route::post('catalogs/risks/specifications/set', [RisksController::class, 'setSpecification']);
Route::post('catalogs/risks/specifications/update', [RisksController::class, 'updateSpecification']);
Route::post('catalogs/risks/specifications/delete', [RisksController::class, 'deleteSpecification']);

// Interpretations
Route::get('catalogs/risks/categories/interpretations', [RisksController::class, 'getInterpretations']);
Route::post('catalogs/risks/categories/interpretations/set', [RisksController::class, 'setInterpretations']);

// Help
Route::post('catalogs/risks/help', [RisksController::class, 'getRiskHelpDT']);
Route::get('catalogs/risks/help', [RisksController::class, 'getRiskHelp']);
Route::get('catalogs/risks/help/get', [RisksController::class, 'getDataRiskHelp']);
Route::post('catalogs/risks/help/set', [RisksController::class, 'setRiskHelp']);
Route::post('catalogs/risks/help/update', [RisksController::class, 'updateRiskHelp']);
Route::post('catalogs/risks/help/delete', [RisksController::class, 'deleteRiskHelp']);

