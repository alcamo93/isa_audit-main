<?php

namespace App\Traits\V2;

use App\Models\V2\Catalogs\RequirementType;
use App\Models\V2\Catalogs\QuestionType;
use App\Models\V2\Catalogs\Question;

trait RelationRequirements 
{
  public function getRequirementTypeByAnswerQuestion($idQuestion) 
  {
    $question = Question::findOrFail($idQuestion);
    // search about question type
    $idQuestionType = $question->id_question_type;
    $isFederal = $idQuestionType == QuestionType::FEDERAL;
    $isState = $idQuestionType == QuestionType::STATE;
    $isLocal = $idQuestionType == QuestionType::LOCAL;
    // filter by locations about type
    if ($isFederal) {
      return [
        RequirementType::IDENTIFICATION_FEDERAL,
        RequirementType::REQUIREMENT_IDENTIFICATION_COMPOSE
      ];
    }
    if ($isState) {
      return [
        RequirementType::IDENTIFICATION_STATE, 
        RequirementType::REQUIREMENT_IDENTIFICATION_COMPOSE
      ];
    }
    if ($isLocal) {
      return [
        RequirementType::IDENTIFICATION_LOCAL, 
        RequirementType::REQUIREMENT_IDENTIFICATION_COMPOSE
      ];
    }
  }
}