<?php

namespace App\Http\Controllers\V2\Audit;

use App\Classes\ActionPlan\CreateActionPlan;
use App\Classes\ActionPlan\Report\BuildDataReportActionPlan;
use App\Exports\ActionPlan\ActionPlanExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ActionRegisterRequest;
use App\Models\V2\Audit\ActionPlanRegister;
use App\Traits\V2\ResponseApiTrait;
use App\Traits\V2\ActionPlanTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ActionRegisterController extends Controller
{
  use ResponseApiTrait, ActionPlanTrait;

  /**
   * Store the specified resource in storage.
   *
   * @param  \App\Http\Requests\ActionRegisterRequest  $request
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  string  $section
   * @param  int  $idSectionRegister
   * @return \Illuminate\Http\Response
   * 
   * Params used in middleware route
   */
  public function store(ActionRegisterRequest $request, $idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister) 
  {
    try {
      DB::beginTransaction();
      $registerableId = $idSectionRegister;
      $initDate = $request->input('init_date');
      $endDate = $request->input('end_date');
      $type = $section;
      $action = new CreateActionPlan($registerableId, $initDate, $endDate, $type);
      $initAction = $action->initActionRegister();
      if ( !$initAction['status'] ) {
        DB::rollback();
        return $this->errorResponse($initAction['message']);
      }
      DB::commit();
      return $this->successResponse($request, Response::HTTP_CREATED, 'Plan de Acción Creado');
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  string  $section
   * @param  int  $idSectionRegister
   * @return \Illuminate\Http\Response
   * 
   * Params used in middleware route
   */
  public function show($idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $id) 
  {
    try {
      $data = ActionPlanRegister::included()->findOrFail($id);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Display the specified resource.
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
  public function reportPlan($idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister) 
  {
    try {
      $filtersQuery = request('filters');
      ini_set('max_execution_time', 18000);
      ini_set('memory_limit', '-1');

      $report = new BuildDataReportActionPlan($idActionRegister, $filtersQuery);
      $data = $report->getDataReport();
      
      if (!$data['success']) {
        return $this->errorResponse($data['message']);
      }

      $documentName = "Reporte Estatus Plan de Acción {$report->originName} - {$report->corporateName}.xlsx";
      return Excel::download(new ActionPlanExport($data), $documentName);
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}