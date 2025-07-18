<?php

namespace App\Http\Controllers\V2\Audit;

use App\Classes\Process\HandlerProcess;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessAuditRequest;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Catalogs\ProfileType;
use App\Traits\V2\ResponseApiTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ProcessController extends Controller
{
  use ResponseApiTrait;

  /**
   * Redirect to view.
   */
  public function view() 
  {
    return view('v2.process.main');
  }

  
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function source()
  {
    try {
      $data = ProcessAudit::included()->withScopes()->filter()->getPerLevel()->getOrPaginate();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    try {
      $profileType = Session::get('user')['profile_level'];
      $data = ProcessAudit::included()->withIndex()->filter()->specialFilter()->getPerLevel()->getOrPaginate()->toArray();
      $data['show_special_filter'] = $profileType == ProfileType::ADMIN_GLOBAL || $profileType == ProfileType::ADMIN_OPERATIVE;
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\ProcessAuditRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(ProcessAuditRequest $request) 
  {
    try {
      $handlerProcess = new HandlerProcess($request->all());

      $verify = $handlerProcess->verifyData();
      if (!$verify['success']) return $this->errorResponse($verify['message']);

      DB::beginTransaction();

      $storeProcess = $handlerProcess->storeProcess();
      if (!$storeProcess['success']) {
        DB::rollback();
        return $this->errorResponse($storeProcess['message']);
      }

      DB::commit();
      return $this->successResponse($storeProcess['instance'], Response::HTTP_CREATED, $storeProcess['message']);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  
  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function single($id) 
  {
    try {
      $data = ProcessAudit::included()->withScopes()->findOrFail($id);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id) 
  {
    try {
      $data = ProcessAudit::included()->withIndex()->findOrFail($id);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\ProcessAuditRequest  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(ProcessAuditRequest $request, $id) 
  {
    try {
      $handlerProcess = new HandlerProcess($request->all(), $id);

      $verify = $handlerProcess->verifyData();
      if (!$verify['success']) return $this->errorResponse($verify['message']);

      DB::beginTransaction();

      $updateProcess = $handlerProcess->updateProcess();
      if (!$updateProcess['success']) {
        DB::rollback();
        return $this->errorResponse($updateProcess['message']);
      }

      DB::commit();
      return $this->successResponse($updateProcess['instance'], Response::HTTP_OK, $updateProcess['message']);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id) 
  {
    try {
      $process = ProcessAudit::findOrFail($id);
      $status = $process->aplicability_register->contract_matters->pluck('contract_aspects')
        ->collapse()->pluck('id_status')->unique();
      if ( $status->count() > 1 || $status->first() != 3 ) {
        $info['title'] = "La aplicabilidad {$process->audit_processes} ha sido inicida";
        $info['message'] = 'No se puede eliminar el ejercicio ya que esta en proceso';
        return $this->successResponse($process, Response::HTTP_OK, '', $info);
      }
      $process->delete();
      return $this->successResponse($process);
    } catch(\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\ProcessAuditRequest  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function renewal(ProcessAuditRequest $request, $id)
  {
    try {
      $handlerProcess = new HandlerProcess($request->all(), $id, true);
      
      $verify = $handlerProcess->verifyData();
      if (!$verify['success']) return $this->errorResponse($verify['message']);
      
      DB::beginTransaction();

      $renewalProcess = $handlerProcess->renewalProcess();
      if (!$renewalProcess['success']) {
        DB::rollback();
        return $this->errorResponse($renewalProcess['message']);
      }

      DB::commit();
      return $this->successResponse($renewalProcess['instance'], Response::HTTP_OK, $renewalProcess['message']);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  /**
   * Redirect to view.
   */
  public function aplicabilityView($idAuditProcess, $idAplicabilityRegister)
  {
    $data = [
      'id_audit_processes' => $idAuditProcess, 
      'id_aplicability_register' => $idAplicabilityRegister
    ]; 
    return view('v2.aplicability.main', ['data' => $data]);
  }

  /**
   * Redirect to view
   */
  public function aplicabilityEvaluateView($idAuditProcess, $idAplicabilityRegister, $idContractMatter, $idContractAspect)
  {
    $data['id_audit_process'] = $idAuditProcess;
    $data['id_aplicability_register'] = $idAplicabilityRegister;
    $data['id_contract_matter'] = $idContractMatter;
    $data['id_contract_aspect'] = $idContractAspect;
    return view('v2.aplicability.evaluate.main', ['data' => $data]);
  }

  /**
   * Redirect to view.
   */
  public function auditView($idAuditProcess, $idAplicabilityRegister, $idAuditRegister)
  {
    $data = [
      'id_audit_processes' => $idAuditProcess, 
      'id_aplicability_register' => $idAplicabilityRegister, 
      'id_audit_register' => $idAuditRegister
    ]; 
    return view('v2.audit.main', ['data' => $data]);
  }

  /**
   * Redirect to view
   */
  public function auditEvaluateView($idAuditProcess, $idAplicabilityRegister, $idAuditRegister, $idAuditMatter, $idAuditAspect)
  {
    $data['id_audit_process'] = $idAuditProcess;
    $data['id_aplicability_register'] = $idAplicabilityRegister;
    $data['id_audit_register'] = $idAuditRegister;
    $data['id_audit_matter'] = $idAuditMatter;
    $data['id_audit_aspect'] = $idAuditAspect;
    return view('v2.audit.evaluate.main', ['data' => $data]);
  }

  /**
   * Redirect to view.
   */
  public function obligationView($idAuditProcess, $idAplicabilityRegister, $idObligationRegister)
  {
    $data = [
      'id_audit_processes' => $idAuditProcess, 
      'id_aplicability_register' => $idAplicabilityRegister, 
      'id_obligation_register' => $idObligationRegister
    ];
    return view('v2.obligation.main', ['data' => $data]);
  }

  /**
   * Redirect to view.
   */
  public function actionPlanView($idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister)
  {
    $data = [
      'id_audit_processes' => $idAuditProcess, 
      'id_aplicability_register' => $idAplicabilityRegister, 
      'origin' => $section,
      'id_section_register' => $idSectionRegister,
      'id_action_register' => $idActionRegister,
    ];
    return view('v2.action.main', ['data' => $data]);
  }

  /**
   * Redirect to view.
   */
  public function taskView($idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister, $idActionPlan)
  {
    $data = [
      'id_audit_processes' => $idAuditProcess, 
      'id_aplicability_register' => $idAplicabilityRegister, 
      'origin' => $section,
      'id_section_register' => $idSectionRegister,
      'id_action_register' => $idActionRegister,
      'id_action_plan' => $idActionPlan
    ];
    return view('v2.action.task.main', ['data' => $data]);
  }

}
