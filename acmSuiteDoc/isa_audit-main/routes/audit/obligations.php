<?php

use App\Http\Controllers\Audit\ObligationsController;
use Illuminate\Support\Facades\Route;

// views
Route::get('obligations', [ObligationsController::class, 'index'])->middleware('profile');
Route::post('obligations', [ObligationsController::class, 'obligationsDT']);
Route::get('obligations/get', [ObligationsController::class, 'getDataObligation']);
Route::post('obligations/set', [ObligationsController::class, 'setObligation']);
Route::post('obligations/update', [ObligationsController::class, 'updateObligation']);
Route::post('obligations/delete', [ObligationsController::class, 'deleteObligation']);
Route::post('obligations/asign-user', [ObligationsController::class, 'obligationAsignUser']);
Route::post('obligations/complete', [ObligationsController::class, 'completeobligation']);
// Route::get('obligations/data/get', 'Audit\ObligationsController@getDataObligation');
// Dates
Route::get('obligations/date/calculate', [ObligationsController::class, 'calculateDates']);
Route::post('obligations/date/set', [ObligationsController::class, 'setDatesObligation']);
// Route::post('obligations/request', 'Audit\ObligationsController@requestDate');
// users
Route::get('obligations/users', [ObligationsController::class, 'getUsersByProcess']);
Route::get('obligations/users/assigned/set', [ObligationsController::class, 'getUsersByProcess']);
Route::post('obligations/users/assigned/set', [ObligationsController::class, 'setUsersAssigned']); 
Route::post('obligations/users/assigned/remove', [ObligationsController::class, 'removeUserAssigned']); 
//action register
Route::post('obligations/action-register', [ObligationsController::class, 'getActionRegistersByContractDT']); 
// Reminders
Route::get('obligations/data/reminders', [ObligationsController::class, 'getObligationReminders']);
Route::post('obligations/requeriments/get', [ObligationsController::class, 'getObligationsRequirements']);