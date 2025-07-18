<?php

use App\Http\Controllers\V2\Catalogs\GuidelineController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api'], 'prefix' => 'v2/catalogs'], function () {
    Route::get('guideline/view', [GuidelineController::class, 'view'])->name('v2.guideline.view');
    Route::get('guideline', [GuidelineController::class, 'index'])->name('v2.guideline.index');
    Route::post('guideline', [GuidelineController::class, 'store'])->name('v2.guideline.store');
    Route::get('guideline/{id}', [GuidelineController::class, 'show'])->name('v2.guideline.show');
    Route::put('guideline/{id}', [GuidelineController::class, 'update'])->name('v2.guideline.update');
    Route::delete('guideline/{id}', [GuidelineController::class, 'destroy'])->name('v2.guideline.destroy');
});