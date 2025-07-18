<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Audit\AplicabilityRegister;
use App\Models\V2\Audit\AuditMatter;
use App\Models\V2\Audit\ActionPlanRegister;
use App\Models\V2\Catalogs\Status;
use App\Models\V2\Historical\Historical;
use App\Traits\V2\UtilitiesTrait;

class AuditRegister extends Model
{
  use UtilitiesTrait;
  
  protected $table = 't_audit_registers';
  protected $primaryKey = 'id_audit_register';

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'audit_matters',
    // 'status',
    // 'action_plan_register'
  ];

  /*
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
    'total',
    'init_date',
    'end_date',
    'id_contract',
    'id_corporate',
    'id_audit_processes',
    'id_aplicability_register',
    'id_status',
  ];

  /**
	 * Attributes 
	 */
	protected $appends = [
		'init_date_format',
		'end_date_format',
    'in_range_date',
	];

  /**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_included = [
    'aplicability_register',
    'aplicability_register.process'
  ];

  /*
	* Get init_date format
	*/
	public function getInitDateFormatAttribute()
	{
    return $this->getFormatDate($this->init_date);
	}

  /* 
	* Get close_date format
	*/
	public function getEndDateFormatAttribute()
	{
    return $this->getFormatDate($this->end_date);
	}

  /*
	* Today's date is within the range of dates
	*/
	public function getInRangeDateAttribute()
	{
    return $this->dateInRange($this->init_date, $this->end_date);
	}

  /**
   * Get the aplicability_register that owns the AuditRegister
   */
  public function aplicability_register()
  {
    return $this->belongsTo(AplicabilityRegister::class, 'id_aplicability_register', 'id_aplicability_register');
  }

  /**
   * Get all of the audit_matters for the AuditRegister
   */
  public function audit_matters()
  {
    return $this->hasMany(AuditMatter::class, 'id_audit_register', 'id_audit_register');
  }

  /**
   * Get the status associated with the AuditRegister
   */
  public function status()
  {
    return $this->belongsTo(Status::class, 'id_status', 'id_status');
  }

  /**
   * Get the action plan associated with the AuditRegister
   */
  public function action_plan_register()
  {
    return $this->morphOne(ActionPlanRegister::class, 'registerable');   
  }

  /**
	 * Get the historicals associated to AuditRegister
	 */
	public function historicals() 
	{
		return $this->morphMany(Historical::class, 'historicalable');
	}

  public function scopeNoLoadRelationships($query)
	{
		$relationships = [
      'audit_matters.audit_aspects' => fn($query) => $query->without('audits'),
			'action_plan_register' => fn($query) => $query->without('action_plans'),
      'aplicability_register' => fn($query) => $query->without('contract_matters'),
      'aplicability_register' => fn($query) => $query->without('obligation_register'),
      'aplicability_register' => fn($query) => $query->without('evaluate_requirements'),
      'aplicability_register.process' => fn($query) => $query->without('aplicability_register'),
		];
		$query->with($relationships);
	}
}