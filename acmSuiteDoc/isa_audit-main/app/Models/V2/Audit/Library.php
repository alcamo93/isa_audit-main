<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Admin\User;
use App\Models\V2\Audit\EvaluateRequirement;
use App\Models\V2\Audit\File;
use App\Models\V2\Audit\FilesNotification;
use App\Models\V2\Audit\LibraryAction;
use App\Models\V2\Catalogs\Category;
use App\Traits\V2\UtilitiesTrait;

class Library extends Model
{
	use UtilitiesTrait;

	protected $table = 'library';

	/*
  * The attributes that are mass assignable.
  *
  * @var array
  */
	protected $fillable = [
		'name',
		'init_date',
		'end_date',
		'load_date',
		'for_review',
		'has_end_date',
		'days',
		'need_renewal',
		'id_user',
		'id_category',
		'id_evaluate_requirement'
	];

	/**
	 * The relationships that should always be loaded.
	 *
	 * @var array
	 */
	protected $with = [
		// 'files',
		// 'category',
		// 'auditor',
		// 'files_notifications',
	];

	/**
	 * Attributes 
	 */
	protected $appends = [
		'init_date_format',
		'end_date_format',
		'load_date_format',
	];

	const UNDER_REVIEW = 1;
	const NO_UNDER_REVIEW = 0;

	/*
	* Get init_date format
	*/
	public function getInitDateFormatAttribute()
	{
		return $this->getFormatDate($this->init_date);
	}

	/*
	* Get end_date format
	*/
	public function getEndDateFormatAttribute()
	{
		return $this->getFormatDate($this->end_date);
	}

	/*
	* Get load_date format
	*/
	public function getLoadDateFormatAttribute()
	{
		return $this->getFormatDate($this->load_date);
	}

	/*
	* Get Full Name
	*/
	public function getFullPathAttribute()
	{
		$fullPath = url($this->url);
		return $fullPath;
	}

	public function scopeWithIndex($query)
	{
		return $query->with(['category', 'auditor', 'files', 'files_notifications']);
	}

	/**
	 * Get the evaluate that owns the Library
	 */
	public function evaluate()
	{
		return $this->belongsTo(EvaluateRequirement::class, 'id_evaluate_requirement', 'id_evaluate_requirement');
	}

	/**
	 * Get all of the files for the Library
	 */
	public function files()
	{
		return $this->hasMany(File::class);
	}

	/**
	 * Get the category that owns the Library
	 */
	public function category()
	{
		return $this->belongsTo(Category::class, 'id_category', 'id_category');
	}

	/**
	 * Get the auditor that owns the Library
	 */
	public function auditor()
	{
		return $this->belongsTo(User::class, 'id_user', 'id_user');
	}

	/**
	 * Get all of the filesNotifications for the Library
	 */
	public function files_notifications()
	{
		return $this->hasMany(FilesNotification::class);
	}

	/**
	 * Get all of the filesNotifications for the Library
	 */
	public function actions()
	{
		return $this->hasMany(LibraryAction::class);
	}
}
