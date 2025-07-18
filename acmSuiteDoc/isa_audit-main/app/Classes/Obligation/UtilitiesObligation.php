<?php

namespace App\Classes\Obligation;

use App\Traits\V2\RequirementTrait;

class UtilitiesObligation
{
  use RequirementTrait;

  public function __construct()
  {
    // 
  }

  public function defineRisk($record, $evaluateRisk = false) 
  {
     // is parent
     $isParent = boolval($record->requirement->has_subrequirement == 1);
     $isChild = !is_null($record->id_subrequirement);
     if ($isParent && !$isChild) return ['Revise los comentarios en los subrequerimientos'];
     // verify type answer
     $hasNotRisk = sizeof($record->risk_totals) == 0;
     $default = ['Riesgo no evaluado'];
 
     if ( $evaluateRisk && $hasNotRisk && !$isParent ) {
       $risk = ['Aún sin especificar'];
     } elseif ( $evaluateRisk && !$hasNotRisk && !$isParent ) {
       $risk = $record->risk_totals->map(fn($item) => "{$item['category']['risk_category']}: {$item['interpretation']}")->toArray();
     } elseif ( $evaluateRisk && !$hasNotRisk && $isChild ) {
       $risk = $record->risk_totals->map(fn($item) => "{$item['category']['risk_category']}: {$item['interpretation']}")->toArray();
     } else {
       $risk = $default;
     }
 
     return $risk;
  }

  public function defineRecord($record, $scope = '', $evaluateRisk = false) 
  {
    $isSubrequirement = !is_null($record->id_subrequirement);
    $item = $isSubrequirement ? $record->subrequirement : $record->requirement;
    $legal = $this->getFieldRequirement($record, 'articles');
    $build['id_requirement'] = $item->id_requirement;
    $build['matter'] = $this->getFieldRequirement($record, 'matter');
    $build['aspect'] = $this->getFieldRequirement($record, 'aspect');
    $build['no_requirement'] = $this->getFieldRequirement($record, 'no_requirement');
    $build['requirement'] = $this->getFieldRequirement($record, 'requirement');
    $build['link_legal'] = $legal['link_legal'];
    $build['legals_name'] = $legal['legals_name'];
    $build['risk'] = $this->defineRisk($record, $evaluateRisk);
    $build['init_date_format'] = $record->init_date_format ?? '--/--/--';
    $build['end_date_format'] = $record->end_date_format ?? '--/--/--';
    $build['periodicity'] = $this->getFieldRequirement($record, 'periodicity');
    $build['status'] = $record->status->status;
    $build['scope'] = $scope;
    $build['user'] = null ?? 'Sin Responsable';
    return $build;
  }
}