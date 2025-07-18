<?php

namespace App\Classes\Audit;

use Illuminate\Support\Facades\Auth;
use App\Models\V2\Audit\EvaluateAuditRequirement;
use App\Models\V2\Audit\AuditRegister;
use App\Models\V2\Audit\Audit;
use App\Models\V2\Audit\RiskTotal;
use App\Models\V2\Audit\RiskAnswer;
use App\Classes\Audit\VerifyAuditAnswer;

class EvaluateAuditAnswer
{
  public $record = null;
  public $answer = null;
  public $recursive = false;
  public $auditRegister = null;
  public $evaluateRisk = false;
  public $isEvaluateParent = false;
  public $idAuditAspect = null;

  public function __construct($idAuditRegister, $idEvaluateAudit, $answer, $recursive = false) 
  {
    $relationships = ['aplicability_register.process'];
    $this->auditRegister = AuditRegister::with($relationships)->findOrFail($idAuditRegister);
    $this->evaluateRisk = boolval($this->auditRegister->aplicability_register->process->evaluate_risk);
    $this->record = EvaluateAuditRequirement::findOrFail($idEvaluateAudit);
    $this->idAuditAspect = $this->record->id_audit_aspect;
    $this->answer = intval($answer);
    $this->recursive = $recursive;
    $this->isEvaluateParent = boolval($this->record->has_subrequirement);
  }

  public function setAnswer() 
  {
    if ($this->isEvaluateParent) {
      $info['success'] = false;
      $info['message'] = 'No es posible establecer respuesta, ya que se basa en las respuestas de todos los Subrequerimientos';
      return $info;
    }
    if ( $this->recursive ) {
      $setMultiple = $this->setRecursiveAnswer($this->record, $this->answer);
      if ( !$setMultiple['success'] ) {
        return $setMultiple;
      }
    } else {
      $set = $this->setOnlyAnswer($this->record, $this->answer);
      if ( !$set['success'] ) {
        return $set;
      }
    }
    // verify answer
    $verify = new VerifyAuditAnswer($this->record->id, $this->evaluateRisk);
    $handlerVerify = $verify->verifyAnswer();
    if ( !$handlerVerify['success'] ) {
      return $handlerVerify;
    }

    $info['success'] = true;
    $info['message'] = 'Registro exitoso';
    return $info;
  }

  private function setOnlyAnswer($evaluateInstance, $answer) {
    try {
      $handlerAnswer = $this->handlerAnswer($evaluateInstance, $answer);
      if ( !$handlerAnswer['success'] ) {
        return $handlerAnswer;
      }
      $handlerData = $this->handlerData($evaluateInstance, $handlerAnswer['audit_instance'], $answer);
      if ( !$handlerData['success'] ) {
        return $handlerData;
      }
      $info['success'] = true;
      $info['message'] = 'Registro exitoso';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al guardar respuesta';
      return $info;
    }
  }

  private function setRecursiveAnswer($evaluateInstance, $answer)
  {
    try {
      if ($answer != Audit::NO_APPLY) {
        $info['success'] = false;
        $info['message'] = 'Para ser recursivo la respuesta debe ser No Aplica';
        return $info;
      }

      $group = EvaluateAuditRequirement::where('id_audit_aspect', $evaluateInstance->id_audit_aspect)
          ->where('id_requirement', $evaluateInstance->id_requirement)->get();
      $childs = $group->whereNotNull('id_subrequirement');
      foreach ($childs as $child) {
        $each = $this->setOnlyAnswer($child, $answer);
        if ( !$each['success'] ) {
          return $each;
        }
      }
      $info['success'] = true;
      $info['message'] = 'Registro exitoso';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al guardar respuesta';
      return $info;
    }
  }

  private function handlerAnswer($evaluateInstance, $answer)
  {
    try {
      $auditInEvaluate = $evaluateInstance->audit;
      // create answer audit
      if ( is_null($auditInEvaluate) ) {
        $audit = Audit::create([
          'answer' => $answer,
          'id_audit_processes' => $evaluateInstance->audit_aspect->id_audit_processes,
          'id_aspect' => $evaluateInstance->audit_aspect->id_aspect,
          'id_audit_aspect' => $evaluateInstance->audit_aspect->id_audit_aspect,
          'id_requirement' => $evaluateInstance->id_requirement,
          'id_subrequirement' => $evaluateInstance->id_subrequirement,
          'id_user' => Auth::id()
        ]);
        $evaluateInstance->update(['id_audit' => $audit->id_audit]);
      } 
      // update answer audit
      else {
        $audit = Audit::find($auditInEvaluate->id_audit);
        $audit->update([
          'answer' => $answer,
          'id_user' => Auth::id()
        ]);
      }
      $info['success'] = true;
      $info['message'] = 'Registro exitoso';
      $info['audit_instance'] = $audit;
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'No se ha podido establcer su respuesta';
      return $info;
    }
  }

  private function handlerData($evaluateInstance, $auditInstance, $answer)
  {
    try {
      $enabledRisk = $answer == Audit::NEGATIVE && $this->evaluateRisk && !$this->isEvaluateParent;
      $statusComplete =  $enabledRisk ? EvaluateAuditRequirement::NO_COMPLETE : EvaluateAuditRequirement::COMPLETE;
      $updateEvaluate = ['complete' => $statusComplete];
      // update both instances
      $auditInstance->update(['finding' => NULL]);
      $evaluateInstance->update($updateEvaluate);
      // delete risk
      if ( $this->evaluateRisk ) {
        $audit = Audit::getRisk()->find($auditInstance->id_audit);
        $riskTotals = $audit->risk_totals->pluck('id_risk_total')->values()->toArray();
        $riskAnswers = $audit->risk_answers->pluck('id_risk_answer')->values()->toArray();
        
        RiskTotal::whereIn('id_risk_total', $riskTotals)->delete();
        RiskAnswer::whereIn('id_risk_answer', $riskAnswers)->delete();
      }

      $info['success'] = true;
      $info['message'] = 'Registro exitoso';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'No se ha podido cambiar las condiciones de su respuesta';
      return $info;
    }
  }
}