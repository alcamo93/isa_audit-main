<?php

use App\Http\Controllers\Catalogues\QuestionsController;
use App\Http\Controllers\Catalogues\AnswerQuestionsDepedencyController;
use Illuminate\Support\Facades\Route;

//questions
// Route::get('catalogs/questions', [QuestionsController::class, 'index'])->middleware('profileSubmodule');
Route::post('catalogs/questions', [QuestionsController::class, 'getQuestionsDT']);
Route::post('catalogs/questions/set', [QuestionsController::class, 'setQuestion']);
Route::get('catalogs/questions/get/{idQuestion}', [QuestionsController::class, 'getQuestion']);
Route::post('catalogs/questions/update', [QuestionsController::class, 'updateQuestion']);
Route::post('catalogs/questions/delete', [QuestionsController::class, 'deleteQuestion']);
Route::post('catalogs/questions/change-status', [QuestionsController::class, 'changeStatusQuestion']);
Route::get('catalogs/questions/{idQuestion}', [QuestionsController::class, 'getQuestionInfo']);
Route::get('catalogs/questions/data/view', [QuestionsController::class, 'getAllDataQuestion']);
// Questions - answers
Route::post('catalogs/questions/answers/dt', [QuestionsController::class, 'getAnswersQuestionDT']);
Route::post('catalogs/questions/answers/{idAnswerQuestion}', [QuestionsController::class, 'getQuestionAnswer']);
Route::post('catalogs/questions/answers', [QuestionsController::class, 'setAnswerQuestion']);
Route::post('catalogs/questions/answer/delete', [QuestionsController::class, 'deleteAnswerQuestion']);
// Questions - answers - Requirements
// se modifico con pluck
Route::post('catalogs/questions/answers/requirements/{idQuestion}/{idAnswerQuestion}', [QuestionsController::class, 'getQuestionRequirements']);
Route::post('catalogs/questions/answers/requirements/select', [QuestionsController::class, 'getRequirementsRelationDT']);
Route::post('catalogs/questions/answers/requirements/relation', [QuestionsController::class, 'updateQuestionRequirements']);
Route::post('catalogs/questions/answers/requirements/assigned', [QuestionsController::class, 'getAssignedQuestionRequirementsDT']);
Route::post('catalogs/questions/answers/subrequirements/assigned', [QuestionsController::class, 'getAssignedQuestionSubequirementsDT']);
// Questions - answers - basis
Route::post('catalogs/questions/basis/data/{idQuestion}', [QuestionsController::class, 'getQuestionBasis']);
Route::post('catalogs/questions/basis/relation', [QuestionsController::class, 'updateQuestionBasis']);
Route::post('catalogs/questions/basis/assigned', [QuestionsController::class, 'getQuestionBasisDT']);
// Answers -questions depedency
Route::get('catalogs/questions/dependency/index', [AnswerQuestionsDepedencyController::class, 'index']);
Route::post('catalogs/questions/dependency/set', [AnswerQuestionsDepedencyController::class, 'set']);
Route::post('catalogs/questions/dependency/remove', [AnswerQuestionsDepedencyController::class, 'remove']);
// Tests answers
Route::get('questions-test', [AnswerQuestionsDepedencyController::class, 'index']);
Route::get('questions-test/set', [AnswerQuestionsDepedencyController::class, 'set']);