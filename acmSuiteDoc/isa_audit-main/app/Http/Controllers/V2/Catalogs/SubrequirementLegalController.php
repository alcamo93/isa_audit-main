<?php

namespace App\Http\Controllers\V2\Catalogs;

use App\Http\Controllers\Controller;
use App\Models\V2\Catalogs\Guideline;
use App\Models\V2\Catalogs\LegalBasi;
use App\Models\V2\Catalogs\Subrequirement;
use App\Traits\V2\ResponseApiTrait;

class SubrequirementLegalController extends Controller
{
  use ResponseApiTrait;
  
  /**
   * Get source for filter
   */
  public function guideline($idForm, $idRequirement, $idSubrequirement) {
    try {
      $data = Guideline::filter()->getRelationsForSubrequirement($idSubrequirement)->get();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Get articles for relations
   */
  public function articles($idForm, $idRequirement, $idSubrequirement) {
    try {
      $data = LegalBasi::filter()->getRelationForSubrequirement($idSubrequirement)->customOrder()->getOrPaginate();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Set/Remove relations 
   */
  public function relation($idForm, $idRequirement, $idSubrequirement, $idLegalBasis) 
  {
    try {
      $subrequirement = Subrequirement::findOrFail($idSubrequirement);
      $subrequirement->articles()->toggle([$idLegalBasis]);
      return $this->successResponse($subrequirement);
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}