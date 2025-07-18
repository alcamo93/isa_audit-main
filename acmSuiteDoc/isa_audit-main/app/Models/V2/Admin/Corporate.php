<?php

namespace App\Models\V2\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\V2\Catalogs\ProfileType;
use App\Models\V2\Catalogs\Status;
use App\Models\V2\Admin\Contract;
use App\Models\V2\Admin\Address;
use App\Models\V2\Admin\User;
use App\Models\V2\Admin\Customer;
use App\Models\V2\Admin\Contact;
use App\Traits\V2\UtilitiesTrait;

class Corporate extends Model
{
	use UtilitiesTrait;

	protected $table = 't_corporates';
	protected $primaryKey = 'id_corporate';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
    'corp_tradename',
		'corp_trademark',
		'rfc',
		'type',
		'id_customer',
		'id_status',
		'id_industry',
  ];

	/**
	 * The relationships that should always be loaded.
	 *
	 * @var array
	 */
	protected $with = [
		// 'addresses',
		// 'industry',
		// 'status',
		// 'image',
	];
	
	/**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_included = [
		'users',
		'contact'
	];

	/**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_filter = [
		'id_customer' => ['field' => 'id_customer', 'type' => 'number', 'relation' => null],
		'id_status' => ['field' => 'id_status', 'type' => 'number', 'relation' => null],
		'type' => ['field' => 'type', 'type' => 'number', 'relation' => null],
		'id_industry' => ['field' => 'id_industry', 'type' => 'number', 'relation' => null],
		'rfc' => ['field' => 'rfc', 'type' => 'string', 'relation' => null],
	];

	 /**
	 * load the scopes that allow use in api request in this model with prefix "scopeWith".
	 * Exmaple: scopeWith + scopeMethod
	 *
	 * @var array
	 */
	protected $allow_scope = [
		'source',
		'image',
		'addresses',
		'index',
	];

	/**
	 * Attributes 
	 */
	protected $appends = [
    'corp_tradename_format',
    'corp_trademark_format',
		'type_text',
	];

	/**
	 * types
	 */
	const NEW_TYPE = 1;
  const OPERATIVE_TYPE = 0;

	/**
   * Get corp_tradename_format
   */
  public function getCorpTradenameFormatAttribute()
  {
    return mb_convert_case($this->corp_tradename, MB_CASE_TITLE, "UTF-8");
  }

  /**
   * Get corp_tradename_format
   */
  public function getCorpTrademarkFormatAttribute()
  {
    return mb_convert_case($this->corp_trademark, MB_CASE_TITLE, "UTF-8");
  }

	/**
   * Get type with text
   */
  public function getTypeTextAttribute()
  {
    return boolval($this->type) ? 'Nueva' : 'Operativa';
  }

	/**
	 * Get the contract for the corporate
	 */
	public function contract()
	{
		return $this->hasOne(Contract::class, 'id_corporate', 'id_corporate');
	}

	/**
	 * Get all of the addresses for the corporate
	 */
	public function addresses()
	{
		return $this->hasMany(Address::class, 'id_corporate', 'id_corporate');
	}

	/**
	 * Get the Industry associated with the Corporate
	 */
	public function industry()
	{
		return $this->hasOne('App\Models\V2\Catalogs\Industry', 'id_industry', 'id_industry');
	}

	/**
	 * Get all of the addresses for the corporate
	 */
	public function users()
	{
		return $this->hasMany(User::class, 'id_corporate', 'id_corporate');
	}

	/**
	 * Get the customer that owns the Corporate
	 */
	public function customer()
	{
		return $this->belongsTo(Customer::class, 'id_customer', 'id_customer');
	}

	 /**
   * Get the status associated with the Corporate
   */
  public function status()
  {
    return $this->belongsTo(Status::class, 'id_status', 'id_status');
  }

	/**
	 * Get the contact for the corporate
	 */
	public function contact()
	{
		return $this->hasOne(Contact::class, 'id_corporate', 'id_corporate');
	}

	/**
	 * Get the corporate's image.
	 */
	public function image()
	{
		return $this->morphOne(Image::class, 'imageable');
	}

	public function scopeWithSource($query)
	{
		return $query->with(['industry', 'status']);
	}

	public function scopeWithImage($query)
	{
		return $query->with('image');
	}

	public function scopeWithAddresses($query)
	{
		return $query->with('addresses');
	}

	public function scopeWithContact($query)
	{
		return $query->with('contact');
	}

	public function scopeWithIndex($query)
	{
		return $query->withSource()->withImage()->withAddresses()->withContact();
	}

	/**
   * add new filters
   */
  public function scopeCustomFilters($query, $idCustomer = null)
  {
		if ( !is_null($idCustomer) ) {
			$query->where('id_customer', $idCustomer);
		}

    $filters = request('filters');
		if ( empty($filters) ) return;
    
		if ( isset($filters['corporate_name']) ) {
			$query->where(function($subquery) use ($filters) {
				$subquery->where('corp_tradename', 'LIKE', "%{$filters['corporate_name']}%")
					->orWhere('corp_trademark', 'LIKE', "%{$filters['corporate_name']}%");
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