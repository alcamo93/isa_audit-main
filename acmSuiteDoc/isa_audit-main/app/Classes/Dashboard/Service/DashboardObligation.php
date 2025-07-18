<?php

namespace App\Classes\Dashboard\Service;

use App\Models\V2\Audit\ProcessAudit;
use App\Classes\Dashboard\Data\DashabordObligation;
use App\Classes\Dashboard\Data\DashboardActionPlan;
use App\Classes\Dashboard\Data\DashabordObligationCustomer;
use Carbon\Carbon; 

class DashboardObligation
{
  public $currentYear = null;

  public function __construct()
  {
    $timezone = Config('enviroment.time_zone_carbon');
    $this->currentYear = intval(Carbon::now($timezone)->format('Y'));
  }

  public function getDataByYearObligation($idCustomer) 
  {
    $customerObligation = new DashabordObligationCustomer($idCustomer);
    $customer = $customerObligation->getCustomer();

    $info['with_data'] = !is_null($customer);
    $info['year'] = $this->currentYear;
    
    if ( !$info['with_data'] ) {
      return $info;
    }

    $info['customer'] = $customer;
    $info['global_projects'] = $customerObligation->getProjects();
    $info['global_matters'] = $customerObligation->filterEvaluatedMatters();
    $info['global_compliance'] = $customerObligation->getCompliance();
    $info['global_historical_month'] = $customerObligation->getHistoricalMonthly();

    return $info;
  }

  public function getDataHistoricalCustomer($idCustomer)
  {
    $customerObligation = new DashabordObligationCustomer($idCustomer);
    $customer = $customerObligation->getCustomer();

    $info['with_data'] = !is_null($customer);
    $info['year'] = $this->currentYear;

    if ( !$info['with_data'] ) {
      return $info;
    }
    
    $info['global_projects'] = $customerObligation->getHistoricalLastYearsPerProjects();
    $info['last_years'] = $customerObligation->getHistoricalLastYears();
    $info['matters_projects'] = $customerObligation->getHistoricalMatterPerProjects();
    return $info;
  }
  
  public function getDataByCorporateObligation($idAuditProcess, $idAplicabilityRegister)
  {
    $filterApplicability = fn($query) => $query->where('id_aplicability_register', $idAplicabilityRegister);
    $relationships = [
      'aplicability_register' => $filterApplicability,
      'aplicability_register.obligation_register.action_plan_register',
    ];
    $queryProcess = ProcessAudit::with($relationships)
      ->where('id_audit_processes', $idAuditProcess)
      ->whereHas('aplicability_register', $filterApplicability)
      ->whereHas('aplicability_register.obligation_register');
    
    $exists = $queryProcess->exists();
    if ( !$exists ) {
      $data['year'] = $this->currentYear;
      $data['with_data'] = false;
      return $data;
    }

    $process = $queryProcess->first();
    $idObligationRegister = $process->aplicability_register->obligation_register->id;
    $obligationRegister = new DashabordObligation($idObligationRegister);
    $project = $obligationRegister->getProject();
    
    $data['with_data'] = true;
    $data['year'] = $this->currentYear;
    $data['customer'] = $project;
    $data['compliance'] = $obligationRegister->getCompliance();
    $data['historical_last_years'] = $obligationRegister->getHistoricalLastYears();
    $data['historical_month'] = $obligationRegister->getDataHistoricalMonthly();
    $data['historical_project'] = $obligationRegister->getHistoricalPerMatters();
    
    return $data;
  }
  
  public function getDataObligationActionCorporate($idAuditProcess, $idAplicabilityRegister)
  {
    $filterApplicability = fn($query) => $query->where('id_aplicability_register', $idAplicabilityRegister);
    $relationships = [
      'aplicability_register' => $filterApplicability,
      'aplicability_register.obligation_register.action_plan_register',
    ];
    $queryProcess = ProcessAudit::with($relationships)
      ->where('id_audit_processes', $idAuditProcess)
      ->whereHas('aplicability_register', $filterApplicability)
      ->whereHas('aplicability_register.obligation_register.action_plan_register');
    
    $exists = $queryProcess->exists();
    if ( !$exists ) {
      $data['with_data'] = false;
      $data['year'] = $this->currentYear;
      return $data;
    }

    $process = $queryProcess->first();
    $actionRegister = $process->aplicability_register->obligation_register->action_plan_register;
    $actionPlan = new DashboardActionPlan($actionRegister->id_action_register);

    $data['with_data'] = true;
    $data['year'] = $this->currentYear;
    $data['monthly'] = $actionPlan->getDataHistoricalMonthly();
    $data['project'] = $actionPlan->getProject();
    $data['compliance'] = $actionPlan->getCompliance(true);
    $data['counts'] = $actionPlan->filterEvaluatedMatters();
    $data['risk'] = $actionPlan->getCountRisk();
    $data['matter_action_requirement'] = $actionPlan->filterEvaluatedMatters();
    $data['matter_action_tasks'] = $actionPlan->filterEvaluatedMattersTask();
    $data['historical_last_years'] = $actionPlan->getHistoricalLastYears();
    $data['historical_project'] = $actionPlan->getHistoricalPerMatters();

    return $data;
  }

  public function getDataActionPlanRecords($idAuditProcess, $idAplicabilityRegister, $isGroupedInMatters = false)
  {
    $filterApplicability = fn($query) => $query->where('id_aplicability_register', $idAplicabilityRegister);
    $relationships = [
      'aplicability_register' => $filterApplicability,
      'aplicability_register.obligation_register.action_plan_register',
    ];
    $queryProcess = ProcessAudit::with($relationships)
      ->where('id_audit_processes', $idAuditProcess)
      ->whereHas('aplicability_register', $filterApplicability)
      ->whereHas('aplicability_register.obligation_register.action_plan_register');
    
    $exists = $queryProcess->exists();
    if ( !$exists ) {
      $info['with_data'] = false;
      $info['year'] = $this->currentYear;
      return $info;
    }

    $process = $queryProcess->first();
    $actionRegister = $process->aplicability_register->obligation_register->action_plan_register;
    $actionPlan = new DashboardActionPlan($actionRegister->id_action_register);

    $records = $actionPlan->getRecords($isGroupedInMatters);

    $info['with_data'] = true;
    $info['year'] = $this->currentYear;
    $info['records'] = $records;
    
    return $info;
  }
}