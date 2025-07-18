<?php

use App\Http\Controllers\V2\Audit\ProcessController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2'], function () {
    Route::controller(ProcessController::class)->group(function () {
        Route::get('process/view', 'view')->name('v2.process.view');
        Route::get('process', 'index')->name('v2.process.index');
        Route::post('process', 'store')->name('v2.process.store');
        Route::get('process/{id}', 'show')->name('v2.process.show');
        Route::put('process/{id}', 'update')->name('v2.process.update');
        Route::delete('process/{id}', 'destroy')->name('v2.process.destroy');
        Route::post('process/{id}/renewal', 'renewal')->name('v2.process.renewal');
    });

    Route::controller(ProcessController::class)->group(function () {
        Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/view', 'aplicabilityView')
            ->name('v2.process.aplicability.view')
            ->middleware('verify.ownership.aplicability.page');
        Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/matter/{idAuditMatter}/aspect/{idAuditAspect}/view', 'aplicabilityEvaluateView')
            ->name('v2.process.aplicability.matter.aspect.view')
            ->middleware('verify.ownership.aplicability.page');
        Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/audit/{idAuditRegister}/view', 'auditView')
            ->name('v2.process.aplicability.audit.view')
            ->middleware('verify.ownership.audit.page');
        Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/audit/{idAuditRegister}/matter/{idAuditMatter}/aspect/{idAuditAspect}/view', 'auditEvaluateView')
            ->name('v2.process.aplicability.audit.matter.aspect.view')
            ->middleware('verify.ownership.audit.page');
        Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/obligation/{idObligationRegister}/view', 'obligationView')
            ->name('v2.process.aplicability.obligation.view')
            ->middleware('verify.ownership.obligation.page');
        Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan/view', 'actionPlanView')
            ->name('v2.process.aplicability.section.action.view')
            ->middleware('verify.ownership.polymorph.page');
        Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan/{idActionPlan}/task/view', 'taskView')
            ->name('v2.process.aplicability.section.action.task.view')
            ->middleware('verify.ownership.polymorph.page');
    });
}); 
