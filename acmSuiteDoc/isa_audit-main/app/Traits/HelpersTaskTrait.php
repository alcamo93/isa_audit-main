<?php

namespace App\Traits;

use App\Models\Admin\ProcessesModel;
use App\Models\Audit\TasksModel;
use App\Models\Audit\ActionPlansModel;
use App\Models\Audit\ActionExpiredModel;
use App\User;
use App\Classes\StatusConstants;
use App\Notifications\TaskAssignmentNotifications;

trait HelpersTaskTrait {
    
    /**
     * Build structure notify user by task
     */
    public function buildTaskUserNotify($idTask, $user) {
        $processCallback = function($query) {
            $query->addSelect([
                'audit_processes' => ProcessesModel::select('audit_processes')
                ->whereColumn('id_audit_processes', 't_action_plans.id_audit_processes')
                ->take(1)
            ]);
        };
        $relationships = [
            'users',
            'action' => $processCallback,
            'action.requirement', 
            'action.subrequirement',
        ];
        $task = TasksModel::with($relationships)->find($idTask);
        $forName = $task->action;
        $reqName = ( is_null($forName->subrequirement) ) ? $forName->requirement->no_requirement : $forName->requirement->no_requirement.' - '.$forName->subrequirement->no_subrequirement;
        $taskUser = collect($task->users);
        $level = $taskUser->firstWhere('id_user', $user)->level;
        $typeLevels = [
            1 => 'Principal',
            2 => 'Secundario', 
            3 => 'Terciario'
        ];
        $data = [
            'title' => 'Le ha sido asignada una tarea',
            'body' => 'Ha sido asignado como 
                <b>Responsable '.$typeLevels[$level].'</b>
                de la tarea <b>'.$task->title.'</b> 
                para el requerimiento: <b>'.$reqName.'</b> correspodiente 
                a la auditoría <b>'.$forName->audit_processes.'</b>',
            'description' => '',
            'link' => 'action'
        ];
        return $data;
    }
    /**
     * send notification to user
     */
    public function notifyTaskUser($idTask, $users) {
        foreach ($users as $u) {
            $build = $this->buildTaskUserNotify($idTask, $u);
            $notify = User::findOrFail($u);
            $notify->notify(new TaskAssignmentNotifications($build));
        } 
    }
}
