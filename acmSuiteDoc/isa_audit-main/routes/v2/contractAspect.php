<?php

use App\Http\Controllers\V2\Audit\ContractAspectController;
use Illuminate\Support\Facades\Route;
// 'verify.ownership.app.api'
Route::group(['middleware' => ['auth'], 'prefix' => 'v2'], function () {
  Route::controller(ContractAspectController::class)->group(function () {
    Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/aspect/all', 'all')->name('v2.register.matter.aspect.all');
    Route::post('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/matter/{idContractMatter}/aspect/{idContractAspect}/complete', 'complete')->name('v2.register.matter.aspect.complete');
    Route::post('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/matter/{idContractMatter}/aspect/{idContractAspect}/sync', 'sync')->name('v2.register.matter.aspect.sync');
  });
});