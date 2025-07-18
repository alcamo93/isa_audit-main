<?php

namespace App\Models\V2\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Admin\Customer;
use App\Models\V2\Admin\Corporate;
use App\Models\V2\Admin\License;
use App\Models\V2\Admin\ContractHistorical;
use App\Models\V2\Catalogs\Status;
use App\Traits\V2\UtilitiesTrait;

class Contract extends Model
{
	use UtilitiesTrait;

	protected $table = 't_contracts';
	protected $primaryKey = 'id_contract';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
    'contract',
		'start_date',
		'end_date',
		'id_license',
		'id_status',
		'id_customer',
		'id_corporate',
  ];

	/**
	 * The relationships that should always be loaded.
	 *
	 * @var array
	 */
	protected $with = [
		// 'customer',
		// 'corporate',
		// 'status',
		// 'license',
		// 'historicals',
	];

	/**
	 * Attributes 
	 */
	protected $appends = [
		'start_date_format',
		'end_date_format',
		'in_range_date',
		'info_dates',
	];

	/**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_included = [
		//
	];

	/**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_filter = [
		'id_customer' => ['field' => 'id_customer', 'type' => 'number', 'relation' => null],
		'id_corporate' => ['field' => 'id_corporate', 'type' => 'number', 'relation' => null],
		'contract' => ['field' => 'contract', 'type' => 'string', 'relation' => null],
		'id_status' => ['field' => 'id_status', 'type' => 'number', 'relation' => null],
	];

	/**
	 * load the scopes that allow use in api request in this model with prefix "scopeWith".
	 * Exmaple: scopeWith + scopeMethod
	 *
	 * @var array
	 */
	protected $allow_scope = [
		// 
	];

	const ACTIVE = 1;
	CONST INACTIVE = 2;

	/*
	* Get init_date format
	*/
	public function getStartDateFormatAttribute()
	{
		return $this->getFormatDate($this->start_date);
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
    return $this->dateInRange($this->start_date, $this->end_date);
	}

	/*
	 * Today's date info about dates record
	 */
	public function getInfoDatesAttribute()
	{
    return $this->infoDates($this->start_date, $this->end_date);
	}

	/**
	 * Get the customer that owns the Contract
	 */
	public function customer() 
	{
		return $this->belongsTo(Customer::class, 'id_customer', 'id_customer');
	}

	/**
	 * Get the corporate that owns the Contract
	 */
	public function corporate() 
	{
		return $this->belongsTo(Corporate::class, 'id_corporate', 'id_corporate');
	}

  /**
   * Get the status associated with the Contract
   */
  public function status()
  {
    return $this->belongsTo(Status::class, 'id_status', 'id_status');
  }

	/**
   * Get the status associated with the Contract
   */
  public function license()
  {
    return $this->belongsTo(License::class, 'id_license', 'id');
  }

	/**
   * Get all of the historicals for the Contract
   */
  public function historicals()
  {
    return $this->hasMany(ContractHistorical::class, 'id_contract', 'id_contract');
  }

	public function scopeWithHistoricals($query)
	{
		$relationships = ['license.period', 'historicals.status', 'historicals.period'];
		return $query->with($relationships);
	}
	
	public function scopeWithLicense($query)
	{
		return $query->with('license');
	}
	
	public function scopeWithStatus($query)
	{
		return $query->with('status');
	}

	public function scopeWithIndex($query)
	{
		$relationships = ['customer', 'corporate.contact'];
		return $query->with($relationships);
	}
}