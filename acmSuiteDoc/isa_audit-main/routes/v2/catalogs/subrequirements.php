<?php

use App\Http\Controllers\V2\Catalogs\SubrequirementController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2/catalogs'], function () {
    Route::get('subrequirements', [SubrequirementController::class, 'index'])->name('old.subrequirements.index');
    Route::get('subrequirements/{id}', [SubrequirementController::class, 'show'])->name('old.subrequirements.show');
});