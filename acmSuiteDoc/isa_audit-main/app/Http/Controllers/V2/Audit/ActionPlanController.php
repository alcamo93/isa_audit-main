<?php

namespace App\Http\Controllers\V2\Audit;

use App\Classes\Utilities\MattersSection;
use App\Http\Controllers\Controller;
use App\Http\Requests\ActionPlanRequest;
use App\Models\V2\Audit\ActionPlan;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Audit\Task;
use App\Models\V2\Catalogs\Scope;
use App\Traits\V2\ActionPlanTrait;
use App\Traits\V2\ResponseApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ActionPlanController extends Controller
{
  use ResponseApiTrait, ActionPlanTrait;

  /**
   * Display a listing of the resource.
   *
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  string  $section
   * @param  int  $idSectionRegister
   * @param  int  $idActionRegister
   * @return \Illuminate\Http\Response
   * 
   * Params used in middleware route
   */
  public function index($idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister)
  {
    $relationships = ['customer', 'corporate', 'scope'];
    $process = ProcessAudit::with($relationships)->find($idAuditProcess);
    $records = ActionPlan::included()->filter()->withIndex()->customFilters()->validateMainTask()
      ->getRisk()->onlyUsables($idActionRegister)->customFilterMain()
      ->customOrder()->getOrPaginate();

    $data = $records->toArray();
    $data['info']['status'] = $this->getTotalsByStatus($idActionRegister)->toArray();
    $data['info']['audit_process'] = $process->audit_processes;
    $data['info']['customer_name'] = $process->customer->cust_tradename;
    $data['info']['corporate_name'] = $process->corporate->corp_tradename;
    $data['info']['scope'] = $process->id_scope === Scope::CORPORATE ? $process->scope->scope : "{$process->scope->scope}: {$process->specification_scope}";
    $data['info']['matters'] = ( new MattersSection('action', $idActionRegister) )->getMatters();

    return $this->successResponse($data);
  }

  // /**
  //  * Display a listing of the resource.
  //  *
  //  * @param  int  $idAuditProcess
  //  * @param  int  $idAplicabilityRegister
  //  * @param  string  $section
  //  * @param  int  $idSectionRegister
  //  * @param  int  $idActionRegister
  //  * @return \Illuminate\Http\Response
  //  * 
  //  * Params used in middleware route
  //  */
  // public function indexExpired($idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister)
  // {
  //   $data = ActionPlan::included()->withExpiredIndex()->filter()->customFilters()->validateMainTask()
  //     ->getRisk()->onlyUsablesWithExpired($idActionRegister)->customFilterExpired()
  //     ->customOrder()->getOrPaginate();

  //   return $this->successResponse($data);
  // }

  /**
   * Display the specified resource.
   *
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  string  $section
   * @param  int  $idSectionRegister
   * @param  int  $idActionRegister
   * @param  int  $idActionPlan
   * @return \Illuminate\Http\Response
   * 
   * Params used in middleware route
   */
  public function show($idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister, $idActionPlan)
  {
    $data = ActionPlan::included()->withShow()->find($idActionPlan);
    return $this->successResponse($data);
  }

  /**
   * update the specified resource from storage.
   *
   * @param  \App\Http\Requests\ActionPlanRequest  $request
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  string  $section
   * @param  int  $idSectionRegister
   * @param  int  $idActionRegister
   * @param  int  $idActionPlan
   * @return \Illuminate\Http\Response
   * 
   * Params used in middleware route
   */
  public function priority(ActionPlanRequest $request, $idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister, $idActionPlan)
  {
    try {
      $requestData = Arr::only($request->all(), ['id_priority']);
      $data = ActionPlan::find($idActionPlan);
      $data->update($requestData);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * update the specified resource from storage.
   *
   * @param  \App\Http\Requests\ActionPlanRequest  $request
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  string  $section
   * @param  int  $idSectionRegister
   * @param  int  $idActionRegister
   * @param  int  $idActionPlan
   * @return \Illuminate\Http\Response
   * 
   * Params used in middleware route
   */
  public function user(ActionPlanRequest $request, $idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister, $idActionPlan)
  {
    try {
      $data = ActionPlan::with('tasks')->included()->find($idActionPlan);

      if ($data->id_status == ActionPlan::EXPIRED_AP) {
        return $this->errorResponse('Este requerimiento está en estatus Vencido, agrega una causa para extender su fecha y modificar el responsable');
      }

      DB::beginTransaction();

      $requestData = $request->all();
      $auditors = collect($requestData);
      $users = $auditors->pluck('id_user');
      $thereAreRepet = $users->count() !== $users->unique()->count();
      $maxAuditors = $users->count() <= 3;
      if ($thereAreRepet && $maxAuditors) {
        return $this->errorResponse('No puede haber más de 3 auditores o auditores repetidos');
      }
      $auditorsSync = [];
      foreach ($requestData as $item) {
        $auditorsSync[intval($item['id_user'])] = [
          'level' => intval($item['level']),
          'days' => intval($item['days'])
        ];
      }
      $data->auditors()->sync($auditorsSync);
      $data->fresh();

      DB::commit();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  /**
   * update the specified resource from storage.
   *
   * @param  \App\Http\Requests\ActionPlanRequest  $request
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  string  $section
   * @param  int  $idSectionRegister
   * @param  int  $idActionRegister
   * @param  int  $idActionPlan
   * @return \Illuminate\Http\Response
   * 
   * Params used in middleware route
   */
  public function expired(ActionPlanRequest $request, $idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister, $idActionPlan)
  {
    try {
      $requestData = $request->all();
      $data = ActionPlan::with('tasks')->find($idActionPlan);
      $requestData['original_date'] = $data->close_date;
      DB::beginTransaction();

      if ($data->id_status != ActionPlan::EXPIRED_AP) {
        return $this->errorResponse('Este requerimiento debe estar como Vencido para extender su fecha');
      }
      // set extension date
      $data->update([
        'close_date' => $requestData['extension_date'],
        'real_close_date' => $requestData['extension_date'],
        'id_status' => ActionPlan::CLOSED_AP,
      ]);
      // Create record in Exprired 
      $data->expired()->create($requestData);

      DB::commit();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }
}
