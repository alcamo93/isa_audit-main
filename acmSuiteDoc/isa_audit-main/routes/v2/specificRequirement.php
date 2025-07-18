<?php

use App\Http\Controllers\V2\Audit\SpecificRequirementController;
use App\Http\Controllers\V2\SpecificRecomendationController;
use App\Http\Controllers\V2\SpecificRequirementLegalController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2'], function () {
  Route::controller(SpecificRequirementController::class)->group(function () {
    Route::get('specific/requirement/view', 'view')->name('v2.specific.requirement.view');
    Route::get('specific/requirement', 'index')->name('v2.specific.requirement.index');
    Route::post('specific/requirement', 'store')->name('v2.specific.requirement.store');
    Route::get('specific/requirement/{idRequirement}', 'show')->name('v2.specific.requirement.show');
    Route::put('specific/requirement/{idRequirement}', 'update')->name('v2.specific.requirement.update');
    Route::delete('specific/requirement/{idRequirement}', 'destroy')->name('v2.specific.requirement.destroy');
  });
  Route::controller(SpecificRequirementLegalController::class)->group(function () {
    Route::get('specific/requirement/{idRequirement}/legal/guideline', 'guideline')->name('v2.specific.requirement.legal.guideline');
    Route::get('specific/requirement/{idRequirement}/legal/article', 'articles')->name('v2.specific.requirement.legal.articles');
    Route::post('specific/requirement/{idRequirement}/legal/relation/{idLegalBasis}', 'relation')->name('v2.specific.requirement.legal.relation');
  });
  Route::controller(SpecificRecomendationController::class)->group(function () {
    Route::get('specific/requirement/{idRequirement}/recomendation/view', 'view')->name('v2.specific.req.recomendation.view');
    Route::get('specific/requirement/{idRequirement}/recomendation', 'index')->name('v2.specific.req.recomendation.index');
    Route::post('specific/requirement/{idRequirement}/recomendation', 'store')->name('v2.specific.req.recomendation.store');
    Route::get('specific/requirement/{idRequirement}/recomendation/{idRecomendation}', 'show')->name('v2.specific.req.recomendation.show');
    Route::put('specific/requirement/{idRequirement}/recomendation/{idRecomendation}', 'update')->name('v2.specific.req.recomendation.update');
    Route::delete('specific/requirement/{idRequirement}/recomendation/{idRecomendation}', 'destroy')->name('v2.specific.req.recomendation.destroy');
  });
});