<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Model;
use App\Traits\V2\UtilitiesTrait;

class Industry extends Model
{
	use UtilitiesTrait;

	protected $table = 'c_industries';
	protected $primaryKey = 'id_industry';

	/*
	* The attributes that are mass assignable.
	*
	* @var array
	*/
	protected $fillable = [
		'industry',  
		'id_status', 
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
		'industry' => ['field' => 'industry', 'type' => 'string', 'relation' => null],
	];
}