<?php

use App\Http\Controllers\V2\Audit\AuditRegisterController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verify.ownership.audit.api'], 'prefix' => 'v2'], function () {
	Route::controller(AuditRegisterController::class)->group(function () {
		Route::post('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/audit/register', 'store')->name('v2.process.applicability.audit.register.store');
		Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/audit/{idAuditRegister}/register', 'show')->name('v2.process.applicability.audit.register.show');
		Route::post('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/audit/{idAuditRegister}/register/complete', 'complete')->name('v2.process.applicability.audit.register.audit.complete');
		Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/audit/{idAuditRegister}/register/report/audit', 'reportAudit')->name('v2.process.applicability.audit.register.report.audit');
		Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/audit/{idAuditRegister}/register/report/document', 'reportDocument')->name('v2.process.applicability.audit.register.report.document');
		Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/audit/{idAuditRegister}/register/report/progress', 'reportProgress')->name('v2.process.applicability.audit.register.report.progress');
	});
});
