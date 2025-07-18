<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Admin\User;
use App\Models\V2\Audit\ActionPlan;
use App\Traits\V2\UtilitiesTrait;

class PlanUser extends Model
{
	use UtilitiesTrait;

	protected $table = 't_plan_user';
	protected $primaryKey = 'id_plan_user';

	/*
	* The attributes that are mass assignable.
	*
	* @var array
	*/
	protected $fillable = [
		'level',
		'days',
		'id_user',
		'id_action_plan',
	];

	/**
	 * The relationships that should always be loaded.
	 *
	 * @var array
	 */
	protected $with = [
		// 'user',
		// 'action',
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
	 * Get the user that owns the PlanUser
	 */
	public function user()
	{
		return $this->belongsTo(User::class, 'id_user', 'id_user');
	}

	/**
	 * Get the action that owns the PlanUser
	 */
	public function action()
	{
		return $this->belongsTo(ActionPlan::class, 'id_action_plan', 'id_action_plan');
	}
}