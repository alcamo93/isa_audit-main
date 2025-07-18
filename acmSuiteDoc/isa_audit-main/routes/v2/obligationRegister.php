<?php

use App\Http\Controllers\V2\Audit\ObligationRegisterController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verify.ownership.obligation.api'], 'prefix' => 'v2'], function () {
    Route::controller(ObligationRegisterController::class)->group(function () {
        Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/obligation', 'index')->name('v2.process.applicability.obligation.register.index');
        Route::post('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/obligation', 'store')->name('v2.process.applicability.obligation.register.store');
        Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/obligation/{idObligationRegister}/report', 'report')->name('v2.process.applicability.obligation.register.report');
    });
});