<?php

namespace App\Classes\Process\Risk;

use App\Classes\Audit\VerifyAuditAnswer;
use App\Classes\Process\Risk\ReplicateRisk;
use App\Models\V2\Audit\Audit;
use App\Models\V2\Audit\Obligation;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Catalogs\RiskInterpretation;
use Illuminate\Support\Facades\DB;

class Risk
{
  public $registerableType, $registerableId, $idRiskAttribute, $type, $parent;
  public $evaluateAutomatic = false;

  public function __construct($registerableId, $registerableType) 
  {
    $this->type = ucfirst(strtolower($registerableType));
    $this->registerableType = $this->getClass($this->type);
    $this->registerableId = intval($registerableId);
    $this->parent = $this->generateParent();
  }

  public function getClass($registerableType) 
  {
    return $registerableType  == 'Audit' ? Audit::class : Obligation::class;
  }

  public function getParent()
  {
    return $this->parent;
  }

  public function generateParent() 
  {
    if ($this->type == 'Audit') {
      $parent = Audit::getRisk()->findOrFail($this->registerableId);
      return $parent;
    }

    if ($this->type == 'Obligation') {
      $parent = Obligation::getRisk()->findOrFail($this->registerableId);
      return $parent;
    }
  }

  public function setRiskAnswer($idRiskCategory, $idRiskAttribute, $answer)
  {
    try {
      DB::beginTransaction();
        // check previous evaluation in other sections
        $findRisk = new ReplicateRisk($this->registerableId, $this->type);
        $evaluateAutomatic = $findRisk->findSameRecords();

        // there is not evaluation risk
        if (!$evaluateAutomatic) {
          $this->setRiskAnswerManual($idRiskCategory, $idRiskAttribute, $answer);
          $verify = $this->verifyAudit($this->registerableId);
          if (!$verify['success']) {
            DB::rollback();
            return $verify;
          }
          DB::commit();
          $data['success'] = true;
          $data['message'] = 'Registro exitoso';
          return $data;
        }
        // there is evaluation so replicate records
        if ($evaluateAutomatic) {
          $replicate = $findRisk->replicateRecordsRisk();
          if (!$replicate) {
            DB::rollback();
            $data['success'] = false;
            $data['message'] = 'Algo salio mal al replicar Nivel de Riesgo';
            return $data;
          }
          $verify = $this->verifyAudit($this->registerableId);
          if (!$verify['success']) {
            DB::rollback();
            return $verify;
          }
          DB::commit();
          $data['success'] = true;
          $data['message'] = 'Registro exitoso';
          $data['info'] = [
            'title' => 'Nivel de riesgo', 
            'message' => 'El nivel de Riesgo ya fue evaluado previamente para este requerimiento'
          ];
          return $data;
        }
    } catch (\Throwable $th) {
      DB::rollback();
      $data['success'] = false;
      $data['message'] = 'Algo salio mal establcer Nivel de Riesgo';
      return $data;
    }
  }

  public function setRiskAnswerManual($idRiskCategory, $idRiskAttribute, $answer) 
  {
    // identifier
    $unique['registerable_type'] = $this->registerableType;
    $unique['registerable_id'] = $this->registerableId;
    $unique['id_risk_category'] = $idRiskCategory;
    $unique['id_risk_attribute'] = $idRiskAttribute;
    // data
    $answerData['answer'] = $answer;
    $answerData['id_risk_attribute'] = $idRiskAttribute;
    $answerData['id_risk_category'] = $idRiskCategory;
    $test = $this->parent->risk_answers()->updateOrCreate($unique, $answerData);
    // set totals
    $this->parent->load('risk_answers');
    $riskAnswers = $this->parent->risk_answers;
    $this->calculateValueCategory($riskAnswers);
  }

  public function calculateValueCategory($riskAnswers)
  {
    try {
      $totalAttributes = 3;
      $categories = $riskAnswers->pluck('id_risk_category')->unique()->values();
      foreach ($categories as $idRiskCategory) {
        $group = $riskAnswers->filter(fn($item) => $item->id_risk_category == $idRiskCategory);
        if ( sizeof($group) < $totalAttributes ) continue;
        $total = $group->sum('answer');
        // identifier
        $unique['registerable_type'] = $this->registerableType;
        $unique['registerable_id'] = $this->registerableId;
        $unique['id_risk_category'] = $idRiskCategory;
        // data
        $answerData['total'] = $total;
        $answerData['id_risk_category'] = $idRiskCategory;
        $this->parent->risk_totals()->updateOrCreate($unique, $answerData);
      }
      $risksCallback = function($query) {
        $query->addSelect([
          'interpretation' => RiskInterpretation::select('interpretation')
          ->whereColumn('id_risk_category', 't_risk_totals.id_risk_category')
          ->whereRaw('t_risk_totals.total BETWEEN interpretation_min AND interpretation_max')
          ->take(1)
        ]);
      };
      $this->parent->load(['risk_totals' => $risksCallback]);
      return true;
    } catch (\Throwable $th) {
      return false;
    }
  }

  private function verifyAudit($registerableId) 
  {
    try {
      if ($this->type != 'Audit') {
        $info['success'] = true;
        $info['message'] = 'Esta sección no verifica el completado';
        return $info;
      }
      $audit = Audit::with('evaluate_requirement.audit_aspect')->findOrFail($registerableId);
      $idEvaluateRequirement = $audit->evaluate_requirement->id;
      $process = ProcessAudit::find($audit->evaluate_requirement->audit_aspect->id_audit_processes);
      $evaluateRisk = $process->evaluate_risk;
      // verify answer
      $verify = new VerifyAuditAnswer($idEvaluateRequirement, $evaluateRisk);
      $handlerVerify = $verify->verifyAnswer();
      if ( !$handlerVerify['success'] ) {
        return $handlerVerify;
      }
      $info['success'] = true;
      $info['message'] = 'Registro exitoso';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al verificar el registro de Auditoria';
      return $info;
    }
  }
}