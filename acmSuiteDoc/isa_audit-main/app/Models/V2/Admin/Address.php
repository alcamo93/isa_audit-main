<?php

namespace App\Models\V2\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Admin\Corporate;
use App\Models\V2\Catalogs\Country;
use App\Models\V2\Catalogs\State;
use App\Models\V2\Catalogs\City;
use App\Traits\V2\UtilitiesTrait;

class Address extends Model
{
  use UtilitiesTrait;

  protected $table = 't_addresses';
  protected $primaryKey = 'id_address';

  /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
    'id_corporate',
    'street',
    'ext_num',
    'int_num',
    'zip',
    'suburb',
    'type',
    'id_country',
    'id_state',
    'id_city',
  ];

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'country',
    // 'state',
    // 'city'
  ];

    /**
	 * Attributes 
	 */
	protected $appends = [
		'full_address',
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
    'id_corporate' => ['field' => 'id_corporate', 'type' => 'number', 'relation' => null],
    'type' => ['field' => 'type', 'type' => 'number', 'relation' => null],
  ];

  const PHYSICAL = 1;
  const FISCAL = 0;

  /*
	 * Get Full Address
	 */
	public function getFullAddressAttribute()
	{
    $extNum = is_null($this->ext_num) ? '' : "{$this->ext_num}";
    $intNum = is_null($this->int_num) ? '' : ", {$this->int_num}";
    $zip = is_null($this->zip) ? '' : ", CP {$this->zip}";
    $address = "{$this->street}, {$extNum}{$intNum} {$this->suburb}{$zip}";
    return $address;
	}

  /**
   * Get type with text
   */
  public function getTypeTextAttribute()
  {
    return boolval($this->type) ? 'Fiscal' : 'Física';
  }

  /**
   * Get the country that owns the Address
   */
  public function country()
  {
    return $this->belongsTo(Country::class, 'id_country', 'id_country');
  }

  /**
   * Get the state that owns the Address
   */
  public function state()
  {
    return $this->belongsTo(State::class, 'id_state', 'id_state');
  }

  /**
   * Get the city that owns the Address
   */
  public function city()
  {
    return $this->belongsTo(City::class, 'id_city', 'id_city');
  }

  /**
   * custom filters
   */
  public function scopeCustomFilter($query, $idCustomer, $idCorporate)
  {
    $corporate = Corporate::find($idCorporate);
    $sameCustomer = $corporate->id_customer == $idCustomer;
    if ( !$sameCustomer ) $query->whereNotNull('id_address');
    $query->where('id_corporate', $idCorporate);
  }
}