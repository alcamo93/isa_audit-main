<?php

use App\Http\Controllers\V2\Catalogs\GuidelineController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2/catalogs'], function () {
    Route::get('guideline/view', [GuidelineController::class, 'view'])->name('v2.guideline.view');
    Route::get('guideline', [GuidelineController::class, 'index'])->name('v2.guideline.index');
    Route::post('guideline', [GuidelineController::class, 'store'])->name('v2.guideline.store');
    Route::get('guideline/{idGuideline}', [GuidelineController::class, 'show'])->name('v2.guideline.show');
    Route::put('guideline/{idGuideline}', [GuidelineController::class, 'update'])->name('v2.guideline.update');
    Route::delete('guideline/{idGuideline}', [GuidelineController::class, 'destroy'])->name('v2.guideline.destroy');
    Route::get('guideline/{idGuideline}/topic', [GuidelineController::class, 'topics'])->name('v2.guideline.topic');
    Route::post('guideline/{idGuideline}/topic/relation/{idTopic}', [GuidelineController::class, 'relation'])->name('v2.guideline.topic.relation');
    Route::get('guideline/{idGuideline}/aspect', [GuidelineController::class, 'aspects'])->name('v2.guideline.aspects');
    Route::post('guideline/{idGuideline}/aspect/relation/{idAspect}/{idMatter}', [GuidelineController::class, 'relationAspects'])->name('v2.guideline.topic.relationAspects');
});