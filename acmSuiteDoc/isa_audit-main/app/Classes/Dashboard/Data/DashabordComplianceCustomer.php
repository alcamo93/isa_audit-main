<?php

namespace App\Classes\Dashboard\Data;

use App\Classes\Dashboard\Data\DashabordCompliance;
use App\Classes\Dashboard\Data\UtilitiesDashboard;
use App\Models\V2\Catalogs\Matter;
use App\Models\V2\Audit\ProcessAudit;
Use Carbon\Carbon;

class DashabordComplianceCustomer
{
  protected $idCustomer = null;
  protected $instances = null;
  protected $currentYear = null;

  protected $status = null;
  protected $matters = null;
  protected $records = null;


  public function __construct($idCustomer)
	{
    $timezone = Config('enviroment.time_zone_carbon');
    $this->currentYear = Carbon::now($timezone)->format('Y');
    $this->idCustomer = $idCustomer;
    $this->instances = $this->getInstances();
    $this->matters = $this->filterEvaluatedMatters();
  }
  
  public function getInstances()
  {
    $completeProjects = ProcessAudit::getProcessKpiWithSpecificSectionToEvaluate('compliance', $this->currentYear, $this->idCustomer)->get();
    
    $corporates = $completeProjects->pluck('id_corporate')->unique();
    
    $projects = $corporates->map(function($idCorporate) use ($completeProjects) {
      $collection = $completeProjects->where('id_corporate', $idCorporate)->pluck('aplicability_register.audit_register')->collapse();
      $auditRegister = $collection->where( 'id_audit_register', $collection->max('id_audit_register') )->first();
      $current = $completeProjects->firstWhere('id_audit_processes', $auditRegister->id_audit_processes ); 
      $current->current_audit_register = $auditRegister;
      return $current;
    })->values();
    
    $instances = collect([]);
    
    foreach ($projects as $project) {
      $instance = new DashabordCompliance($project->current_audit_register->id_audit_register);
      $instances->push($instance);
    }
    
    return $instances->sortBy('id_audit_register')->values();
  }
  
  public function getCustomer()
  {
    $item = $this->instances->first();
    if ( is_null($item) ) return $item; 
    return $item->getCustomer();
  }

  public function getProjects()
  {
    return $this->instances->map(function($instance) {
      return $instance->getProject();
    });
  }

  private function getMatters()
  {
    $projects = $this->instances->map(function($instance) {
      return $instance->getProject();
    });
    
    $matters = $projects->flatMap(fn($item) => $item['matters'])->pluck('id_matter')->unique()->values();
    $aspects = $projects->flatMap(fn($item) => $item['matters'])->flatMap(fn($item) => $item['aspects'])->pluck('id_aspect')->unique()->values();
    
    $filterAspects = fn($query) => $query->whereIn('id_aspect', $aspects);
    $relationships = ['aspects' => $filterAspects];
    $filterMatter = Matter::with($relationships)->whereIn('id_matter', $matters)->get();
    
    $info['matters'] = $filterMatter;
    $info['projects'] = $projects;

    return $info;
  }

  public function filterEvaluatedMatters()
  {
    $infoMaters = $this->getMatters();
    $filterMatter = $infoMaters['matters'];
    $projects = $infoMaters['projects'];

    $allMatters = $projects->flatMap(fn($item) => $item['matters'])->values();

    $matters = $filterMatter->map(function($matter) use ($allMatters) {
      $foundMatters = $allMatters->where('id_matter', $matter->id_matter) ?? collect([]);
      $matter->aspects->map(function($aspect) use ($foundMatters) {
        $foundAspects = $foundMatters->flatMap(fn($item) => $item['aspects'])->where('id_aspect', $aspect->id_aspect)->values() ?? collect([]);
        $aspect->total = round($foundAspects->avg('total'), 2);
        $aspect->total_count = $foundAspects->sum('total_count');
        $aspect->total_expired = $foundAspects->sum('total_expired');
        $aspect->total_critical = $foundAspects->sum('total_critical');
        $aspect->total_operative = $foundAspects->sum('total_operative');
        return $aspect;
      });
      $matter->total = round($foundMatters->avg('total'), 2);
      $matter->total_count = $foundMatters->sum('total_count');
      $matter->total_expired = $foundMatters->sum('total_expired');
      $matter->total_critical = $foundMatters->sum('total_critical');
      $matter->total_operative = $foundMatters->sum('total_operative');
      return $matter;
    })->toArray();

    return $matters;
  }

  public function getCompliance($labelAction = false)
  {
    $matterCollect = is_array($this->matters) ? collect($this->matters) : $this->matters;
    $total = $matterCollect->sum('total');
    $totalRecords = $matterCollect->count();

    $max = 100;
    $value = $total != 0 ? ($total / $totalRecords) : 0;
    $compliance = round($value, 2);
    $noCompliance = round($max - $compliance, 2);

    $labelPositive = $labelAction ? 'Completo' : 'Cumplimiento';
    $labelNegative = $labelAction ? 'Pendiente' : 'Incumplimiento';

    $data = [
      [ 'label' => $labelPositive, 'total' => $compliance, 'color' => '#009299' ],
      [ 'label' => $labelNegative, 'total' => $noCompliance, 'color' => '#003F72']
    ];
    
    return $data;
  }

  public function getHistoricalMonthly()
  {
    $monthlyPerProject = $this->instances->map(function($instance) {
      return $instance->getDataHistoricalMonthly();
    });
    
    $monthsCollect = (new UtilitiesDashboard())->getMonths();

    $monthly = $monthsCollect->map(function($month) use ($monthlyPerProject) {
      $valuesPerMonth = $monthlyPerProject->collapse()->where('id', $month['id']);
      $month['total'] = round($valuesPerMonth->avg('total'), 2);
      $month['total_count'] = $valuesPerMonth->sum('total_count');
      return $month;
    });
    
    return $monthly;
  }

  public function getHistoricalLastYears()
  {
    $lastYearsPerProject = $this->instances->map(function($instance) {
      return $instance->getHistoricalLastYears();
    });

    $yearsCollect = (new UtilitiesDashboard())->getLastYears();

    $years = $yearsCollect->map(function($year) use ($lastYearsPerProject) {
      $valuesPerYear = $lastYearsPerProject->collapse()->where('year', $year['year']);
      $year['total'] = round($valuesPerYear->avg('total'), 2);
      $year['total_count'] = $valuesPerYear->sum('total_count');
      return $year;
    });

    return $years;
  }

  public function getHistoricalLastYearsPerProjects()
  {
    $lastYearsPerProject = $this->instances->map(function($instance) {
      return $instance->getHistoricalLastYears();
    });

    return $lastYearsPerProject;
  }

  public function getHistoricalMatterPerProjects()
  {
    $historicalMatterPerProject = $this->instances->map(function($instance) {
      return $instance->getHistoricalPerMatters();
    });

    return $historicalMatterPerProject;
  }
}