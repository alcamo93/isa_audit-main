<?php

namespace App\Traits\V2;

use App\Models\V2\Admin\Address;
use App\Models\V2\Admin\Corporate;
use App\Models\V2\Catalogs\Requirement;
use App\Models\V2\Catalogs\Subrequirement;
use App\Models\V2\Catalogs\Guideline;
use App\Models\V2\Catalogs\RequirementType;
use App\Models\V2\Catalogs\ApplicationType;
use App\Models\V2\Catalogs\QuestionType;
use App\Models\V2\Catalogs\Question;

trait RelationLegals 
{
  /**
   * Search guidelines compatibles with requirement
   */
  public function getGuidelineByRequirement($idRequirement)
  {
    $requirement = Requirement::findOrFail($idRequirement);
    $idRequirementType = $requirement->id_requirement_type;

    $isSpecific = $idRequirementType == RequirementType::REQUIREMENT_SPECIFIC;
    $isFederal = $idRequirementType == RequirementType::IDENTIFICATION_FEDERAL;
    $isState = $idRequirementType == RequirementType::IDENTIFICATION_STATE || $idRequirementType == RequirementType::REQUIREMENT_STATE;
    $isLocal = $idRequirementType == RequirementType::REQUIREMENT_LOCAL || $idRequirementType == RequirementType::IDENTIFICATION_LOCAL;
    $isCompose = $idRequirementType == RequirementType::REQUIREMENT_COMPOSE || $idRequirementType == RequirementType::REQUIREMENT_IDENTIFICATION_COMPOSE;
    
    if ($isSpecific) {
      $corporate = Corporate::findOrFail($requirement->id_corporate);
      $address = $corporate->addresses->where('type', Address::PHYSICAL)->first();
      if ( is_null($address) ) {
        return collect([]);
      }
      $data = Guideline::where('id_application_type', ApplicationType::ACT)
        ->where('id_state', $address->id_state)->where('id_city', $address->id_city)->get();
      return $data;
    }

    if ($isFederal) {
      $data = Guideline::where('id_application_type', ApplicationType::FEDERAL)
        ->whereNull('id_state')->whereNull('id_city')->get();
      return $data;
    }
    if ($isState) {
      $data = Guideline::where('id_application_type', ApplicationType::STATE)
        ->where('id_state', $requirement->id_state)->whereNull('id_city')->get();
      return $data;
    }
    if ($isLocal) {
      $data = Guideline::where('id_application_type', ApplicationType::LOCAL)
        ->where('id_state', $requirement->id_state)->where('id_city', $requirement->id_city)->get();
      return $data;
    }
    if ($isCompose) {
      $idApplicationType = $requirement->id_application_type;
      $data = Guideline::where('id_application_type', $idApplicationType);
      if ($idApplicationType == ApplicationType::FEDERAL) {
        $data->whereNull('id_state')->whereNull('id_city');
      }
      if ($idApplicationType == ApplicationType::STATE) {
        $data->where('id_state', $requirement->id_state)->whereNull('id_city');
      }
      if ($idApplicationType == ApplicationType::LOCAL) {
        $data->where('id_state', $requirement->id_state)->where('id_city', $requirement->id_city);
      }
      return $data->get();
    }
  }

  /**
   * Search guidelines compatibles with subrequirement
   */
  public function getGuidelineBySubrequirement($idSubrequirement)
  {
    $subrequirement = Subrequirement::findOrFail($idSubrequirement);
    $idRequirementType = $subrequirement->id_requirement_type;

    $isFederal = $idRequirementType == RequirementType::SUB_IDENTIFICATION_FEDERAL;
    $isState = $idRequirementType == RequirementType::SUB_IDENTIFICATION_STATE || $idRequirementType == RequirementType::SUBREQUIREMENT_STATE;
    $isLocal = $idRequirementType == RequirementType::SUBREQUIREMENT_LOCAL || $idRequirementType == RequirementType::SUB_IDENTIFICATION_LOCAL;

    if ($isFederal) {
      $data = Guideline::where('id_application_type', ApplicationType::FEDERAL)
        ->whereNull('id_state')->whereNull('id_city')->get();
      return $data;
    }
    if ($isState) {
      $data = Guideline::where('id_application_type', ApplicationType::STATE)
        ->where('id_state', $subrequirement->id_state)->whereNull('id_city')->get();
      return $data;
    }
    if ($isLocal) {
      $data = Guideline::where('id_application_type', ApplicationType::LOCAL)
        ->where('id_state', $subrequirement->id_state)->where('id_city', $subrequirement->id_city)->get();
      return $data;
    }
  }

  /**
   * Search guidelines compatibles with question
   */
  public function getGuidelineByQuestion($idQuestion)
  {
    $question = Question::findOrFail($idQuestion);
    $idQuestionType = $question->id_question_type;

    $isFederal = $idQuestionType == QuestionType::FEDERAL;
    $isState = $idQuestionType == QuestionType::STATE;
    $isLocal = $idQuestionType == QuestionType::LOCAL;

    if ($isFederal) {
      $data = Guideline::where('id_application_type', ApplicationType::FEDERAL)
        ->whereNull('id_state')->whereNull('id_city')->get();
      return $data;
    }
    if ($isState) {
      $data = Guideline::where('id_application_type', ApplicationType::STATE)
        ->where('id_state', $question->id_state)->whereNull('id_city')->get();
      return $data;
    }
    if ($isLocal) {
      $data = Guideline::where('id_application_type', ApplicationType::LOCAL)
        ->where('id_state', $question->id_state)->where('id_city', $question->id_city)->get();
      return $data;
    }
  }
}