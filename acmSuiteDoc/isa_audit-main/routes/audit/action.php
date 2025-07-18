<?php

use App\Http\Controllers\Audit\ActionPlansController;
use App\Http\Controllers\Audit\TasksController;
use Illuminate\Support\Facades\Route;

// views process
Route::get('action', [ActionPlansController::class, 'index'])->middleware('profile');
Route::post('action/register', [ActionPlansController::class, 'getActionRegistersByContractDT']);
// view action plan
Route::post('action/action-plan', [ActionPlansController::class, 'getActionPlanDT']);
// Route::post('action/action-plan/sub', 'Audit\ActionPlansController@getSubActionPlanDT');
Route::get('action/action-plan/counter', [ActionPlansController::class, 'getCounter']);
Route::get('action/action-plan/data', [ActionPlansController::class, 'getDataActionPlan']);
Route::post('action/action/priority', [ActionPlansController::class, 'changePriority']);
// action expired
Route::post('action/action-expired', [ActionPlansController::class, 'getActionExpiredDT']);
Route::post('action/action-expired/set', [ActionPlansController::class, 'setExpired']);
Route::post('action/action-expired/set/again', [ActionPlansController::class, 'setAgainExpired']);
// source
Route::get('action/matters', [ActionPlansController::class, 'getMattersByIdActionRegister']);
Route::get('action/aspects', [ActionPlansController::class, 'getAspectsByMatter']);
Route::get('action/users', [ActionPlansController::class, 'getUsersByProcess']);
// tasks
Route::post('action/tasks', [TasksController::class, 'getTaskDT']);
// Route::get('action/tasks/periods', 'Audit\TasksController@getPeriodsForTask');
Route::get('action/tasks/data', [TasksController::class, 'getTask']);
Route::post('action/tasks/set', [TasksController::class, 'setTask']);
Route::post('action/tasks/update', [TasksController::class, 'updateTask']);
Route::post('action/tasks/delete', [TasksController::class, 'deleteTask']);
Route::post('action/tasks/complete', [TasksController::class, 'completeTask']);
Route::post('action/tasks/status', [TasksController::class, 'updateStatusTask']);
Route::post('action/tasks/users/assigned/remove', [TasksController::class, 'removeTaskUser']);
// Reminders tasks
Route::get('action/tasks/data/reminders', [TasksController::class, 'getTaskReminders']);
// assigned
Route::get('action/data', [ActionPlansController::class, 'getActionPlan']);
Route::post('action/users/assigned/set', [ActionPlansController::class, 'setUsersAssigned']);
Route::post('action/users/assigned/remove', [ActionPlansController::class, 'removeUserAssigned']);

Route::post('action/tasks/users/assigned/set', [TasksController::class, 'setUsersAssigned']);
Route::post('action/requirements/date/set', [ActionPlansController::class, 'setCloseDates']);
Route::post('action/requirements/request', [ActionPlansController::class, 'setRequestDate']);
// acepted
Route::post('action/requirement/complete', [ActionPlansController::class, 'completeRequirement']);
Route::post('action/task/complete', [TasksController::class, 'completeTask']);
// Reminders
// Route::get('action/data/reminders', 'Audit\ActionPlansController@getActionPlanReminders');
// Route::post('action/dates/reminders', 'Audit\ActionPlansController@setActionPlanReminders');
Route::get('action/expired', [TasksController::class, 'expiredTest']);
