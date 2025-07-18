<?php

namespace App\Models\V2\Audit;

use App\Models\V2\Audit\Library;
use App\Traits\V2\UtilitiesTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryAction extends Model
{
	use UtilitiesTrait, HasFactory;

	protected $table = 'library_action';
	/*
    * The attributes that are mass assignable.
    *
    * @var array
    */
	protected $fillable = [
    'action',
    'data',
    'user_id',
		'library_id',
	];

	/**
	 * Attributes 
	 */
	protected $appends = [
    // 
	];

	/**
	 * Relationship [one to many (inverse)]
	 */
	public function library()
	{
		return $this->belongsTo(Library::class);
	}
}
