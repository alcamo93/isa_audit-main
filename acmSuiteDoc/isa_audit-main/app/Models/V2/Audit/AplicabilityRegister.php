<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Audit\ContractMatter;
use App\Models\V2\Audit\AuditRegister;
use App\Models\V2\Audit\ObligationRegister;
use App\Models\V2\Audit\EvaluateRequirement;
use App\Models\V2\Catalogs\Status;
use App\Traits\V2\UtilitiesTrait;

class AplicabilityRegister extends Model
{
  use UtilitiesTrait;
  
  protected $table = 't_aplicability_registers';
  protected $primaryKey = 'id_aplicability_register';

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'contract_matters',
    // 'audit_register',
    // 'obligation_register',
    // 'status'
  ];

  /*
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
    'id_contract',
    'id_corporate',
    'id_audit_processes',
    'id_status',
  ];

  /**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_included = [
    'corporate',
    'process',
    'matters',
  ];

  /**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_filter = [
    'id_audit_processes' => ['field' => 'id_audit_processes', 'type' => 'number', 'relation' => null],
    'id_corporate' => ['field' => 'id_corporate', 'type' => 'number', 'relation' => null],
  ];

  const NOT_CLASSIFIED_APLICABILITY = 3;
  const CLASSIFIED_APLICABILITY = 4;
  const EVALUATING_APLICABILITY = 5;
  const FINISHED_APLICABILITY = 6;
  
  /**
   * Get the Process that owns the AplicabilityRegister
   */
  public function process()
  {
    return $this->belongsTo(ProcessAudit::class, 'id_audit_processes', 'id_audit_processes');
  }

  /**
   * Get all of the contract_matters for the AplicabilityRegister
   */
  public function contract_matters()
  {
    return $this->hasMany(ContractMatter::class, 'id_aplicability_register', 'id_aplicability_register');
  }

  /**
   * Get the status associated with the ProcessAudit
   */
  public function status()
  {
    return $this->belongsTo(Status::class, 'id_status', 'id_status');
  }

  
  /**
   * Get the audits associated to ProcessAudit
   */
  public function audit_register() 
  {
    return $this->hasMany(AuditRegister::class, 'id_aplicability_register', 'id_aplicability_register');
  }

  /**
   * Get the audits associated to ProcessAudit
   */
  public function obligation_register() 
  {
    return $this->hasOne(ObligationRegister::class, 'id_aplicability_register', 'id_aplicability_register');
  }

  /**
   * Get evaluates requirements
   */
  public function evaluate_requirements()
  {
    return $this->hasMany(EvaluateRequirement::class, 'aplicability_register_id', 'id_aplicability_register');
  }

  public function scopeNoLoadRelationships($query)
	{
		$relationships = [
			'action_plan_register' => fn($query) => $query->without('action_plans'),
      'aplicability_register' => fn($query) => $query->without('contract_matters'),
      'aplicability_register' => fn($query) => $query->without('obligation_register'),
      'aplicability_register' => fn($query) => $query->without('evaluate_requirements'),
		];
		$query->with($relationships);
	}

  /**
   * Verify ownership level action of the section used in paths 
   */
  public function scopeVerifyOwnershipPolymorph($query, $idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister = null, $idActionPlan = null, $idTask = null)
  {
    $sections = collect([
      [ 'key' => 'obligation', 'relation' => 'obligation_register', 'registerable_id' => 'id' ],
      [ 'key' => 'audit', 'relation' => 'audit_register', 'registerable_id' => 'id_audit_register' ],
    ]);

    $sectionFound = $sections->where('key', $section)->first();

    if ( is_null($sectionFound) ) return false;

    $filterSection = fn($subquery) => $subquery->where($sectionFound['registerable_id'], $idSectionRegister);
      $query->with([$sectionFound['relation'] => $filterSection])
        ->where('id_audit_processes', $idAuditProcess)->where('id_aplicability_register', $idAplicabilityRegister)
        ->whereHas($sectionFound['relation'], $filterSection);

    if (!is_null($idActionRegister)) {
      $filterActionRegister = fn($subquery) => $subquery->where('id_action_register', $idActionRegister);
      $query->with(["{$sectionFound['relation']}.action_plan_register" => $filterActionRegister])
        ->whereHas("{$sectionFound['relation']}.action_plan_register", $filterActionRegister);
    }

    if (!is_null($idActionPlan)) {
      $filterActionPlan = fn($subquery) => $subquery->where('id_action_plan', $idActionPlan);
      $query->with(["{$sectionFound['relation']}.action_plan_register.action_plans" => $filterActionPlan])
        ->whereHas("{$sectionFound['relation']}.action_plan_register.action_plans", $filterActionPlan);
    }

    if (!is_null($idTask)) {
      $filterTasks = fn($subquery) => $subquery->where('id_task', $idTask);
      $query->with(["{$sectionFound['relation']}.action_plan_register.action_plans.tasks" => $filterTasks])
        ->whereHas("{$sectionFound['relation']}.action_plan_register.action_plans.tasks", $filterTasks);
    }

    return $query->exists();
  }

  /**
   * Verify ownership level audit of the section used in paths 
   */
  public function scopeVerifyOwnershipAudit($query, $idAuditProcess, $idAplicabilityRegister, $idAuditRegister = null, $idAuditMatter = null, $idAuditAspect = null, $idAuditEvaluate = null)
  {
    $query->where('id_audit_processes', $idAuditProcess)->where('id_aplicability_register', $idAplicabilityRegister);
    
    if ( !is_null($idAuditRegister) ) {
      $filterAuditRegister = fn($subquery) => $subquery->where('id_audit_register', $idAuditRegister);
      $query->with(['audit_register' => $filterAuditRegister])
        ->whereHas('audit_register', $filterAuditRegister);
    }
    
    if ( !is_null($idAuditMatter) ) {
      $filterMatter = fn($subquery) => $subquery->where('id_audit_matter', $idAuditMatter);
      $query->with(['audit_register.audit_matters' => $filterMatter])
        ->whereHas('audit_register.audit_matters', $filterMatter);
    }

    if ( !is_null($idAuditAspect) ) {
      $filterAspect = fn($subquery) => $subquery->where('id_audit_aspect', $idAuditAspect);
      $query->with(['audit_register.audit_matters.audit_aspects' => $filterAspect])
        ->whereHas('audit_register.audit_matters.audit_aspects', $filterAspect);
    }

    if ( !is_null($idAuditEvaluate) ) {
      $filterAuditEvaluate = fn($subquery) => $subquery->where('id', $idAuditEvaluate);
      $query->with(['audit_register.audit_matters.audit_aspects.audits_evaluates' => $filterAuditEvaluate])
        ->whereHas('audit_register.audit_matters.audit_aspects.audits_evaluates', $filterAuditEvaluate);
    }

    return $query->exists();
  }

  /**
   * Verify ownership level audit of the section used in paths 
   */
  public function scopeVerifyOwnershipObligation($query, $idAuditProcess, $idAplicabilityRegister, $idObligationRegister = null, $idObligation = null)
  {
    $query->where('id_audit_processes', $idAuditProcess)->where('id_aplicability_register', $idAplicabilityRegister);

    if ( !is_null($idObligationRegister) ) {
      $filterObligationRegister = fn($subquery) => $subquery->where('id', $idObligationRegister);
      $query->with(['obligation_register' => $filterObligationRegister])
        ->whereHas('obligation_register', $filterObligationRegister);
    }
    
    if ( !is_null($idObligation) ) {
      $filterMatter = fn($subquery) => $subquery->where('id_obligation', $idObligation);
      $query->with(['obligation_register.obligations' => $filterMatter])
        ->whereHas('obligation_register.obligations', $filterMatter);
    }

    return $query->exists();
  }

  /**
   * Verify ownership level aplicability of the section used in paths 
   */
  public function scopeVerifyOwnershipAplicability($query, $idAuditProcess, $idAplicabilityRegister, $idContractMatter = null, $idContractAspect = null, $idAplicabilityEvaluate = null)
  {
    $query->where('id_audit_processes', $idAuditProcess)->where('id_aplicability_register', $idAplicabilityRegister);
    
    if ( !is_null($idContractMatter) ) {
      $filterMatter = fn($subquery) => $subquery->where('id_contract_matter', $idContractMatter);
      $query->with(['contract_matters' => $filterMatter])
        ->whereHas('contract_matters', $filterMatter);
    }

    if ( !is_null($idContractAspect) ) {
      $filterAspect = fn($subquery) => $subquery->where('id_contract_aspect', $idContractAspect);
      $query->with(['contract_matters.contract_aspects' => $filterAspect])
        ->whereHas('contract_matters.contract_aspects', $filterAspect);
    }

    if ( !is_null($idAplicabilityEvaluate) ) {
      $filterAplicabilityEvaluate = fn($subquery) => $subquery->where('id', $idAplicabilityEvaluate);
      $query->with(['contract_matters.contract_aspects.evaluate_question' => $filterAplicabilityEvaluate])
        ->whereHas('contract_matters.contract_aspects.evaluate_question', $filterAplicabilityEvaluate);
    }

    return $query->exists();
  }

  /**
   * Verify ownership level aplicability of the section used in paths 
   */
  public function scopeVerifyOwnershipPolymorphComment($query, $idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister, $idActionPlan, $idTask, $idComment)
  {
    $sections = collect([
      [ 'key' => 'obligation', 'relation' => 'obligation_register', 'registerable_id' => 'id' ],
      [ 'key' => 'audit', 'relation' => 'audit_register', 'registerable_id' => 'id_audit_register' ],
    ]);

    $sectionFound = $sections->where('key', $section)->first();

    if ( is_null($sectionFound) ) return false;

    $filterSection = fn($subquery) => $subquery->where($sectionFound['registerable_id'], $idSectionRegister);
    $filterActionRegister = fn($subquery) => $subquery->where('id_action_register', $idActionRegister);
    $filterActionPlan = fn($subquery) => $subquery->where('id_action_plan', $idActionPlan);
    $filterTasks = fn($subquery) => $subquery->where('id_task', $idTask);

    $query->where('id_audit_processes', $idAuditProcess)->where('id_aplicability_register', $idAplicabilityRegister)
      ->whereHas($sectionFound['relation'], $filterSection)
      ->whereHas("{$sectionFound['relation']}.action_plan_register", $filterActionRegister)
      ->whereHas("{$sectionFound['relation']}.action_plan_register.action_plans", $filterActionPlan)
      ->whereHas("{$sectionFound['relation']}.action_plan_register.action_plans.tasks", $filterTasks);

    if (!is_null($idComment)) {
      $filterTasks = fn($subquery) => $subquery->where('id_comment', $idComment);
      $query->whereHas("{$sectionFound['relation']}.action_plan_register.action_plans.tasks.comments", $filterTasks);
    }

    return $query->exists();
  }
}
