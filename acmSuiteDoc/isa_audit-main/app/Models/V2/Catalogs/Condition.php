<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Model;
use App\Traits\V2\UtilitiesTrait;

class Condition extends Model
{
	use UtilitiesTrait;

	protected $table = 'c_conditions';
	protected $primaryKey = 'id_condition';

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

	const CRITICAL = 1;
	const OPERATIVE = 2;
	const RECOMENDATION = 3;
}