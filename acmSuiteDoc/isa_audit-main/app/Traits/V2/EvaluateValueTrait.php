<?php 

namespace App\Traits\V2;

trait EvaluateValueTrait 
{
  /**
   * @param array|collect $record
   * @param bool $isEvaluate
   */
  private function defineFinding($record, $isEvaluate = false) 
  {
    // no has answer
    $record = $isEvaluate ? $record['audit'] : $record;
    if ( is_null($record) ) return 'N/A';
    // is parent
    $isParent = boolval($record['requirement']['has_subrequirement'] == 1);
    $isChild = !is_null($record['id_subrequirement']);
    if ($isParent && !$isChild) return 'Revise los comentarios en los subrequerimientos';
    // verify type answer
    $hasNotFinding = is_null($record['finding']);
    $default = 'N/A';
    $keyAnswer = $record['key_answer']['key'];

    if ( $keyAnswer == 'NEGATIVE' && $hasNotFinding && !$isParent) {
      $finding = 'Aún sin especificar';
    } elseif ( $keyAnswer == 'NEGATIVE' && $hasNotFinding && !$isParent ) {
      $finding = $record['finding'];
    } else {
      $finding = $hasNotFinding ? $default : $record['finding'];
    }
    
    return $finding;
  }

  /**
   * @param array|collect $record
   * @param bool $isEvaluate
   */
  private function defineRisk($record, $isEvaluate = false) 
  {
    // no has answer
    $record = $isEvaluate ? $record['audit'] : $record;
    if ( is_null($record)) return ['N/A'];
    // is parent
    $isParent = boolval($record['requirement']['has_subrequirement'] == 1);
    $isChild = !is_null($record['id_subrequirement']);
    if ($isParent && !$isChild) return ['Revise los comentarios en los subrequerimientos'];
    // verify type answer
    $hasNotRisk = sizeof($record['risk_totals']) == 0;
    $default = ['N/A'];
    $keyAnswer = $record['key_answer']['key'];

    if ( $keyAnswer == 'NEGATIVE' && $hasNotRisk && !$isParent ) {
      $risk = ['Aún sin especificar'];
    } elseif ( $keyAnswer == 'NEGATIVE' && !$hasNotRisk && !$isParent ) {
      $risk = $record['risk_totals']->map(fn($record) => "{$record['category']['risk_category']}: {$record['interpretation']}")->toArray();
    } else {
      $risk = $default;
    }

    return $risk;
  }

    /**
   * @param array|collect $record
   * @param bool $isEvaluate
   */
  private function defineAnswer($record, $isEvaluate = false) 
  {
    // no has answer
    $record = $isEvaluate ? $record['audit'] : $record;
    if ( is_null($record) ) return [
      'label' => 'Pendiente',
      'key' => 'PENDING'
    ];

    $answer = $record['key_answer'];

    return $answer;
  }
}