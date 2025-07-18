<?php

use App\Http\Controllers\V2\Audit\AuditController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verify.ownership.audit.api'], 'prefix' => 'v2'], function () {
  Route::controller(AuditController::class)->group(function () {
    Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/audit/{idAuditRegister}/matter/{idAuditMatter}/aspect/{idAuditAspect}/evaluate', 'index')->name('v2.process.applicability.audit.matter.aspect.evaluate.index');
    Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/audit/{idAuditRegister}/matter/{idAuditMatter}/aspect/{idAuditAspect}/evaluate/{idAuditEvaluate}', 'show')->name('v2.process.applicability.audit.matter.aspect.evaluate.show');
    Route::post('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/audit/{idAuditRegister}/matter/{idAuditMatter}/aspect/{idAuditAspect}/evaluate/{idAuditEvaluate}/answer', 'answer')->name('v2.process.applicability.audit.matter.aspect.evaluate.answer');
    Route::post('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/audit/{idAuditRegister}/matter/{idAuditMatter}/aspect/{idAuditAspect}/evaluate/{idAuditEvaluate}/finding', 'finding')->name('v2.process.applicability.audit.matter.aspect.evaluate.finding');
    Route::post('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/audit/{idAuditRegister}/matter/{idAuditMatter}/aspect/{idAuditAspect}/evaluate/{idAuditEvaluate}/images', 'images')->name('v2.process.applicability.audit.matter.aspect.evaluate.images');
  });
});