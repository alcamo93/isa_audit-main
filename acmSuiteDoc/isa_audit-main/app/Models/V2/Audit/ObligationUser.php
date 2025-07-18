<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Admin\User;
use App\Models\V2\Audit\Obligation;
use App\Traits\V2\UtilitiesTrait;

class ObligationUser extends Model
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
		'id_user',
		'id_obligation',
	];

	/**
	 * The relationships that should always be loaded.
	 *
	 * @var array
	 */
	protected $with = [
		// 'user',
		// 'obligation',
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
	 * Get the user that owns the ObligationUser
	 */
	public function user()
	{
		return $this->belongsTo(User::class, 'id_user', 'id_user');
	}

	/**
	 * Get the obligation that owns the ObligationUser
	 */
	public function obligation()
	{
		return $this->belongsTo(Obligation::class, 'id_obligation', 'id_obligation');
	}
}