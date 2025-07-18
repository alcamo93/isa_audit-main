<?php

namespace App\Classes\Dashboard\Data;

use App\Classes\Dashboard\Data\DashboardHistorical;
use App\Classes\Dashboard\Data\UtilitiesDashboard;
use App\Models\V2\Audit\Audit;
use App\Models\V2\Audit\AuditRegister;
use App\Models\V2\Catalogs\Matter;
use App\Models\V2\Catalogs\RiskCategory;
use App\Models\V2\Audit\EvaluateAuditRequirement;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Catalogs\ApplicationType;

class DashabordAudit
{
  protected $idAuditRegister = null;
  protected $project = null;
  protected $auditRegister = null;
  protected $answerAudit = null;
  protected $matters = null;
  protected $records = null;


  public function __construct($idAuditRegister)
	{
    $this->idAuditRegister = $idAuditRegister;
    $relationshipsAudit = ['audit_matters.audit_aspects'];
    $this->auditRegister = AuditRegister::with($relationshipsAudit)->find($idAuditRegister);
    $relationshipsProcess = [
      'corporate.image',
      'customer.images' => fn($query) => $query->where('usage', 'dashboard'), 
      'aplicability_register.contract_matters.contract_aspects',
    ];
    $this->project = ProcessAudit::with($relationshipsProcess)->find($this->auditRegister->id_audit_processes);
    $this->getDataRecords();
    $this->getAnswers();
    $this->matters = $this->filterEvaluatedMatters();
  }

  private function getDataRecords()
  {
    $auditAspectIds = $this->auditRegister->audit_matters->flatMap(fn($aspect) => $aspect->audit_aspects)
      ->pluck('id_audit_aspect')->toArray();

    $relationships = ['requirement.evidence', 'subrequirement.evidence'];
    $this->records = EvaluateAuditRequirement::with($relationships)->getRisk()->whereIn('id_audit_aspect', $auditAspectIds)->customOrder()->get();
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

  private function getAnswers() 
  {
    $this->answerAudit = collect([
      ['id' => 0, 'key' => 'NEGATIVE', 'label' => 'No Cumple'],
      ['id' => 1, 'key' => 'AFFIRMATIVE', 'label' => 'Cumple'],
      ['id' => 2, 'key' => 'NO_APPLY', 'label' => 'No Aplica']
    ]);
  }

  public function getProject()
  {
    $utilities = new UtilitiesDashboard();

    $info = $utilities->getCustomer($this->project);
    // parent sections per obligations
    $info['id_audit_register'] = $this->idAuditRegister;
    // matters
    $mattersCollect = collect($this->matters);
    $info['total'] = round($mattersCollect->avg('total'), 2);
    $info['total_count'] = $mattersCollect->sum('total_count');
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
    // get current audit aspect
    $auditAspect = $this->auditRegister->audit_matters->flatMap(fn($item) => $item->audit_aspects)
      ->where('id_aspect', $aspect->id_aspect)->first(); 
    // if there is audit aspect so filter only parent requirements with answer in audit
    $idAuditAspect = $auditAspect->id_audit_aspect ?? 0;
    $answersPerAspect = $this->records->where('id_audit_aspect', $idAuditAspect)
      ->whereNull('id_subrequirement')->pluck('audit')->filter(fn($record) => !is_null($record));
    // set values per audit aspect
    $aspect->total = $auditAspect->total ?? 0;
    $aspect->total_count = $answersPerAspect->where('answer', Audit::NEGATIVE)->count();
    // set values per answer
    $aswersAudit = $this->answerAudit;
    $aspect->count_answers = $aswersAudit->map(function($answer) use ($answersPerAspect) {
      $answer['count'] = $answersPerAspect->where('answer', $answer['id'])->count();
      return $answer;
    })->toArray();
    
    return $aspect;
  }

  private function getReqValuesPerMatter($matter)
  {
    // get current audit matter
    $auditMatter = $this->auditRegister->audit_matters->where('id_matter', $matter->id_matter)->first();
    // set values per audit aspect
    $matter->total = $auditMatter->total ?? 0;
    $matter->total_count = $matter->aspects->sum('total_count') ?? 0;
    // set values per answer
    $answerAudit = $this->answerAudit;
    $matter->count_answers = $answerAudit->map(function($answer) use ($matter) {
      $answer['count'] = $matter->aspects->flatMap(fn($aspect) => $aspect->count_answers)->where('id', $answer['id'])->sum('count');
      return $answer;
    })->toArray();
    
    return $matter;
  }

  public function getCompliance($labelAction = false)
  {
    $max = 100;
    $compliance = $this->auditRegister->total;
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
    $project = ProcessAudit::find($this->auditRegister->id_audit_processes);
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
    $historical = new DashboardHistorical($this->auditRegister);

    $hsitoricalData = $historical->getDataHistoricalMonthly();

    return $hsitoricalData;
  }

  public function getHistoricalLastYears()
  {
    $historical = new DashboardHistorical($this->auditRegister);

    $hsitoricalData = $historical->getHistoricalLastYears();

    return $hsitoricalData;
  }

  public function getHistoricalPerMatters()
  {
    $historical = new DashboardHistorical($this->auditRegister);

    $hsitoricalData = $historical->getHistoricalPerMatters();

    return $hsitoricalData;
  }
}