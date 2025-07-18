<?php

namespace App\Models\V2\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\V2\Catalogs\ProfileType;
use App\Traits\V2\UtilitiesTrait;

class Customer extends Model
{
  use UtilitiesTrait;

  protected $table = 't_customers';
  protected $primaryKey = 'id_customer';

  /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
    'cust_tradename',
    'cust_trademark',
    'logo',
    'sm_logo',
    'lg_logo',
  ];

  /**
	 * The relationships that should always be loaded.
	 *
	 * @var array
	 */
	protected $with = [
		// 'images',
	];

  /**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_included = [
    'corporates'
  ];

  /**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_filter = [
		'cust_tradename' => ['field' => 'cust_tradename', 'type' => 'string', 'relation' => null],
		'cust_trademark' => ['field' => 'cust_trademark', 'type' => 'string', 'relation' => null],
	];

  /**
	 * load the scopes that allow use in api request in this model with prefix "scopeWith".
	 * Exmaple: scopeWith + scopeMethod
	 *
	 * @var array
	 */
	protected $allow_scope = [
		'images',
	];

  /**
	 * Attributes 
	 */
	protected $appends = [
		'full_path',
    'cust_tradename_format',
    'cust_trademark_format',
	];

  /*
	* Get Full path
	*/
	public function getFullPathAttribute()
	{ 
		$fullPath = url("/assets/img/customers/{$this->logo}");
		return $fullPath;
	}

  /**
   * Get cust_tradename_format
   */
  public function getCustTradenameFormatAttribute()
  {
    return mb_convert_case($this->cust_tradename, MB_CASE_TITLE, "UTF-8");
  }

  /**
   * Get cust_tradename_format
   */
  public function getCustTrademarkFormatAttribute()
  {
    return mb_convert_case($this->cust_trademark, MB_CASE_TITLE, "UTF-8");
  }

  /**
   * Get all of the corporates for the Customer
   */
  public function corporates()
  {
    return $this->hasMany(Corporate::class, 'id_customer', 'id_customer');
  }

  /**
   * Get all of the customers's images.
   */
  public function images()
  {
    return $this->morphMany(Image::class, 'imageable');
  }

  /**
   * with info
   */
  public function scopeWithImages($query)
  {
    return $query->with(['images']);
  }

  /**
   * custom filters in query
   */
  public function scopeCustomFilters($query)
  {
    $filters = request('filters');
		if ( empty($filters) ) return;
    
		if ( isset($filters['customer_name']) ) {
      $query->where('cust_tradename', 'LIKE', "%{$filters['customer_name']}%")
        ->orWhere('cust_trademark', 'LIKE', "%{$filters['customer_name']}%");
		}
  }

  /**
   * filter for catalog service by auth user
   */
  public function scopeFilterUserAuth($query)
  {
    $userAuthenticate = Auth::user();
    $profileType = $userAuthenticate->profile->id_profile_type;
    if ($profileType != ProfileType::ADMIN_GLOBAL && $profileType != ProfileType::ADMIN_OPERATIVE) {
      $query->where('id_customer', $userAuthenticate->id_customer);
    }
  }

   /**
   * Verify ownership level Contact of the section used in paths 
   */
  public function scopeVerifyOwnershipCorporateSource($query, $type, $idCustomer, $idCorporate, $idSource = null)
  {
    $query->where('id_customer', $idCustomer);

    if ($type == 'corporate') return $query->exists();

    $filterCorporate = fn($subquery) => $subquery->where('id_corporate', $idCorporate);
    $query->with(['corporates' => $filterCorporate])->whereHas('corporates', $filterCorporate);

    if ( $type == 'contact' && !is_null($idSource) ) {
      $filterAspect = fn($subquery) => $subquery->where('id_contact', $idSource);
      $query->with(['corporates.contact' => $filterAspect])
        ->whereHas('corporates.contact', $filterAspect);
    }

    if ( $type == 'address' && !is_null($idSource) ) {
      $filterAspect = fn($subquery) => $subquery->where('id_address', $idSource);
      $query->with(['corporates.addresses' => $filterAspect])
        ->whereHas('corporates.addresses', $filterAspect);
    }

    return $query->exists();
  }
}