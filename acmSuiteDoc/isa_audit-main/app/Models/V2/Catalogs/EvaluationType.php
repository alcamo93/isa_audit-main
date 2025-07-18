<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\V2\UtilitiesTrait;

class EvaluationType extends Model
{
	use HasFactory, UtilitiesTrait;

	protected $table = 'evaluation_types';
	
	/**
	 * Remove timestamps
	 */
	public $timestamps = false;
	
	const EVALUATE_AUDIT = 1;
	const EVALUATE_OBLIGATION = 2;
	const EVALUATE_BOTH = 3;
}
