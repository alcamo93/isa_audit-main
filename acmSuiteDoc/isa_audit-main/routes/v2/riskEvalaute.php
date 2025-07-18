<?php

use App\Http\Controllers\V2\Audit\RiskEvaluateController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2'], function () {
  Route::controller(RiskEvaluateController::class)->group(function () {
    Route::get('process/{idAuditProcess}/aplicability/{idAplicabilityRegister}/risk/{sectionKey}/{idRegisterSection}/evaluate/{registerableId}', 'show')->name('v2.process.aplicability.risk.show');
    Route::post('process/{idAuditProcess}/aplicability/{idAplicabilityRegister}/risk/{sectionKey}/{idRegisterSection}/evaluate/{registerableId}', 'evaluate')->name('v2.process.aplicability.risk.evaluate');
  });
});
