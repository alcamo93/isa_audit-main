<?php

namespace App\Http\Controllers\V2\Catalogs;

use App\Http\Controllers\Controller;
use App\Models\V2\Catalogs\Question;
use App\Models\V2\Catalogs\AnswersQuestion;
use App\Models\V2\Catalogs\AnswerQuestionDependency;
use App\Traits\V2\ResponseApiTrait;

class DependencyController extends Controller
{
  use ResponseApiTrait;

  /**
   * Get records for relations
   */
  public function records($idForm, $idQuestion) {
    try {
      $answers = AnswersQuestion::filter()->getDependencyPerQuestion($idQuestion)->customOrder()->getOrPaginate()->toArray();
      $data = $this->buildPoolQuestion($answers, $idQuestion);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Set/Remove relations 
   */
  public function relation($idForm, $idQuestion, $idAnswerQuestion, $idQuestionBlock) 
  {
    try {
      $allQuestions = Question::getQuestionForDependency($idQuestion)->get();
      $notBelongToPool = !$allQuestions->pluck('id_question')->contains($idQuestionBlock);
      if ( $notBelongToPool ) {
        $message = 'La pregunta que desea bloquear no pertenece al mismo grupo de la pregunta que especificas o cambio su orden';
        return $this->errorResponse($message);
      }
      $answerQuestion = AnswersQuestion::findOrFail($idAnswerQuestion);
      $answerQuestion->dependency()->toggle([$idQuestionBlock]);
      return $this->successResponse($answerQuestion);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Remove all relations per questions
   */
  public function remove($idForm, $idQuestion)
  {
    try {
      $answers = AnswersQuestion::with('dependency')->where('id_question', $idQuestion)->get();
      foreach ($answers as $answer) {
        $questionsIds = $answer->dependency->pluck('id_question')->values()->toArray();
        $relations = AnswerQuestionDependency::where('id_answer_question', $answer->id_answer_question)
          ->whereIn('id_question', $questionsIds);
        $relations->delete();
      }
      return $this->successResponse([$answers]);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Build pool question block and free
   */
  private function buildPoolQuestion($answers, $idQuestion)
  {
    $allQuestions = Question::getQuestionForDependency($idQuestion)->get()->toArray();
    foreach ($answers['data'] as $index => $answer) {
      $poolQuestions = collect($answer['dependency']);
      $questionsSelected = $poolQuestions->pluck('id_question')->values();
      $questionsFree = collect($allQuestions)->whereNotIn('id_question', $questionsSelected)->values();
      $questionsFree->map( fn($item) => $poolQuestions->push($item) );
      $questionsMix = $poolQuestions->values()->sortBy('order')->values()->toArray();
      foreach ($questionsMix as $key => $question) {
        $questionsMix[$key]['evaluates'] = $this->getAttributes($question);
      }
      $answers['data'][$index]['dependency'] = $questionsMix;
    }
    return $answers;
  }
  
  /**
   * 
   */
  private function getAttributes($question)
  {
    $hasPivot = isset($question['pivot']) ? true : false;
    $idStatus = $question['id_status'];
    if ($idStatus == 1 && $hasPivot) {
      return [
        'color' => 'success',
        'label' => 'Seleccionada',
        'block' => true
      ];
    }
    if ($idStatus == 1 && !$hasPivot) {
      return [
        'color' => 'secondary',
        'label' => 'Libre',
        'block' => false
      ];
    }
    if ($idStatus == 2 && $hasPivot) {
      return [
        'color' => 'warning',
        'label' => 'Seleccionada (Inavtivo)',
        'block' => true
      ];
    }
    if ($idStatus == 2 && !$hasPivot) {
      return [
        'color' => 'danger',
        'label' => 'Libre (Inavtivo)',
        'block' => false
      ];
    }
  }
}