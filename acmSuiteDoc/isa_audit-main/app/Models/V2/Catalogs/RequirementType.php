<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\V2\UtilitiesTrait;
use App\Traits\V2\RelationRequirements;

class RequirementType extends Model
{
  use HasFactory, UtilitiesTrait, RelationRequirements;

  protected $table = 'c_requirement_types';
  protected $primaryKey = 'id_requirement_type';

  // group 0 (main) REQUIREMENT_SPECIFIC ignore
  const IDENTIFICATION_FEDERAL = 1;
  const IDENTIFICATION_STATE = 2;
  const REQUIREMENT_STATE = 4;
  const REQUIREMENT_COMPOSE = 5;
  const REQUIREMENT_SPECIFIC = 11;
  const REQUIREMENT_LOCAL = 12;
  const IDENTIFICATION_LOCAL = 13;
  const REQUIREMENT_IDENTIFICATION_COMPOSE = 17;
  // group subrequirement
  const SUB_IDENTIFICATION_FEDERAL = 6;
  const SUB_IDENTIFICATION_STATE = 8;
  const SUBREQUIREMENT_STATE = 10;
  const SUBREQUIREMENT_LOCAL = 14;
  const SUB_IDENTIFICATION_LOCAL = 15;

  public function scopeGetMainGroup($query)
  {
    $query->orderBy('requirement_type', 'ASC');

    $option = request('option');
    
    if ( empty($option) ) return;

    if ( $option == 'main' ) {
      $query->where('group', 0)->where('id_requirement_type', '!=' , 11);
      return;
    }

    $idRequirement = request('idRequirement');
    if ( $option == 'sub' && !empty($idRequirement) ) {
      $currentRequirement = Requirement::findOrFail($idRequirement);
      $identification = $currentRequirement->requirement_type->identification;
      $applicationType = $currentRequirement->id_application_type;
      $query->where('group', $applicationType)->where('identification', $identification);
      return;
    }

    // return void without options
    $query->whereNull('id_requirement_type');
  }

  public function scopeGetRelationsForAnswerQuestion($query, $idQuestion)
  {
    $filterRequirementTypes = $this->getRequirementTypeByAnswerQuestion($idQuestion);
    $query->whereIn('id_requirement_type', $filterRequirementTypes);
  }
}