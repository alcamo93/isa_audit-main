<?php

use App\Http\Controllers\Audit\AplicabilityController;
use Illuminate\Support\Facades\Route;

// Route::get('aplicability/register/{idAplicabilityRegister}', [AplicabilityController::class, 'index'])->middleware('profile');
Route::post('aplicability/resgisters', [AplicabilityController::class, 'aplicabilityRegistersDT']); 
Route::post('aplicability/resgisters/aspect/delete', [AplicabilityController::class, 'deleteContractAspect']);
Route::get('aplicability/resgisters/matters/{idAplicabilityRegister}', [AplicabilityController::class, 'aplicabilityRegistersMatters']);
Route::get('aplicability/resgisters/matter/progress/{idAplicabilityRegister}', [AplicabilityController::class, 'aplicabilityRegistersMatterProgress']);
Route::post('aplicability/resgisters/aspects', [AplicabilityController::class, 'aplicabilityRegistersAspectsDT']);
Route::get('aplicability/aspects/questions', [AplicabilityController::class, 'getQuestionsByAspect']);
Route::get('aplicability/aspect/question/legal/{idQuestion}', [AplicabilityController::class, 'getLegalsQuestions']);
Route::post('aplicability/aspect/question/answer', [AplicabilityController::class, 'setAnswers']);
Route::post('aplicability/aspect/question/answer/multiple', [AplicabilityController::class, 'setMultiAnswers']);
Route::post('aplicability/aspect/classify', [AplicabilityController::class, 'setClassifyAspectQuiz']);
Route::get('aplicability/aspect/answers', [AplicabilityController::class, 'getAspectAnswers']);
Route::post('aplicability/audit/set',  [AplicabilityController::class, 'setInAudit']);
Route::post('aplicability/comments/set',  [AplicabilityController::class, 'setComments']);

//
// Route::get('aplicability/report/{idAplicabilityRegister}', [AplicabilityController::class, 'getReport']);