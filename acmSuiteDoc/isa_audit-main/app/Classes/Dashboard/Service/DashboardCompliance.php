<?php

namespace App\Classes\Dashboard\Service;

use App\Models\V2\Audit\ProcessAudit;
use App\Classes\Dashboard\Data\DashabordCompliance;
use App\Classes\Dashboard\Data\DashboardActionPlan;
use App\Classes\Dashboard\Data\DashabordComplianceCustomer;
use Carbon\Carbon;

class DashboardCompliance
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
  public function getDataByYearCompliance($idCustomer) 
  {
    $customerCompliance = new DashabordComplianceCustomer($idCustomer);
    $customer = $customerCompliance->getCustomer();
    
    $info['with_data'] = !is_null($customer);
    $info['year'] = $this->currentYear;
    
    if ( !$info['with_data'] ) {
      return $info;
    }

    $info['customer'] = $customer;
    $info['global_projects'] = $customerCompliance->getProjects();
    $info['global_matters'] = $customerCompliance->filterEvaluatedMatters();
    $info['global_compliance'] = $customerCompliance->getCompliance();
    $info['global_historical_month'] = $customerCompliance->getHistoricalMonthly();

    return $info;
  }
  
  public function getComplianceDataHistoricalCustomer($idCustomer)
  {
    $customerObligation = new DashabordComplianceCustomer($idCustomer);
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

  public function getDataComplianceCorporate($idAuditProcess, $idAplicabilityRegister, $idAuditRegister)
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

    $complianceRegister = new DashabordCompliance($idAuditRegister);
    $project = $complianceRegister->getProject();
    
    $data['with_data'] = true;
    $data['year'] = $this->currentYear;
    $data['customer'] = $project;
    $data['compliance'] = $complianceRegister->getCompliance();
    $data['matters'] = $project['matters'];
    $data['last_years'] = $complianceRegister->getHistoricalLastYears();
    $data['historical_month'] = $complianceRegister->getDataHistoricalMonthly();
    $data['historical'] = $complianceRegister->getHistoricalPerMatters();
    
    return $data;
  }

  public function getDataComplianceActionCorporate($idAuditProcess, $idAplicabilityRegister, $idAuditRegister)
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
      ->whereHas('aplicability_register.audit_register', $filter)
      ->whereHas('aplicability_register.audit_register.action_plan_register'); // quitar esto

    $exists = $queryProcess->exists();
    if ( !$exists ) { // el contenido de este if sera auditoria por default
      $data['with_data'] = false;
      $data['year'] = $this->currentYear;
      return $data;
    }

    // $auditRegister = $process->aplicability_register->audit_register->first();
    // $actionRegister = $auditRegister->action_plan_register;
    // $actionPlan = new DashboardActionPlan($actionRegister->id_action_register);

    // $data['with_data'] = true;
    // $data['year'] = $this->currentYear;
    // $data['monthly'] = $actionPlan->getDataHistoricalMonthly();
    // $data['project'] = $actionPlan->getProject();
    // $data['compliance'] = $actionPlan->getCompliance(true);
    // $data['counts'] = $actionPlan->filterEvaluatedMatters();
    // $data['risk'] = $actionPlan->getCountRisk();
    // $data['findings'] = $actionPlan->getFindingsTotal();
    // // faltan estos
    // // $data['matter_action_requirement'] = $actionPlan->filterEvaluatedMatters();
    // // $data['matter_action_tasks'] = $actionPlan->filterEvaluatedMattersTask();
    // // $data['historical_last_years'] = $actionPlan->getHistoricalLastYears();
    // // $data['historical_project'] = $actionPlan->getHistoricalPerMatters();

    /**
     * TODO: CREAR UN METODO EN COMPLIENCE DONDE TOMAR VALORES DE AUDITORIA SI NO EXISTE EN PLAN DE ACCIÓN
     * DE TODOS MODOS SE DEBE OBTENER LOS VALORES DE LA AUDITORIA ACTUAL
     */

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
    $data['matter_action_requirement'] = $actionPlan->filterEvaluatedMatters();
    $data['matter_action_tasks'] = $actionPlan->filterEvaluatedMattersTask();
    $data['historical_last_years'] = $actionPlan->getHistoricalLastYears();
    $data['historical_project'] = $actionPlan->getHistoricalPerMatters();

    return $data;
  }

  public function getDataActionPlanRecords($idAuditProcess, $idAplicabilityRegister, $idAuditRegister, $isGroupedInMatters = false)
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
      ->whereHas('aplicability_register.audit_register', $filter)
      ->whereHas('aplicability_register.obligation_register.action_plan_register');
    
    $exists = $queryProcess->exists();
    if ( !$exists ) {
      $info['with_data'] = true;
      $info['year'] = $this->currentYear;
      $info['records'] = [];
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