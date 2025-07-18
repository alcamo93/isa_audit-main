<?php

use App\Http\Controllers\V2\Audit\RiskRegisterController;
use App\Http\Controllers\V2\Catalogs\Risk\RiskCategoryController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2/risk/'], function () {
  Route::get('register/view/{id}', [RiskRegisterController::class, 'view'])->name('risk.register.view');
  Route::get('category', [RiskCategoryController::class, 'index'])->name('risk.category.index');
});