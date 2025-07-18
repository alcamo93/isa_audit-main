<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Audit\ActionPlan;
use App\Models\V2\Catalogs\Status;
use App\Traits\V2\UtilitiesTrait;

class ActionExpired extends Model
{
  use UtilitiesTrait;
  
  protected $table = 't_action_expired';
  protected $primaryKey = 'id_action_expired';

  /*
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
    'cause',
    'real_close_date',
    'id_status',
    'id_action_plan',
  ];
  
  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'status'
  ];

  public function expired_tasks()
  {
    return $this->hasManyThrough(
      'App\Models\Audit\TaskExpiredModel', 
      'App\Models\Audit\TasksModel',
      'id_action_plan',
      'id_task',
      'id_action_expired',
      'id_task'
    );
  }

  /**
   * Get the action plan associated with the ActionExpired
   */
  public function action() 
  {
    return $this->belongsTo(ActionPlan::class, 'id_action_plan', 'id_action_plan');
  }

  /**
   * Get the status associated with the ActionExpired
   */
  public function status() 
  {
    return $this->belongsTo(Status::class, 'id_status', 'id_status');
  }
}
