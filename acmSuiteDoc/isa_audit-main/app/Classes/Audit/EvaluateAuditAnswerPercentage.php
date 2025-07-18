<?php

namespace App\Classes\Audit;

use App\Models\V2\Audit\Audit;
use App\Models\V2\Audit\AuditAspect;
use App\Models\V2\Audit\AuditMatter;
use App\Models\V2\Audit\AuditRegister;
use App\Models\V2\Audit\EvaluateAuditRequirement;
use Illuminate\Support\Arr;

class EvaluateAuditAnswerPercentage
{

  public function __construct() 
  {
    
  }

  public function calculateAspectLevel($id) 
  {
    try {
      $auditAspect = AuditAspect::findOrFail($id);

      $currentStatus = $auditAspect->id_status;
      if ($currentStatus == Audit::FINISHED_AUDIT_AUDIT) {
        $labelStatus = $currentStatus->status->status;
        $info['success'] = true;
        $info['message'] = "El aspecto tiene estatus {$labelStatus}, no se volverá a calcular porcentages";
        return $info;
      }
      
      $relationships = ['audit'];
      $allEvaluates = EvaluateAuditRequirement::with($relationships)->where('id_audit_aspect', $id)->get();
      $allAnswers = $allEvaluates->pluck('audit')->filter(fn($item) => !is_null($item))->values();
      $evaluates = $allEvaluates->whereNull('id_subrequirement');
      $answers = $evaluates->pluck('audit')->filter(fn($item) => !is_null($item))->values();
      
      $aspect['total'] = $evaluates->where('audit.answer', '!=', Audit::NO_APPLY)->count();
      $aspect['negatives'] = $answers->where('answer', Audit::NEGATIVE)->count();
      $aspect['affirmatives'] = $answers->where('answer', Audit::AFFIRMATIVE)->count();
      
      $percent = $aspect['affirmatives'] != 0 ? ($aspect['affirmatives'] / $aspect['total']) * 100 : 0;
      $aspect['percentAspect'] = round($percent, 2);
      $dataUpdate = [ 'total' => $aspect['percentAspect'] ];

      $canModifyStatus = $currentStatus != Audit::AUDITED_AUDIT;
      $evaluatesIncomplete = $allEvaluates->pluck('complete')->contains(0);
      if ($canModifyStatus || $evaluatesIncomplete) {
        $idStatus = ($allAnswers->count() > 0) ? Audit::AUDITING_AUDIT : Audit::NOT_AUDITED_AUDIT;
        $dataUpdate = Arr::add($dataUpdate, 'id_status', $idStatus);
      } 
      
      $auditAspect->update($dataUpdate);
      
      $matterCalculate = $this->calculateMatterLevel($auditAspect->id_audit_matter);

      return $matterCalculate;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Error al calcular el avance de cumplimiento a nivel Aspecto';
      return $info;
    }
  }

  private function calculateMatterLevel($id) 
  {
    try {
      $allAspects = AuditAspect::where('id_audit_matter', $id)->get();
      $matter['totalSumAspects'] = $allAspects->sum('total');
      $matter['totalAspects'] = $allAspects->count();
      $percent = $matter['totalSumAspects'] / $matter['totalAspects'];
      $matter['percentMatter'] = round($percent , 2);
      
      $updateMatter = AuditMatter::findOrFail($id);
      $updateMatter->update([ 'total' => $matter['percentMatter'] ]);

      $updateStatusMatter = $this->updateStatusMatter($id);
      if ( !$updateStatusMatter['success'] ) {
        return $updateStatusMatter;
      }

      $globalCalculate = $this->calculateGlobalLevel($updateMatter->id_audit_register);

      return $globalCalculate;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Error al calcular el avance de cumplimiento a nivel Materia';
      return $info;
    }
  }

  private function calculateGlobalLevel($id)
  {
    try {
      $allMatters = AuditMatter::where('id_audit_register', $id)->get();
        
      $global['totalSumMatter'] = $allMatters->sum('total');
      $global['totalMatter'] = $allMatters->count();
      $percent = $global['totalSumMatter'] / $global['totalMatter'];
      $global['percentGlobal'] = round($percent , 2);

      $updateAuditRegister = AuditRegister::findOrFail($id);
      $updateAuditRegister->update([ 'total' => $global['percentGlobal'] ]);

      $updateStatusGlobal = $this->updateStatusAuditRegisters($id);
      if ( !$updateStatusGlobal['success'] ) {
        return $updateStatusGlobal;
      }

      $info['success'] = true;
      $info['message'] = 'Registro exitoso';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Error al calcular el avance de cumplimiento a nivel Global';
      return $info;
    }
  }

  private function updateStatusMatter($idAuditMatter)
  {
    try {
      $auditMatter = AuditMatter::with('matter', 'audit_aspects')->find($idAuditMatter);
      $allAspects = $auditMatter->audit_aspects;
      $data['total'] = $allAspects->count();
      $data['notAudited'] = $allAspects->where('id_status', Audit::NOT_AUDITED_AUDIT)->count();
      $data['audited'] = $allAspects->where('id_status', Audit::AUDITED_AUDIT)->count();
      $data['auditing'] = $allAspects->where('id_status', Audit::AUDITING_AUDIT)->count();
      $data['finished'] = $allAspects->where('id_status', Audit::FINISHED_AUDIT_AUDIT)->count();
      $data['compliance'] = $auditMatter->total;

      if ( ($data['total'] == 0) || ($data['total'] == $data['notAudited'] ) ) {
          $data['statusMatter'] = 'Sin auditar';
          $currentStatus = Audit::NOT_AUDITED_AUDIT;
      }
      elseif (  ( $data['audited'] > 0 ) && ( ( $data['audited']+$data['finished'] ) == $data['total'] ) ) {
        $currentStatus = Audit::AUDITED_AUDIT;
        $data['statusMatter'] = 'Auditado';
      }
      elseif ($data['total'] == $data['finished']) {
        $data['audited'] = $data['total'];
        $data['statusMatter'] = 'Finalizado';
        $currentStatus = Audit::FINISHED_AUDIT_AUDIT;
      }
      elseif ($data['total'] != $data['finished']) {
        // Update status to contract matter
        if ( ( $data['total'] == $data['audited'] ) && ( $data['audited'] > 0 ) ){
          $currentStatus = Audit::AUDITED_AUDIT;
          $data['statusMatter'] = 'Auditado';
        }
        elseif ( ($data['audited'] > 0) || ($data['auditing'] > 0) || ($data['notAudited'] > 0)){
          $currentStatus = Audit::AUDITING_AUDIT;
          $data['statusMatter'] = 'Evaluando';
        }
        elseif ($data['audited'] == 0 && $data['auditing'] == 0){
          $data['statusMatter'] = 'Sin auditar';
          $currentStatus = Audit::NOT_AUDITED_AUDIT;
        }
      }
      $auditMatter->update(['id_status' => $currentStatus]);

      $info['success'] = true;
      $info['message'] = 'Registro exitoso';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Error al establcer el estatus a nivel Materia';
      return $info;
    }
  }

  private function updateStatusAuditRegisters($id)
  {
    try {
      $all = AuditMatter::where('id_audit_register', $id)->get();
      $data['total'] = $all->count();
      $data['audited'] = $all->where('id_status', Audit::AUDITED_AUDIT)->count();
      $data['notAudited'] = $all->where('id_status', Audit::NOT_AUDITED_AUDIT)->count();
      $data['auditing'] = $all->where('id_status', Audit::AUDITING_AUDIT)->count();
      $data['finished'] = $all->where('id_status', Audit::FINISHED_AUDIT_AUDIT)->count();

      // Update status to contract matter
      if (  ( $data['audited'] > 0 ) && ( ( $data['audited']+$data['finished'] ) == $data['total'] ) ) {
        $currentStatus = Audit::AUDITED_AUDIT;
        $data['statusMatter'] = 'Auditado';
      }
      else {
        if ( $data['total'] == $data['finished'] ){
          $currentStatus = Audit::FINISHED_AUDIT_AUDIT;
          $data['statusAudit'] = 'Finalizado';
        }
        elseif ( ( $data['audited'] > 0 ) && ( $data['total'] == $data['audited'] ) ){
          $currentStatus = Audit::AUDITED_AUDIT;
          $data['statusAudit'] = 'Auditado';
        }
        elseif ( ($data['audited'] > 0) || ($data['auditing'] > 0) || ($data['notAudited'] > 0) ){
          $currentStatus = Audit::AUDITING_AUDIT;
          $data['statusAudit'] = 'Auditando';
        }
        elseif ($data['audited'] == 0 && $data['auditing'] == 0){
          $currentStatus = Audit::NOT_AUDITED_AUDIT;
          $data['statusAudit'] = 'Sin auditar';
        }
      }
      $updateAuditRegister = AuditRegister::findOrFail($id);
      $updateAuditRegister->update(['id_status' => $currentStatus]);

      $info['success'] = true;
      $info['message'] = 'Registro exitoso';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Error al establcer el estatus a nivel Materia';
      return $info;
    }
  }
}