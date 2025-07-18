<?php

use App\Http\Controllers\Admin\ProcessesController;
use Illuminate\Support\Facades\Route;

Route::get('processes', [ProcessesController::class, 'index'])->middleware('profile');
Route::post('processes', [ProcessesController::class, 'getProcessesDT']);
Route::post('processes/set',  [ProcessesController::class, 'setProcesses']);
Route::get('processes/{idProcesses}', [ProcessesController::class, 'getProcesses']);
Route::post('processes/update/', [ProcessesController::class, 'updateProcesses']);
Route::post('processes/delete', [ProcessesController::class, 'deleteProcesses']);
// resorucer
Route::get('processes/users/{idProcesses}', [ProcessesController::class, 'getUserProcesss']);
Route::get('processes/corporates/{idCorporate}', [ProcessesController::class, 'getCorporatesToAudit']);
Route::get('processes/validate/{idAuditProcess}/specifics/{idAspect}', [ProcessesController::class, 'getValidateSpecificReqProcesss']);
