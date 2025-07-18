<?php

namespace App\Http\Controllers\V2\Catalogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Models\V2\Catalogs\RequirementType;
use App\Models\V2\Catalogs\Requirement;
use App\Models\V2\Catalogs\AnswersQuestion;
use App\Traits\V2\ResponseApiTrait;

class AnswerQuestionRequirementController extends Controller
{
  use ResponseApiTrait;
  
  /**
   * Get source for filter
   */
  public function requirementTypes($idForm, $idQuestion, $idAnswerQuestion) 
  {
    try {
      $data = RequirementType::filter()->getRelationsForAnswerQuestion($idQuestion)->get();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Get requirements for relations
   */
  public function requirements($idForm, $idQuestion, $idAnswerQuestion) 
  {
    try {
      $data = Requirement::filter()->getRelationForAnswerQuestion($idQuestion, $idAnswerQuestion)->customOrderRequirement()->getOrPaginate();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Set/Remove relations 
   */
  public function relation($idForm, $idQuestion, $idAnswerQuestion, $idRequirement) 
  {
    try {
      $requirement = Requirement::findOrFail($idRequirement);
      if ( $idForm != $requirement->form_id ) {
        return $this->errorResponse("El requerimiento no pertenece al mismo formulario, por favor selecciona un requerimiento que corresponda");
      }
      if ( boolval($requirement->has_subrequirement) ) {
        $typeName = $requirement->requirement_type->requirement_type;
				return $this->errorResponse("{$typeName}, por favor selecciona subrequreriemintos dentro de su sección");
      }
      $answerQuestion = AnswersQuestion::findOrFail($idAnswerQuestion);
      $relation = [$idRequirement => ['id_question' => $answerQuestion->id_question, 'id_subrequirement' => null]];
      $answerQuestion->requirements_assigned()->toggle($relation);
      return $this->successResponse($answerQuestion);
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}