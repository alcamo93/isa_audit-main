<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\V2\UtilitiesTrait;

class Scope extends Model
{
	use HasFactory, UtilitiesTrait;

	protected $table = 'c_scope';
	protected $primaryKey = 'id_scope';

	/**
	 * Remove timestamps
	 */
	public $timestamps = false;
	
	const CORPORATE = 1;
	const AREA = 2;
}
