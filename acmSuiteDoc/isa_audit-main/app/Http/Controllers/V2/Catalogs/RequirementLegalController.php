<?php

namespace App\Http\Controllers\V2\Catalogs;

use App\Http\Controllers\Controller;
use App\Models\V2\Catalogs\Guideline;
use App\Models\V2\Catalogs\LegalBasi;
use App\Models\V2\Catalogs\Requirement;
use App\Traits\V2\ResponseApiTrait;

class RequirementLegalController extends Controller
{
  use ResponseApiTrait;
  
  /**
   * Get source for filter
   */
  public function guideline($idForm, $idRequirement) {
    try {
      $data = Guideline::filter()->getRelationsForRequirement($idRequirement)->get();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Get articles for relations
   */
  public function articles($idForm, $idRequirement) {
    try {
      $data = LegalBasi::filter()->getRelationForRequirement($idRequirement)->customOrder()->getOrPaginate();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Set/Remove relations 
   */
  public function relation($idForm, $idRequirement, $idLegalBasis) 
  {
    try {
      $requirement = Requirement::findOrFail($idRequirement);
      $requirement->articles()->toggle([$idLegalBasis]);
      return $this->successResponse($requirement);
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}