<?php

namespace App\Classes\Dashboard\Data;

use App\Classes\Dashboard\Data\DashboardActionPlan;
use App\Classes\Dashboard\Data\DashabordAudit;
use App\Classes\Dashboard\Data\DashboardHistorical;
use App\Classes\Dashboard\Data\UtilitiesDashboard;
use App\Models\V2\Audit\AuditRegister;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Catalogs\RiskCategory;

class DashabordCompliance
{
  protected $idAuditRegister = null;
  protected $idActionRegister = null;
  protected $project = null;
  protected $dashboardAudit = null;
  protected $dashboardAction = null;
  protected $auditRegister = null;
  protected $actionRegister = null;
  protected $answerAudit = null;
  protected $matters = null;
  protected $records = null;

  public function __construct($idAuditRegister)
	{
    $this->auditRegister = AuditRegister::findOrFail($idAuditRegister);
    $relationshipsProcess = [
      'corporate.image',
      'customer.images' => fn($query) => $query->where('usage', 'dashboard'), 
      'aplicability_register.contract_matters.contract_aspects',
    ];
    $this->project = ProcessAudit::with($relationshipsProcess)->find($this->auditRegister->id_audit_processes);
    $this->actionRegister = $this->auditRegister->action_plan_register;

    $this->idAuditRegister = $idAuditRegister;
    $this->idActionRegister = $this->actionRegister->id_action_register ?? null;

    $this->dashboardAudit = new DashabordAudit($idAuditRegister);
    if ( !is_null($this->idActionRegister) ) {
      $this->dashboardAction = new DashboardActionPlan( $this->idActionRegister );
    }

    $this->matters = $this->filterEvaluatedMatters();
  }

  public function getDataRecords()
  {
    $this->records = collect([]);
  }

  public function getCustomer()
  {
    if ( is_null($this->project) ) return null;
    
    $utilities = new UtilitiesDashboard();
    $info = $utilities->getCustomer($this->project);

    return $info;
  }

  public function getRecords()
  {
    return $this->records;
  }

  public function getProject()
  {
    $projectAudit = $this->dashboardAudit->getProject();

    $info['id_audit_processes'] = $projectAudit['id_audit_processes'];
    $info['id_aplicability_register'] = $projectAudit['id_aplicability_register'];
    $info['cust_tradename'] = $projectAudit['cust_tradename'];
    $info['corp_tradename'] = $projectAudit['corp_tradename'];
    $info['cust_full_path'] = $projectAudit['cust_full_path'];
    $info['corp_full_path'] = $projectAudit['corp_full_path'];
    $info['address'] = $projectAudit['address'];
    $info['id_audit_register'] = $this->idAuditRegister;
    $info['id_action_register'] = $this->idActionRegister;
    // matters
    $mattersCollect = collect($this->matters);
    $info['total'] = round($mattersCollect->avg('total'), 2);
    $info['total_count'] = $mattersCollect->sum('total_count');
    $info['matters'] = $this->matters;
    
    return $info;
  }

  public function filterEvaluatedMatters()
  {
    // get values per audit
    $filterMatter = $this->dashboardAudit->filterEvaluatedMatters();
    if ( is_null($this->idActionRegister) ) {
      return $filterMatter;
    }
    // get values per action plan audit
    $filterMatterAP = $this->dashboardAction->filterEvaluatedMatters();
    $matters = collect($filterMatter)->map(function($matter) use ($filterMatterAP) {
      $foundMatter = collect($filterMatterAP)->where('id_matter', $matter['id_matter'])->first();
      $matter['aspects'] = collect($matter['aspects'])->map(function($aspect) use ($foundMatter) {
        $foundAspect = collect($foundMatter['aspects'])->where('id_aspect', $aspect['id_aspect'])->first();
        $aspect = $this->getReqValuesPerAspect($aspect, $foundAspect);
        return $aspect;
      })->toArray();
      $matter = $this->getReqValuesPerMatter($matter);
      return $matter;
    })->toArray();

    return $matters;
  }

  private function getValueCompliance($auditScore, $actionPlanScore)
  {
    $auditRemaining = 100 - $auditScore;
    $actionPlanEquivalent = round( (($actionPlanScore * $auditRemaining) / 100) , 2 );
    $totalScore = $auditScore + $actionPlanEquivalent;
    
    return $totalScore;
  }

  private function getReqValuesPerAspect($aspect, $aspectAP)
  { 
    $aspect['total'] = $this->getValueCompliance($aspect['total'], $aspectAP['total']);
    $aspect['total_count'] = $aspect['total_count'] - $aspectAP['total_count'];
    
    return $aspect;
  }

  private function getReqValuesPerMatter($matter)
  {
    $aspects = collect($matter['aspects']);
    $matter['total'] = round($aspects->avg('total'), 2);
    $matter['total_count'] = $aspects->sum('total_count');
    
    return $matter;
  }

  public function getCompliance($labelAction = false)
  {
    $totals = collect($this->matters)->pluck('total');

    $max = 100;
    $compliance = round( $totals->avg(), 2 );
    $noCompliance = round($max - $compliance, 2);

    $labelPositive = $labelAction ? 'Completo' : 'Cumplimiento';
    $labelNegative = $labelAction ? 'Pendiente' : 'Incumplimiento';

    $data = [
      [ 'label' => $labelPositive, 'total' => $compliance, 'color' => '#009299' ],
      [ 'label' => $labelNegative, 'total' => $noCompliance, 'color' => '#003F72']
    ];
    
    return $data;
  }

  public function getCountRisk()
  { 
    $categories = RiskCategory::with('interpretations')->get();
    
    $records = $this->records->whereNull('id_subrequirement')->pluck('audit')->filter(fn($record) => !is_null($record));

    $categoriesRisk = $categories->map(function($category) use ($records) {
      $category->interpretations->map(function($interpretation) use ($category, $records) {
        $totals = $records->pluck('risk_totals')->collapse();
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

  public function getDataHistoricalMonthly() 
  {
    $historicalAudit = new DashboardHistorical($this->auditRegister);
    $historicalAuditData = $historicalAudit->getDataHistoricalMonthly();

    if ( is_null($this->idActionRegister) ) {
      return $historicalAuditData;
    }

    $historicalActionPlan = new DashboardHistorical($this->actionRegister);
    $historicalActionPlanData = $historicalActionPlan->getDataHistoricalMonthly();

    $historicalComplianceData = $this->getHistoricalValueCompliance($historicalAuditData, $historicalActionPlanData);

    return $historicalComplianceData;
  }

  public function getHistoricalLastYears()
  {
    $historicalAudit = new DashboardHistorical($this->auditRegister);
    $historicalAuditData = $historicalAudit->getHistoricalLastYears();

    if ( is_null($this->idActionRegister) ) {
      return $historicalAuditData;
    }

    $historicalActionPlan = new DashboardHistorical($this->actionRegister);
    $historicalActionPlanData = $historicalActionPlan->getHistoricalLastYears();

    $auditYears = collect($historicalAuditData['years']);
    $actionPlanYears = collect($historicalActionPlanData['years']);

    $historicalAuditData['years'] = $auditYears->map(function($year) use ($actionPlanYears) {
      $foundYear = $actionPlanYears->where('year', $year['year'])->first();
      $year['months'] = $this->getHistoricalValueCompliance($year['months'], $foundYear['months']);
      $currentYear = collect($year);
      $year['total'] = round( $currentYear->avg('total'), 2 );
      $year['total_count'] = $currentYear->sum('total_count');
      return $year;
    })->toArray();

    return $historicalAuditData;
  }

  public function getHistoricalPerMatters()
  {
    $info = $this->getCustomer();

    $historicalAudit = new DashboardHistorical($this->auditRegister);
    $historicalAuditData = $historicalAudit->getHistoricalPerMatters();

    $info = array_merge($info, $historicalAuditData);

    if ( is_null($this->idActionRegister) ) {
      return $info;
    }
    
    $historicalActionPlan = new DashboardHistorical($this->actionRegister);
    $historicalActionPlanData = $historicalActionPlan->getHistoricalPerMatters();

    $info = array_merge($info, $historicalActionPlanData);

    return $info;
  }

  private function getHistoricalValueCompliance($historicalAuditData, $historicalActionPlanData)
  {
    $historicalComplianceData = collect($historicalAuditData)->map(function($month) use ($historicalActionPlanData) {
      $foundMonth = collect($historicalActionPlanData)->where('id', $month['id'])->first();
      $month['historical_total'] = collect($month['historical_total'])->map(function($matter) use ($foundMonth) {
        $foundMatter = collect($foundMonth['historical_total'])->where('matter_id', $matter['matter_id'])->first();
        $matter['aspects'] = collect($matter['aspects'])->map(function($aspect) use ($foundMatter) {
          $foundAspect = collect($foundMatter['aspects'])->where('aspect_id', $aspect['aspect_id'])->first();
          $aspect['total'] = $this->getValueCompliance($aspect['total'], $foundAspect['total']);
          $aspect['total_count'] = $aspect['total_count'] - $foundAspect['total_count'];
          return $aspect;
        })->toArray();
        $currentAspects = collect($matter['aspects']);
        $matter['total'] = round( $currentAspects->avg('total'), 2 );
        $matter['total_count'] = $currentAspects->sum('total_count');
        return $matter;
      })->toArray();
      $currentMonth = collect($month['historical_total']);
      $month['total'] = round( $currentMonth->avg('total'), 2 );
      $month['total_count'] = $currentMonth->sum('total_count');
      return $month;
    })->toArray();

    return $historicalComplianceData;
  }
}