<?php

use App\Http\Controllers\V2\Audit\ActionRegisterController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verify.ownership.polymorph.api'], 'prefix' => 'v2'], function () {
    Route::controller(ActionRegisterController::class)->group(function () {
        Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}', 'show')->name('v2.process.applicability.section.action.register.show');
        Route::post('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action', 'store')->name('v2.process.applicability.section.action.register.store');
        Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/report/plan', 'reportPlan')->name('v2.process.applicability.section.action.register.report.plan');
    });
});