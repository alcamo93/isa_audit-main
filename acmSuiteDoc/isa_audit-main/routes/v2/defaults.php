<?php

use App\Http\Controllers\V2\DefaultController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'default'], function () {
    Route::controller(DefaultController::class)->group(function () {
        Route::get('modules/create', 'modules')->name('modules.modules');
        Route::get('modules/process', 'process')->name('modules.process');
        Route::get('modules/periodicity', 'periodicity')->name('modules.periodicity');
        Route::get('modules/files', 'files')->name('modules.files');
        Route::get('modules/evaluates', 'evaluates')->name('modules.evaluates');
        Route::get('modules/track', 'track')->name('modules.track');
    });
});