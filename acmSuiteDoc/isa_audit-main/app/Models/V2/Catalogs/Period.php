<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Model;
use App\Traits\V2\UtilitiesTrait;

class Period extends Model
{
	use UtilitiesTrait;

	protected $table = 'periods';
	protected $primaryKey = 'id';

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
}