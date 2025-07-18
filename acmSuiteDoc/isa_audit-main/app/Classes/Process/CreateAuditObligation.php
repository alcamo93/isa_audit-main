<?php

namespace App\Classes\Process;

use App\Models\V2\Audit\AuditAspect;
use App\Models\V2\Audit\AuditMatter;
use App\Models\V2\Audit\AuditRegister;
use App\Models\V2\Audit\AplicabilityRegister;
use App\Models\V2\Audit\Audit;
use App\Models\V2\Audit\EvaluateRequirement;
use App\Models\V2\Audit\EvaluateAuditRequirement;
use App\Models\V2\Audit\Obligation;
use App\Models\V2\Audit\ObligationRegister;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Catalogs\ApplicationType;
use App\Models\V2\Catalogs\Condition;
use App\Models\V2\Catalogs\EvaluationType;
use Carbon\Carbon;

class CreateAuditObligation
{
  protected $idAplicabilityRegister = null;
  protected $initDate = null;
  protected $endDate = null;
  protected $process = null;
  protected $record = null;
  protected $formsAudit = null;
  protected $timezone = '';

  public function __construct($idAplicabilityRegister, $initDate = null, $endDate = null)
  {
    $this->idAplicabilityRegister = $idAplicabilityRegister;
    $this->record = AplicabilityRegister::with(['contract_matters.contract_aspects'])->findOrFail($this->idAplicabilityRegister);
    $this->process = ProcessAudit::findOrFail($this->record->id_audit_processes);
    $this->formsAudit = $this->getAplicabilityForms();
    $this->initDate = $initDate;
    $this->endDate = $endDate;
    $this->timezone = Config('enviroment.time_zone_carbon');
  }

  private function getAplicabilityForms()
  {
    $data = [];
    foreach ($this->record->contract_matters as $matter) {
      $aspects = $matter->contract_aspects->where('id_application_type', '!=', ApplicationType::NOT_APPLICABLE);
      foreach ($aspects as $aspect) {
        array_push($data, $aspect);
      } 
    }
    return collect( $data );
  }

  public function initAudit() 
  {
    $evaluationType = $this->process->evaluation_type_id;
    $evaluateAudit = $evaluationType == EvaluationType::EVALUATE_AUDIT || $evaluationType == EvaluationType::EVALUATE_BOTH;

    if ( !$evaluateAudit ) {
      $data['status'] = false;
      $data['message'] = 'No se eligio evaluar Auditoría, no se puede agregar alguna';
      return $data;
    }

    $audits = AuditRegister::where('id_aplicability_register', $this->idAplicabilityRegister)->get();
    $numberAuditAllowed = $this->process->per_year;
    $numberAuditCurrent = $audits->count();
    $today = Carbon::now($this->timezone)->toDateString();
    if ( $today > $this->process->end_date ) {
      $data['status'] = false;
      $data['message'] = "No es posible crear más auditorías, la fecha limite fue {$this->process->end_date}";
      return $data;
    }
  
    if ( $numberAuditCurrent == $numberAuditAllowed ) {
      $data['status'] = false;
      $data['message'] = 'Se ha cumplido con el número de evaluaciones en este año';
      return $data;
    }

    $auditRegister = [
      'init_date' => $this->initDate,
      'end_date' => $this->endDate,
      'id_aplicability_register' => $this->idAplicabilityRegister,
      'id_contract' => $this->record->id_contract,
      'id_corporate' => $this->process->id_corporate,
      'id_audit_processes' => $this->process->id_audit_processes,
      'id_status' => Audit::NOT_AUDITED_AUDIT,
    ];
    $audit = AuditRegister::create($auditRegister);

    $mattersUnique = $this->formsAudit->pluck('id_matter')->unique();
    $matters = $mattersUnique->map(function($item) use ($audit) {
      return [
        'self_audit' => 0,
        'id_audit_register' => $audit->id_audit_register,
        'id_contract' => $this->record->id_contract,
        'id_matter' => $item,
        'id_audit_processes' => $this->process->id_audit_processes,
        'id_status' => Audit::NOT_AUDITED_AUDIT,
      ];
    });
    $aspectsData = $this->formsAudit;

    $contractAspectsEvaluate = $aspectsData->map(function($item) {
      return [
        'form_id' => $item->form_id,
        'id_contract_aspect' => $item->id_contract_aspect,
        'id_aspect' => $item->id_aspect,
      ];
    });

    $aspects = $aspectsData->map(function($item) {
      return [
        'form_id' => $item->form_id,
        'self_audit' => 0,
        'id_audit_matter' => null,
        'id_contract' => $this->record->id_contract,
        'id_matter' => $item->form->matter_id,
        'id_aspect' => $item->form->aspect_id,
        'id_audit_processes' => $this->process->id_audit_processes,
        'id_application_type' => $item->id_application_type,
        'id_status' => Audit::NOT_AUDITED_AUDIT,
        'id_state' => $item->id_state,
      ];
    });

    foreach ($matters as $matter) {
      $tmpMatter = AuditMatter::create($matter);
      $tmpAspects = $aspects->where('id_matter', $tmpMatter['id_matter']);
      foreach ($tmpAspects as $aspect) {
        $aspect['id_audit_matter'] = $tmpMatter->id_audit_matter;
        $auditAspect = AuditAspect::create($aspect);
        // set evaluate audit requirements
        $aspectByForm = $contractAspectsEvaluate->firstWhere('form_id', $auditAspect->form_id);
        $requirementPerAspect = EvaluateRequirement::where('id_contract_aspect', $aspectByForm['id_contract_aspect'])->get();
        $evaluateForAudit = $requirementPerAspect->map(function($item) use ($auditAspect) {
          return [
            'complete' => 0,
            'id_audit_aspect' => $auditAspect->id_audit_aspect,
            'id_requirement' => $item->id_requirement,
            'id_subrequirement' => $item->id_subrequirement,
          ];
        })->toArray();
        EvaluateAuditRequirement::insert($evaluateForAudit);
      }
    }

    $data['status'] = true;
    $data['message'] = 'Auditoría iniciada';
    return $data;
  }

  public function initObligation() 
  {
    $evaluationType = $this->process->evaluation_type_id;
    $evaluateObligation = $evaluationType == EvaluationType::EVALUATE_OBLIGATION || $evaluationType == EvaluationType::EVALUATE_BOTH;

    if ( !$evaluateObligation ) {
      $data['status'] = false;
      $data['message'] = 'No se eligio evaluar Obligaciones';
      return $data;
    }

    $obligation = ObligationRegister::where('id_aplicability_register', $this->idAplicabilityRegister)->get();
    $numberAuditCurrent = $obligation->count();
  
    if ( $numberAuditCurrent == 1 ) {
      $data['status'] = false;
      $data['message'] = 'Solo puede existir una evaluacion de Obligaciones';
      return $data;
    }

    $auditRegister = ObligationRegister::create([
      'init_date' => $this->initDate,
      'end_date' => $this->endDate,
      'id_aplicability_register' => $this->idAplicabilityRegister,
    ]);
    $aspectsToEvaluate = $this->record->contract_matters->flatMap(fn($item) => $item->contract_aspects)->pluck('id_contract_aspect')->toArray();
    $allRequirements = EvaluateRequirement::with(['requirement', 'subrequirement'])->whereIn('id_contract_aspect', $aspectsToEvaluate)->get();

    $obligations = [];
    $requirementsToEvaluate = $allRequirements->where('requirement.id_condition', Condition::CRITICAL)->where('requirement.has_subrequirement', 0);
    $subrequirementsToEvaluate = $allRequirements->where('subrequirement.id_condition', Condition::CRITICAL)->whereNotNull('id_subrequirement');

    foreach ($requirementsToEvaluate as $requirement) {
      array_push($obligations, [
        'obligation_register_id' => $auditRegister->id,
        'id_requirement' => $requirement->id_requirement,
        'id_subrequirement' => NULL,
        'id_status' => Obligation::NO_STARTED_OBLIGATION,
      ]);
    }

    foreach ($subrequirementsToEvaluate as $subrequirement) {
      array_push($obligations, [
        'obligation_register_id' => $auditRegister->id,
        'id_requirement' => $subrequirement->id_requirement,
        'id_subrequirement' => $subrequirement->id_subrequirement,
        'id_status' => Obligation::NO_STARTED_OBLIGATION,
      ]);
    }
    
    if ( sizeof($obligations) == 0 ) {
      $data['status'] = false;
      $data['message'] = "No se cuenta con ningún Requerimiento critico para ninguno de los aspectos";
      return $data;
    }

    Obligation::insert($obligations);

    $data['status'] = true;
    $data['message'] = 'Obligaciones Activadas';
    return $data;
  }
}