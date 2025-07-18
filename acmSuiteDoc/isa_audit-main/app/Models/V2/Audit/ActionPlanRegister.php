<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Admin\Corporate;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Audit\ActionPlan;
use App\Models\V2\Historical\Historical;
use App\Traits\V2\UtilitiesTrait;

class ActionPlanRegister extends Model
{
  use UtilitiesTrait;
  
  protected $table = 't_action_registers';
  protected $primaryKey = 'id_action_register';

  /**
   * Get the parent registerable model (AuditRegister or ObligationRegister).
   */
  public function registerable()
  {
    return $this->morphTo();
  }

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'action_plans',
  ];

  /*
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
    'init_date',
    'end_date',
    'id_audit_processes',
    'id_corporate',
    'id_status',
  ];

  /**
	 * Attributes 
	 */
	protected $appends = [
		'init_date_format',
		'end_date_format',
    'origin',
    'origin_key',
    'in_range_date',
	];

  /**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_included = [
    'process',
  ];

  /**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_filter = [
    'audit_processes' => ['field' => 'audit_processes', 'type' => 'string', 'relation' => 'process'],
    'id_customer' => ['field' => 'id_customer', 'type' => 'number', 'relation' => 'process'],
    'id_corporate' => ['field' => 'id_corporate', 'type' => 'number', 'relation' => 'process'],
    'id_scope' => ['field' => 'id_scope', 'type' => 'number', 'relation' => 'process'],
    'date' => ['field' => 'init_date,end_date', 'type' => 'date_range_custom', 'relation' => null],
    'registrable' => ['field' => 'registerable_type', 'type' => 'class_name', 'relation' => null],
  ];

  /*
	* Get init_date format
	*/
	public function getInitDateFormatAttribute()
	{
    return $this->getFormatDate($this->init_date);
	}

  /*
	* Get end_date format
	*/
	public function getEndDateFormatAttribute()
	{
    return $this->getFormatDate($this->end_date);
	}

  /*
	* Get origin module
	*/
	public function getOriginAttribute()
	{
    $types = [
      class_basename(AuditRegister::class) => 'Auditoría',
      class_basename(ObligationRegister::class) => 'Permisos Críticos',
    ];
		$origin = class_basename( $this->registerable_type );

    return $types[$origin];
	}

  /*
	* Get origin module
	*/
	public function getOriginKeyAttribute()
	{
    $types = [
      class_basename(AuditRegister::class) => 'audit',
      class_basename(ObligationRegister::class) => 'obligation'
    ];
		$origin = class_basename( $this->registerable_type );
    
		return $types[$origin];
	}
  
  /*
	* Today's date is within the range of dates
	*/
	public function getInRangeDateAttribute()
	{
    return $this->dateInRange($this->init_date, $this->end_date);
	}

  /**
   * Get the process that owns the ActionPlanRegister
   */
  public function process()
  {
    return $this->belongsTo(ProcessAudit::class, 'id_audit_processes', 'id_audit_processes');
  }

  /**
   * Get all of the action_plans for the ActionPlanRegister
   */
  public function action_plans()
  {
    return $this->hasMany(ActionPlan::class, 'id_action_register', 'id_action_register');
  }

  /**
   * Get the corporate that owns the ActionPlanRegister
   */
  public function corporate()
  {
    return $this->belongsTo(Corporate::class, 'id_corporate', 'id_corporate');
  }

  /**
	 * Get the historicals associated to ObligationRegister
	 */
	public function historicals() 
	{
		return $this->morphMany(Historical::class, 'historicalable');
	}
}