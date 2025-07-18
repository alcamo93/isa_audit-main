<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Admin\User;
use App\Traits\V2\UtilitiesTrait;

class AuditorObligation extends Model
{
	use UtilitiesTrait;

	protected $table = 't_obligation_user';
	protected $primaryKey = 'id_obligation_user';

	/*
	* The attributes that are mass assignable.
	*
	* @var array
	*/
	protected $fillable = [
		'level',
		'days',
		'id_obligation',
		'id_user',
	];

	/**
	 * The relationships that should always be loaded.
	 *
	 * @var array
	 */
	protected $with = [
		// 'user',
	];
	
	/**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_included = [
		'user',
	];

	/**
	 * Get the user that owns the AuditorObligation
	 */
	public function user()
	{
		return $this->belongsTo(User::class, 'id_user', 'id_user');
	}
}