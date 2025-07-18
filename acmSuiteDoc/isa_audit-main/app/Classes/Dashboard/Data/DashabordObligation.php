<?php

namespace App\Classes\Dashboard\Data;

use App\Classes\Dashboard\Data\DashboardHistorical;
use App\Classes\Dashboard\Data\UtilitiesDashboard;
use App\Models\V2\Audit\AplicabilityRegister;
use App\Models\V2\Audit\ObligationRegister;
use App\Models\V2\Audit\Obligation;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Catalogs\ApplicationType;
use App\Models\V2\Catalogs\Condition;
use App\Models\V2\Catalogs\Matter;
use App\Models\V2\Catalogs\RiskCategory;
use App\Models\V2\Catalogs\Status;

class DashabordObligation
{
  protected $idObligationRegister = null;
  protected $project = null;
  protected $obligationRegister = null;
  protected $customer = null;
  protected $status = null;
  protected $matters = null;
  protected $records = null;


  public function __construct($idObligationRegister)
	{
    $this->idObligationRegister = $idObligationRegister;
    $this->obligationRegister = ObligationRegister::find($idObligationRegister);
    $applicabilityRegister = AplicabilityRegister::find($this->obligationRegister->id_aplicability_register);
    $relationshipsProcess = [
      'corporate.image',
      'customer.images' => fn($query) => $query->where('usage', 'dashboard'), 
      'aplicability_register.contract_matters.contract_aspects',
    ];
    $this->project = ProcessAudit::with($relationshipsProcess)->find($applicabilityRegister->id_audit_processes);
    $this->customer = $this->getCustomer();
    $this->getDataRecords();
    $this->getStatus();
    $this->matters = $this->filterEvaluatedMatters();
  }

  private function getDataRecords()
  {
    $this->records = Obligation::where('obligation_register_id', $this->idObligationRegister)->getRisk()->customOrder()->get();
  }

  public function getRecords()
  {
    return $this->records;
  }

  private function getStatus() 
  {
    $this->status = Status::where('group', Status::OBLIGATION_GROUP)->get();
  }

  public function getCustomer()
  {
    if ( is_null($this->project) ) return null;
    
    $utilities = new UtilitiesDashboard();
    $info = $utilities->getCustomer($this->project);

    return $info;
  }

  public function getProject()
  {
    $info = $this->customer;
    // parent sections per obligations
    $info['id_obligation_register'] = $this->idObligationRegister;
    // matters
    $mattersCollect = collect($this->matters);
    $info['total'] = round($mattersCollect->avg('total'), 2);
    $info['total_count'] = $mattersCollect->sum('total_count');
    $info['total_expired'] = $mattersCollect->sum('total_expired');
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
    $positivesStatus = [ Obligation::FOR_EXPIRED_OBLIGATION, Obligation::APPROVED_OBLIGATION, Obligation::NO_DATES_OBLIGATION ];
    $negativesStatus = [ Obligation::NO_STARTED_OBLIGATION, Obligation::EXPIRED_OBLIGATION, Obligation::NO_EVIDENCE_OBLIGATION ];
    $expiredStatus = [ Obligation::EXPIRED_OBLIGATION, Obligation::NO_EVIDENCE_OBLIGATION ];
    
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

    $statusObligation = $this->status;
    $aspect->count_status = $statusObligation->map(function($status) use ($aspect) {
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

    $statusObligation = $this->status;
    $matter->count_status = $statusObligation->map(function($status) use ($matter) {
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

  public function getCountRisk()
  { 
    $project = ProcessAudit::find($this->obligationRegister->id_audit_processes);
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

    $data['evaluate_risk'] = $project->evaluate_risk ?? false;
    $data['categories'] = $categoriesRisk->toArray();
    
    return $data;
  }

  public function getDataHistoricalMonthly() 
  {
    $historical = new DashboardHistorical($this->obligationRegister);

    $hsitoricalData = $historical->getDataHistoricalMonthly();

    return $hsitoricalData;
  }

  public function getHistoricalLastYears()
  {
    $historical = new DashboardHistorical($this->obligationRegister);

    $hsitoricalData = $historical->getHistoricalLastYears();

    return $hsitoricalData;
  }

  public function getHistoricalPerMatters()
  {
    $info = $this->getCustomer();

    $historical = new DashboardHistorical($this->obligationRegister);
    
    $hsitoricalData = $historical->getHistoricalPerMatters();

    $info = array_merge($info, $hsitoricalData);

    return $info;
  }
}