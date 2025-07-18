<?php

use App\Http\Controllers\V2\Catalogs\RecomendationController;
use App\Http\Controllers\V2\Catalogs\SubRecomendationController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verify.ownership.form.requirement.recomendation.api'], 'prefix' => 'v2/catalogs'], function () {
  Route::get('form/{idForm}/requirement/{idRequirement}/recomendation/view', [RecomendationController::class, 'view'])->name('req.recomendation.view');
  Route::get('form/{idForm}/requirement/{idRequirement}/recomendation', [RecomendationController::class, 'index'])->name('req.recomendation.index');
  Route::post('form/{idForm}/requirement/{idRequirement}/recomendation', [RecomendationController::class, 'store'])->name('req.recomendation.store');
  Route::get('form/{idForm}/requirement/{idRequirement}/recomendation/{idRecomendation}', [RecomendationController::class, 'show'])->name('req.recomendation.show');
  Route::put('form/{idForm}/requirement/{idRequirement}/recomendation/{idRecomendation}', [RecomendationController::class, 'update'])->name('req.recomendation.update');
  Route::delete('form/{idForm}/requirement/{idRequirement}/recomendation/{idRecomendation}', [RecomendationController::class, 'destroy'])->name('req.recomendation.destroy');
});

Route::group(['middleware' => ['auth', 'verify.ownership.form.requirement.subrecomendation.api'], 'prefix' => 'v2/catalogs'], function () {
  Route::get('form/{idForm}/requirement/{idRequirement}/subrequirement/{idSubrequirement}/recomendation/view', [SubRecomendationController::class, 'view'])->name('sub.recomendation.view');
  Route::get('form/{idForm}/requirement/{idRequirement}/subrequirement/{idSubrequirement}/recomendation', [SubRecomendationController::class, 'index'])->name('sub.recomendation.index');
  Route::post('form/{idForm}/requirement/{idRequirement}/subrequirement/{idSubrequirement}/recomendation', [SubRecomendationController::class, 'store'])->name('sub.recomendation.store');
  Route::get('form/{idForm}/requirement/{idRequirement}/subrequirement/{idSubrequirement}/recomendation/{idRecomendation}', [SubRecomendationController::class, 'show'])->name('sub.recomendation.show');
  Route::put('form/{idForm}/requirement/{idRequirement}/subrequirement/{idSubrequirement}/recomendation/{idRecomendation}', [SubRecomendationController::class, 'update'])->name('sub.recomendation.update');
  Route::delete('form/{idForm}/requirement/{idRequirement}/subrequirement/{idSubrequirement}/recomendation/{idRecomendation}', [SubRecomendationController::class, 'destroy'])->name('sub.recomendation.destroy');
});