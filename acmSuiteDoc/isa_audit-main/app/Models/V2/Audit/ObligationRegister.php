<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Audit\AplicabilityRegister;
use App\Models\V2\Audit\Obligation;
use App\Models\V2\Audit\ActionPlanRegister;
use App\Models\V2\Historical\Historical;
use App\Traits\V2\UtilitiesTrait;

class ObligationRegister extends Model
{
	use UtilitiesTrait;

	protected $table = 'obligation_registers';

	protected $fillable = [
		'total',
		'init_date',
		'end_date',
		'id_aplicability_register',
	];

	/**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
		// 'action_plan_register',
		// 'obligations'
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
    'aplicability_register.process',
  ];

  /**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_filter = [
    'audit_processes' => ['field' => 'audit_processes', 'type' => 'string', 'relation' => 'aplicability_register.process'],
    'id_customer' => ['field' => 'id_customer', 'type' => 'number', 'relation' => 'aplicability_register.process'],
    'id_corporate' => ['field' => 'id_corporate', 'type' => 'number', 'relation' => 'aplicability_register.process'],
    'id_scope' => ['field' => 'id_scope', 'type' => 'number', 'relation' => 'aplicability_register.process'],
    'date' => ['field' => 'init_date,end_date', 'type' => 'date_range_custom', 'relation' => null],
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
	 * Get the aplicability_register that owns the ObligationRegister
	 */
	public function aplicability_register()
	{
		return $this->belongsTo(AplicabilityRegister::class, 'id_aplicability_register', 'id_aplicability_register');
	}

	/**
	 * Get all of the obligations for the ObligationRegister
	 */
	public function obligations()
	{
		return $this->hasMany(Obligation::class, 'obligation_register_id', 'id');
	}

	/**
   * Get the action plan associated with the ObligationRegister
   */
  public function action_plan_register()
  {
		return $this->morphOne(ActionPlanRegister::class, 'registerable');
  }

	/**
	 * Get the historicals associated to ObligationRegister
	 */
	public function historicals() 
	{
		return $this->morphMany(Historical::class, 'historicalable');
	}
}