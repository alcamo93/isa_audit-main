<?php

use App\Http\Controllers\V2\Audit\AplicabilityController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verify.ownership.aplicability.api'], 'prefix' => 'v2'], function () {
  Route::controller(AplicabilityController::class)->group(function () {
    Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/matter/{idContractMatter}/aspect/{idContractAspect}/evaluate', 'index')->name('v2.process.applicability.matter.aspect.evaluate.index');
    Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/matter/{idContractMatter}/aspect/{idContractAspect}/evaluate/{idEvaluateQuestion}', 'show')->name('v2.process.applicability.matter.aspect.evaluate.show');
    Route::post('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/matter/{idContractMatter}/aspect/{idContractAspect}/evaluate/{idEvaluateQuestion}/answer', 'answer')->name('v2.process.applicability.matter.aspect.evaluate.answer');
    Route::post('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/matter/{idContractMatter}/aspect/{idContractAspect}/evaluate/{idEvaluateQuestion}/comment', 'comment')->name('v2.process.applicability.matter.aspect.evaluate.comment');
  });
});