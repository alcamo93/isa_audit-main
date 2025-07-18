<?php

namespace App\Traits\V2;

use Illuminate\Database\Eloquent\Builder;
use App\Models\V2\Audit\ActionPlan;
use App\Models\V2\Audit\AuditRegister;
use App\Models\V2\Audit\ObligationRegister;
use App\Models\V2\Historical\Historical;
use App\Models\V2\Catalogs\RiskCategory;
use App\Models\V2\Admin\Address;
use Carbon\Carbon; 

trait DashboardTrait
{
  /**
   * @param App\Models\V2\Audit\ProcessAudit $project
   */
  private function getCustomer($project)
  {
    $corporate = $project->corporate ?? null;
    $customer = $project->customer ?? null;
    $customerImage = isset($customer->images) && $customer->images->isNotEmpty() ? $customer->images->firstWhere('usage', 'dashboard')->full_path : null;
    $corporateImage = isset($corporate->image) && !is_null($corporate->image) ? $corporate->image->full_path : null;
    $data['id_audit_processes'] = $project->id_audit_processes ?? '';
    $data['id_aplicability_register'] = $project->aplicability_register->id_aplicability_register ?? '';
    $data['cust_tradename'] = $customer->cust_tradename_format ?? '';
    $data['corp_tradename'] = $corporate->corp_tradename_format ?? '';
    $data['cust_full_path'] = $customerImage;
    $data['corp_full_path'] = $corporateImage;
    $data['address'] = is_null($corporate) ? '' : $this->getAddress($corporate);
    
    return $data;
  }

  /**
   * @param App\Models\V2\Admin\Corporate $corporate
   */
  private function getAddress($corporate)
  {
    if ( is_null($corporate) ) return null; 
    $data = $corporate->addresses->firstWhere('type', Address::PHYSICAL);
    $address = "{$data->full_address}, {$data->city->city}, {$data->state->state} {$data->country->country}";
    return $address;
  }

  /**
   * @param  string $type (obligation, audit, compliance)
   * @param  App\Models\V2\Audit\ProcessAudit $process
   * @param  null|int $specificIdAuditRegister
   */
  private function getCountRisk($type, $process, $specificIdAuditRegister = null)
  {
    if ($type == 'audit' || $type == 'compliance') {
      $auditRegisters = $process->aplicability_register->audit_register;
      $auditRegister = $auditRegisters->where('id_audit_register', $specificIdAuditRegister)->first();
      $idActionRegister = $auditRegister->action_plan_register->id_action_register ?? null;  
    }

    if ($type == 'obligation') {
      $idActionRegister = $process->aplicability_register->obligation_register->action_plan_register->id_action_register ?? null;
    }
    
    $evaluateRisk = boolval($process->evaluate_risk);
    $records = ActionPlan::getRisk()->where('id_action_register', $idActionRegister)->get();

    if ( $evaluateRisk && sizeof($records) == 0 ) {
      $records = collect([]);
    }
    
    $categories = RiskCategory::with('interpretations')->get();
    
    $categoriesRisk = $categories->map(function($category) use ($records) {
      $category->interpretations->map(function($interpretation) use ($records, $category) {
        $totals = $records->pluck('risk_totals')->collapse();
        $interpretation->count = $totals->where('id_risk_category', $category->id_risk_category)
          ->where('interpretation', $interpretation->interpretation)->count();
        return $interpretation;
      });
      return $category;
    });

    $data['evaluate_risk'] = $evaluateRisk;
    $data['categories'] = $categoriesRisk->toArray();
    
    return $data;
  }

  private function getLastYears($currentYear)
  {
    $colorsYears = ['#113C53', '#2581cc', '#4eaf8f'];
    $firstYear = $currentYear - 2;
    $range = range($firstYear, $currentYear);
    
    $years = [];
    foreach ($range as $key => $year) {
      $tmp['color'] = $colorsYears[$key];
      $tmp['year'] = $year;
      array_push($years, $tmp);
    }

    $data['years'] = $years;
    $data['range']['old'] = $firstYear;
    $data['range']['current'] = $currentYear;
    return $data;
  }

  private function getCalculateByProject($type, $project)
  {
    $dataYears = $this->getLastYears($this->currentYear);
    
    if ($type == 'audit' || $type == 'compliance') {
      $historicals = $project->current_audit_register->historicals; 
    }
    if ($type == 'obligation') {
      $historicals = $project->aplicability_register->obligation_register->historicals; 
    }
    $groupedHistoricals = $historicals->groupBy(function ($historical) {
      return Carbon::parse($historical->date)->format('Y');
    });

    // last 3 years by projects
    $structureYears = [];
    foreach ($dataYears['years'] as $itemYear) {
      $records = $groupedHistoricals[$itemYear['year']] ?? [];
      $months = $this->getHistoricalByMonth(collect($records));
      // average by month
      $sum = collect($months)->sum('total');
      $value = $sum != 0 ? ($sum / 12) : 0;
      $total = round($value, 2);
      // sum total count by month
      $totalCount = collect($months)->sum('total_count');
      // structure
      $tmpYear['year'] = $itemYear['year'];
      $tmpYear['color'] = $itemYear['color'];
      $tmpYear['total'] = $total;
      $tmpYear['total_count'] = $totalCount;
      $tmpYear['months'] = $months;
      array_push($structureYears, $tmpYear);
    }
    $data['customer'] = $project->customer->cust_tradename_format;
    $data['corporate'] = $project->corporate->corp_tradename_format;
    $data['audit_processes'] = $project->process_name;
    $data['date'] = "{$project->date} - {$project->end_date}";
    $data['years'] = $structureYears;

    return $data;
  }

  private function getHistoricalByMonth($historicalsRecords)
  {
    $monthsArray = [
      ['id' => 1, 'name' => 'Enero', 'key' => 'ENE', 'total' => 0, 'total_count' => 0],
      ['id' => 2, 'name' => 'Febrero', 'key' => 'FEB', 'total' => 0, 'total_count' => 0],
      ['id' => 3, 'name' => 'Marzo', 'key' => 'MAR', 'total' => 0, 'total_count' => 0],
      ['id' => 4, 'name' => 'Abril', 'key' => 'ABR', 'total' => 0, 'total_count' => 0],
      ['id' => 5, 'name' => 'Mayo', 'key' => 'MAY', 'total' => 0, 'total_count' => 0],
      ['id' => 6, 'name' => 'Junio', 'key' => 'JUN', 'total' => 0, 'total_count' => 0],
      ['id' => 7, 'name' => 'Julio', 'key' => 'JUL', 'total' => 0, 'total_count' => 0],
      ['id' => 8, 'name' => 'Agosto', 'key' => 'AGO', 'total' => 0, 'total_count' => 0],
      ['id' => 9, 'name' => 'Septiembre', 'key' => 'SEP', 'total' => 0, 'total_count' => 0],
      ['id' => 10, 'name' => 'Octubre', 'key' => 'OCT', 'total' => 0, 'total_count' => 0],
      ['id' => 11, 'name' => 'Noviembre', 'key' => 'NOV', 'total' => 0, 'total_count' => 0],
      ['id' => 12, 'name' => 'Diciembre', 'key' => 'DIC', 'total' => 0, 'total_count' => 0],
    ];

    if ( sizeof($historicalsRecords) == 0 ) return $monthsArray;
    
    foreach ($monthsArray as $key => $month) {
      $allInMonth = $historicalsRecords->filter(function($item) use ($month) {
        $date = strtotime($item['date']);
        return date('n', $date) == $month['id'];
      });

      $lastHistorical = $allInMonth->sortBy('id')->last();

      $monthsArray[$key]['total'] = is_null($lastHistorical) ? 0 : $lastHistorical->total;
      $monthsArray[$key]['total_count'] = is_null($lastHistorical) ? 0 : $lastHistorical->total_count;
    }

    return $monthsArray;
  }

  private function getFindingsTotal($matters)
  {
    $max = 100;
    $collectionMatter = collect($matters);
    
    $criticalTotal = $collectionMatter->sum('count_critical');
    $operativeTotal = $collectionMatter->sum('count_operative');
    $allTotal = $criticalTotal + $operativeTotal;

    $percentageCritical = $criticalTotal != 0 ? ($criticalTotal / $allTotal) * $max : 0;
    $percentageOperative = $operativeTotal != 0 ? ($operativeTotal / $allTotal) * $max : 0;
    $aspects = $collectionMatter->pluck('aspects')->collapse()->toArray();
    
    $data['count_total'] = $allTotal;
    $data['percentage'] = [
      [ 'label' => 'Critica', 'total' => round($percentageCritical, 2), 'color' => '#003F72' ],
      [ 'label' => 'Operativa', 'total' => round($percentageOperative, 2), 'color' => '#009299' ]
    ];
    $data['aspects'] = $aspects;
    
    return $data;
  }

  /**
   * @param  string $type (obligation, audit, compliance)
   * @param  App\Models\V2\Audit\ProcessAudit $process
   * @param  null|int $specificIdAuditRegister
   */
  private function getCalculateActionPlanMonthly($type, $process, $specificIdAuditRegister = null)
  {
    $monthsCollect = collect([
      ['id' => 1, 'name' => 'Enero', 'key' => 'ENE', 'total' => 0, 'total_count' => 0],
      ['id' => 2, 'name' => 'Febrero', 'key' => 'FEB', 'total' => 0, 'total_count' => 0],
      ['id' => 3, 'name' => 'Marzo', 'key' => 'MAR', 'total' => 0, 'total_count' => 0],
      ['id' => 4, 'name' => 'Abril', 'key' => 'ABR', 'total' => 0, 'total_count' => 0],
      ['id' => 5, 'name' => 'Mayo', 'key' => 'MAY', 'total' => 0, 'total_count' => 0],
      ['id' => 6, 'name' => 'Junio', 'key' => 'JUN', 'total' => 0, 'total_count' => 0],
      ['id' => 7, 'name' => 'Julio', 'key' => 'JUL', 'total' => 0, 'total_count' => 0],
      ['id' => 8, 'name' => 'Agosto', 'key' => 'AGO', 'total' => 0, 'total_count' => 0],
      ['id' => 9, 'name' => 'Septiembre', 'key' => 'SEP', 'total' => 0, 'total_count' => 0],
      ['id' => 10, 'name' => 'Octubre', 'key' => 'OCT', 'total' => 0, 'total_count' => 0],
      ['id' => 11, 'name' => 'Noviembre', 'key' => 'NOV', 'total' => 0, 'total_count' => 0],
      ['id' => 12, 'name' => 'Diciembre', 'key' => 'DIC', 'total' => 0, 'total_count' => 0],
    ]);

    if ($type == 'audit' || $type == 'compliance') {
      $auditRegisters = $process->aplicability_register->audit_register;
      $auditRegister = $auditRegisters->where('id_audit_register', $specificIdAuditRegister)->first();
      $records = $auditRegister->action_plan_register->historicals ?? collect([]);
    }

    if ($type == 'obligation') {
      $obligationRegister = $process->aplicability_register->obligation_register;
      $records = $obligationRegister->action_plan_register->historicals ?? collect([]);
    }

    $dataMonthly = $monthsCollect->map(function($month) use ($records) {
      // get records per month
      $historicalsPerMont = $records->filter(function($item) use ($month) {
        $date = strtotime($item->date);
        return date('n', $date) == $month['id'];
      })->values();
      // get average per month
      $groupsPerMatter = $historicalsPerMont->pluck('matters')->collapse()->groupBy('matter_id');
      $month['historical_total'] = $groupsPerMatter->map(function($group, $matterId) use ($groupsPerMatter) {
        $matter = $groupsPerMatter[$matterId]->first();
        $groupPerAspect = $group->pluck('aspects')->collapse()->groupBy('aspect_id');
        $matter->aspects = $groupPerAspect->map(function($groupAspect) {
          $sum = $groupAspect->sum('total');
          $count = $groupAspect->count();
          $value = $sum != 0 ? ($sum / $count) : 0;
          $total = round($value, 2);
          $aspect = $groupAspect->first();
          $aspect->total = $total;
          return $aspect->toArray();
        });
        $sum = $matter->aspects->sum('total');
        $count = $matter->aspects->count();
        $value = $sum != 0 ? ($sum / $count) : 0;
        $total = round($value, 2);
        $matter->total = $total;
        return $matter->toArray();
      })->values()->toArray();
      $mattersPerMonth = collect($month['historical_total']);
      $sum = $mattersPerMonth->sum('total');
      $count = $mattersPerMonth->count();
      $value = $sum != 0 ? ($sum / $count) : 0;
      $total = round($value, 2);
      $month['total'] = $total;
      return $month;
    });
    
    return $dataMonthly->toArray();
  }

  private function getMatterPercent($aspects, $idMatter)
  {
    $positives = $aspects->where('id_matter', $idMatter)->sum('total');
    $totalCounts = $aspects->where('id_matter', $idMatter)->sum('total_count');
    $totalExpired = $aspects->where('id_matter', $idMatter)->sum('total_expired');
    $totalRecords = $aspects->where('id_matter', $idMatter)->count();

    $value = $positives != 0 ? ($positives / $totalRecords) : 0;
    $total = round($value, 2);
    
    $info['total'] = $total;
    $info['total_count'] = $totalCounts;
    $info['total_expired'] = $totalExpired;
    return $info;
  }

  private function getMatterPercentGlobal($projects, $idMatter)
  {
    $matters = collect($projects)->pluck('matters')->collapse();
    
    $positives = $matters->where('id_matter', $idMatter)->sum('total');
    $totalCounts = $matters->where('id_matter', $idMatter)->sum('total_count');
    $totalExpired = $matters->where('id_matter', $idMatter)->sum('total_expired');
    $totalRecords = $matters->where('id_matter', $idMatter)->count();

    $value = $positives != 0 ? ($positives / $totalRecords) : 0;
    $total = round($value, 2);
    
    $info['total'] = $total;
    $info['total_count'] = $totalCounts;
    $info['total_expired'] = $totalExpired;
    return $info;
  }

  private function getGlobalCompliance($matters, $all = true, $labelAction = false)
  {
    $matterCollect = is_array($matters) ? collect($matters) : $matters;
    $totalGlobal = $matterCollect->sum('total');
    $totalCount = $matterCollect->sum('total_count');
    $totalExpired = $matterCollect->sum('total_expired');
    $totalRecords = $matterCollect->count();

    $max = 100;
    $value = $totalGlobal != 0 ? ($totalGlobal / $totalRecords) : 0;
    $total = round($value, 2);
    $sub = round($max - $total, 2);
  
    $data['total'] = $total;
    $data['total_count'] = $totalCount;
    $data['total_expired'] = $totalExpired;

    if ($all) {
      $labelPositive = $labelAction ? 'Completo' : 'Cumplimiento';
      $labelNegative = $labelAction ? 'Pendiente' : 'Incumplimiento';
      $data = [
        [ 'label' => $labelPositive, 'total' => $total, 'color' => '#009299' ],
        [ 'label' => $labelNegative, 'total' => $sub, 'color' => '#003F72']
      ];
    }
    
    return $data;
  }

  private function getHistoricalByProject($type, $year, $project)
  {
    $projectInfo = $this->getCustomer($project);

    $allMatters = $this->filterEvaluatedMatters($project);
    
    if ($type == 'audit') {
      $idSection = $project->current_audit_register->id_audit_register;
      $historicalsRecords = Historical::where('historicalable_id', $idSection)->where('historicalable_type', AuditRegister::class)->whereYear('date', $year)->get();
    }

    if ($type == 'obligation') {
      $idSection = $project->aplicability_register->obligation_register->id;
      $historicalsRecords = Historical::where('historicalable_id', $idSection)->where('historicalable_type', ObligationRegister::class)->whereYear('date', $year)->get();
    }

    $monthsArray = [
      ['id' => 1, 'name' => 'Enero', 'key' => 'ENE', 'total' => 0, 'total_count' => 0],
      ['id' => 2, 'name' => 'Febrero', 'key' => 'FEB', 'total' => 0, 'total_count' => 0],
      ['id' => 3, 'name' => 'Marzo', 'key' => 'MAR', 'total' => 0, 'total_count' => 0],
      ['id' => 4, 'name' => 'Abril', 'key' => 'ABR', 'total' => 0, 'total_count' => 0],
      ['id' => 5, 'name' => 'Mayo', 'key' => 'MAY', 'total' => 0, 'total_count' => 0],
      ['id' => 6, 'name' => 'Junio', 'key' => 'JUN', 'total' => 0, 'total_count' => 0],
      ['id' => 7, 'name' => 'Julio', 'key' => 'JUL', 'total' => 0, 'total_count' => 0],
      ['id' => 8, 'name' => 'Agosto', 'key' => 'AGO', 'total' => 0, 'total_count' => 0],
      ['id' => 9, 'name' => 'Septiembre', 'key' => 'SEP', 'total' => 0, 'total_count' => 0],
      ['id' => 10, 'name' => 'Octubre', 'key' => 'OCT', 'total' => 0, 'total_count' => 0],
      ['id' => 11, 'name' => 'Noviembre', 'key' => 'NOV', 'total' => 0, 'total_count' => 0],
      ['id' => 12, 'name' => 'Diciembre', 'key' => 'DIC', 'total' => 0, 'total_count' => 0],
    ];

    $matters = $allMatters->map(function($matter) use ($monthsArray, $historicalsRecords) {
      $months = collect($monthsArray);
      $historicalLevelMatters = $historicalsRecords->pluck('matters')->collapse();
      $recordsPerMatter = $historicalLevelMatters->where('matter_id', $matter->id_matter)->values();
      
      $monthPerMatter = $months->map(function($month) use ($recordsPerMatter) {
        $allInMonth = $recordsPerMatter->filter(function($item) use ($month) {
          $date = strtotime($item['date']);
          return date('n', $date) == $month['id'];
        });
        $lastHistorical = $allInMonth->sortBy('id')->last();
        $month['total'] = is_null($lastHistorical) ? 0 : $lastHistorical->total;
        $month['total_count'] = is_null($lastHistorical) ? 0 : $lastHistorical->total_count;
        return $month;
      });

      $matter->months = $monthPerMatter;
      return $matter;
    });

    $projectInfo['matters'] = $matters->toArray();

    return $projectInfo;
  }
}