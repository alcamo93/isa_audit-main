<?php

namespace App\Http\Controllers\V2\Audit;

use App\Classes\ActionPlan\CreateEvaluateObligationAP;
use App\Classes\Utilities\MattersSection;
use App\Http\Controllers\Controller;
use App\Http\Requests\ObligationRequest;
use App\Traits\V2\ResponseApiTrait;
use App\Models\V2\Audit\AuditorObligation;
use App\Models\V2\Audit\Obligation;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Catalogs\Scope;
use App\Models\V2\Catalogs\Status;
use App\Traits\V2\ObligationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ObligationController extends Controller
{
  use ResponseApiTrait, ObligationTrait;

  /**
   * Display a listing of the resource.
   *
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  int  $idObligationRegister
   * @return \Illuminate\Http\Response
   * 
   * * Params used in Middleware route
   */
  public function index($idAuditProcess, $idAplicabilityRegister, $idObligationRegister)
  {
    $relationships = ['customer', 'corporate', 'scope'];
    $process = ProcessAudit::with($relationships)->find($idAuditProcess);
    $data = Obligation::included()->withIndex()->getRisk()->filter()->customFilter($idObligationRegister)->customOrder()->getOrPaginate()->toArray();

    $data['info']['status'] = Status::where('group', Status::OBLIGATION_GROUP)->get()->toArray();
    $data['info']['audit_process'] = $process->audit_processes;
    $data['info']['customer_name'] = $process->customer->cust_tradename;
    $data['info']['corporate_name'] = $process->corporate->corp_tradename;
    $data['info']['scope'] = $process->id_scope === Scope::CORPORATE ? $process->scope->scope : "{$process->scope->scope}: {$process->specification_scope}";
    $data['info']['evaluate_risk'] = boolval($process->evaluate_risk);
    $data['info']['matters'] = ( new MattersSection('obligation', $idObligationRegister) )->getMatters();

    return $this->successResponse($data);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  int  $idObligationRegister
   * @param  int  $idObligation  
   * @return \Illuminate\Http\Response
   * 
   * Params used in Middleware route
   */
  public function show($idAuditProcess, $idAplicabilityRegister, $idObligationRegister, $idObligation) 
  {
    try {
      $data = Obligation::included()->getRisk()->findOrFail($idObligation);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Requests\ObligationRequest  $request
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  int  $idObligationRegister
   * @param  int  $idObligation     
   * @return \Illuminate\Http\Response
   * 
   * Params used in Middleware route
   */
  public function handlerUser(ObligationRequest $request, $idAuditProcess, $idAplicabilityRegister, $idObligationRegister, $idObligation) 
  {
    try {
      $obligation = Obligation::findOrFail($idObligation);

      DB::beginTransaction();
      $idUser = $request->input('id_user');
      $this->setUserObligation($obligation, $idUser);
      $obligationUpdate = $obligation->fresh();
      DB::commit();

      return $this->successResponse($obligationUpdate);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  /**
   * Remove the specified resource from storage.
   * 
   * @param  App\Http\Requests\ObligationRequest  $request
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  int  $idObligationRegister
   * @param  int  $idObligation     
   * @return \Illuminate\Http\Response
   * 
   * Params used in Middleware route
   */
  public function hasFile(ObligationRequest $request, $idAuditProcess, $idAplicabilityRegister, $idObligationRegister, $idObligation) 
  {
    try {
      $obligation = Obligation::findOrFail($idObligation);

      DB::beginTransaction();
      $obligation->update([ 'id_status' => Obligation::NO_EVIDENCE_OBLIGATION ]);
      $idUser = $request->input('id_user');
      $this->setUserObligation($obligation, $idUser);
      $obligationUpdate = $obligation->fresh();
      $this->setStatusObligation($obligationUpdate, $obligationUpdate->library);
      $forActionPlan = new CreateEvaluateObligationAP($obligationUpdate);
      $createAP = $forActionPlan->createRecordAP();
      $info = $createAP['info'] ?? null;
      DB::commit();

      return $this->successResponse($obligationUpdate, 200, $createAP['message'], $info);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  private function setUserObligation($obligation, $idUser = null)
  {
    $user = is_null($idUser) ? Auth::id() : $idUser;
    // find
    $find['level'] = 1;
    $find['id_obligation'] = $obligation->id_obligation;
    // data
    $requestData['level'] = 1;
    $requestData['id_obligation'] = $obligation->id_obligation;
    $requestData['id_user'] = $user;
    AuditorObligation::updateOrCreate($find, $requestData);      
  }
}
