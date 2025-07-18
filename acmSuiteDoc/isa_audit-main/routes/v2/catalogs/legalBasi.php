<?php

use App\Http\Controllers\V2\Catalogs\LegalBasiController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verify.ownership.article.guideline.api'], 'prefix' => 'v2/catalogs'], function () {
    Route::get('guideline/{idGuideline}/legal_basi/view', [LegalBasiController::class, 'view'])->name('v2.legal_basi.view');
    Route::get('guideline/{idGuideline}/legal_basi', [LegalBasiController::class, 'index'])->name('v2.legal_basi.index');
    Route::post('guideline/{idGuideline}/legal_basi', [LegalBasiController::class, 'store'])->name('v2.legal_basi.store');
    Route::get('guideline/{idGuideline}/legal_basi/{idArticle}', [LegalBasiController::class, 'show'])->name('v2.legal_basi.show');
    Route::put('guideline/{idGuideline}/legal_basi/{idArticle}', [LegalBasiController::class, 'update'])->name('v2.legal_basi.update');
    Route::delete('guideline/{idGuideline}/legal_basi/{idArticle}', [LegalBasiController::class, 'destroy'])->name('v2.legal_basi.destroy');
});