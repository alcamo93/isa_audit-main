<?php

namespace App\Models\V2\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Traits\V2\UtilitiesTrait;

class ContractDetail extends Model
{
	use UtilitiesTrait;

	protected $table = 't_contract_details';
	protected $primaryKey = 'id_contract_detail';

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