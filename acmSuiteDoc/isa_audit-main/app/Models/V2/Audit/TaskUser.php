<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Admin\User;
use App\Models\V2\Audit\Task;
use App\Traits\V2\UtilitiesTrait;

class TaskUser extends Model
{
	use UtilitiesTrait;

	protected $table = 't_task_user';
	protected $primaryKey = 'id_task_user';

	/*
	* The attributes that are mass assignable.
	*
	* @var array
	*/
	protected $fillable = [
		'level',
		'days',
		'id_user',
		'id_task',
	];

	/**
	 * The relationships that should always be loaded.
	 *
	 * @var array
	 */
	protected $with = [
		// 'user',
		// 'task',
	];
	
	/**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_included = [
		'user',
	];

	const PRIMARY_LEVEL = 1;
	const SECONDARY_LEVEL = 2;

	/**
	 * Get the user that owns the TaskUser
	 */
	public function user()
	{
		return $this->belongsTo(User::class, 'id_user', 'id_user');
	}

	/**
	 * Get the task that owns the TaskUser
	 */
	public function task()
	{
		return $this->belongsTo(Task::class, 'id_task', 'id_task');
	}
}