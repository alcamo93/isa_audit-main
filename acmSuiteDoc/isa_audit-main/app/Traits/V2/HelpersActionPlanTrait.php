<?php

namespace App\Traits\V2;

use App\Models\V2\Audit\Task;
use App\Models\V2\Audit\ActionPlan;
use App\Models\V2\Audit\ActionExpired;
use App\Models\V2\Catalogs\Status;
use App\Models\Admin\ProfilesModel;
use App\Models\Admin\PeopleModel;
use App\User;
use App\Classes\StatusConstants;
use App\Notifications\ExpiredActionExpiredNotifications;
use Carbon\Carbon;
use Config;

trait HelpersActionPlanTrait 
{
  /**
   * Set dates in action by tasks
   */
  public function setDatesActionByTasks($idActionPlan)
  {
    try {
      $tasks = Task::where('id_action_plan', $idActionPlan)->get();
      $minDate = $tasks->pluck('init_date')->min();
      $maxDate = $tasks->pluck('close_date')->max(); 
      ActionPlan::find($idActionPlan)
        ->update(['init_date' => $minDate, 'close_date' => $maxDate]);
      return true;
    } catch (\Throwable $th) {
      return false;
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
      return $idStatus;
    } catch (\Throwable $th) {
      return 0;
    }
  }
  /**
   * set status ap by status tasks
   */
  public function statusActionByTask($idActionPlan)
  {
    try {
      $status = Status::select('id_status', 'status')->where('group', 4)->get();
      $action = ActionPlan::with(['tasks', 'tasks'])->find($idActionPlan);
      $tasks = $action->tasks;
      // count tasks by status
      $countStatus = [];
      $countTotalTasks = $tasks->count();
      foreach ($status as $key => $s) {
        $countStatus[$key]['id_status'] = $s->id_status;
        $countStatus[$key]['status'] = $s->status;
        $countStatus[$key]['count'] = $tasks->where('id_status', $s->id_status)->count();
      }
      $idStatusReq = $this->statusByCountTask($countStatus, $countTotalTasks);
      // set status to requirement according to stage 
      return $action->update(['id_status' => $idStatusReq]);
    } catch (\Throwable $th) {
      return false;
    }
  }

  /**
   * count tasks by status
   */
  public function statusByCountTask($countStatus, $countTotalTasks) 
  {
    $countStatus = collect($countStatus);
    $idStatusReq = ActionPlan::UNASSIGNED_AP;
    if ($countTotalTasks > 0) {
      // condition for expired requirement
      $expired = $countStatus->where('id_status', Task::EXPIRED_TASK)->first()['count'];
      if ($expired > 0) {
        $idStatusReq = ActionPlan::EXPIRED_AP;
        return $idStatusReq;
      }
      // condition for approved requirement
      $approved = $countStatus->where('id_status', Task::APPROVED_TASK)->first()['count'];
      if ($approved == $countTotalTasks) {
        $idStatusReq = ActionPlan::COMPLETED_AP;
        return $idStatusReq;
      }
      // condition for porgress requirement
      $noStarted = $countStatus->where('id_status', Task::NO_STARTED_TASK)->first()['count'];
      $progress = $countStatus->where('id_status', Task::PROGRESS_TASK)->first()['count'];
      $rejected = $countStatus->where('id_status', Task::REJECTED_TASK)->first()['count'];
      $totalProgress = $noStarted + $progress + $rejected;
      $review = $countStatus->where('id_status', Task::REVIEW_TASK)->first()['count'];
      $totalNoProgress = $expired + $approved + $review;
      if ( $totalProgress >= $totalNoProgress ) {
        $idStatusReq = ActionPlan::PROGRESS_AP;
        return $idStatusReq;
      }
      // condition for review requirement
      $totalNoReview = $expired + $approved + $noStarted + $progress + $rejected;
      if ( $review > 0 ) {
        $idStatusReq = ActionPlan::REVIEW_AP;
        return $idStatusReq;
      }
    } else return $idStatusReq;
  }
}