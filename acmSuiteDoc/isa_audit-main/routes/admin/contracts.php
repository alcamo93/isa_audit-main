<?php

use App\Http\Controllers\Admin\ContractsController;
use Illuminate\Support\Facades\Route;

// view
Route::get('contracts', [ContractsController::class, 'index'])->middleware('profile');
Route::post('contracts', [ContractsController::class, 'getContractsDT']);
// crud
Route::get('contracts/{idContract}', [ContractsController::class, 'getContract']);
Route::post('contracts/set', [ContractsController::class, 'setContract']);
Route::post('contracts/update', [ContractsController::class, 'updateContract']);
Route::post('contracts/delete', [ContractsController::class, 'deleteContract']);
Route::post('contracts/status', [ContractsController::class, 'updateContractStatus']);
//
Route::post('contracts/set-extension', [ContractsController::class, 'setContractExtension']);

// source
Route::post('contracts/set/dates', [ContractsController::class, 'calculateDateEnd']);
Route::post('contracts/update/dates', [ContractsController::class, 'updateCalculateDateEnd']);
// employ
Route::post('contracts/validate', [ContractsController::class, 'validateAplicability']);
Route::post('contracts/registers/matters', [ContractsController::class, 'registerMattersDT']);
Route::post('contracts/registers/aspects', [ContractsController::class, 'registerAspectsDT']);
Route::post('contracts/matter/update', [ContractsController::class, 'setMatter']);
Route::post('contracts/aspect/update', [ContractsController::class, 'setAspect']);
