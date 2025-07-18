<?php

namespace App\Classes\ActionPlan;

use App\Classes\ActionPlan\CreateActionPlanList;
use App\Models\V2\Audit\AuditRegister;
use App\Models\V2\Audit\ObligationRegister;
use App\Models\V2\Audit\Audit;
use App\Models\V2\Audit\Obligation;
use Illuminate\Support\Facades\DB;

class CreateActionPlan
{
  public $registerableId = null;
  public $initDate = null;
  public $endDate = null;
  public $type = null;
  public $actionRegister = null;
  public $parent = null;

  public function __construct($registerableId, $initDate, $endDate, $type){
    $this->registerableId = $registerableId;
    $this->initDate = $initDate;
    $this->endDate = $endDate;
    $this->type = $type;
    $this->parent = $this->generateParent();
  }

  public function generateParent() 
  {
    if ($this->type == 'audit') {
      $relationshipsAudit = [
        'aplicability_register.audit_register.audit_matters.audit_aspects',
        'aplicability_register.audit_register.action_plan_register',
      ];
      $parent = AuditRegister::with($relationshipsAudit)->findOrFail($this->registerableId);
      return $parent;
    }

    if ($this->type == 'obligation') {
      $relationshipsObligation = [
        'aplicability_register.obligation_register.obligations',
        'aplicability_register.obligation_register.action_plan_register',
      ];
      $parent = ObligationRegister::with($relationshipsObligation)->findOrFail($this->registerableId);
      return $parent;
    }
  }
  
  public function initActionRegister() 
  {
    try {
      DB::beginTransaction();
      $aplicabilityRegister = $this->parent->aplicability_register;
      
      if ( !is_null($this->parent->action_plan_register) ) {
        $data['status'] = false;
        $data['message'] = 'Ya se tiene un Plan de Acción creado';
        return $data;
      }

      $this->actionRegister = $this->parent->action_plan_register()->create([
        'init_date' => $this->initDate,
        'end_date' => $this->endDate,
        'id_corporate' => $aplicabilityRegister->id_corporate,
        'id_audit_processes' => $aplicabilityRegister->id_audit_processes,
        'id_status' => 1,
      ]);
      
      if ($this->type == 'audit') {
        $create = $this->createRecordsFromAudit();
        $data['status'] = $create['success'];
        $data['message'] = $create['message'];
        if ($create['success']) {
          DB::commit();
          return $data;
        }
        DB::rollback();
        return $data;
      }
  
      if ($this->type == 'obligation') {
        $create = $this->createRecordsFromObligation();
        $data['status'] = $create['success'];
        $data['message'] = $create['message'];
        if ($create['success']) {
          DB::commit();
          return $data;
        }
        DB::rollback();
        return $data;
      }

    } catch (\Throwable $th) {
      DB::rollback();
      $data['status'] = false;
      $data['message'] = 'Error general en registro de Plan de Acción';
      return $data;
    }
  }

  public function createRecordsFromAudit()
  {
    try {
      $allAuditAspect = $this->parent->audit_matters->map(function($matter) {
        return $matter->audit_aspects->map(fn($aspect) => $aspect->id_audit_aspect);
      })->collapse()->toArray();

      $noComplies = 0;
      $allNoComplice = Audit::with(['requirement', 'subrequirement'])->getRisk()
        ->whereIn('id_audit_aspect', $allAuditAspect)
        ->where('answer', $noComplies)->get();

      // create structure action plan 
      $createRecordAP = new CreateActionPlanList( $this->actionRegister, $this->parent->aplicability_register, $allNoComplice );
      $createdRecords = $createRecordAP->createRecordOfAudits();

      return $createdRecords;
    } catch (\Throwable $th) {
      $data['success'] = false;
      $data['message'] = 'Error general en registros de Plan de Acción de Auditoría';
      return $data;
    }
  }

  public function createRecordsFromObligation() 
  {
    try {
      $status = [Obligation::FOR_EXPIRED_OBLIGATION, Obligation::EXPIRED_OBLIGATION, Obligation::NO_EVIDENCE_OBLIGATION];
      $this->parent->aplicability_register->obligation_register->obligations->load(['risk_totals', 'risk_answers']);
      $allAuditAspect = $this->parent->aplicability_register->obligation_register->obligations;
      $recordForPlan = $allAuditAspect->whereIn('id_status', $status);

      // create structure action plan 
      $createRecordAP = new CreateActionPlanList( $this->actionRegister, $this->parent->aplicability_register, $recordForPlan );
      $createdRecords = $createRecordAP->createRecordOfObligations();
      
      return $createdRecords;
    } catch (\Throwable $th) {
      $data['success'] = false;
      $data['message'] = 'Error general en registros de Plan de Acción de Obligaciones';
      return $data;
    }
  }
}