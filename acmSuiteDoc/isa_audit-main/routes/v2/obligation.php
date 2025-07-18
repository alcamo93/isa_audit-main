<?php

use App\Http\Controllers\V2\Audit\ObligationController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verify.ownership.obligation.api'], 'prefix' => 'v2'], function () {
    Route::controller(ObligationController::class)->group(function () {
        Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/obligation/{idObligationRegister}', 'index')->name('v2.process.applicability.obligation.records.index');
        Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/obligation/{idObligationRegister}/record/{idObligation}', 'show')->name('v2.process.applicability.obligation.records.show');
        Route::put('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/obligation/{idObligationRegister}/record/{idObligation}/user', 'handlerUser')->name('v2.process.applicability.obligation.records.user');
        Route::put('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/obligation/{idObligationRegister}/record/{idObligation}/file', 'hasFile')->name('v2.process.applicability.obligation.records.file');
    });
});