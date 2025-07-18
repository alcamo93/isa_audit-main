<?php

use App\Http\Controllers\V2\Audit\TaskController;
use App\Http\Controllers\V2\Audit\TaskCommentController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verify.ownership.polymorph.api'], 'prefix' => 'v2'], function () {
    Route::controller(TaskController::class)->group(function () {
      Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan/{idActionPlan}/task', 'index')->name('v2.process.applicability.section.action.plan.task.index');
      Route::post('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan/{idActionPlan}/task', 'store')->name('v2.process.applicability.section.action.plan.task.store');
      Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan/{idActionPlan}/task/{idTask}', 'show')->name('v2.process.applicability.section.action.plan.task.show');
      Route::put('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan/{idActionPlan}/task/{idTask}', 'update')->name('v2.process.applicability.section.action.plan.task.update');
      Route::put('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan/{idActionPlan}/task/{idTask}/main', 'update')->name('v2.process.applicability.section.action.plan.task.main');
      Route::put('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan/{idActionPlan}/task/{idTask}/file', 'file')->name('v2.process.applicability.section.action.plan.task.file');
      Route::put('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan/{idActionPlan}/task/{idTask}/status', 'status')->name('v2.process.applicability.section.action.plan.task.status');
      Route::delete('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan/{idActionPlan}/task/{idTask}', 'destroy')->name('v2.process.applicability.section.action.plan.task.destroy');
      Route::put('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan/{idActionPlan}/task/{idTask}/expired', 'expired')->name('v2.process.applicability.section.action.plan.task.expired');
    });
});

Route::group(['middleware' => ['auth', 'verify.ownership.polymorph.comment.api'], 'prefix' => 'v2'], function () {
  Route::controller(TaskCommentController::class)->group(function () {
    Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan/{idActionPlan}/task/{idTask}/comment', 'index')->name('v2.process.applicability.section.action.plan.task.comment.index');
    Route::post('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan/{idActionPlan}/task/{idTask}/comment', 'store')->name('v2.process.applicability.section.action.plan.task.comment.store');
    Route::get('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan/{idActionPlan}/task/{idTask}/comment/{idComment}', 'show')->name('v2.process.applicability.section.action.plan.task.comment.show');
    Route::put('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan/{idActionPlan}/task/{idTask}/comment/{idComment}', 'update')->name('v2.process.applicability.section.action.plan.task.comment.update');
    Route::delete('process/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/plan/{idActionPlan}/task/{idTask}/comment/{idComment}', 'destroy')->name('v2.process.applicability.section.action.plan.task.comment.destroy');
  });
});