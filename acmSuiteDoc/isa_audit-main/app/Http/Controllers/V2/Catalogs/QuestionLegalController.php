<?php

namespace App\Http\Controllers\V2\Catalogs;

use App\Http\Controllers\Controller;
use App\Models\V2\Catalogs\Guideline;
use App\Models\V2\Catalogs\LegalBasi;
use App\Models\V2\Catalogs\Question;
use App\Traits\V2\ResponseApiTrait;

class QuestionLegalController extends Controller
{
  use ResponseApiTrait;
  
  /**
   * Get source for filter
   */
  public function guideline($idForm, $idQuestion) {
    try {
      $data = Guideline::filter()->getRelationsForQuestion($idQuestion)->get();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Get articles for relations
   */
  public function articles($idForm, $idQuestion) {
    try {
      $data = LegalBasi::filter()->getRelationForQuestion($idQuestion)->customOrder()->getOrPaginate();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Set/Remove relations 
   */
  public function relation($idForm, $idQuestion, $idLegalBasis) 
  {
    try {
      $question = Question::findOrFail($idQuestion);
      $question->articles()->toggle([$idLegalBasis]);
      return $this->successResponse($question);
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}