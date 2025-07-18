<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Audit\Task;
use App\Models\V2\Catalogs\Status;

class TaskExpired extends Model
{
  protected $table = 't_task_expired';
  protected $primaryKey = 'id_task_expired';
  
  protected $fillable = [
    'init_date',
    'close_date',
    'id_task', 
    'id_status', 
  ];

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'status',
  ];

  public function task() 
  {
    return $this->belongsTo(Task::class, 'id_task', 'id_task');
  }

  public function status() 
  {
    return $this->belongsTo(Status::class, 'id_status', 'id_status');
  }
}