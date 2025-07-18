<?php

namespace App\Models\V2\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Catalogs\Period;
use App\Models\V2\Catalogs\Status;
use App\Traits\V2\UtilitiesTrait;

class ContractHistorical extends Model
{
	use UtilitiesTrait;

	protected $table = 'contract_historical';
	protected $primaryKey = 'id';

	CONST CREATE = 1;
	CONST UPDATE = 2;
	CONST EXTENSION = 3;
	CONST RENEWAL = 4;
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'sequence',
    'type',
		'start_date',
		'end_date',
		'num_period',
		'id_period',
		'id_status',
		'id_contract',
  ];

	/**
	 * The relationships that should always be loaded.
	 *
	 * @var array
	 */
	protected $with = [
		// 'period',
		// 'status'
	];

	/**
	 * Attributes 
	 */
	protected $appends = [
		'start_date_format',
		'end_date_format',
		'created_date_format',
		'type_contract',
		'color',
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
		'id_contract' => ['field' => 'id_contract', 'type' => 'number', 'relation' => null],
	];

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
	 * Get end_date format
	 */
	public function getCreatedDateFormatAttribute()
	{
		return $this->getFormatDate($this->created_at);
	}

	/*
	* Get init_date format
	*/
	public function getTypeContractAttribute()
	{
		$types = [
			'1' => 'Creación',
			'2' => 'Actualización',
			'3' => 'Extensión',
			'4' => 'Renovación',
		];
		return $types[$this->type];
	}

	/*
	* Get color by type
	*/
	public function getColorAttribute()
	{
		$types = [
			'1' => 'info',
			'2' => 'success',
			'3' => 'warning',
			'4' => 'danger',
		];
		return $types[$this->type];
	}

	/**
   * Get the period associated with the License
   */
  public function period()
  {
    return $this->belongsTo(Period::class, 'id_period', 'id');
  }

  /**
   * Get the status associated with the Contract
   */
  public function status()
  {
    return $this->belongsTo(Status::class, 'id_status', 'id_status');
  }
}