<?php

use App\Http\Controllers\V2\Catalogs\RequirementController;
use App\Http\Controllers\V2\Catalogs\RequirementLegalController;
use App\Http\Controllers\V2\Catalogs\SubrequirementController;
use App\Http\Controllers\V2\Catalogs\SubrequirementLegalController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verify.ownership.form.requirement.api'], 'prefix' => 'v2/catalogs'], function () {
    Route::get('form/{idForm}/requirement/view', [RequirementController::class, 'view'])->name('v2.requirement.view');
    Route::get('form/{idForm}/requirement', [RequirementController::class, 'index'])->name('v2.requirement.index');
    Route::post('form/{idForm}/requirement', [RequirementController::class, 'store'])->name('v2.requirement.store');
    Route::get('form/{idForm}/requirement/{idRequirement}', [RequirementController::class, 'show'])->name('v2.requirement.show');
    Route::put('form/{idForm}/requirement/{idRequirement}', [RequirementController::class, 'update'])->name('v2.requirement.update');
    Route::delete('form/{idForm}/requirement/{idRequirement}', [RequirementController::class, 'destroy'])->name('v2.requirement.destroy');
});

Route::group(['middleware' => ['auth', 'verify.ownership.form.requirement.api'], 'prefix' => 'v2/catalogs'], function () {
    Route::get('form/{idForm}/requirement/{idRequirement}/legal/guideline', [RequirementLegalController::class, 'guideline'])->name('v2.requirement.legal.guideline');
    Route::get('form/{idForm}/requirement/{idRequirement}/legal/article', [RequirementLegalController::class, 'articles'])->name('v2.requirement.legal.articles');
    Route::post('form/{idForm}/requirement/{idRequirement}/legal/relation/{idLegalBasis}', [RequirementLegalController::class, 'relation'])->name('v2.requirement.legal.relation');
});

Route::group(['middleware' => ['auth', 'verify.ownership.form.requirement.api'], 'prefix' => 'v2/catalogs'], function () {
    Route::get('form/{idForm}/requirement/{idRequirement}/subrequirement/view', [SubrequirementController::class, 'view'])->name('v2.subrequirement.view');
    Route::get('form/{idForm}/requirement/{idRequirement}/subrequirement', [SubrequirementController::class, 'index'])->name('v2.subrequirement.index');
    Route::post('form/{idForm}/requirement/{idRequirement}/subrequirement', [SubrequirementController::class, 'store'])->name('v2.subrequirement.store');
    Route::get('form/{idForm}/requirement/{idRequirement}/subrequirement/{idSubrequirement}', [SubrequirementController::class, 'show'])->name('v2.subrequirement.show');
    Route::put('form/{idForm}/requirement/{idRequirement}/subrequirement/{idSubrequirement}', [SubrequirementController::class, 'update'])->name('v2.subrequirement.update');
    Route::delete('form/{idForm}/requirement/{idRequirement}/subrequirement/{idSubrequirement}', [SubrequirementController::class, 'destroy'])->name('v2.subrequirement.destroy');
});

Route::group(['middleware' => ['auth', 'verify.ownership.form.requirement.api'], 'prefix' => 'v2/catalogs'], function () {
    Route::get('form/{idForm}/requirement/{idRequirement}/subrequirement/{idSubrequirement}/legal/guideline', [SubrequirementLegalController::class, 'guideline'])->name('v2.subrequirement.legal.guideline');
    Route::get('form/{idForm}/requirement/{idRequirement}/subrequirement/{idSubrequirement}/legal/article', [SubrequirementLegalController::class, 'articles'])->name('v2.subrequirement.legal.articles');
    Route::post('form/{idForm}/requirement/{idRequirement}/subrequirement/{idSubrequirement}/legal/relation/{idLegalBasis}', [SubrequirementLegalController::class, 'relation'])->name('v2.subrequirement.legal.relation');
});