<?php 

namespace App\Classes\Applicability;

use App\Models\V2\Audit\Aplicability;
use App\Models\V2\Audit\ContractAspect;
use App\Models\V2\Audit\EvaluateApplicabilityQuestion;
use App\Models\V2\Catalogs\AnswerValue;
use App\Models\V2\Catalogs\QuestionType;

class StateSurvey 
{
  protected $idContractAspect = null;
  protected $contractAspect = null;
  protected $stateQuestions = null;

  public function __construct($idContractAspect)
  {
    $this->idContractAspect = $idContractAspect;
    $this->contractAspect = ContractAspect::findOrFail($idContractAspect);
  }

  private function getInitDependency() 
  {
    $questions = EvaluateApplicabilityQuestion::with(['question.answers.dependency'])
      ->where('id_contract_aspect', $this->idContractAspect)
      ->customOrderEvaluateQuestion()->get();
  
    $allQuestion = $questions->pluck('id_question');
    $lockedQuestion = $questions->pluck('question')->flatMap(fn($item) => $item->answers)
      ->flatMap(fn($item) => $item->dependency)->pluck('id_question')->unique();

    $blockQuestion = $allQuestion->filter(fn($item) => !$lockedQuestion->contains($item))->values();

    return [
      'locked' => $lockedQuestion, 
      'unlocked' => $blockQuestion
    ];
  }

  private function getType($type) 
  {
    $types = [
      'complete' => ['key' => 'complete', 'message' => 'Completado'],
      'pending' => ['key' => 'pending', 'message' => 'Selecciona una respuesta'],
      'blocked' => ['key' => 'blocked', 'message' => 'Pregunta bloqueada, pasa a la siguiente pregunta']
    ];
    
    return $types[$type] ?? $types['pending'];
  }

    private function priorityPerApplicationType($applicability) 
  { 
    $isFederal = $applicability->where('id_question_type', QuestionType::FEDERAL)->where('id_answer_value', AnswerValue::AFFIRMATIVE)->isNotEmpty();
    if ( $isFederal ) {
      return [
        'allow' => [QuestionType::FEDERAL],
        'block' => [QuestionType::STATE, QuestionType::LOCAL],
        'has_affirmative_answers' => true,
      ];
    }
    
    $isState = $applicability->where('id_question_type', QuestionType::STATE)->where('id_answer_value', AnswerValue::AFFIRMATIVE)->isNotEmpty();
    if ( $isState ) {
      return [
        'allow' => [QuestionType::FEDERAL, QuestionType::STATE],
        'block' => [QuestionType::LOCAL],
        'has_affirmative_answers' => true,
      ];
    }

    $isLocal = $applicability->where('id_question_type', QuestionType::LOCAL)->where('id_answer_value', AnswerValue::AFFIRMATIVE)->isNotEmpty();
    if ( $isLocal ) {
      return [
        'allow' => [QuestionType::FEDERAL, QuestionType::STATE, QuestionType::LOCAL],
        'block' => [],
        'has_affirmative_answers' => true,
      ];
    }
    
    $defaultPriority = [
      'allow' => [QuestionType::FEDERAL, QuestionType::STATE, QuestionType::LOCAL],
      'block' => [],
      'has_affirmative_answers' => false,
    ];

    return $defaultPriority;
  }

  private function getApplicabilityState()
  {
    $relationships = ['answer_question'];
    $applicability = Aplicability::with($relationships)->where('id_contract_aspect', $this->idContractAspect)->get();
    return $applicability->map(function($item) {
      return [
        'order' => $item['question']['order'],
        'id_question' => $item['id_question'],
        'id_answer_question' => $item['id_answer_question'],
        'id_answer_value' => $item['id_answer_value'],
        'id_question_type' => $item['question']['id_question_type'],
        'id_evaluate_question' => $item['id_evaluate_question'],
      ];
    });
  }

  private function getFormEvaluateState() 
  {
    $questions = EvaluateApplicabilityQuestion::with(['question.answers.dependency'])
      ->where('id_contract_aspect', $this->idContractAspect)
      ->customOrderEvaluateQuestion()->get();

    return $questions->map(function($item) {
      return [
        'order' => $item['question']['order'],
        'id_question' => $item['id_question'],
        'id_question_type' => $item['question']['id_question_type'],
        'id_evaluate_question' => $item['id'],
        'answers' => $item['question']['answers']->map(function($item) {
          return [
            'id_answer_question' => $item['id_answer_question'],
            'id_answer_value' => $item['id_answer_value'],
            'allowed' => $item['dependency']->pluck('id_question')
          ];
        })
      ];
    });
  }

  private function getOrderPerStage($stage, $itemOrderEvaluate, $currentOrderEvaluate) 
  {
    if ($stage == 'after') {
      return $itemOrderEvaluate <= $currentOrderEvaluate;
    }
    return $itemOrderEvaluate < $currentOrderEvaluate;
  }

  public function getState($idEvaluateQuestion, $stage = 'after') 
  {
    $generalState = $this->getInitDependency(); // get all evaluate question in locked and unlocked 
    $questionsEvaluateState = $this->getFormEvaluateState(); // get all evaluate question with details
    $answersApplicabilityState = $this->getApplicabilityState(); // get all applicability records with answers (id_answer_question with value or null)
    $priority = $this->priorityPerApplicationType($answersApplicabilityState); // get all question type allow and block
    
    // get allow questions (questions has answers with value)
    $applicabilityAnswerWithValue = $answersApplicabilityState->whereNotNull('id_answer_question')->whereIn('id_question_type', $priority['allow'])->values();
    $questionApplicability = $applicabilityAnswerWithValue->pluck('id_question');
    $answerApplicability = $applicabilityAnswerWithValue->pluck('id_answer_question');

    $allowed = $questionsEvaluateState->whereIn('id_question', $questionApplicability)->map(function($item) use ($answerApplicability) {
      $answers = $item['answers']->whereIn('id_answer_question', $answerApplicability);
      return $answers->flatMap(fn($item) => $item['allowed']);
    })->collapse()->unique()->values();
    
    // get block questions (questions are question_type with value block)
    $applicabilityAnswerWithNull = $answersApplicabilityState->whereNull('id_answer_question')->whereIn('id_question_type', $priority['allow'])->values();
    $questionsEvaluateTypeBlock = $questionsEvaluateState->whereIn('id_question_type', $priority['block'])->values();

    $blocked = $questionsEvaluateTypeBlock->pluck('id_question')->unique()->values();
    
    // current question evaluate
    $foundQuestionEvaluate = $questionsEvaluateState->where('id_evaluate_question', $idEvaluateQuestion)->first();
    $questionsPrevious = $questionsEvaluateState->where('order', '<=', $foundQuestionEvaluate['order'])->pluck('id_question');

    $freeQuestionWithAnswerValue = $applicabilityAnswerWithValue->pluck('id_question')->intersect($generalState['unlocked'])->merge($questionsPrevious)->unique()->values();
    $freeQuestionWithAnswerNull = $applicabilityAnswerWithNull->pluck('id_question')->intersect($generalState['unlocked'])->unique()->values();

    // keep states and ignore questions
    $keepUnlocked = $generalState['unlocked']->diff($blocked)->values()->filter(fn($item) => !$freeQuestionWithAnswerValue->contains($item))->values();
    $keepLocked = $generalState['locked']->diff($allowed)->values()->filter(fn($item) => !$freeQuestionWithAnswerValue->contains($item))->values();

    // handler current order
    $questionLessOrEqualTo = $questionsEvaluateState->filter( fn($item) => $this->getOrderPerStage( $stage, $item['order'], $foundQuestionEvaluate['order']) )->values()
      ->whereIn('id_question_type', $priority['allow'])->pluck('id_question');

    // remove question with answer in applicability and current 
    $blocked = $blocked->filter(fn($item) => !$questionLessOrEqualTo->contains($item))->values();
    $allowed = $allowed->filter(fn($item) => !$questionLessOrEqualTo->contains($item))->values();
    $keepLocked = $keepLocked->filter(fn($item) => !$questionLessOrEqualTo->contains($item))->merge($blocked)->values();
    $keepUnlocked = $keepUnlocked->filter(fn($item) => !$questionLessOrEqualTo->contains($item))->merge($allowed)->values();

    if ( !$priority['has_affirmative_answers'] ) {
      $keepUnlocked = $keepUnlocked->merge($freeQuestionWithAnswerNull)->unique()->values();
    }

    return [
      'keep_locked' => $keepLocked,
      'keep_unlocked' => $keepUnlocked
    ];
  }

  public function verifyQuestions($questions)
  { 
    $state = $this->getInitDependency($this->idContractAspect);
    $initStatusContractAspect = $this->contractAspect->id_status == Aplicability::NOT_CLASSIFIED_APLICABILITY;
    
    return $questions->map(function($item) use ($state, $initStatusContractAspect) {
      $answers = collect([]);
      $messages = collect([]);
      $hasApplicability = $item['applicability']->isNotEmpty();
      
      $item->applicability->each(function($answer) use ($answers) {
        $answers->push($answer['id_answer_value']);
      });

      if ( $hasApplicability ) {
        $messages->push( $this->getType('complete') );
      } else {
        $messages->push( $this->getType('pending') );
        $answers->push(null);
      }

      if ( $hasApplicability ) {
        $isBlocked = is_null($answers->first());
        if ( $isBlocked ) {
          $messages->push( $this->getType('blocked') );
          $messages = $messages->filter(fn($item) => $item['key'] == 'blocked')->values();
        }  
      }
      
      $hasBlocked = $state['locked']->contains($item->id_question);
      if ( $initStatusContractAspect && $hasBlocked && $hasApplicability ) {
        $messages->push( $this->getType('blocked') );
        $messages = $messages->filter(fn($item) => $item['key'] == 'blocked')->values();
      }

      $item->evaluate = $messages->toArray();
      
      return $item;
    });
  }
}