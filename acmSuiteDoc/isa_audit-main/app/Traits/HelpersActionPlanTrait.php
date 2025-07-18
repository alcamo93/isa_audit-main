<?php

namespace App\Traits;

use App\Models\Audit\TasksModel;
use App\Models\Audit\ActionPlansModel;
use App\Models\Audit\ActionExpiredModel;
use App\Models\Catalogues\StatusModel;
use App\Models\Admin\ProfilesModel;
use App\Models\Admin\PeopleModel;
use App\User;
use App\Classes\StatusConstants;
use App\Notifications\ExpiredActionExpiredNotifications;
use Carbon\Carbon;

trait HelpersActionPlanTrait {

    /**
     * Set dates in action by tasks
     */
    public function setDatesActionByTasks($idActionPlan){
        $tasks = TasksModel::where('id_action_plan', $idActionPlan)->get();
        $minDate = $tasks->pluck('init_date')->min();
        $maxDate = $tasks->pluck('close_date')->max(); 
        ActionPlansModel::find($idActionPlan)
            ->update(['init_date' => $minDate, 'close_date' => $maxDate]);
        return StatusConstants::SUCCESS;
    }
    /**
     * Get status task by dates
     */
    public function getStatusTaskByDates($initDate, $endDate){
        $today = Carbon::now('America/Mexico_City')->toDateString();
        $inRange = ($today >= $initDate) && ($today <= $endDate);
        $isPast = $today > $endDate;
        $idStatus = ($inRange) ? TasksModel::PROGRESS : TasksModel::NO_STARTED;
        $idStatus = ($isPast) ? TasksModel::EXPIRED : $idStatus;
        return $idStatus;
    }
    /**
     * set status ap by status tasks
     */
    public function statusActionByTask($idActionPlan){
        $status = StatusModel::select('id_status', 'status')->where('group', 4)->get();
        $action = ActionPlansModel::with(['expired', 'tasks', 'tasks.taskExpired'])->find($idActionPlan);
        // If $expired is null then Stage is NORMAL Else Stage is EXPIRED 
        $expired = $action->expired;
        $tasks = $action->tasks;
        if ( is_null($expired) ) {
            $tasksEvaluate = collect($tasks);
            $stage = TasksModel::NORMAL_STAGE;
        }
        else {
            $tasksEvaluate = $tasks->pluck('taskExpired');
            $stage = TasksModel::EXPIRED_STAGE;
        }
        // count tasks by status
        $countStatus = [];
        $countTotalTasks = $tasksEvaluate->count();
        foreach ($status as $key => $s) {
            $countStatus[$key]['id_status'] = $s->id_status;
            $countStatus[$key]['status'] = $s->status;
            $countStatus[$key]['count'] = $tasksEvaluate->where('id_status', $s->id_status)->count();
        }
        $idStatusReq = $this->statusByCountTask($countStatus, $countTotalTasks);
        // set status to requirement according to stage 
        if ( $stage == TasksModel::NORMAL_STAGE ) {
            $updateStatus = $action->update(['id_status' => $idStatusReq]);
        }
        else {
            $updateStatus = ActionExpiredModel::where('id_action_plan', $idActionPlan)
                ->update(['id_status' => $idStatusReq]);
        }
        return StatusConstants::SUCCESS;
    }
    /**
     * count tasks by status
     */
    public function statusByCountTask($countStatus, $countTotalTasks) {
        $countStatus = collect($countStatus);
        $idStatusReq = ActionPlansModel::UNASSIGNED_AP;
        if ($countTotalTasks > 0) {
            // condition for expired requirement
            $expired = $countStatus->where('id_status', TasksModel::EXPIRED)->first()['count'];
            if ($expired > 0) {
                $idStatusReq = ActionPlansModel::EXPIRED_AP;
                return $idStatusReq;
            }
            // condition for approved requirement
            $approved = $countStatus->where('id_status', TasksModel::APPROVED)->first()['count'];
            if ($approved == $countTotalTasks) {
                $idStatusReq = ActionPlansModel::COMPLETED_AP;
                return $idStatusReq;
            }
            // condition for porgress requirement
            $noStarted = $countStatus->where('id_status', TasksModel::NO_STARTED)->first()['count'];
            $progress = $countStatus->where('id_status', TasksModel::PROGRESS)->first()['count'];
            $rejected = $countStatus->where('id_status', TasksModel::REJECTED)->first()['count'];
            $totalProgress = $noStarted + $progress + $rejected;
            $review = $countStatus->where('id_status', TasksModel::REVIEW)->first()['count'];
            $totalNoProgress = $expired + $approved + $review;
            if ( $totalProgress >= $totalNoProgress ) {
                $idStatusReq = ActionPlansModel::PROGRESS_AP;
                return $idStatusReq;
            }
            // condition for review requirement
            $totalNoReview = $expired + $approved + $noStarted + $progress + $rejected;
            if ( $review >= $totalNoReview ) {
                $idStatusReq = ActionPlansModel::REVIEW_AP;
                return $idStatusReq;
            }
        } else return $idStatusReq;
    }
    /**
     * send notifcation to users for expired requirements in expired stage
     */
    public function expiredActionExpiredNotify($idActionPlan, $dataRequest) {
        $userCallback = function($query) {
            $query->addSelect([
                'id_profile_type' => ProfilesModel::select('id_profile_type')
                ->whereColumn('id_profile', 't_users.id_profile')
                ->take(1)
            ]);
        };
        $relationships = [
            'requirement',
            'subrequirement',
            'users.user' => $userCallback, 
            'process.corporate.users' => $userCallback];
        $plan = ActionPlansModel::with($relationships)->findOrFail($idActionPlan);
        $responsible = $plan->users->where('level', 1)->first()['user'];
        $notifyUsers = $plan->process->corporate->users
            ->where('id_user', '!=', $responsible->id_user)
            ->where('id_profile_type', '<', $responsible->id_profile_type)
            ->pluck('id_user')->toArray();
        // Notify Params
        $notifyParams['realCloseDateOld'] = str_split($dataRequest['realCloseDateOld'], 10)[0];
        $notifyParams['realCloseDate'] = $dataRequest['realCloseDate'];
        $notifyParams['cause'] = $dataRequest['cause'];
        $notifyParams['reqName'] = ( is_null($plan->subrequirement) ) ? $plan->requirement->no_requirement : $plan->requirement->no_requirement.' - '.$plan->subrequirement->no_subrequirement;
        $notifyParams['auditName'] = $plan->process->audit_processes;
        // notify build
        $titleNotify = 'Requerimiento nuevamente vencido';
        $linkNotify = 'action';
        $templatePath = 'mails.reminders.expiredActionPlan';
        $dataMail['info'] = [
            'title' => $titleNotify,
            'body' => 'Hubo un cambio en la fecha de cierre de '.$notifyParams['realCloseDateOld'].' a '.$notifyParams['realCloseDate'].' 
                en el <b>'.$notifyParams['reqName'].'</b> correspondiente a la auditoría <b>'.$notifyParams['auditName'].'</b> 
                por la causa: <b>'.$notifyParams['cause'].'</b>',
            'description' => $titleNotify,
            'link' => $linkNotify
        ];
        $dataMail['mail'] = [
            'title' => $titleNotify,
            'body' => $notifyParams,
            'link' => $linkNotify
        ];
        foreach ($notifyUsers as $key => $u) {
            $notify = User::find($u);
            $notify->notify(new ExpiredActionExpiredNotifications($dataMail, $templatePath, $titleNotify));
        }
        return StatusConstants::SUCCESS;
    }
}