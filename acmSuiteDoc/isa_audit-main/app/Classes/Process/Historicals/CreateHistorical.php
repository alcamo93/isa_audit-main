<?php

namespace App\Classes\Process\Historicals;

use App\Models\V2\Audit\Audit;
use App\Models\V2\Audit\Obligation;
use App\Models\V2\Audit\ActionPlan;
use Carbon\Carbon;

class CreateHistorical
{
  public $record = null;
  public $type = null;
  public $today = null;

  /**
   * @param App\Models\V2\Audit\ObligationRegister | App\Models\V2\Audit\ActionPlanRegister | App\Models\V2\Audit\AuditRegister $record
   * @param string $type
   */
  public function __construct($record, $type)
  {
    $this->record = $record;
    $this->type = $type;
    $timezone = Config('enviroment.time_zone_carbon');
    $this->today = Carbon::now($timezone)->toDateString();
  }

  public function setHistorical() 
  {
    if ($this->type == 'audit') {
      $this->calculateAudit();
      return;
    }

    $records = collect([]);
    $relationships = ['requirement.matter', 'requirement.aspect'];

    if ( $this->type == 'action' ) {
      $records = ActionPlan::with($relationships)->where('id_action_register', $this->record->id_action_register)->get();
    }
    if ( $this->type == 'obligation' ) {
      $records = Obligation::with($relationships)->where('obligation_register_id', $this->record->id)->get();
    }
    
    $this->calculate($records);
  }

  private function calculate($records)
  {
    // calculate by aspects
    $aspects = $records->pluck('requirement.aspect')->unique();
    $aspectsTotals = $this->calculateByAspects($aspects, $records);
    // calculate by matters
    $matters = $records->pluck('requirement.matter')->unique();
    $mattersTotals = $this->calculateByMatters($matters, $aspectsTotals);
    // calculate global
    $globalTotals = $this->getGlobalPercent($mattersTotals);
    // to create global percent historical
    $createHistorical = $this->record->historicals()->create([
      'total' => $globalTotals['total'],
      'total_count' => $globalTotals['total_count'],
      'date' => $this->today,    
    ]);
    // to build structure by matter
    $mattersHistorical = $mattersTotals->map(function($matter) {
      return [
        'total' => $matter->total,
        'total_count' => $matter->total_count,
        'date' => $this->today,
        'matter_id' => $matter->id_matter,
      ];
    });
    // to create by matter
    foreach ($mattersHistorical as $matter) {
      $createMatter = $createHistorical->matters()->create($matter);
      // to build structure by aspect
      $currentsAspects = $aspectsTotals->where('id_matter', $matter['matter_id']);
      $aspectsToCreate = $currentsAspects->map(function($aspect) {
        return [
          'total' => $aspect->total,
          'total_count' => $aspect->total_count,
          'date' => $this->today,
          'aspect_id' => $aspect->id_aspect,
        ];
      });
      // create all aspects
      $createMatter->aspects()->createMany($aspectsToCreate);
    }
  }

  private function calculateByAspects($aspects, $records)
  {
    $statusActionPlan = [
      ActionPlan::COMPLETED_AP,
      ActionPlan::REVIEW_AP,
    ];

    $statusObligation = [
      Obligation::FOR_EXPIRED_OBLIGATION,
      Obligation::APPROVED_OBLIGATION,
    ];

    $status = $this->type == 'action' ? $statusActionPlan : $statusObligation;

    foreach ($aspects as $aspect) {
      $result = $this->getAspectPercent($records, $aspect->id_aspect, $status);
      $aspect->total = $result['total'];
      $aspect->total_count = $result['total_count'];
    }

    return $aspects;
  }

  private function getAspectPercent($records, $idAspect, $positivesStatus)
  {
    $positives = $records->where('requirement.id_aspect', $idAspect)->whereIn('id_status', $positivesStatus)->count();
    $totalRecords = $records->where('requirement.id_aspect', $idAspect)->count();

    $value = $positives != 0 ? ($positives / $totalRecords) * 100 : 0;
    $info['total'] = round($value, 2);
    $info['total_count'] = $positives;
    return $info;
  }

  private function calculateByMatters($matters, $aspects)
  {
    foreach ($matters as $matter) {
      $result = $this->getMatterPercent($aspects, $matter->id_matter);
      $matter->total = $result['total'];
      $matter->total_count = $result['total_count'];
    }

    return $matters;
  }

  private function getMatterPercent($aspects, $idMatter)
  {
    $positives = $aspects->where('id_matter', $idMatter)->sum('total');
    $totalCount = $aspects->where('id_matter', $idMatter)->sum('total_count');
    $totalRecords = $aspects->where('id_matter', $idMatter)->count();

    $value = $positives != 0 ? ($positives / $totalRecords) : 0;
    $info['total'] = round($value, 2);
    $info['total_count'] = $totalCount;
    return $info;
  }

  private function getGlobalPercent($matters)
  {
    $totalGlobal = $matters->sum('total');
    $totalCountGlobal = $matters->sum('total_count');
    $totalRecords = $matters->count();

    $value = $totalGlobal != 0 ? ($totalGlobal / $totalRecords) : 0;

    $info['total'] = round($value, 2);
    $info['total_count'] = $totalCountGlobal;
    return $info;
  }

  private function calculateAudit()
  {
    $this->record = $this->record->load('audit_matters.audit_aspects');
    $idAuditAspect = $this->record->audit_matters->flatMap(fn($item) => $item['audit_aspects'])->pluck('id_audit_aspect')->toArray();
    $poolAudits = Audit::with(['requirement'])->whereIn('id_audit_aspect', $idAuditAspect)->get();
    $createHistorical = $this->record->historicals()->create([
      'total' => $this->record->total,
      'total_count' => $poolAudits->count(),
      'date' => $this->today,
    ]);
    foreach ($this->record->audit_matters as $matter) {
      $countTotalMatter = $poolAudits->where('requirement.id_matter', $matter['id_matter'])->count();
      $createMatter = $createHistorical->matters()->create([
        'total' => $matter->total,
        'total_count' => $countTotalMatter,
        'date' => $this->today,
        'matter_id' => $matter->id_matter,
      ]);
      foreach ($matter->audit_aspects as $aspect) {
        $countTotalAspect = $poolAudits->where('requirement.id_aspect', $aspect['id_aspect'])->count();
        $createMatter->aspects()->create([
          'total' => $aspect->total,
          'total_count' => $countTotalAspect,
          'date' => $this->today,
          'aspect_id' => $aspect->id_aspect,
        ]);
      }
    }
  }
}