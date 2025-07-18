<?php

namespace App\Classes\ActionPlan;

use App\Classes\ActionPlan\CreateActionPlanList;
use App\Models\V2\Audit\ObligationRegister;
use App\Models\V2\Audit\Obligation;
use App\Models\V2\Audit\EvaluateRequirement;
use Carbon\Carbon;

class CreateEvaluateObligationAP
{
  public $model; 

  public function __construct($model)
  {
    $this->model = $model;
  }

  public function createRecordAP()
  {
    $status = $this->model->id_status;
    $statusForActionPlan = collect([
      Obligation::FOR_EXPIRED_OBLIGATION,
      Obligation::EXPIRED_OBLIGATION,
      Obligation::NO_EVIDENCE_OBLIGATION,
    ]);
    // create in action plan
    $createRecordInAP = $statusForActionPlan->contains($status);
    if ( !$createRecordInAP ) {
      $data['success'] = false;
      $data['message'] = 'El estaus no es "FOR_EXPIRED_OBLIGATION, EXPIRED_OBLIGATION o NO_EVIDENCE_OBLIGATION"';
      return $data;
    }
    // find or create AP register
    $relationships = ['aplicability_register', 'action_plan_register'];
    $parent = ObligationRegister::with($relationships)->find($this->model->obligation_register_id);
    // evaluate risk
    $process = $parent->aplicability_register->process()->first();
    $riskTotals = $this->model->risk_totals()->get();
    if ( boolval($process->evaluate_risk) && $riskTotals->count() < 3 ) {
      $data['success'] = true;
      $data['message'] = 'Registro exitoso';
      $data['info'] = [
        'title' => 'Evaluar requerimiento',
        'message' => 'Por favor no olvides evaluar Nivel de Riesgo, una vez evaluado a final del día pasará a Plan de Acción',
      ];
      return $data;
    }
    // evaluate AP register
    $actionRegister = $parent->action_plan_register;
    if ( is_null($actionRegister) ) {
      // get today
      $timezone = Config('enviroment.time_zone_carbon');
      $today = Carbon::now($timezone)->toDateTimeString();
      // create action plan
      $actionRegister = $parent->action_plan_register()->create([
        'init_date' => Carbon::createFromFormat('Y-m-d H:i:s', $today, $timezone)->toDateTimeString(),
        'end_date' => Carbon::createFromFormat('Y-m-d H:i:s', $today, $timezone)->addMonths(12)->toDateTimeString(),
        'id_corporate' => $parent->aplicability_register->id_corporate,
        'id_audit_processes' => $parent->aplicability_register->id_audit_processes,
        'id_status' => 1,
      ]);
    }
    // search model in action plan
    $recordAP = $actionRegister->action_plans->filter(function($item) {
      $sameReq = $item->id_requirement == $this->model->id_requirement;
      $sameSub = $item->id_subrequirement == $this->model->id_subrequirement;
      return $sameReq && $sameSub;
    })->first();
    // create record in action plan 
    $allowAlert = is_null($recordAP); 
    if ( is_null($recordAP) ) {
      // create structure action plan 
      $createRecordAP = new CreateActionPlanList( $actionRegister, $parent->aplicability_register, collect([ $this->model ]) );
      $createdRecords = $createRecordAP->createRecordOfObligations();
      $mainTask = $createdRecords['records']->pluck('tasks')->collapse()->filter(fn($item) => $item->main_task == 1)->first();
      $idAplicabilityRegister = $parent->id_aplicability_register;
      $evaluate = EvaluateRequirement::where('aplicability_register_id', $parent->id_aplicability_register)
        ->where('id_requirement', $this->model->id_requirement)
        ->where('id_subrequirement', $this->model->id_subrequirement)->first();
      $mainTask->evaluates()->sync($evaluate->id_evaluate_requirement);
    }
    $data['success'] = true;
    $data['message'] = 'Registro exitoso';
    if ( $allowAlert ) {
      $data['info'] = [
        'title' => 'Registro En Plan de Acción',
        'message' => 'Este requerimiento ha pasado a Plan de Acción',
      ];
    }
    return $data;
  }
}