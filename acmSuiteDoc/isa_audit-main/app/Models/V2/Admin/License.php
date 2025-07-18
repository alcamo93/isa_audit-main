<?php

namespace App\Models\V2\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Admin\Contract;
use App\Models\V2\Catalogs\ProfileType;
use App\Models\V2\Catalogs\Period;
use App\Models\V2\Catalogs\Status;
use App\Traits\V2\UtilitiesTrait;

class License extends Model
{
	use UtilitiesTrait;

	protected $table = 'licenses';
	protected $primaryKey = 'id';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
    'name',
		'num_period',
		'period_id',
		'status_id',
  ];

	/**
	 * The relationships that should always be loaded.
	 *
	 * @var array
	 */
	protected $with = [
		// 'period',
		// 'status',
		// 'quantity',
	];

	/**
	 * Attributes 
	 */
	protected $appends = [
		// 
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
		'name' => ['field' => 'name', 'type' => 'string', 'relation' => null],
		'status_id' => ['field' => 'status_id', 'type' => 'number', 'relation' => null],
	];

	/**
	 * load the scopes that allow use in api request in this model with prefix "scopeWith".
	 * Exmaple: scopeWith + scopeMethod
	 *
	 * @var array
	 */
	protected $allow_scope = [
		'source',
		'quantity',
	];

	/**
	 * Get the users associated to License
	 */
	public function quantity()
	{
		return $this->belongsToMany(ProfileType::class, 'license_profile_type', 'id_license', 'id_profile_type',)
			->withPivot('quantity', 'id');
	}

	/**
   * Get the period associated with the License
   */
  public function period()
  {
    return $this->belongsTo(Period::class, 'period_id', 'id');
  }

  /**
   * Get the status associated with the License
   */
  public function status()
  {
    return $this->belongsTo(Status::class, 'status_id', 'id_status');
  }

	/**
   * Get the contracts associated with the License
   */
  public function contracts()
  {
    return $this->hasMany(Contract::class, 'id_lisense', 'id');
  }

	public function scopeWithSource($query)
	{
		return $query->with(['period','status']);
	}

	public function scopeWithQuantity($query)
	{
		return $query->with(['quantity']);
	}

	public function scopeWithIndex($query)
	{
		return $query->with(['period','status','quantity']);
	}
}