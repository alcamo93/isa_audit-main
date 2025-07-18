<?php

namespace App\Traits\V2;

use App\Classes\Utilities\DetailPath;
use App\Models\V2\Catalogs\Evidence;

trait RequirementTrait
{
  /**
   * @param array|collect $record (some record what use the relations requirement and subrequirement)
   * @param string $field (get specific value if is requirement or subrequirement)
   */
  private function getFieldRequirement($record, $field, $default = '---') 
  {
    $record = gettype($record) == 'array' ? $record : $record->toArray();
    $requirement = $record['requirement'];
    $subrequirement = $record['subrequirement'];

    if ($field == 'full_requirement') {
      $number = $subrequirement['no_subrequirement'] ?? $requirement['no_requirement'] ?? $default;
      $text = $subrequirement['subrequirement'] ?? $requirement['requirement'] ?? $default;
      return "{$number} - {$text}";
    }

    if ($field == 'requirement') {
      return $subrequirement['subrequirement'] ?? $requirement['requirement'] ?? $default;
    }

    if ($field == 'no_requirement') {
      return $subrequirement['no_subrequirement'] ?? $requirement['no_requirement'] ?? $default;
    }

    if ( $field == 'evidence' ) {
      return $subrequirement['evidence']['evidence'] ?? $requirement['evidence']['evidence'] ?? $default;
    }

    if ( $field == 'evidence_document' ) {
      return $this->defineEvidence($record);
    }
    
    if ( $field == 'condition' ) {
      return $subrequirement['condition']['condition'] ?? $requirement['condition']['condition'] ?? $default;
    }

    if ( $field == 'matter' ) {
      return $subrequirement['matter']['matter'] ?? $requirement['matter']['matter'] ?? $default;
    }

    if ( $field == 'aspect' ) {
      return $subrequirement['aspect']['aspect'] ?? $requirement['aspect']['aspect'] ?? $default;
    }

    if ($field == 'help') {
      return $subrequirement['help_subrequirement'] ?? $requirement['help_requirement'] ?? $default;
    }

    if ($field == 'document') {
      return $subrequirement['document'] ?? $requirement['document'] ?? $default;
    }

    if ($field == 'description') {
      return $subrequirement['description'] ?? $requirement['description'] ?? $default;
    }

    if ($field == 'application_type') {
      return $subrequirement['application_type']['application_type'] ?? $requirement['application_type']['application_type'] ?? $default;
    }

    if ($field == 'periodicity') {
      return $subrequirement['periodicity']['name'] ?? $requirement['periodicity']['name'] ?? $default;
    }

    if ($field == 'articles') {
      return $this->defineLegal($record);
    }

    return '----';
  }

  /**
   * @param array|collect $record (some record what use the relations requirement and subrequirement)
   */
  private function defineLegal($record) 
  {
    $isSubrequirement = !is_null($record['id_subrequirement']);
    $item = $isSubrequirement ? $record['subrequirement'] : $record['requirement'];

    $params['id'] = $isSubrequirement ? $record['id_subrequirement'] : $record['id_requirement'];
    $params['type'] = $isSubrequirement ? 'sub' : 'req';
    
    $legal['link_legal'] = (new DetailPath('articles', $params))->getPath();
    $legal['legals_name'] = collect($item['articles'])->map(function($article) {
      return "Basado de {$article['guideline']['guideline']}: {$article['legal_basis']}";
    })->join('<br>');

    return $legal;
  }
  
  /**
   * @param array|collect $record (some record what use the relations requirement and subrequirement)
   */
  private function defineEvidence($record)
  {
    $isSubrequirement = !is_null($record['id_subrequirement']);
    $record = $isSubrequirement ? $record['subrequirement'] : $record['requirement'];

    if ( is_null($record['id_evidence']) ) return 'N/A';
    if ($record['id_evidence'] == Evidence::SPECIFIC) {
      return  "{$record['evidence']['evidence']}: {$record['document']}" ;
    }
    return $record['evidence']['evidence'];
  }

}