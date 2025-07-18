<?php

namespace App\Http\Controllers\V2\Catalogs;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\V2\Catalogs\Subrequirement;
use App\Models\V2\Catalogs\AnswersQuestion;
use App\Models\V2\Catalogs\AnswerQuestionRequirement;
use App\Models\V2\Catalogs\Requirement;
use App\Traits\V2\ResponseApiTrait;

class AnswerQuestionSubrequirementController extends Controller
{
  use ResponseApiTrait;

  /**
   * Get subrequirements for relations
   */
  public function subrequirements($idForm, $idQuestion, $idAnswerQuestion, $idRequirement) {
    try {
      $records = Subrequirement::filter()->getRelationForAnswerQuestion($idAnswerQuestion, $idRequirement)->customOrderRequirement()->getOrPaginate();
      $data = $this->verifyAllSelected($records, $idAnswerQuestion, $idRequirement);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Set/Remove relations 
   */
  public function relation($idForm, $idQuestion, $idAnswerQuestion, $idRequirement, $idSubrequirement) 
  {
    try {
      DB::beginTransaction();
      $requirement = Requirement::findOrFail($idRequirement);
      if ( $idForm != $requirement->form_id ) {
        return $this->errorResponse("El requerimiento no pertenece al mismo formulario, por favor selecciona un requerimiento que corresponda");
      }
      $subrequirement = Subrequirement::findOrFail($idSubrequirement);
      $sameRequirement = $subrequirement->id_requirement == $idRequirement;
      if ( !$sameRequirement ) {
				return $this->errorResponse('El subrequerimiento no pertence al requerimiento que especifica');
      }
      // set/remove subrequirement
      $answerQuestion = AnswersQuestion::with('question')->findOrFail($idAnswerQuestion);
      $relation = [ $idSubrequirement => ['id_question' => $answerQuestion->id_question, 'id_requirement' => $idRequirement] ];
      $answerQuestion->subrequirements_assigned()->toggle($relation);
      // set/remove requirement parent
      $existParent = AnswerQuestionRequirement::where('id_answer_question', $answerQuestion->id_answer_question)
        ->where('id_question', $answerQuestion->id_question)->where('id_requirement', $idRequirement)
        ->where('id_subrequirement', null)->get()->isNotEmpty();
      $hasSubrequirements = $answerQuestion->subrequirements_assigned->isNotEmpty();
      $parentRelation = [ 
        'id_answer_question' => $answerQuestion->id_answer_question, 
        'id_question' => $answerQuestion->id_question, 
        'id_requirement' => $idRequirement,
        'id_subrequirement' => null
      ];
      // validate if exist parent else create
      if ( $hasSubrequirements && !$existParent ) {
        AnswerQuestionRequirement::updateOrCreate($parentRelation, $parentRelation);
      } 
      // validate destroy parent when no has subrequirement relation
      if ( !$hasSubrequirements && $existParent) {
        $destroy = AnswerQuestionRequirement::where($parentRelation)->first();
        $destroy->delete();
      }
      DB::commit();
      return $this->successResponse($answerQuestion);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  public function allHandler(Request $request, $idForm, $idQuestion, $idAnswerQuestion, $idRequirement)
  {
    try {
      $selected = $request->input('selected');
      if ($selected) {
        $answerQuestion = AnswersQuestion::with('question')->findOrFail($idAnswerQuestion);
        $subrequirements = Subrequirement::where('id_requirement', $idRequirement)->get();
        $parentRelation = [ 
          'id_answer_question' => $answerQuestion->id_answer_question, 
          'id_question' => $answerQuestion->id_question, 
          'id_requirement' => $idRequirement,
          'id_subrequirement' => null
        ];
        AnswerQuestionRequirement::updateOrCreate($parentRelation, $parentRelation);
        foreach ($subrequirements as $subrequirement) {
          $currentSubrequirement = [
            'id_answer_question' => $answerQuestion->id_answer_question, 
            'id_question' => $answerQuestion->id_question, 
            'id_requirement' => $idRequirement,
            'id_subrequirement' => $subrequirement->id_subrequirement
          ];
          AnswerQuestionRequirement::updateOrCreate($currentSubrequirement, $currentSubrequirement);
        }
      } else {
        $destroy = AnswerQuestionRequirement::where('id_answer_question', $idAnswerQuestion)
          ->where('id_requirement', $idRequirement);
        $destroy->delete();
      }

      $response = [
        'action' => $selected ? 'Asignar' : 'Remover', 
        'id_answer_question' => $idAnswerQuestion,
        'id_requirement' => $idRequirement
      ];
      return $this->successResponse($response);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  private function verifyAllSelected($records, $idAnswerQuestion, $idRequirement)
  {
    try {
      $data = $records->toArray();
      $allSubrequirements = Subrequirement::where('id_requirement', $idRequirement)->count();
      $manualFilter = ['has_relation' => 1];
      $subrequimentsSelected = Subrequirement::getRelationForAnswerQuestion($idAnswerQuestion, $idRequirement, $manualFilter)->count();
      $data['all_selected'] = $allSubrequirements == $subrequimentsSelected;
      return $data;
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}