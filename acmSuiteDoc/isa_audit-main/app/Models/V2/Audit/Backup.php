<?php

namespace App\Models\V2\Audit;

use App\Models\V2\Admin\Image;
use App\Models\V2\Admin\User;
use App\Models\V2\Audit\ProcessAudit;
use App\Traits\V2\UtilitiesTrait;
use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
  use UtilitiesTrait;

  protected $table = 'backups';

  /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
    'init_date',
    'end_date',
    'id_user',
    'id_audit_processes',
  ];

  /**
	 * The relationships that should always be loaded.
	 *
	 * @var array
	 */
	protected $with = [
    // 'user',
    // 'process',
		// 'file',
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
		// 
	];

  /**
	 * Attributes 
	 */
	protected $appends = [
		'init_date_format',
		'end_date_format',
	];

  /*
	* Get init_date format
	*/
	public function getInitDateFormatAttribute()
	{
		return $this->getFormatDatetime($this->init_date);
	}

	/*
	* Get end_date format
	*/
	public function getEndDateFormatAttribute()
	{
		return $this->getFormatDatetime($this->end_date);
	}

  /**
   * Get the Process that owns the AplicabilityRegister
   */
  public function process()
  {
    return $this->belongsTo(ProcessAudit::class, 'id_audit_processes', 'id_audit_processes')
      ->without('aplicability_register');
  }

  /**
	 * Get the user that owns the AuditorModel
	 */
	public function user()
	{
		return $this->belongsTo(User::class, 'id_user', 'id_user');
	}

  /**
   * Get all of the customers's file.
   */
  public function file()
  {
    return $this->morphOne(Image::class, 'imageable');
  }

	public function scopeWithDownload($query)
	{
		return $query->with(['process.customer','process.corporate']);
	}
}