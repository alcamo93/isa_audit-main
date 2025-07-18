<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Audit\Task;
use App\Models\V2\Audit\ContractAspect;
use App\Models\V2\Audit\Obligation;
use App\Models\V2\Audit\Library;
use App\Models\V2\Audit\AplicabilityRegister;
use App\Models\V2\Catalogs\Requirement;
use App\Models\V2\Catalogs\Subrequirement;
use App\Traits\V2\CustomOrderTrait;
use App\Traits\V2\UtilitiesTrait;

class EvaluateRequirement extends Model
{
  use UtilitiesTrait, CustomOrderTrait;

  protected $table = 't_evaluate_requirement';
  protected $primaryKey = 'id_evaluate_requirement';

  /*
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
    'complete',
    'show_library',
    'id_contract_aspect',
    'aplicability_register_id',
    'id_requirement',
    'id_subrequirement',
  ];

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'library',
  ];

  public function requirement() 
  {
    return $this->belongsTo(Requirement::class, 'id_requirement', 'id_requirement');
  }

  public function subrequirement() 
  {
    return $this->belongsTo(Subrequirement::class, 'id_subrequirement', 'id_subrequirement');
  }

  public function contract_aspect() 
  {
    return $this->belongsTo(ContractAspect::class, 'id_contract_aspect', 'id_contract_aspect');
  }

  public function aplicability_register()
  {
    return $this->belongsTo(AplicabilityRegister::class, 'aplicability_register_id', 'id_aplicability_register');
  }

  public function obligations()
	{
		return $this->morphedByMany(Obligation::class, 'evaluateable', 'evaluateables', 'evaluate_requirement_id');
	}
  
	public function tasks()
	{
		return $this->morphedByMany(Task::class, 'evaluateable', 'evaluateables', 'evaluate_requirement_id');
	}

  public function library()
  {
    return $this->hasOne(Library::class, 'id_evaluate_requirement', 'id_evaluate_requirement');
  }

  public function scopeWithLibrary($query)
  {
    $relationships = [
      'requirement.evidence', 
      'subrequirement.evidence', 
      'requirement.condition', 
      'subrequirement.condition', 
      'library.category', 
      'library.auditor.person', 
      'library.auditor.image', 
      'obligations', 
      'tasks'
    ];
    return $query->with($relationships);
  }

  public function scopeForLibrary($query)
  {
    $filters = request('filters');
		if ( empty($filters) ) $query->where('id_evaluate_requirement', 0);
    
		if ( isset($filters['id_audit_processes']) ) {
      $process = ProcessAudit::where('id_audit_processes', $filters['id_audit_processes'])->first();

      if ( is_null($process) ) return;

      $query->withLibrary()->where('show_library', 1)
        ->where('aplicability_register_id', $process->aplicability_register->id_aplicability_register);
		}
  }

  public function scopeForLibraryZip($query, $idAuditProcess)
  {
    $process = ProcessAudit::noLoadRelationships()->where('id_audit_processes', $idAuditProcess)->first();  
    if ( is_null($process) ) return;

    $idAplicabilityRegister = $process->aplicability_register->id_aplicability_register;

    $exclud = EvaluateRequirement::with('requirement')
      ->whereHas('requirement', fn($query) => $query->where('has_subrequirement', 1))
      ->where('aplicability_register_id', $idAplicabilityRegister)
      ->whereNull('id_subrequirement')
      ->pluck('id_evaluate_requirement')->toArray();
    
    $query->with(['requirement', 'subrequirement', 'library', 'obligations', 'tasks'])
      ->whereHas('library')
      ->where('aplicability_register_id', $idAplicabilityRegister)
      ->where('show_library', 1)
      ->whereNotIn('id_evaluate_requirement', $exclud);
  }

  public function scopeFilterLibrary($query)
  {
    $filters = request('filters');

    if ( empty($filters) ) return;

    if ( isset($filters['name']) ) {
      $query->whereHas('library', function($subquery) use ($filters) {
        $subquery->where('name', 'LIKE', "%{$filters['name']}%");
      });
		}

    if ( isset($filters['id_category']) ) {
      $query->whereHas('library', function($subquery) use ($filters) {
        $subquery->where('id_category', $filters['id_category']);
      });
		}

    if ( isset($filters['id_matter']) ) {
      $query->whereHas('requirement', function($subquery) use ($filters) {
        $subquery->where('id_matter', $filters['id_matter']);
      });
		}

    if ( isset($filters['id_aspect']) ) {
      $query->whereHas('requirement', function($subquery) use ($filters) {
        $subquery->where('id_aspect', $filters['id_aspect']);
      });
		}
  }

  public function scopeExcludeParents($query)
  {
    $filters = request('filters');
		if ( empty($filters) ) $query->where('id_evaluate_requirement', 0);
    
		if ( isset($filters['id_audit_processes']) ) {
      $process = ProcessAudit::noLoadRelationships()->where('id_audit_processes', $filters['id_audit_processes'])->first();
      
      if ( is_null($process) ) return;

      $aplicabilityRegisterId = $process->aplicability_register->id_aplicability_register;
      $exclud = EvaluateRequirement::with('requirement')
        ->whereHas('requirement', fn($query) => $query->where('has_subrequirement', 1))
        ->where('aplicability_register_id', $aplicabilityRegisterId)
        ->whereNull('id_subrequirement')
        ->pluck('id_evaluate_requirement')->toArray();
      
      $query->whereNotIn('id_evaluate_requirement', $exclud);
    }
  }
}