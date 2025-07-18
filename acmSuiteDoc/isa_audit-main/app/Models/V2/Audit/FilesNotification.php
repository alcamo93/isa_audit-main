<?php

namespace App\Models\V2\Audit;

use App\Models\V2\Audit\Library;
use App\Traits\V2\UtilitiesTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilesNotification extends Model
{
	use UtilitiesTrait, HasFactory;

	/*
    * The attributes that are mass assignable.
    *
    * @var array
    */
	protected $fillable = [
		'library_id',
		'date',
		'done',
	];

	/**
	 * Attributes 
	 */
	protected $appends = [
    'date_format',
		'done_human'
	];

	const DONE = 1;
	CONST NO_DONE = 0;

	/*
	* Get load_date format
	*/
	public function getDateFormatAttribute()
	{
		return $this->getFormatDate($this->date);
	}

	/*
	* Get load_date format
	*/
	public function getDoneHumanAttribute()
	{
		return $this->done == $this::DONE ? 'Enviado' : 'Pendiente';
	}

	/**
	 * Relationship [one to many (inverse)]
	 */
	public function library()
	{
		return $this->belongsTo(Library::class);
	}
}
