<?php

namespace App\Models\V2\Admin;

use App\Models\V2\Admin\Corporate;
use App\Models\V2\Admin\Person;
use App\Models\V2\Admin\Profile;
use App\Models\V2\Catalogs\ProfileType;
use App\Models\V2\Catalogs\Status;
use App\Traits\V2\UtilitiesTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
	use HasApiTokens, Notifiable, UtilitiesTrait;
	/** 
	 *  Table specification in model
	 */
	protected $table = 't_users';
	protected $primaryKey = 'id_user';
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'email', 
		'password',
		'secondary_email',
		'picture',
		'id_customer',
		'id_corporate',
		'id_person',
		'id_status',
		'id_profile'
	];
	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 
		'remember_token',
	];
	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
	];

	/**
	 * The relationships that should always be loaded.
	 *
	 * @var array
	 */
	protected $with = [
		// 'status',
		// 'person',
		// 'profile',
		// 'image',
	];

	/**
	 * Attributes 
	 */
	protected $appends = [
		'full_path',
	];

	/**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_included = [
		'person',
		'corporate'
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
		'email' => ['field' => 'email', 'type' => 'string', 'relation' => null],
		'phone' => ['field' => 'phone', 'type' => 'string', 'relation' => 'person'],
	];

	const ACTIVE = 1;
	const INACTIVE = 2;
	
	/**
	 * Get the person that owns the User
	 */
	public function person() 
	{
		return $this->belongsTo(Person::class, 'id_person', 'id_person');
	}

	/**
	 * Get the profile that owns the User
	 */
	public function profile() 
	{
		return $this->belongsTo(Profile::class, 'id_profile', 'id_profile');
	}

	/**
   * Get the status associated with the User
   */
  public function status()
  {
    return $this->belongsTo(Status::class, 'id_status', 'id_status');
  }

	/**
	 * Get the user's image.
	 */
	public function image()
	{
		return $this->morphOne(Image::class, 'imageable');
	}

	/**
	 * Get the corporate that owns the User
	 */
	public function corporate() 
	{
		return $this->belongsTo(Corporate::class, 'id_corporate', 'id_corporate');
	}

	/*
	 * Get Full Name
	 */
	public function getFullPathAttribute()
	{
		$domain = asset('');
		$fullPath = "{$domain}assets/img/faces/{$this->picture}";
		return $fullPath;
	}

	public function scopeWithIndex($query)
	{
		return $query->with(['status','person','profile.type','image']);
	}

	public function scopeWithInfo($query)
	{
		return $query->with(['person','profile.type','image']);
	}

	public function scopeWithIndexAccount($query)
	{
		return $query->with(['corporate', 'status','person','profile.type','image']);
	}

	public function scopeCustomFilters($query)
  {
		$filters = request('filters');
		if ( empty($filters) ) return;

    if ( isset($filters['name']) ) {
			$query->whereHas('person', function($subquery) use ($filters) {
				$queryConcat = DB::raw('CONCAT_WS(" ", first_name, second_name, last_name)');
				$subquery->where($queryConcat, 'LIKE', '%'.$filters['name'].'%');
			});
		}
  }

	/**
   * filter for catalog service by auth user
   */
  public function scopeFilterUserAuth($query)
  {
    $userAuthenticate = Auth::user();
    $profileType = $userAuthenticate->profile->id_profile_type;
    if ($profileType == ProfileType::CORPORATE) {
      $query->where('id_customer', $userAuthenticate->id_customer);
    }
		if ($profileType == ProfileType::COORDINATOR || $profileType == ProfileType::OPERATIVE) {
			$query->where('id_customer', $userAuthenticate->id_customer)
				->where('id_corporate', $userAuthenticate->id_corporate);
		}
  }
}