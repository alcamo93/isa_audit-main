<?php

use App\Http\Controllers\V2\Audit\ActionPlanController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verify.ownership.polymorph.api'], 'prefix' => 'v2'], function () {
    Route::controller(ActionPlanController::class)->group(function () {
        Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan', 'index')->name('v2.process.applicability.section.action.plan.index');
        // Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan/expired', 'indexExpired')->name('v2.process.applicability.section.action.plan.index.expired');
        Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan/{idActionPlan}', 'show')->name('v2.process.applicability.section.action.plan.show');
        Route::put('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan/{idActionPlan}/priority', 'priority')->name('v2.process.applicability.section.action.plan.priority');
        Route::put('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan/{idActionPlan}/user', 'user')->name('v2.process.applicability.section.action.plan.user');
        Route::put('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan/{idActionPlan}/expired', 'expired')->name('v2.process.applicability.section.action.plan.expired');
        Route::delete('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan/{idActionPlan}', 'destroy')->name('v2.process.applicability.section.action.plan.destroy');
    });
}); 