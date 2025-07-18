<?php

use App\Http\Controllers\V2\Catalogs\QuestionController;
use App\Http\Controllers\V2\Catalogs\AnswerQuestionController;
use App\Http\Controllers\V2\Catalogs\QuestionLegalController;
use App\Http\Controllers\V2\Catalogs\AnswerQuestionRequirementController;
use App\Http\Controllers\V2\Catalogs\AnswerQuestionSubrequirementController;
use App\Http\Controllers\V2\Catalogs\DependencyController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verify.ownership.form.question.api'], 'prefix' => 'v2/catalogs'], function () {
  Route::get('form/{idForm}/question/view', [QuestionController::class, 'view'])->name('v2.question.view');
  Route::get('form/{idForm}/question', [QuestionController::class, 'index'])->name('v2.question.index');
  Route::post('form/{idForm}/question', [QuestionController::class, 'store'])->name('v2.question.store');
  Route::get('form/{idForm}/question/{idQuestion}', [QuestionController::class, 'show'])->name('v2.question.show');
  Route::put('form/{idForm}/question/{idQuestion}', [QuestionController::class, 'update'])->name('v2.question.update');
  Route::delete('form/{idForm}/question/{idQuestion}', [QuestionController::class, 'destroy'])->name('v2.question.destroy');
  Route::put('form/{idForm}/question/{idQuestion}/status', [QuestionController::class, 'status'])->name('v2.question.status');
});

Route::group(['middleware' => ['auth', 'verify.ownership.form.question.api'], 'prefix' => 'v2/catalogs'], function () {
  Route::get('form/{idForm}/question/{idQuestion}/answer', [AnswerQuestionController::class, 'index'])->name('v2.answer.index');
  Route::post('form/{idForm}/question/{idQuestion}/answer', [AnswerQuestionController::class, 'store'])->name('v2.answer.store');
  Route::get('form/{idForm}/question/{idQuestion}/answer/{idAnswerQuestion}', [AnswerQuestionController::class, 'show'])->name('v2.answer.show');
  Route::put('form/{idForm}/question/{idQuestion}/answer/{idAnswerQuestion}', [AnswerQuestionController::class, 'update'])->name('v2.answer.update');
  Route::delete('form/{idForm}/question/{idQuestion}/answer/{idAnswerQuestion}', [AnswerQuestionController::class, 'destroy'])->name('v2.answer.destroy');
});

Route::group(['middleware' => ['auth', 'verify.ownership.form.question.api'], 'prefix' => 'v2/catalogs'], function () {
  Route::get('form/{idForm}/question/{idQuestion}/legal/guideline', [QuestionLegalController::class, 'guideline'])->name('v2.question.legal.guideline');
  Route::get('form/{idForm}/question/{idQuestion}/legal/article', [QuestionLegalController::class, 'articles'])->name('v2.question.legal.articles');
  Route::post('form/{idForm}/question/{idQuestion}/legal/relation/{idLegalBasis}', [QuestionLegalController::class, 'relation'])->name('v2.question.legal.relation');
});

Route::group(['middleware' => ['auth', 'verify.ownership.form.question.api'], 'prefix' => 'v2/catalogs'], function () {
  Route::get('form/{idForm}/question/{idQuestion}/answer/{idAnswerQuestion}/requirement/requirement_types', [AnswerQuestionRequirementController::class, 'requirementTypes'])->name('v2.answer.requirement.requirementTypes');
  Route::get('form/{idForm}/question/{idQuestion}/answer/{idAnswerQuestion}/requirement/records', [AnswerQuestionRequirementController::class, 'requirements'])->name('v2.answer.requirement.records');
  Route::post('form/{idForm}/question/{idQuestion}/answer/{idAnswerQuestion}/requirement/relation/{idRequirement}', [AnswerQuestionRequirementController::class, 'relation'])->name('v2.answer.requirement.relation');
});

Route::group(['middleware' => ['auth', 'verify.ownership.form.question.api'], 'prefix' => 'v2/catalogs'], function () {
  Route::get('form/{idForm}/question/{idQuestion}/answer/{idAnswerQuestion}/requirement/{idRequirement}/subrequirements', [AnswerQuestionSubrequirementController::class, 'subrequirements'])->name('v2.answer.requirement.subrequirements');
  Route::post('form/{idForm}/question/{idQuestion}/answer/{idAnswerQuestion}/requirement/{idRequirement}/relation/{id_subrequirement}', [AnswerQuestionSubrequirementController::class, 'relation'])->name('v2.answer.requirement.subrequirement.relation');
  Route::get('form/{idForm}/question/{idQuestion}/answer/{idAnswerQuestion}/requirement/{idRequirement}/verify', [AnswerQuestionSubrequirementController::class, 'verifyAll'])->name('v2.answer.requirement.verify');
  Route::post('form/{idForm}/question/{idQuestion}/answer/{idAnswerQuestion}/requirement/{idRequirement}/all', [AnswerQuestionSubrequirementController::class, 'allHandler'])->name('v2.answer.requirement.subrequirement.handler');
});

Route::group(['middleware' => ['auth', 'verify.ownership.form.question.api'], 'prefix' => 'v2/catalogs'], function () {
  Route::get('form/{idForm}/question/{idQuestion}/dependency/records', [DependencyController::class, 'records'])->name('v2.question.answer.dependency.records');
  Route::post('form/{idForm}/question/{idQuestion}/answer/{idAnswerQuestion}/dependency/{idQuestionBlock}', [DependencyController::class, 'relation'])->name('v2.question.answer.dependency.relation');
  Route::delete('form/{idForm}/question/{idQuestion}/dependency/remove', [DependencyController::class, 'remove'])->name('v2.question.answer.dependency.remove');
});