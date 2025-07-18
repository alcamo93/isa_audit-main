<?php

namespace App\Classes\Process\Risk;

use App\Models\V2\Audit\AuditRegister;
use App\Models\V2\Audit\ObligationRegister;
use App\Models\V2\Audit\Audit;
use App\Models\V2\Audit\Obligation;

class ReplicateRisk
{
  public $type = null;
  public $places = [];
  public $parent = null;
  public $registerableId = null;
  public $registerableType = null;
  public $idAplicabilityRegister = null;
  public $idRequirement = null;
  public $idSubrequirement = null;
  public $totalRiskCategories = 3;

  public function __construct($registerableId, $registerableType) 
  {
    $this->type = $registerableType;
    $this->registerableId = $registerableId;
    $this->registerableType = $this->getClass($registerableType);
    $this->generateParent();
  }

  public function getClass($registerableType) 
  {
    return $registerableType  == 'Audit' ? Audit::class : Obligation::class;
  }

  public function generateParent() 
  {
    if ($this->type == 'Audit') {
      $this->parent = Audit::with('audit_aspect.audit_matter.audit_register')->getRisk()->findOrFail($this->registerableId);
      $this->idAplicabilityRegister = $this->parent->audit_aspect->audit_matter->audit_register->id_aplicability_register;
    }

    if ($this->type == 'Obligation') {
      $this->parent = Obligation::with('obligation_register')->getRisk()->findOrFail($this->registerableId);
      $this->idAplicabilityRegister = $this->parent->obligation_register->id_aplicability_register;
    }

    $this->idRequirement = $this->parent->id_requirement;
    $this->idSubrequirement = $this->parent->id_subrequirement;
  }

  public function findSameRecords()
  {
    $this->getObligations();
    $this->getAudits();
    $riskEvaluated = collect($this->places)->firstWhere('risk_evaluated', true);
    return !is_null($riskEvaluated);
  }

  public function replicateRecordsRisk()
  {
    $success = false;
    $riskEvaluated = collect($this->places)->firstWhere('risk_evaluated', true);

    if ( is_null($riskEvaluated) ) {
      return $success;
    }

    collect($riskEvaluated['risk_answers'])->each(function($item) {
      // identifier
      $unique['registerable_type'] = $this->registerableType;
      $unique['registerable_id'] = $this->registerableId;
      $unique['id_risk_category'] = $item['id_risk_category'];
      $unique['id_risk_attribute'] = $item['id_risk_attribute'];
      // data
      $answerData['answer'] = $item['answer'];
      $answerData['id_risk_attribute'] = $item['id_risk_attribute'];
      $answerData['id_risk_category'] = $item['id_risk_category'];
      // set answer
      $this->parent->risk_answers()->updateOrCreate($unique, $answerData);
    });
    
    collect($riskEvaluated['risk_totals'])->each(function($item) {
      // identifier
      $unique['registerable_type'] = $this->registerableType;
      $unique['registerable_id'] = $this->registerableId;
      $unique['id_risk_category'] = $item['id_risk_category'];
      // data
      $answerData['total'] = $item['total'];
      $answerData['id_risk_category'] = $item['id_risk_category'];
      // set category
      $this->parent->risk_totals()->updateOrCreate($unique, $answerData);
    });

    $success = true;
    return $success;
  }

  public function getObligations()
  { 
    $filterObligation = function($query) {
      $query->where('id_requirement', $this->idRequirement)
        ->where('id_subrequirement', $this->idSubrequirement)
        ->where('id_obligation', '!=', $this->registerableId);
    };
    $relationships = [
      'obligations' => $filterObligation,
      'obligations.risk_totals',
      'obligations.risk_answers',
    ];
    $obligationRegister = ObligationRegister::with($relationships)
      ->where('id_aplicability_register', $this->idAplicabilityRegister)
      ->whereHas('obligations', $filterObligation)->first();

    if ( is_null($obligationRegister) ) return;
    
    $record = $obligationRegister->obligations->first();
    $evaluatedRisk = $record->risk_totals->count() == $this->totalRiskCategories;
    $totals = $record->risk_totals->toArray();
    $answers = $record->risk_answers->toArray();

    array_push($this->places, [
      'registerable_type' => 'Obligation',
      'registerable_id' => $obligationRegister->id,
      'init_date_format' => $obligationRegister->init_date_format,
      'end_date_format' => $obligationRegister->end_date_format,
      'risk_evaluated' => $evaluatedRisk,
      'risk_totals' => $totals,
      'risk_answers' => $answers,
    ]);
  }

  public function getAudits()
  {
    $filterAudit = function($query) {
      $query->where('id_requirement', $this->idRequirement)
        ->where('id_subrequirement', $this->idSubrequirement)
        ->where('id_audit', '!=', $this->registerableId);
    };
    $relationships = [
      'audit_matters.audit_aspects.audits' => $filterAudit,
      'audit_matters.audit_aspects.audits.risk_totals',
      'audit_matters.audit_aspects.audits.risk_answers',
    ];
    $auditRegister = AuditRegister::with($relationships)
      ->where('id_aplicability_register', $this->idAplicabilityRegister)
      ->whereHas('audit_matters.audit_aspects.audits', $filterAudit)
      ->get();

    $auditRegister->each(function($item) {
      $record = $item->audit_matters->pluck('audit_aspects')->collapse()->pluck('audits')->collapse()->first();
      $evaluatedRisk = $record->risk_totals->count() == $this->totalRiskCategories;
      $totals = $record->risk_totals->toArray();
      $answers = $record->risk_answers->toArray();

      array_push($this->places, [
        'registerable_type' => 'Audit',
        'registerable_id' => $item->id_audit_register,
        'init_date_format' => $item->init_date_format,
        'end_date_format' => $item->end_date_format,
        'risk_evaluated' => $evaluatedRisk,
        'risk_totals' => $totals,
        'risk_answers' => $answers,
      ]);
    });
  }
}