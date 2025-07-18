<?php

use App\Http\Controllers\V2\Audit\AuditAspectController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verify.ownership.audit.api'], 'prefix' => 'v2'], function () {
  Route::controller(AuditAspectController::class)->group(function () {
    Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/audit/{idAuditRegister}/aspect/all', 'all')->name('v2.register.audit.matter.aspect.all');
    Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/audit/{idAuditRegister}/matter/{idAuditMatter}/aspect/', 'index')->name('v2.register.audit.matter.aspect.index');
    Route::post('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/audit/{idAuditRegister}/matter/{idAuditMatter}/aspect/{idAuditAspect}/complete', 'complete')->name('v2.register.audit.matter.aspect.complete');
  });
});