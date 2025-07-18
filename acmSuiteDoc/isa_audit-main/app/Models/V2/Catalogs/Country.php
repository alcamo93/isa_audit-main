<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Model;
use App\Traits\V2\UtilitiesTrait;

class Country extends Model
{
	use UtilitiesTrait;

	protected $table = 'c_countries';
	protected $primaryKey = 'id_country';

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