<?php

namespace App\Classes\Dashboard\Service;

use App\Models\V2\Audit\ProcessAudit;
use App\Classes\Dashboard\Data\DashabordAudit as DashboardAuditRegister;
use App\Classes\Dashboard\Data\DashboardActionPlan;
use App\Classes\Dashboard\Data\DashabordAuditCustomer;
use Carbon\Carbon; 

class DashboardAudit
{
  public $currentYear = null;

  public function __construct()
  {
    $timezone = Config('enviroment.time_zone_carbon');
    $this->currentYear = intval(Carbon::now($timezone)->format('Y')); 
  }

  /**
   * main
   */
  public function getDataByYearAudit($idCustomer) 
  {
    $customerAudit = new DashabordAuditCustomer($idCustomer);
    $customer = $customerAudit->getCustomer();
    
    $info['with_data'] = !is_null($customer);
    $info['year'] = $this->currentYear;
    
    if ( !$info['with_data'] ) {
      return $info;
    }

    $info['customer'] = $customer;
    $info['global_projects'] = $customerAudit->getProjects();
    $info['global_matters'] = $customerAudit->filterEvaluatedMatters();
    $info['global_compliance'] = $customerAudit->getCompliance();
    $info['global_historical_month'] = $customerAudit->getHistoricalMonthly();

    return $info;
  }

  public function getAuditDataHistoricalCustomer($idCustomer)
  {
    $customerObligation = new DashabordAuditCustomer($idCustomer);
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

  public function getDataAuditCorporate($idAuditProcess, $idAplicabilityRegister, $idAuditRegister, $withRecords = false)
  {
    $filterAplicability = fn($query) => $query->where('id_aplicability_register', $idAplicabilityRegister);
    $filter = fn($query) => $query->where('id_audit_register', $idAuditRegister);
    $relationships = [
      'aplicability_register' => $filterAplicability,
      'aplicability_register.audit_register' => $filter
    ];
    $queryProcess = ProcessAudit::with($relationships)
      ->where('id_audit_processes', $idAuditProcess)
      ->whereHas('aplicability_register', $filterAplicability)
      ->whereHas('aplicability_register.audit_register', $filter);
    
    $exists = $queryProcess->exists();
    if ( !$exists ) {
      $data['with_data'] = false;
      $data['year'] = $this->currentYear;
      return $data;
    }
    
    $auditRegister = new DashboardAuditRegister($idAuditRegister);
    
    $data['with_data'] = true;
    $data['year'] = $this->currentYear;
    $data['customer'] = $auditRegister->getProject();
    $data['compliance'] = $auditRegister->getCompliance();
    $data['matters'] = $auditRegister->filterEvaluatedMatters();
    $data['last_years'] = $auditRegister->getHistoricalLastYears();

    if ($withRecords) {
      $data['records'] = $auditRegister->getRecords();
    }
    
    return $data;
  }

  public function getDataAuditActionCorporate($idAuditProcess, $idAplicabilityRegister, $idAuditRegister)
  {
    $filterAplicability = fn($query) => $query->where('id_aplicability_register', $idAplicabilityRegister);
    $filter = fn($query) => $query->where('id_audit_register', $idAuditRegister);
    $relationships = [
      'aplicability_register' => $filterAplicability,
      'aplicability_register.audit_register' => $filter,
      'aplicability_register.audit_register.action_plan_register'
    ];
    $queryProcess = ProcessAudit::with($relationships)
      ->where('id_audit_processes', $idAuditProcess)
      ->whereHas('aplicability_register', $filterAplicability)
      ->whereHas('aplicability_register.audit_register', $filter)
      ->whereHas('aplicability_register.audit_register.action_plan_register');
    
    $exists = $queryProcess->exists();
    if ( !$exists ) {
      $data['with_data'] = false;
      $data['year'] = $this->currentYear;
      return $data;
    }

    $process = $queryProcess->first();
    $auditRegister = $process->aplicability_register->audit_register->first();
    $actionRegister = $auditRegister->action_plan_register;
    $actionPlan = new DashboardActionPlan($actionRegister->id_action_register);

    $data['with_data'] = true;
    $data['year'] = $this->currentYear;
    $data['monthly'] = $actionPlan->getDataHistoricalMonthly();
    $data['project'] = $actionPlan->getProject();
    $data['compliance'] = $actionPlan->getCompliance(true);
    $data['counts'] = $actionPlan->filterEvaluatedMatters();
    $data['risk'] = $actionPlan->getCountRisk();
    $data['findings'] = $actionPlan->getFindingsTotal();

    return $data;
  }
}