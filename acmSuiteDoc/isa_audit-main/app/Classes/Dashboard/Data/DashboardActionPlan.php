<?php

namespace App\Classes\Dashboard\Data;

use App\Models\V2\Audit\ActionPlan;
use App\Models\V2\Audit\ActionPlanRegister;
use App\Classes\Dashboard\Data\DashboardHistorical;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Audit\Task;
use App\Models\V2\Catalogs\ApplicationType;
use App\Models\V2\Catalogs\Condition;
use App\Models\V2\Catalogs\Matter;
use App\Models\V2\Catalogs\RiskCategory;
use App\Models\V2\Catalogs\Status;

class DashboardActionPlan
{
  protected $idActionRegister = null;
  protected $actionRegister = null;
  protected $project = null;
  protected $type = null;
  protected $section = null;
  protected $records = null;
  protected $matters = null;
  protected $statusActionPlan = null;
  protected $statusTasks = null;

	public function __construct($idActionRegister)
	{
    $this->idActionRegister = $idActionRegister;
    $this->actionRegister = ActionPlanRegister::find($idActionRegister);
    $relationships = [
      'corporate', 
      'aplicability_register.contract_matters.contract_aspects',
      'aplicability_register.audit_register',
      'aplicability_register.obligation_register'
    ];
    $this->project = ProcessAudit::with($relationships)->find($this->actionRegister->id_audit_processes);
    $this->type = class_basename($this->actionRegister->registerable_type);
    $this->section = $this->actionRegister->section;
    $relationships = [
      'requirement.matter', 'subrequirement.matter', 'requirement.aspect', 'subrequirement.aspect', 
      'tasks.comments', 'tasks.auditors.person', 'tasks.auditors.image', 'tasks.status',
      'auditors.person', 'auditors.image', 'priority'
    ];
    $this->records = ActionPlan::with($relationships)->getRisk()->onlyUsables($idActionRegister)->customOrder()->get();
    $this->getStatus();
    $this->matters = $this->filterEvaluatedMatters();
  }

  private function getStatus() 
  {
    $this->statusActionPlan = Status::where('group', Status::ACTION_PLAN_GROUP)->where('id_status', '!=', ActionPlan::CLOSED_AP)->get();
    $this->statusTasks = Status::where('group', Status::TASK_GROUP)->get();
  }

  public function getRecords($isGroupedInMatters = false)
  {
    if ($isGroupedInMatters) {
      return $this->filterEvaluatedMattersTask();
    }
    return $this->records;
  }

  public function getProject()
  {
    // owner
    $info['corp_tradename'] = $this->project->corporate->corp_tradename_format;
    $info['audit_processes'] = $this->project->process_name;
    // ids
    $info['id_corporate'] = $this->project->id_corporate;
    $info['id_audit_processes'] = $this->project->id_audit_processes;
    $info['id_aplicability_register'] = $this->project->aplicability_register->id_aplicability_register;
    // parent sections per obligations
    if ($this->type == 'ObligationRegister') {
      $info['obligation_register_id'] = $this->project->aplicability_register->obligation_register->id;
    }
    // parent sections per audits
    if ($this->type == 'AuditRegister') {
      $auditRegisters = $this->project->aplicability_register->audit_register;
      $auditRegister = $auditRegisters->filter(function($item) {
        $tmpIdActionRegister = $item->action_plan_register->id_action_register ?? null;
        return $tmpIdActionRegister == $this->idActionRegister;
      })->first();
      $info['id_audit_register'] = $auditRegister->id_audit_register;
    }
    // current action register
    $info['id_action_register'] = $this->idActionRegister;
    $info['origin'] = $this->actionRegister->origin;
    // matters
    $mattersCollect = collect($this->matters);
    $info['total'] = round($mattersCollect->avg('total'), 2);
    $info['total_count'] = $mattersCollect->sum('total_count');
    $info['total_expired'] = $mattersCollect->sum('total_expired');
    $info['total_critical'] = $mattersCollect->sum('total_critical');
    $info['matters'] = $this->matters;
    
    return $info;
  }

  private function getMatters()
  {
    $allowAspects = $this->project->aplicability_register->contract_matters
      ->flatMap(fn($item) => $item->contract_aspects)
      ->filter(fn($item) => $item->id_application_type != ApplicationType::NOT_APPLICABLE);
    
    $matters = $allowAspects->pluck('id_matter')->unique()->values()->toArray();
    $aspects = $allowAspects->pluck('id_aspect')->unique()->values()->toArray();
    
    $filterAspects = fn($query) => $query->whereIn('id_aspect', $aspects);
    $relationships = ['aspects' => $filterAspects];
    $filterMatter = Matter::with($relationships)->whereIn('id_matter', $matters)->get();
    
    return $filterMatter;
  }

  public function filterEvaluatedMatters()
  {
    $filterMatter = $this->getMatters();
    
    $matters = $filterMatter->map(function($matter) {
      $matter->aspects->map(function($aspect) {
        $aspect = $this->getReqValuesPerAspect($aspect);
        return $aspect;
      });
      $matter = $this->getReqValuesPerMatter($matter);
      return $matter;
    })->toArray();

    return $matters;
  }

  private function getReqValuesPerAspect($aspect)
  {
    $positivesStatus = [ ActionPlan::COMPLETED_AP ];
    $negativesStatus = [ ActionPlan::UNASSIGNED_AP, ActionPlan::PROGRESS_AP, ActionPlan::REVIEW_AP, ActionPlan::EXPIRED_AP, ActionPlan::CLOSED_AP ];
    $expiredStatus = [ ActionPlan::EXPIRED_AP ];
    
    $totalStatus = array_merge($positivesStatus, $negativesStatus);
    $recordPerAspect = $this->records->where('requirement.id_aspect', $aspect->id_aspect);
    $positives = $recordPerAspect->whereIn('id_status', $positivesStatus)->count();
    $totalRecords = $recordPerAspect->whereIn('id_status', $totalStatus)->count();
    $totalExpireds = $recordPerAspect->whereIn('id_status', $expiredStatus)->count();
    $totalCritical = $recordPerAspect->where('requirement.id_condition', Condition::CRITICAL)->count();
    $totalOperative = $recordPerAspect->where('requirement.id_condition', Condition::OPERATIVE)->count();

    $value = $totalRecords != 0 ? ($positives / $totalRecords) * 100 : 0;
    $total = round($value, 2);

    $aspect->total = $total;
    $aspect->total_count = $positives;
    $aspect->total_expired = $totalExpireds;
    $aspect->total_critical = $totalCritical;
    $aspect->total_operative = $totalOperative;

    $statusAP = $this->statusActionPlan;
    $aspect->count_status = $statusAP->map(function($status) use ($aspect) {
      $status->count = $this->records->where('id_aspect', $aspect->id_aspect)->where('id_status', $status->id_status)->count();
      return $status;
    })->toArray();

    return $aspect;
  }

  private function getReqValuesPerMatter($matter)
  {
    $aspects = $matter->aspects->where('id_matter', $matter->id_matter);
    $total = $aspects->avg('total');
    $totalCount = $aspects->sum('total_count');
    $totalExpireds = $aspects->sum('total_expired');
    $totalCritical = $aspects->sum('total_critical');
    $totalOperative = $aspects->sum('total_operative');

    $matter->total = round($total, 2);
    $matter->total_count = $totalCount;
    $matter->total_expired = $totalExpireds;
    $matter->total_critical = $totalCritical;
    $matter->total_operative = $totalOperative;

    $statusAP = $this->statusActionPlan;
    $matter->count_status = $statusAP->map(function($status) use ($matter) {
      $status->count = $matter->aspects->flatMap(fn($aspect) => $aspect->count_status)->where('id_status', $status->id_status)->sum('count');
      return $status->toArray();
    })->toArray();
    
    return $matter;
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

  public function getDataHistoricalMonthly() 
  {
    $historical = new DashboardHistorical($this->actionRegister);

    $hsitoricalData = $historical->getDataHistoricalMonthly();

    return $hsitoricalData;
  }

  public function getHistoricalLastYears()
  {
    $historical = new DashboardHistorical($this->actionRegister);

    $hsitoricalData = $historical->getHistoricalLastYears();

    return $hsitoricalData;
  }

  public function getHistoricalPerMatters()
  {
    $historical = new DashboardHistorical($this->actionRegister);

    $hsitoricalData = $historical->getHistoricalPerMatters();

    return $hsitoricalData;
  }

  public function getCountRisk()
  {
    $categories = RiskCategory::with('interpretations')->get();
    
    $categoriesRisk = $categories->map(function($category) {
      $category->interpretations->map(function($interpretation) use ($category) {
        $totals = $this->records->pluck('risk_totals')->collapse();
        $interpretation->count = $totals->where('id_risk_category', $category->id_risk_category)
          ->where('interpretation', $interpretation->interpretation)->count();
        return $interpretation;
      });
      return $category;
    });

    $data['evaluate_risk'] = boolval($this->project->evaluate_risk);
    $data['categories'] = $categoriesRisk->toArray();
    
    return $data;
  }

  public function filterEvaluatedMattersTask()
  {
    $filterMatter = $this->getMatters();

    $matters = $filterMatter->map(function($matter) {
      $matter->aspects->map(function($aspect) {
        $aspect = $this->getTaskValuesPerAspect($aspect);
        return $aspect;
      });
      $matter = $this->getTaskValuesPerMatter($matter);
      return $matter;
    })->toArray();

    return $matters;
  }

  private function getTaskValuesPerAspect($aspect)
  { 
    $recordPerAspect = $this->records->where('requirement.id_aspect', $aspect->id_aspect);
    $mainTasks = $recordPerAspect->flatMap(fn($item) => $item->tasks)->where('main_task', Task::MAIN_TASK);

    $aspect->count = $mainTasks->count();
    $aspect->total = $mainTasks->count() != 0 ? $mainTasks->where('id_status', ActionPlan::COMPLETED_AP)->count() / $mainTasks->count() * 100 : 0;

    $statusTasks = $this->statusTasks;
    $aspect->count_status = $statusTasks->map(function($status) use ($mainTasks) {

      $totalRecords = $mainTasks->count();
      $totalPerStatus = $mainTasks->where('id_status', $status->id_status)->count();

      $value = $totalRecords != 0 ? ($totalPerStatus / $totalRecords) * 100 : 0;
      $total = round($value, 2);

      $status->count = $totalPerStatus;
      $status->total = $total;

      return $status;
    })->toArray();

    return $aspect;
  }

  private function getTaskValuesPerMatter($matter)
  {
    $statusTasks = $this->statusTasks;
    $matter->count = $matter->aspects->map(fn($item) => $item->count)->sum();
    $matter->total = $matter->aspects->map(fn($item) => $item->total)->avg();

    $matter->count_status = $statusTasks->map(function($status) use ($matter) {
      $status->count = $matter->aspects->flatMap(fn($item) => $item->count_status)->where('id_status', $status->id_status)->sum('count');
      $status->total = round($matter->aspects->flatMap(fn($item) => $item->count_status)->where('id_status', $status->id_status)->avg('total'), 2);
      return $status->toArray();
    })->toArray();

    return $matter;
  }

  public function getFindingsTotal()
  {
    $max = 100;
    $collectionMatter = collect($this->matters);
    
    $criticalTotal = $collectionMatter->sum('total_critical');
    $operativeTotal = $collectionMatter->sum('total_operative');
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
}