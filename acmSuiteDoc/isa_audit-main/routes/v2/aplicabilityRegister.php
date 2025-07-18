<?php

use App\Http\Controllers\V2\Audit\AplicabilityRegisterController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verify.ownership.aplicability.api'], 'prefix' => 'v2'], function () {
    Route::controller(AplicabilityRegisterController::class)->group(function () {
        Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/register', 'show')->name('v2.process.applicability.show');
        Route::post('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/register/complete', 'complete')->name('v2.process.applicability.complete');
        Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/register/report/answers', 'reportAnswers')->name('v2.process.applicability.report.answers');
        Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/register/report/results', 'reportResults')->name('v2.process.applicability.report.results');
    });
});