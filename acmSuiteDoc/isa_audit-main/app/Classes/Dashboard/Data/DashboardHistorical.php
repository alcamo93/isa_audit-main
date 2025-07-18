<?php

namespace App\Classes\Dashboard\Data;

use App\Models\V2\Audit\ActionPlanRegister;
use App\Models\V2\Audit\AplicabilityRegister;
use App\Models\V2\Audit\AuditRegister;
use App\Models\V2\Audit\ObligationRegister;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Catalogs\ApplicationType;
use App\Models\V2\Catalogs\Matter;
use App\Models\V2\Historical\Historical;
use Carbon\Carbon;

class DashboardHistorical
{
  protected $registerRecord = null;
  protected $historicalableId = null;
  protected $currentYear = null;
  protected $classPath = null;
  protected $idAplicabilityRegister = null;
  protected $project = null;

  public function __construct($registerRecord)
	{
    $this->registerRecord = $registerRecord;
    $timezone = Config('enviroment.time_zone_carbon');
    $this->currentYear = Carbon::now($timezone)->format('Y');
    $this->classPath = get_class($this->registerRecord);
    $this->historicalableId = $this->getIdHistoricable();
    $this->getAplicabilityRegister();
  }

  private function getAplicabilityRegister() 
  {
    $relationshipsProcess = [
      'aplicability_register',
      'corporate.image',
      'customer.images' => fn($query) => $query->where('usage', 'dashboard'), 
    ];
    if ( $this->classPath == ObligationRegister::class ) {
      $this->idAplicabilityRegister = $this->registerRecord->id_aplicability_register;
      $applicabilityRegister = AplicabilityRegister::find($this->idAplicabilityRegister);
      $this->project = ProcessAudit::with($relationshipsProcess)->find($applicabilityRegister->id_audit_processes);
    }

    if ( $this->classPath == AuditRegister::class ) {
      $this->idAplicabilityRegister = $this->registerRecord->id_aplicability_register;
      $this->project = ProcessAudit::with($relationshipsProcess)->find($this->registerRecord->id_audit_processes);
    }
    
    if ( $this->classPath == ActionPlanRegister::class ) {
      $this->project = ProcessAudit::with($relationshipsProcess)->find($this->registerRecord->id_audit_processes);
      $this->idAplicabilityRegister = $this->project->aplicability_register->id_aplicability_register;
    }
  }

  private function getIdHistoricable() 
  {
    if ( $this->classPath == ActionPlanRegister::class ) {
      return $this->registerRecord->id_action_register;
    }
    if ( $this->classPath == ObligationRegister::class ) {
      return $this->registerRecord->id;
    }
    if ( $this->classPath == AuditRegister::class ) {
      return $this->registerRecord->id_audit_register;
    }
  }

  private function getMatters()
  {
    $relationshipsRegister = ['contract_matters.contract_aspects'];
    $aplicabilityRegister = AplicabilityRegister::with($relationshipsRegister)->find($this->idAplicabilityRegister);
    
    $allowAspects = $aplicabilityRegister->contract_matters
      ->flatMap(fn($item) => $item->contract_aspects)
      ->filter(fn($item) => $item->id_application_type != ApplicationType::NOT_APPLICABLE);
    
    $matters = $allowAspects->pluck('id_matter')->unique()->values()->toArray();
    $aspects = $allowAspects->pluck('id_aspect')->unique()->values()->toArray();
    
    $filterAspects = fn($query) => $query->whereIn('id_aspect', $aspects);
    $relationships = ['aspects' => $filterAspects];
    $filterMatter = Matter::with($relationships)->whereIn('id_matter', $matters)->get();
    
    return $filterMatter;
  }

  public function getHistoricalPerMatters($year = null)
  {
    $year = $year ?? $this->currentYear;

    $monthsCollect = (new UtilitiesDashboard())->getMonths();
    $mattersCollect = $this->getMatters();
    $records = Historical::with(['matters.aspects'])->where('historicalable_id', $this->historicalableId)
      ->where('historicalable_type', $this->classPath)
      ->whereYear('date', $year)
      ->lastInMonth($year, $this->historicalableId, $this->classPath)
      ->get();

    $recordsMatters = $records->flatMap(fn($item) => $item->matters);
    $recordsAspects = $records->flatMap(fn($item) => $item->matters)->flatMap(fn($item) => $item->aspects);

    $matters = $mattersCollect->map(function($matter) use ($monthsCollect, $recordsMatters, $recordsAspects) {
      $matterTmp = $matter;

      $matter->aspects = $matter->aspects->map(function($aspect) use ($monthsCollect, $recordsAspects) {
        $aspectTmp = $aspect;

        $monthsPerAspect = $monthsCollect->map(function($month) use ($aspect, $recordsAspects) {
          $recordsPerAspect = $recordsAspects->where('aspect_id', $aspect->id_aspect);
          $historicalsPerMonth = $this->getRecordsPerMonth($recordsPerAspect, $month['id']);
          $month['total'] = $historicalsPerMonth->last()->total ?? 0;
          $month['total_count'] = $historicalsPerMonth->last()->total_count ?? 0;
          return $month;
        });

        $aspect->total = round($monthsPerAspect->avg('total'), 2);
        $aspect->total_count = $monthsPerAspect->sum('total_count');
        $aspectTmp->months = $monthsPerAspect->toArray();
        return $aspectTmp;
      });

      $monthsPerMatter = $monthsCollect->map(function($month) use ($matter, $recordsMatters) {
        $recordsPerMatter = $recordsMatters->where('matter_id', $matter->id_matter);
        $historicalsPerMonth = $this->getRecordsPerMonth($recordsPerMatter, $month['id']);
        $month['total'] = $historicalsPerMonth->last()->total ?? 0;
        $month['total_count'] = $historicalsPerMonth->last()->total_count ?? 0;
        return $month;
      });

      $matterTmp->total = round($monthsPerMatter->avg('total'), 2);
      $matterTmp->total_count = $monthsPerMatter->sum('total_count');
      $matterTmp->months = $monthsPerMatter->toArray();
      
      return $matterTmp;
    })->toArray();

    $allMonths = collect($matters)->flatMap(fn($matter) => $matter['months']);
    $totalMonths = $monthsCollect->map(function($month) use ($allMonths) {
      $valuesPerMonth = $allMonths->where('id', $month['id'])->values();
      $month['total'] = round( $valuesPerMonth->avg('total'), 2 );
      $month['total_count'] = $valuesPerMonth->sum('total_count');
      return $month;
    });
    
    $totalAvg = round($totalMonths->avg('total'), 2);
    $totalCount = $totalMonths->sum('total_count');

    $totals['months'] = $totalMonths->toArray();
    $totals['total'] = $totalAvg;
    $totals['total_count'] = $totalCount;

    $info['totals'] = $totals;
    $info['matters'] = $matters;
    
    return $info;
  }

  public function getDataHistoricalMonthly($year = null)
  {
    $year = $year ?? $this->currentYear;

    $monthsCollect = (new UtilitiesDashboard())->getMonths();
    $records = Historical::with(['matters.aspects'])->where('historicalable_id', $this->historicalableId)
      ->where('historicalable_type', $this->classPath)
      ->whereYear('date', $year)
      ->lastInMonth($year, $this->historicalableId, $this->classPath)
      ->get();

    $dataMonthly = $monthsCollect->map(function ($month) use ($records) {
      $historicalsPerMonth = $this->getRecordsPerMonth($records, $month['id']);
      $month['historical_total'] = $this->calculateHistoricalTotal($historicalsPerMonth);

      $mattersPerMonth = collect($month['historical_total']);
      $month['total'] = $this->calculateAverage($mattersPerMonth, 'total');

      return $month;
    });

    return $dataMonthly->toArray();
  }

  private function getRecordsPerMonth($records, $monthId)
  {
    return $records->filter(function ($item) use ($monthId) {
      return date('n', strtotime($item->date)) == $monthId;
    })->values();
  }

  private function calculateHistoricalTotal($historicalsPerMonth)
  {
    $groupsPerMatter = $historicalsPerMonth->flatMap(fn($item) => $item->matters)->groupBy('matter_id');

    return $groupsPerMatter->map(function ($group) {
      $matter = $group->first();
      $groupPerAspect = $group->flatMap(fn($item) => $item->aspects)->groupBy('aspect_id');

      $matter->aspects = $groupPerAspect->map(function ($groupAspect) {
        return $this->calculateAspectTotal($groupAspect);
      });

      $matter->total = $this->calculateAverage($matter->aspects, 'total');
      return $matter->toArray();
    })->values()->toArray();
  }

  private function calculateAspectTotal($groupAspect)
  {
    $total = round($this->calculateAverage($groupAspect, 'total'), 2);
    $aspect = $groupAspect->first();
    $aspect->total = $total;
    return $aspect->toArray();
  }

  private function calculateAverage($collection, $key)
  {
    $sum = $collection->sum($key);
    $count = $collection->count();
    return round(($count ? $sum / $count : 0), 2);
  }

  public function getHistoricalLastYears()
  {
    $utilitiesDashboard = new UtilitiesDashboard();
    $lastYears = $utilitiesDashboard->getLastYears();

    $info = $utilitiesDashboard->getCustomer($this->project);

    $info['years'] = $lastYears->map(function($itemYear) {
      $months = $this->getDataHistoricalMonthly($itemYear['year']);
      $item = $itemYear;
      $item['total'] = round( collect($months)->avg('total'), 2 );
      $item['total_count'] = collect($months)->sum('total_count');
      $item['months'] = $months;
      return $item;
    })->toArray();

    return $info;
  }
}
