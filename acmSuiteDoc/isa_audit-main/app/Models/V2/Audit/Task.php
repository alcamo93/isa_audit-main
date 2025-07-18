<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Admin\User;
use App\Models\V2\Audit\ActionPlan;
use App\Models\V2\Audit\ExpiredCause;
use App\Models\V2\Audit\TaskNotification;
use App\Models\V2\Audit\EvaluateRequirement;
use App\Models\V2\Audit\Comment;
use App\Models\V2\Catalogs\Status;
use App\Traits\V2\UtilitiesTrait;
use App\Traits\V2\PermissionFileTrait;

class Task extends Model
{
  use UtilitiesTrait, PermissionFileTrait;
  
  protected $table = 't_tasks';
  protected $primaryKey = 'id_task';

  protected $fillable = [
    'title',
    'task',
		'init_date',
		'close_date',
    'block',
    'stage',
    'main_task',
		'id_action_plan',
		'id_status',
	];

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'status',
    // 'auditors',
    // 'evaluates',
  ];
  
  /**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_included = [
    'action',
    'notifications'
  ];
  
  // Task status
  const NO_TASKS = 0; // Extra value
  const NO_STARTED_TASK = 11;
  const PROGRESS_TASK = 12;
  const EXPIRED_TASK = 14;
  const REVIEW_TASK = 15;
  const APPROVED_TASK = 19;
  const REJECTED_TASK = 26;
  // blocking values
  const BLOCK_ENABLED = 1;
  const BLOCK_DISABLED = 0;
  // stage values
  const NORMAL_STAGE = 1;
  const EXPIRED_STAGE = 2;
  // is main
  const MAIN_TASK = 1;
  const NO_MAIN_TASK = 1;

  /**
	 * Attributes 
	 */
	protected $appends = [
		'init_date_format',
		'close_date_format',
    'permissions',
    'type_task',
	];

  /**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_filter = [
    'id_action_plan' => ['field' => 'id_action_plan', 'type' => 'number', 'relation' => null],
    'id_status' => ['field' => 'id_status', 'type' => 'number', 'relation' => null],
  ];

  /*
	* Get init_date format
	*/
	public function getInitDateFormatAttribute()
	{
    return $this->getFormatDate($this->init_date);
	}

  /* 
	* Get close_date format
	*/
	public function getCloseDateFormatAttribute()
	{
    return $this->getFormatDate($this->close_date);
	}

  /**
   * get permission for user loggin per task
   */
  public function getPermissionsAttribute() 
  {
    return $this->getPermissionTask($this);
  }

  /**
   * get permission for user loggin per task
   */
  public function getTypeTaskAttribute() 
  {
    return $this->main_task == Task::MAIN_TASK ? 'Tarea' : 'Subtarea';
  }

  /**
   * Get the action plan associated with the Tasks
   */
  public function action() 
  {
    return $this->belongsTo(ActionPlan::class, 'id_action_plan', 'id_action_plan');
  }

  /**
   * Get the status associated with the Tasks
   */
  public function status()
  {
    return $this->belongsTo(Status::class, 'id_status', 'id_status');
  }

  /**
   * The auditors that belong to the ActionPlan
   */
  public function auditors() 
  {
    return $this->belongsToMany(User::class, 't_task_user', 'id_task', 'id_user')
			->withPivot('level', 'days');
  }

  /**
   * Get all of the comments for the Task
   *
   */
  public function comments()
  {
    return $this->morphMany(Comment::class, 'commentable');
  }

  /**
   * Get the task expired associated with the Task
   */
  public function expired() 
  {
    return $this->morphMany(ExpiredCause::class, 'expiredable');
  }

  /**
   * Get the last audit associated to Task
   */
  public function last_expired() 
  {
    return $this->morphOne(ExpiredCause::class, 'expiredable')->latestOfMany('id');
  }

  /**
	 * Get all of the EvaluateRequirement for the Task
	 */
	public function evaluates()
	{
		return $this->morphToMany(EvaluateRequirement::class, 'evaluateable', 'evaluateables', 'evaluateable_id', 'evaluate_requirement_id');
	}

  /**
	 * Get all of the TaskNotification for the Library
	 */
	public function notifications()
	{
		return $this->hasMany(TaskNotification::class, 'task_id', 'id_task');
	}

  public function scopeCustomFilter($query, $idActionPlan)
  {
    $query->where('id_action_plan', $idActionPlan);

    $filters = request('filters');
    if ( empty($filters) ) return;

    if ( isset($filters['id_status']) ) {
      $query->where('id_status', $filters['id_status']);
    }
  }

  public function scopeTaskMainByAP($query)
  {  
    $option = request('option');
    if ( empty($option) && $option == 'files') return;

    $filters = request('filters');
		if ( empty($filters) && isset($filters['id_action_register']) ) return;
    
    if ( isset($filters['id_action_register']) ) {
			$query->whereHas('action', function($subquery) use ($filters) {
				$subquery->where('id_action_register', $filters['id_action_register']);
			})
      ->where('main_task', 1);
		}
  }
}