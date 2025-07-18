<?php

namespace App\Classes\Audit;

use App\Classes\Utilities\DetailPath;
use App\Traits\V2\RequirementTrait;

class UtilitiesAudit
{
  use RequirementTrait;

  public function __construct()
  {
    // 
  }

  public function defineFinding($item, $isEvaluate = false) 
  {
    // no has answer
    $record = $isEvaluate ? $item->audit : $item;
    if ( is_null($record)) return 'N/A';
    // is parent
    $isParent = boolval($record->requirement->has_subrequirement == 1);
    $isChild = !is_null($record->id_subrequirement);
    if ($isParent && !$isChild) return 'Revise los comentarios en los subrequerimientos';
    // verify type answer
    $hasNotFinding = is_null($record->finding);
    $default = 'N/A';
    $keyAnswer = $record->key_answer['key'];

    if ( $keyAnswer == 'NEGATIVE' && $hasNotFinding && !$isParent) {
      $finding = 'Aún sin especificar';
    } elseif ( $keyAnswer == 'NEGATIVE' && $hasNotFinding && !$isParent ) {
      $finding = $record->finding;
    } else {
      $finding = $hasNotFinding ? $default : $record->finding;
    }
    
    return $finding;
  }

  public function defineRisk($item, $isEvaluate = false) 
  {
    // no has answer
    $record = $isEvaluate ? $item->audit : $item;
    if ( is_null($record) ) return ['N/A'];
    // is parent
    $isParent = boolval($record->requirement->has_subrequirement == 1);
    $isChild = !is_null($record->id_subrequirement);
    if ($isParent && !$isChild) return ['Revise los comentarios en los subrequerimientos'];
    // verify type answer
    $hasNotRisk = sizeof($record->risk_totals) == 0;
    $default = ['N/A'];
    $keyAnswer = $record->key_answer['key'];

    if ( $keyAnswer == 'NEGATIVE' && $hasNotRisk && !$isParent ) {
      $risk = ['Aún sin especificar'];
    } elseif ( $keyAnswer == 'NEGATIVE' && !$hasNotRisk && !$isParent ) {
      $risk = $record->risk_totals->map(fn($item) => "{$item['category']['risk_category']}: {$item['interpretation']}")->toArray();
    } elseif ( $keyAnswer == 'NEGATIVE' && !$hasNotRisk && $isChild ) {
      $risk = $record->risk_totals->map(fn($item) => "{$item['category']['risk_category']}: {$item['interpretation']}")->toArray();
    } else {
      $risk = $default;
    }

    return $risk;
  }

  /**
   * @param array|collect $record (some record what use the relations requirement and subrequirement)
   */
  private function defineImages($record) 
  {
    $params['id'] = $record['id_audit'];
    $params['type'] = 'audit';
    
    $hasImages = sizeof($record['images']) > 0;

    if ( !$hasImages ) {
      $data['link_images'] = Config('enviroment.domain_frontend');
      $data['name_images'] = 'No cuenta con imagenes cargadas';
      return $data;
    }

    $data['link_images'] = (new DetailPath('images', $params))->getPath();
    $data['name_images'] = "Ver imagenes de Hallazgos";

    return $data;
  }

  public function defineRecord($record) 
  {
    $isSubrequirement = !is_null($record->id_subrequirement);
    $item = $isSubrequirement ? $record->subrequirement : $record->requirement;
    $build['id_requirement'] = $item->id_requirement;
    $build['matter'] = $this->getFieldRequirement($record, 'matter');
    $build['aspect'] = $this->getFieldRequirement($record, 'aspect');
    $build['no_requirement'] = $this->getFieldRequirement($record, 'no_requirement');
    $build['requirement'] = $this->getFieldRequirement($record, 'requirement');
    $build['description'] = $this->getFieldRequirement($record, 'description');
    $build['evidence'] = $this->getFieldRequirement($record, 'evidence_document');
    $legal = $this->getFieldRequirement($record, 'articles');
    $build['link_legal'] = $legal['link_legal'];
    $build['legals_name'] = $legal['legals_name'];
    $build['finding'] = $this->defineFinding($record);
    $build['answer'] = $record->key_answer['label'];
    $build['key_answer'] = $record->key_answer['key'];
    $build['risk'] = $this->defineRisk($record);
    $build['childs'] = [];
    $build['child_findings'] = [];
    $build['order'] = $item->order;
    $images = $this->defineImages($record);
    $build['link_images'] = $images['link_images'];
    $build['name_images'] = $images['name_images'];
    return $build;
  }
}