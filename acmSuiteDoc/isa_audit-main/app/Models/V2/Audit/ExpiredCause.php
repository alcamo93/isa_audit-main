<?php

namespace App\Models\V2\Audit;

use App\Traits\V2\UtilitiesTrait;
use Illuminate\Database\Eloquent\Model;

class ExpiredCause extends Model
{
  use UtilitiesTrait;
  
  protected $table = 'expired_cause';
  
  /**
   * Get the parent expiredable model (ActionPlan and Task).
   */
  public function expiredable()
  {
    return $this->morphTo();
  }

  /*
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
		'cause',
    'original_date',
    'extension_date',
		'expiredable_id',
		'expiredable_type',
  ];

  /**
	 * Attributes 
	 */
	protected $appends = [
    'original_date_format',
		'extension_date_format',
	];

  /*
	* Get original_date format
	*/
	public function getOriginalDateFormatAttribute()
	{
    return $this->getFormatDate($this->original_date);
	}

  /*
	* Get extension_date format
	*/
	public function getExtensionDateFormatAttribute()
	{
    return $this->getFormatDate($this->extension_date);
	}
}