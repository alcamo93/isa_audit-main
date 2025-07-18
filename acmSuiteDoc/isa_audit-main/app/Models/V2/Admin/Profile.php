<?php

namespace App\Models\V2\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Catalogs\ProfileType;
use App\Models\V2\Catalogs\Status;
use App\Traits\V2\UtilitiesTrait;

class Profile extends Model
{
	use UtilitiesTrait;
	
	protected $table = 't_profiles';
	protected $primaryKey = 'id_profile';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'profile_name',
		'id_status',
		'id_profile_type',
		'id_customer',
		'id_corporate',
	];

	/**
	 * The relationships that should always be loaded.
	 *
	 * @var array
	 */
	protected $with = [
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
		'id_status' => ['field' => 'id_status', 'type' => 'number', 'relation' => null],
		'id_profile_type' => ['field' => 'id_profile_type', 'type' => 'number', 'relation' => null],
		'profile_name' => ['field' => 'profile_name', 'type' => 'string', 'relation' => null],
	];

	/**
	 * load the scopes that allow use in api request in this model with prefix "scopeWith".
	 * Exmaple: scopeWith + scopeMethod
	 *
	 * @var array
	 */
	protected $allow_scope = [
		'source',
		'owner',
	];

	/**
	 * Get the customer that owns the Profile
	 */
	public function customer()
	{
		return $this->belongsTo(Customer::class, 'id_customer', 'id_customer');
	}

	/**
	 * Get the corporate that owns the Profile
	 */
	public function corporate()
	{
		return $this->belongsTo(Corporate::class, 'id_corporate', 'id_corporate');
	}

	/**
	 * Get the Type associated the Profile
	 */
	public function type()
	{
		return $this->belongsTo(ProfileType::class, 'id_profile_type', 'id_profile_type');
	}

	/**
   * Get the status associated with the Profile
   */
  public function status()
  {
    return $this->belongsTo(Status::class, 'id_status', 'id_status');
  }

	public function scopeWithSource($query)
	{
		return $query->with(['type', 'status']);
	}

	public function scopeWithOwner($query)
	{
		return $query->with(['customer', 'corporate']);
	}

	public function scopeWithIndex($query)
	{
		return $query->with(['customer', 'corporate', 'type', 'status']);
	}
}