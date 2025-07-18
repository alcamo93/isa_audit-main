<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Audit\Audit;
use App\Models\V2\Audit\AuditMatter;
use App\Models\V2\Catalogs\Matter;
use App\Models\V2\Catalogs\Aspect;
use App\Models\V2\Catalogs\Status;
use App\Models\V2\Catalogs\ApplicationType;
use App\Traits\V2\UtilitiesTrait;

class AuditAspect extends Model
{
  use UtilitiesTrait;
  
  protected $table = 'r_audit_aspects';
  protected $primaryKey = 'id_audit_aspect';

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'matter',
    // 'aspect',
    // 'status',
    // 'application_type'
  ];

  /*
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'self_audit',
    'total',
    'form_id',
    'id_audit_matter',
    'id_contract',
    'id_matter',
    'id_aspect',
    'id_audit_processes',
    'id_status',
    'id_application_type',
    'id_state'
  ];

  /**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_filter = [
    'id_audit_matter' => ['field' => 'id_audit_matter', 'type' => 'number', 'relation' => null],
    'id_audit_aspect' => ['field' => 'id_audit_aspect', 'type' => 'number', 'relation' => null],
    'id_status' => ['field' => 'id_status', 'type' => 'number', 'relation' => null],
  ];

  /**
   * Get the matter that owns the ContractMatter
   */
  public function matter()
  {
    return $this->belongsTo(Matter::class, 'id_matter', 'id_matter');
  }

  /**
   * Get the aspect that owns the ContractAspect
   */
  public function aspect()
  {
    return $this->belongsTo(Aspect::class, 'id_aspect', 'id_aspect');
  }

  /**
   * Get the status associated with the ProcessAudit
   */
  public function status()
  {
    return $this->belongsTo(Status::class, 'id_status', 'id_status');
  }

  /**
   * Get all of the audits for the AuditAspect
   */
  public function audits()
  {
    return $this->hasMany(Audit::class, 'id_audit_aspect', 'id_audit_aspect');
  }

  /**
   * Get all of the audits for the AuditAspect
   */
  public function audits_evaluates()
  {
    return $this->hasMany(EvaluateAuditRequirement::class, 'id_audit_aspect', 'id_audit_aspect');
  }

  /**
   * Get the audit_matter that owns the AuditAspect
   */
  public function audit_matter()
  {
    return $this->belongsTo(AuditMatter::class, 'id_audit_matter', 'id_audit_matter');
  }

  /**
	 * Get the application_type that owns the Guideline
	 */
	public function application_type()
	{
		return $this->belongsTo(ApplicationType::class, 'id_application_type', 'id_application_type');
	}

  public function scopeCustomFilter($query, $idAuditRegister, $idAuditMatter = null)
  {
    $queryAuditMatter = AuditMatter::where('id_audit_register', $idAuditRegister);
    if ( !is_null($idAuditMatter) ) {
      $queryAuditMatter->where('id_audit_matter', $idAuditMatter);
    }
    $idsAuditMatter = $queryAuditMatter->get()->pluck('id_audit_matter')->toArray();
    $query->whereIn('id_audit_matter', $idsAuditMatter);
  }

  public function scopeCustomOrder($query)
	{
		$query->whereHas('matter', function($subquery) {
			$subquery->orderBy('order', 'ASC');
		})->whereHas('aspect', function($subquery) {
			$subquery->orderBy('order', 'ASC');
		});
	}
}