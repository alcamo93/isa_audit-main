<?php

namespace App\Classes\Audit;

use App\Classes\Audit\EvaluateAuditAnswerPercentage;
use App\Models\V2\Audit\EvaluateAuditRequirement;
use App\Models\V2\Audit\Audit;
use Illuminate\Support\Facades\Auth;

class VerifyAuditAnswer
{
  public $record = null;
  public $evaluateRisk = false;
  public $isChild = false;

  public function __construct($idEvaluateAudit, $evaluateRisk) 
  {
    $this->evaluateRisk = $evaluateRisk;
    $this->record = EvaluateAuditRequirement::getRisk()->findOrFail($idEvaluateAudit);
    $this->isChild = !is_null($this->record->id_subrequirement);
  }

  public function verifyAnswer() 
  {
    try {
      if ( $this->isChild ) {
        $allEvaluateAudits = EvaluateAuditRequirement::where('id_audit_aspect', $this->record->id_audit_aspect)
          ->where('id_requirement', $this->record->id_requirement)->get();
        // evaluates all childs
        $evaluateAuditChilds = $allEvaluateAudits->whereNotNull('id_subrequirement')->whereNotNull('audit')->values();
        foreach ($evaluateAuditChilds as $evaluateAuditSub) {
          $isComplete = $this->verifyCompleteByValue($evaluateAuditSub->audit);
          if ( !$isComplete['success'] ) {
            return $isComplete;
          }
          $evaluateAuditSub->update([ 'complete' => $isComplete['is_complete'] ]);
        }
        // evaluate parent if childs is complete
        $evaluateParent = $this->verifyParent($allEvaluateAudits);  
        if ( !$evaluateParent['success'] ) {
          return $evaluateParent;
        }
      }
      // verify 
      $audit = $this->record->audit;
      $isComplete = $this->verifyCompleteByValue($audit);
      if ( !$isComplete['success'] ) {
        return $isComplete;
      }
      $this->record->update([ 'complete' => $isComplete['is_complete'] ]);
      
      $calculate = new EvaluateAuditAnswerPercentage();
      $handlerCalculate = $calculate->calculateAspectLevel($this->record->id_audit_aspect);
      if ( !$handlerCalculate['success'] ) {
        return $handlerCalculate;
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

  private function verifyCompleteByValue($audit)
  {
    try {
      if ($audit->answer != Audit::NEGATIVE) {
        $info['success'] = true;
        $info['message'] = 'No hay validaciones que verificar para esta respuesta';
        $info['is_complete'] = true;
        return $info;
      }
      // conditions
      $hasFinding = !is_null($audit->finding);
      $hascompleteRisk = true; // default true if evaluate_risk is false
      if ($this->evaluateRisk) {
        $hascompleteRisk = $audit->risk_totals->count() == 3;
      }
      $isComplete = $hasFinding && $hascompleteRisk;
      $info['is_complete'] = $isComplete;
      $info['success'] = true;
      $info['message'] = 'Verificacion exitosa';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al verificar condiciones de registro evaluado';
      return $info;
    }
  }

  private function verifyParent($allEvaluateAudits)
  {
    try {
      $completeChilds = $allEvaluateAudits->whereNotNull('id_subrequirement')->where('complete', 1)->count();
      $allChilds = $allEvaluateAudits->whereNotNull('id_subrequirement')->count();
      $areComplete = $allChilds == $completeChilds;
      if (!$areComplete) {
        $info['success'] = true;
        $info['message'] = 'Aún no son evaluados todos los subrequerimientos';
        return $info;
      }
      // get answer value 
      $allAudits = $allEvaluateAudits->whereNotNull('id_subrequirement')->pluck('audit');
      $valuesAnswers = collect([
        [ 'key' => 'AFFIRMATIVE', 'count' => $allAudits->where('answer', Audit::AFFIRMATIVE)->count(), 'value' => Audit::AFFIRMATIVE ],
        [ 'key' => 'NEGATIVE', 'count' => $allAudits->where('answer', Audit::NEGATIVE)->count(), 'value' => Audit::NEGATIVE ],
        [ 'key' => 'NO_APPLY', 'count' => $allAudits->where('answer', Audit::NO_APPLY)->count(), 'value' => Audit::NO_APPLY ],
      ]);
      
      $negativeAnswers = $valuesAnswers->where('key','NEGATIVE')->first();
      $affirmativeAnswers = $valuesAnswers->where('key','AFFIRMATIVE')->first();
      $noApplyAnswers = $valuesAnswers->where('key','NO_APPLY')->first();
      
      if ($negativeAnswers['count'] > 0) $answer = $negativeAnswers;
      elseif ($affirmativeAnswers['count'] > 0) $answer = $affirmativeAnswers;
      else $answer = $noApplyAnswers;
      
      $auditEvaluateParent = $allEvaluateAudits->whereNull('id_subrequirement')->first();
      $auditRecord = [
        'id_audit_processes' => $auditEvaluateParent->audit_aspect->id_audit_processes,
        'id_aspect' => $auditEvaluateParent->audit_aspect->id_aspect,
        'id_audit_aspect' => $auditEvaluateParent->audit_aspect->id_audit_aspect,
        'id_requirement' => $auditEvaluateParent->id_requirement,
        'id_subrequirement' => $auditEvaluateParent->id_subrequirement,
      ];
      $auditAnswer = collect($auditRecord)->put('answer', $answer['value'])->put('id_user', Auth::id())->toArray();
      $audit = Audit::updateOrCreate($auditRecord, $auditAnswer);
      $auditEvaluateParent->update(['id_audit' => $audit->id_audit, 'complete' => 1]);

      $info['success'] = true;
      $info['message'] = "Requerimiento completado [{$answer['value']}]";
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al completar requeriemiento padre';
      return $info;
    }
  }
}