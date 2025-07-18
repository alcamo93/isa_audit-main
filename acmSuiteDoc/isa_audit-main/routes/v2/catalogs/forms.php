<?php

use App\Http\Controllers\V2\Catalogs\FormController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2/catalogs'], function () {
    Route::get('forms/view', [FormController::class, 'view'])->name('forms.view');
    Route::get('forms', [FormController::class, 'index'])->name('forms.index');
    Route::post('forms', [FormController::class, 'store'])->name('forms.store');
    Route::get('forms/{id}', [FormController::class, 'show'])->name('forms.show');
    Route::put('forms/{id}', [FormController::class, 'update'])->name('forms.update');
    Route::delete('forms/{id}', [FormController::class, 'destroy'])->name('forms.destroy');
    Route::put('forms/{id}/change-current', [FormController::class, 'changeCurrent'])->name('forms.change.current');
    Route::post('forms/{id}/duplicate', [FormController::class, 'duplicateForm'])->name('forms.change.duplicate');
});