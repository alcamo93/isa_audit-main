<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Model;
use App\Traits\V2\UtilitiesTrait;

class City extends Model
{
	use UtilitiesTrait;

	protected $table = 'c_cities';
	protected $primaryKey = 'id_city';

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
		'id_state' => ['field' => 'id_state', 'type' => 'number', 'relation' => null],
	];
}