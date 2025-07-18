<?php

namespace App\Http\Controllers\V2\Dashboard;

use App\Classes\Dashboard\Service\DashboardObligation;
use App\Classes\Dashboard\Service\DashboardAudit;
use App\Classes\Dashboard\Service\DashboardCompliance;
use App\Http\Controllers\Controller;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Audit\ActionPlan;
use App\Models\V2\Audit\AplicabilityRegister;
use App\Models\V2\Audit\Audit;
use App\Models\V2\Admin\Customer;
use App\Models\V2\Admin\User;
use App\Models\V2\Audit\Obligation;
use App\Traits\V2\ResponseApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
  use ResponseApiTrait;

  public function showCustomersView()
  {
    return view('v2.dashboard.main');
  }

  public function showCustomerView($idCustomer)
  {
    return view('v2.dashboard.customer_view', ['id_customer' => $idCustomer]);
  }

  public function showCorporateObligationView($idAuditProcess, $idAplicabilityRegister, $obligationRegisterId)
  {
    return view('v2.dashboard.corporate_obligation_view', [
      'id_audit_process' => $idAuditProcess,
      'id_aplicability_register' => $idAplicabilityRegister,
      'obligation_register_id' => $obligationRegisterId
    ]);
  }

  public function showCorporateAuditView($idAuditProcess, $idAplicabilityRegister, $idAuditRegister)
  {
    return view('v2.dashboard.corporate_audit_view', [
      'id_audit_process' => $idAuditProcess,
      'id_aplicability_register' => $idAplicabilityRegister,
      'id_audit_register' => $idAuditRegister
    ]);
  }
  
  public function showCorporateAllView($idAuditProcess, $idAplicabilityRegister)
  {
    return view('v2.dashboard.corporate_all_view', [
      'id_audit_process' => $idAuditProcess,
      'id_aplicability_register' => $idAplicabilityRegister
    ]);
  }

  public function showCorporateComplianceView($idAuditProcess, $idAplicabilityRegister, $idAuditRegister)
  {
    return view('v2.dashboard.corporate_compliance_view', [
      'id_audit_process' => $idAuditProcess,
      'id_aplicability_register' => $idAplicabilityRegister,
      'id_audit_register' => $idAuditRegister
    ]);
  }
  /**
   * To get customers with process
   */
  public function getCustomers()
  {
    try {
      $corporatesWhitProcess = ProcessAudit::select('id_corporate')->distinct('id_corporate')->pluck('id_corporate')->toArray();
      $allProcess = $this->allProjects($corporatesWhitProcess);
      $allCustomers = $allProcess->pluck('id_customer')->unique();
      $data = Customer::with(['images'])->whereIn('id_customer', $allCustomers)->customFilters()->getOrPaginate();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * To get customers with process
   */
  public function getCorporatesByCustomer()
  {
    try {
      $user = User::find( Auth::id() );
      $corporateWhitProcess = ProcessAudit::where('id_customer', $user['id_customer'])
        ->select('id_corporate')->distinct('id_corporate')->pluck('id_corporate')->toArray();
      $data = $this->allProjects($corporateWhitProcess);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * To get customers with process
   */
  public function getCorporates()
  {
    try {
      $user = User::find( Auth::id() );
      $corporateWhitProcess = ProcessAudit::where('id_corporate', $user['id_corporate'])
        ->select('id_corporate')->distinct('id_corporate')->pluck('id_corporate')->toArray();
      $data = $this->allProjects($corporateWhitProcess);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function getDataObligationCustomer($idCustomer) 
  {
    try {
      $dashboard = new DashboardObligation();
      $data = $dashboard->getDataByYearObligation($idCustomer);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function getDataHistoricalCustomer($idCustomer)
  {
    try {
      $dashboard = new DashboardObligation();
      $data = $dashboard->getDataHistoricalCustomer($idCustomer);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function getDataAuditCustomer($idCustomer)
  {
    try {
      $dashboard = new DashboardAudit();
      $data = $dashboard->getDataByYearAudit($idCustomer);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function getDataAuditHistoricalCustomer($idCustomer)
  {
    try {
      $dashboard = new DashboardAudit();
      $data = $dashboard->getAuditDataHistoricalCustomer($idCustomer);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function getDataComplianceCustomer($idCustomer)
  {
    try {
      $dashboard = new DashboardCompliance();
      $data = $dashboard->getDataByYearCompliance($idCustomer);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function getDataComplianceHistoricalCustomer($idCustomer)
  {
    try {
      $dashboard = new DashboardCompliance();
      $data = $dashboard->getComplianceDataHistoricalCustomer($idCustomer);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function getAllDataCorporate($idAuditProcess, $idAplicabilityRegister) 
  {
    try {
      $filterApplicability = fn($query) => $query->where('id_aplicability_register', $idAplicabilityRegister);
      $record = ProcessAudit::with(['aplicability_register' => $filterApplicability])->noLoadRelationships()
        ->whereHas('aplicability_register', $filterApplicability)
        ->where('id_audit_processes', $idAuditProcess)->first();
      
      $record->current_audit = null;
      if ( sizeof($record->aplicability_register->audit_register) > 0 ) {
        $allAudits = $record->aplicability_register->audit_register;
        $auditRegister = $allAudits->where( 'id_audit_register', $allAudits->max('id_audit_register') )->first();
        $record->current_audit = $auditRegister;
      }
      return $this->successResponse($record);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function getDataObligationCorporate($idAuditProcess, $idAplicabilityRegister)
  {
    try {
      $dashboard = new DashboardObligation();
      $data = $dashboard->getDataByCorporateObligation($idAuditProcess, $idAplicabilityRegister);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function getDataObligationActionCorporate($idAuditProcess, $idAplicabilityRegister)
  {
    try {
      $dashboard = new DashboardObligation();
      $data = $dashboard->getDataObligationActionCorporate($idAuditProcess, $idAplicabilityRegister);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function getDataAuditCorporate($idAuditProcess, $idAplicabilityRegister, $idAuditRegister)
  {
    try {
      $dashboard = new DashboardAudit();
      $data = $dashboard->getDataAuditCorporate($idAuditProcess, $idAplicabilityRegister, $idAuditRegister);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function getDataAuditActionCorporate($idAuditProcess, $idAplicabilityRegister, $idAuditRegister)
  {
    try {
      $dashboard = new DashboardAudit();
      $data = $dashboard->getDataAuditActionCorporate($idAuditProcess, $idAplicabilityRegister, $idAuditRegister);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function getDataComplianceCorporate($idAuditProcess, $idAplicabilityRegister, $idAuditRegister)
  {
    try {
      $dashboard = new DashboardCompliance();
      $data = $dashboard->getDataComplianceCorporate($idAuditProcess, $idAplicabilityRegister, $idAuditRegister);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function getDataComplianceActionCorporate($idAuditProcess, $idAplicabilityRegister, $idAuditRegister)
  {
    try {
      $dashboard = new DashboardCompliance();
      $data = $dashboard->getDataComplianceActionCorporate($idAuditProcess, $idAplicabilityRegister, $idAuditRegister);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function getRecordsDashboardObligation($idAuditProcess, $idAplicabilityRegister, $idObligationRegister) {
    try {
      $verifyParams = $this->verifyParamsObligation($idAuditProcess, $idAplicabilityRegister, $idObligationRegister);
      if (!$verifyParams['success']) {
        return $this->errorResponse($verifyParams['message']);
      }
      $data = Obligation::getRisk()->getRecordsDashboardObligation($idObligationRegister)->customOrder()->getOrPaginate();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function getRecordsDashboardAudit($idAuditProcess, $idAplicabilityRegister, $idAuditRegister) {
    try {
      $verifyParams = $this->verifyParamsAudit($idAuditProcess, $idAplicabilityRegister, $idAuditRegister);
      if (!$verifyParams['success']) {
        return $this->errorResponse($verifyParams['message']);
      }
      $data = Audit::getRisk()->getRecordsDashboardAuditOrCompliance('audit', $idAuditRegister)->customOrder()->getOrPaginate();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function getRecordsDashboardCompliance($idAuditProcess, $idAplicabilityRegister, $idAuditRegister) {
    try {
      $verifyParams = $this->verifyParamsAudit($idAuditProcess, $idAplicabilityRegister, $idAuditRegister);
      if (!$verifyParams['success']) {
        return $this->errorResponse($verifyParams['message']);
      }
      $data = Audit::getRisk()->getRecordsDashboardAuditOrCompliance('compliance', $idAuditRegister)->customOrder()->getOrPaginate();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function getRecordsDashboardActionPlan($idAuditProcess, $idAplicabilityRegister, $sectionName, $idSectionRegister, $idActionRegister)
  {
    try {
      $verifyParams = $this->verifyParamsAction($idAuditProcess, $idAplicabilityRegister, $sectionName, $idSectionRegister, $idActionRegister);
      if (!$verifyParams['success']) {
        return $this->errorResponse($verifyParams['message']);
      }
      $data = ActionPlan::getRisk()->getRecordsDashboardActionPlan($idActionRegister)->onlyUsables($idActionRegister)->customOrder()->getOrPaginate();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  private function verifyParamsAudit($idAuditProcess, $idAplicabilityRegister, $idAuditRegister)
  {
    try {
      $aplicabilityRegister = AplicabilityRegister::find($idAplicabilityRegister);
      $sameProcess = $aplicabilityRegister->id_audit_processes == $idAuditProcess;
      $auditRegister = $aplicabilityRegister->audit_register->firstWhere('id_audit_register', $idAuditRegister);
      if (!$sameProcess || is_null($auditRegister)) {
        $data['success'] = false;
        $data['message'] = 'Los parametros especificados son incorrectos';
        return $data;
      }

      $data['success'] = true;
      $data['message'] = 'Verificacion correcta';
      return $data;
    } catch (\Throwable $th) {
      $data['success'] = false;
      $data['message'] = 'Hubo un error al verificar peramenttros';
      return $data;
    }
  }

  private function verifyParamsObligation($idAuditProcess, $idAplicabilityRegister, $idObligationRegister)
  {
    try {
      $aplicabilityRegister = AplicabilityRegister::find($idAplicabilityRegister);
      $sameProcess = $aplicabilityRegister->id_audit_processes == $idAuditProcess;
      $hasIdObligationRegister = $aplicabilityRegister->obligation_register->id ?? null;
      $sameObligation = $hasIdObligationRegister == $idObligationRegister;
      if (!$sameProcess || !$sameObligation) {
        $data['success'] = false;
        $data['message'] = 'Los parametros especificados son incorrectos';
        return $data;
      }

      $data['success'] = true;
      $data['message'] = 'Verificacion correcta';
      return $data;
    } catch (\Throwable $th) {
      $data['success'] = false;
      $data['message'] = 'Hubo un error al verificar peramenttros';
      return $data;
    }
  }

  private function verifyParamsAction($idAuditProcess, $idAplicabilityRegister, $sectionName, $idSectionRegister, $idActionRegister)
  {

    try {
      $aplicabilityRegister = AplicabilityRegister::find($idAplicabilityRegister);

      if ($sectionName == 'audit' || $sectionName == 'compliance') {
        $sameProcess = $aplicabilityRegister->id_audit_processes == $idAuditProcess;
        $auditRegister = $aplicabilityRegister->audit_register->firstWhere('id_audit_register', $idSectionRegister);
        $queryIdActionRegister = $auditRegister->action_plan_register->id_action_register ?? null;
        $sameActionRegister = $queryIdActionRegister == $idActionRegister;
        $validate = !$sameProcess || is_null($auditRegister) || !$sameActionRegister;
      }

      if ($sectionName == 'obligation') {
        $sameProcess = $aplicabilityRegister->id_audit_processes == $idAuditProcess;
        $hasIdObligationRegister = $aplicabilityRegister->obligation_register->id ?? null;
        $sameObligation = $hasIdObligationRegister == $idSectionRegister;
        $queryIdActionRegister = $aplicabilityRegister->obligation_register->action_plan_register->id_action_register ?? null;
        $sameActionRegister = $queryIdActionRegister == $idActionRegister;
        $validate = !$sameProcess || !$sameObligation || !$sameActionRegister;
      }

      if ($validate) {
        $data['success'] = false;
        $data['message'] = 'Los parametros especificados son incorrectos';
        return $data;
      }

      $data['success'] = true;
      $data['message'] = 'Verificacion correcta';
      return $data;
    } catch (\Throwable $th) {
      $data['success'] = false;
      $data['message'] = 'Hubo un error al verificar peramenttros';
      return $data;
    }
  }

  private function allProjects($arrayId) 
  {
    $completeProjects = ProcessAudit::getProcessKpiWithSomeSectionActive($arrayId)->get();

    $corporates = $completeProjects->pluck('id_corporate')->unique()->values();

    // Get last process per corporate
    $projects = $corporates->map(function($idCorporate) use ($completeProjects) {
      $collection = $completeProjects->where('id_corporate', $idCorporate)->pluck('aplicability_register');
      $aplicabilityRegister = $collection->where( 'id_aplicability_register', $collection->max('id_aplicability_register') )->first();
      return $completeProjects->firstWhere('aplicability_register.id_aplicability_register', $aplicabilityRegister->id_aplicability_register); 
    })->values();
    
    return $projects;
  }
}
