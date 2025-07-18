<?php

use App\Http\Controllers\Catalogues\AuditRequirementsController;
use App\Http\Controllers\Catalogues\RecomendationsController;
use Illuminate\Support\Facades\Route;

//requirements
Route::get('catalogs/requirements', [AuditRequirementsController::class, 'index'])->middleware('profileSubmodule');
Route::post('catalogs/requirements/requirements', [AuditRequirementsController::class, 'getRequirementsDT']);
Route::post('catalogs/requirements/requirements/set', [AuditRequirementsController::class, 'setRequirement']);
Route::post('catalogs/requirements/requirements/get', [AuditRequirementsController::class, 'getRequirementById']);
Route::post('catalogs/requirements/requirements/update', [AuditRequirementsController::class, 'updateRequirement']);
Route::post('catalogs/requirements/requirements/delete', [AuditRequirementsController::class, 'deleteRequirement']);
Route::post('catalogs/requirements/requirements-basis/{idRequirement}', [AuditRequirementsController::class, 'getRequirementBasis']);
Route::post('catalogs/requirements/requirements-basis', [AuditRequirementsController::class, 'updateRequirementBasis']);
Route::post('catalogs/requirements/requirements-basis-dt', [AuditRequirementsController::class, 'getRequirementBasisDT']);
Route::get('catalogs/requirements/requirements/view', [AuditRequirementsController::class, 'getAllDataRequirement']);
//requirements for customers
Route::get('requirements', [AuditRequirementsController::class, 'indexCustomers'])->middleware('profile');

// Subrequirements 
Route::post('catalogs/requirements/subrequirements', [AuditRequirementsController::class, 'getSubrequirementsDT']);
Route::post('catalogs/requirements/subrequirements/set', [AuditRequirementsController::class, 'setSubrequirement']);
Route::post('catalogs/requirements/subrequirements/get', [AuditRequirementsController::class, 'getSubrequirementById']);
Route::post('catalogs/requirements/subrequirements/update', [AuditRequirementsController::class, 'updateSubrequirement']);
Route::post('catalogs/requirements/subrequirements/delete', [AuditRequirementsController::class, 'deleteSubrequirement']);
Route::post('catalogs/requirements/subrequirements-basis/{idSubrequirement}', [AuditRequirementsController::class, 'getSubRequirementBasis']);
Route::post('catalogs/requirements/subrequirements-basis', [AuditRequirementsController::class, 'updateSubRequirementBasis']);
Route::post('catalogs/requirements/subrequirements-basis-dt', [AuditRequirementsController::class, 'getSubrequirementBasisDT']);
Route::post('catalogs/requirements/subrequirement-types', [AuditRequirementsController::class, 'getSubrequirementTypes']);
Route::get('catalogs/requirements/subrequirements/view', [AuditRequirementsController::class, 'getAllDataSubrequirement']);

// Recomendations 
Route::post('catalogs/requirements/requirement-recomendations', [RecomendationsController::class, 'getRecomendationsByIdRequirement']);
Route::post('catalogs/requirements/requirement-recomendation', [RecomendationsController::class, 'getRequirementRecomendation']);
Route::post('catalogs/requirements/recomendations/set', [RecomendationsController::class, 'setRecomendation']);
Route::post('catalogs/requirements/recomendations/update', [RecomendationsController::class, 'updateRecomendation']);
Route::post('catalogs/requirements/recomendations/delete', [RecomendationsController::class, 'deleteRecomendation']); 

Route::post('catalogs/subrequirements/subrequirement-recomendations', [RecomendationsController::class, 'getRecomendationsByIdSubrequirement']);
Route::post('catalogs/subrequirements/subrequirement-recomendation', [RecomendationsController::class, 'getSubrequirementRecomendation']);
Route::post('catalogs/subrequirements/recomendations/set', [RecomendationsController::class, 'setSubReqRecomendation']);
Route::post('catalogs/subrequirements/recomendations/update', [RecomendationsController::class, 'updateSubrecomendation']);
Route::post('catalogs/subrequirements/recomendations/delete', [RecomendationsController::class, 'deleteSubReqRecomendation']);
