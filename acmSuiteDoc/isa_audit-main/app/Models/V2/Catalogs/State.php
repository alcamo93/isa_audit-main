<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Model;
use App\Traits\V2\UtilitiesTrait;

class State extends Model
{
	use UtilitiesTrait;

	protected $table = 'c_states';
	protected $primaryKey = 'id_state';

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
		'id_country' => ['field' => 'id_country', 'type' => 'number', 'relation' => null],
	];
}