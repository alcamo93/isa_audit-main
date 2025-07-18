<?php

namespace App\Classes\Applicability;

use App\Classes\Applicability\StateSurvey;
use App\Models\V2\Audit\Aplicability;
use App\Models\V2\Audit\ContractAspect;
use App\Models\V2\Audit\EvaluateApplicabilityQuestion;
use App\Models\V2\Catalogs\AnswersQuestion;
use App\Models\V2\Catalogs\Question;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class HandlerAnswerApplicability
{
  protected $idEvaluateQuestion = null;
  protected $answerValues = null;
  protected $evaluateQuestion = null;
  protected $allowMultipleAnswer = null;
  protected $contractAspect = null;

  /**
   * 
   * @param integer $idEvaluateQuestion
   * 
   */
  public function __construct($idEvaluateQuestion, $answerValues)
  {
    $this->idEvaluateQuestion = $idEvaluateQuestion;
    $this->answerValues = AnswersQuestion::whereIn('id_answer_question', collect($answerValues)->pluck('id_answer_question'))->get();
    $this->evaluateQuestion = EvaluateApplicabilityQuestion::with(['contract_aspect', 'question.answers.dependency', 'applicability.answer_question'])->find($idEvaluateQuestion);
    $this->contractAspect = ContractAspect::find($this->evaluateQuestion->id_contract_aspect);
  }

  /**
   * verify and set answer in applicability
   */
  public function setApplicabilityAnswer() 
  {
    $isFinishAspect = $this->contractAspect->id_status == Aplicability::FINISHED_APLICABILITY;
    if ( $isFinishAspect ) {
      $info['success'] = false;
      $info['message'] = 'El Aspecto ha sido finalizado, no puede modificarse';
      return $info;
    }

    $verifyQuestion = $this->verifyQuestion();
    if ( !$verifyQuestion['success'] ) return $verifyQuestion;

    $verifyType = $this->verifyTypeQuestion();
    if ( !$verifyType['success'] ) return $verifyType;

    $buildAnswers = $this->buildAnswerApplicability();
    if ( !$buildAnswers['success'] ) return $buildAnswers;

    $buildAnswersDependency = $this->buildAnswerApplicabilityDependency();
    if ( !$buildAnswersDependency['success'] ) return $buildAnswersDependency;

    $verifyStatus = $this->verifyStatus();
    if ( $verifyStatus['success'] ) return $verifyStatus;

    $info['success'] = true;
    $info['message'] = 'Registro exitoso';
    return $info;
  }

  /**
   * get dependency  
   */
  private function getFormState($stage = 'after') 
  {
    $stateSurvey = new StateSurvey($this->evaluateQuestion->id_contract_aspect);
    return $stateSurvey->getState($this->idEvaluateQuestion, $stage);
  }

  private function verifyQuestion()
  {
    if ( $this->contractAspect->id_status == Aplicability::FINISHED_APLICABILITY ) {
      $info['success'] = false;
      $info['message'] = 'El aspecto ha sido finalizado, no se puede modificar';
      return $info;
    }

    $state = $this->getFormState('before');
    $hasDependecy = $state['keep_locked']->contains($this->evaluateQuestion->id_question);
    if ( $hasDependecy ) {
      $info['success'] = false;
      $info['message'] = 'Esta pregunta esta bloqueada hasta que se seleccione una respuesta que la habilite';
      return $info;
    }

    $info['success'] = true;
    $info['message'] = 'Todo en orden, ninguna respuesta la condiciona';
    return $info;
  }

  /**
   * verify type question
   */
  private function verifyTypeQuestion() 
  {
    $this->allowMultipleAnswer = $this->evaluateQuestion->question->allow_multiple_answers;

    if ( $this->allowMultipleAnswer == Question::SINGLE_ANSWER && $this->answerValues->count() > 1 ) {
      $info['success'] = false;
      $info['message'] = 'Esta pregunta no permite respuesta multiple, por favor envia solo una opción';
      return $info;
    }

    $info['success'] = true;
    $info['message'] = 'Todo en orden';
    return $info;
  }

  /**
   * build answer for applicability
   */
  private function buildAnswerApplicability() 
  {
    try {
      if ( $this->allowMultipleAnswer == Question::MULTIPLE_ANSWER ) {
        $applicabilityRecords = $this->evaluateQuestion->applicability;
        $answerQuestionPrevious = collect($applicabilityRecords->map(fn($item) => $item->answer_question->id_answer_question)->toArray());
        $answerQuestionCurrent = $this->answerValues->pluck('id_answer_question');
        // answer in request and answers 
        $inKeep = $answerQuestionPrevious->intersect($answerQuestionCurrent)->values();
        $inAdd = $answerQuestionCurrent->diff($answerQuestionPrevious)->values();
        $inRemove = $answerQuestionPrevious->diff($answerQuestionCurrent)->values();
        
        $applicabilityIds = $applicabilityRecords->whereIn('id_answer_question', $inRemove);
        $applicabilityIds->each( fn($item) => $item->delete() );
      }
      
      $build = $this->answerValues->map(function($item) {
        return [
          'id_answer_value' => $item->id_answer_value,
          'id_contract_aspect' => $this->evaluateQuestion->id_contract_aspect,
          'id_audit_processes' => $this->contractAspect->id_audit_processes,
          'id_aspect' => $this->evaluateQuestion->contract_aspect->id_aspect,
          'id_question' => $this->evaluateQuestion->id_question,
          'id_answer_question' => $item->id_answer_question,
          'id_user' => Auth::id(),
          'id_evaluate_question' => $this->idEvaluateQuestion,
        ];
      });

      $findFieldAnswer = $this->allowMultipleAnswer == Question::MULTIPLE_ANSWER 
        ? ['id_contract_aspect', 'id_question', 'id_answer_question']
        : ['id_contract_aspect', 'id_question'];

      foreach ($build as $item) {
        $findData = Arr::only($item, $findFieldAnswer);
        Aplicability::updateOrCreate($findData, $item);
      }
      
      $info['success'] = true;
      $info['message'] = 'Todo en orden';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al intentar construir las respuestas';
      return $info;
    }
  }

  /**
   * build applicability dependency
   */
  private function buildAnswerApplicabilityDependency()
  {
    try {
      $state = $this->getFormState();
      
      $blockEvaluates = EvaluateApplicabilityQuestion::whereIn('id_question', $state['keep_locked'])
        ->where('id_contract_aspect', $this->evaluateQuestion->id_contract_aspect)->get();

      $blocked = $state['keep_locked']->map(function($idQuestion) use ($blockEvaluates) {
        $foundEvaluate = $blockEvaluates->where('id_question', $idQuestion)->first();
        return [
          'id_answer_value' => null,
          'id_contract_aspect' => $this->evaluateQuestion->id_contract_aspect,
          'id_aspect' => $this->evaluateQuestion->contract_aspect->id_aspect,
          'id_question' => $idQuestion,
          'id_answer_question' => null,
          'id_user' => Auth::id(),
          'id_evaluate_question' => $foundEvaluate->id,
        ];
      });
      
      $blocked->each(function($item) {
        $findData = Arr::only($item, ['id_contract_aspect', 'id_question']);
        Aplicability::updateOrCreate($findData, $item);
      });

      $allowEvaluates = Aplicability::whereIn('id_question', $state['keep_unlocked'])
        ->where('id_contract_aspect', $this->evaluateQuestion->id_contract_aspect)->get();
      
      $allowEvaluates->each(function($item) {
        $item->delete();
      });

      $info['success'] = true;
      $info['message'] = 'Todo en orden';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al restablecer estado de formulario';
      return $info;
    }
  }

  private function verifyStatus()
  {
    try {
      if ( $this->contractAspect->id_status == Aplicability::NOT_CLASSIFIED_APLICABILITY ) {
        $this->contractAspect->update([ 'id_status' => Aplicability::EVALUATING_APLICABILITY ]);
      } 
      if ( $this->contractAspect->id_status == Aplicability::CLASSIFIED_APLICABILITY ) {
        $evaluate = new Evaluate($this->evaluateQuestion->id_contract_aspect);
        $applicabilityTypeEvaluate = $evaluate->getApplicationTypeAspect();
        if ( !$applicabilityTypeEvaluate['success'] ) return $applicabilityTypeEvaluate;
      }
  
      $info['success'] = true;
      $info['message'] = 'Estatus actualizado exitosamente';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al actualizar o clasificar aspecto';
      return $info;
    }
  }
}