<?php

namespace App\Classes\ActionPlan;

use App\Models\V2\Audit\ActionPlan;
use App\Models\V2\Audit\Task;
use App\Models\V2\Catalogs\Status;
use Carbon\Carbon;

class UtilitiesActionPlan 
{
  public function __construct()
  {
    
  }

  /**
   * Set dates in action by tasks
   */
  public function setDatesActionByTasks($idActionPlan)
  {
    try {
      $tasks = Task::where('id_action_plan', $idActionPlan)->get();
      $actionPlan = ActionPlan::withExists('last_expired')->with('last_expired')->find($idActionPlan);
      
      $minDate = $tasks->pluck('init_date')->min();
      $maxDate = $actionPlan->exist_last_expired ? $actionPlan->extension_date : $tasks->pluck('close_date')->max();

      $actionPlan->update(['init_date' => $minDate, 'close_date' => $maxDate]);

      $info['success'] = true;
      $info['message'] = 'Fechas establecidas';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'No se pudo reestablecer las fechas del requerimiento';
      return $info;
    }
  }
  /**
   * Get status task by dates
   */
  public function getStatusTaskByDates($initDate, $endDate)
  {
    try {
      $timezone = Config('enviroment.time_zone_carbon');
      $today = Carbon::now($timezone)->toDateString();
      $inRange = ($today >= $initDate) && ($today <= $endDate);
      $isPast = $today > $endDate;
      $idStatus = ($inRange) ? Task::PROGRESS_TASK : Task::NO_STARTED_TASK;
      $idStatus = ($isPast) ? Task::EXPIRED_TASK : $idStatus;
      
      $info['success'] = true;
      $info['message'] = 'Estatus calculado';
      $info['id_status'] = $idStatus;
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'La Tarea de Cierre no puede estar en estatus Vencido';
      return $info;
    }
  }
  /**
   * set status ap by status tasks
   */
  public function statusActionByTask($idActionPlan)
  {
    try {
      $status = Status::select('id_status', 'status')->where('group', Status::TASK_GROUP)->get();
      $action = ActionPlan::with(['tasks'])->find($idActionPlan);

      $countStatus = [];
      $tasksEvaluate = $action->tasks;
      $countTotalTasks = $tasksEvaluate->count();
      foreach ($status as $key => $item) {
        $countStatus[$key]['id_status'] = $item->id_status;
        $countStatus[$key]['status'] = $item->status;
        $countStatus[$key]['count'] = $tasksEvaluate->where('id_status', $item->id_status)->count();
      }
      $idStatusReq = $this->statusByCountTask($countStatus, $countTotalTasks, $action->id_status);
      $action->update(['id_status' => $idStatusReq]);

      $info['success'] = true;
      $info['message'] = 'Estaus establecido';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'No se pudo reestablecer el estatus del requerimiento';
      return $info;
    }
  }

  /**
   * count tasks by status
   */
  private function statusByCountTask($countStatus, $countTotalTasks, $statusActionPlan) 
  {
    $countStatus = collect($countStatus);
    // if there are not tasks, to return status UNASSIGNED_AP
    if ($countTotalTasks == 0) return ActionPlan::UNASSIGNED_AP;
    // condition for expired requirement
    $expired = $countStatus->where('id_status', Task::EXPIRED_TASK)->first();
    if ( $expired['count'] > 0 && $statusActionPlan != ActionPlan::CLOSED_AP ) {
      return ActionPlan::EXPIRED_AP;
    }
    // condition for approved requirement
    $approved = $countStatus->where('id_status', Task::APPROVED_TASK)->first();
    if ($approved['count'] == $countTotalTasks) {
      return ActionPlan::COMPLETED_AP;
    }
    // sum status for progress
    $noStarted = $countStatus->where('id_status', Task::NO_STARTED_TASK)->first();
    $progress = $countStatus->where('id_status', Task::PROGRESS_TASK)->first();
    $rejected = $countStatus->where('id_status', Task::REJECTED_TASK)->first();
    $totalProgress = $noStarted['count'] + $progress['count'] + $rejected['count'];
    // sum status for no progress
    $review = $countStatus->where('id_status', Task::REVIEW_TASK)->first();
    $totalNoProgress = $expired['count'] + $approved['count'] + $review['count'];
    // condition for porgress requirement
    if ( $totalProgress >= $totalNoProgress ) {
      return ActionPlan::PROGRESS_AP;
    }
    // condition for review requirement
    if ( $review['count'] > 0 ) {
      return ActionPlan::REVIEW_AP;
    }
    // if there are not matches, then to return same requirement status
    return $statusActionPlan;
  }
}