<?php

use App\Http\Controllers\Audit\AuditController;
use Illuminate\Support\Facades\Route;

// views
Route::get('audit/register/{idAuditRegister}', [AuditController::class, 'index'])->middleware('profile');
// owner
Route::post('audit/resgisters', [AuditController::class, 'auditRegistersDT']);
Route::post('audit/action/set',  [AuditController::class, 'setInAction']);
Route::get('audit/report/{idAuditRegister}', [AuditController::class, 'getReport']);
Route::get('audit/progress/report/{idAuditRegister}', [AuditController::class, 'getReportAuditProgress']);
Route::get('audit/document/report/{idAuditRegister}', [AuditController::class, 'getReportAuditDocument']);

// info
Route::get('audit/resgisters/matters/{idAuditRegister}', [AuditController::class, 'auditRegistersMatters']);
Route::get('audit/resgisters/matter/progress/{idAuditRegister}', [AuditController::class, 'auditRegistersMatterProgress']);
Route::post('audit/resgisters/aspects', [AuditController::class, 'auditRegistersAspectsDT']);
Route::post('audit/aspect/classify', [AuditController::class, 'setClassifyAspect']);
Route::post('audit/resgisters/aspect/delete', [AuditController::class, 'deleteAuditAspect']);
// Requiremnts quiz
Route::get('audit/quiz/aspect', [AuditController::class, 'auditQuizAspects']);
Route::get('audit/requirement/{idRequirement}', [AuditController::class, 'getRequirement']);
Route::get('audit/requirement/basies/{idRequirement}', [AuditController::class, 'getRequirementBasies']);
// Route::get('audit/requirement/recomendation/{idRequirement}', 'Audit\AuditController@getRequirementRecomendations');
Route::get('audit/aspect/answers', [AuditController::class, 'getAspectAnswers']);
// Subrequirements quiz
Route::get('audit/quiz/subrequirement', [AuditController::class, 'getSubrequirements']);
Route::get('audit/requirement/sub/{idSubrequirement}', [AuditController::class, 'getSubrequirement']);
Route::get('audit/requirement/basies/sub/{idSubrequirement}', [AuditController::class, 'getSubrequirementBasies']);
// Route::get('audit/requirement/recomendation/sub/{idSubrequirement}', 'Audit\AuditController@getSubrequirementRecomendations');
Route::get('audit/requirement/answers/sub', [AuditController::class, 'getSubrequirementAnswers']);
Route::post('audit/requirement/evaluate/sub', [AuditController::class, 'evaluateSubrequirementsAnswers']);
// Answers quiz
Route::post('audit/aspect/requiremnt/answer', [AuditController::class, 'setAnswers']);
Route::get('audit/aspect/answer', [AuditController::class, 'getOnlyAnswer']);
Route::post('audit/requirement/finding/set', [AuditController::class, 'setFinding']);
Route::post('audit/aspect/answer/evaluate', [AuditController::class, 'setAnswerEvaluate']);
// Route::post('audit/requirement/recomendation/set', 'Audit\AuditController@setAuditRecomendation');
// Risk requirements
// Route::get('audit/aspect/risk/data', 'Audit\AuditController@getDataRisk');
Route::post('audit/aspect/risk/set/answer', [AuditController::class, 'setAnswerRisk']);

Route::get('audit/test/{idAudit}', [AuditController::class, 'auditTest']);
