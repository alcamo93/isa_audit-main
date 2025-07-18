<?php

namespace App\Traits;

use Illuminate\Support\Facades\Session;
use App\Models\Audit\TasksModel;
use App\Models\Audit\ActionPlansModel;
use App\Classes\ProfilesConstants;

trait PermissionActionPlanTrait {
    
    /**
     * Get permission for requirements
     */
    public function getPermissionReq($idActionPlan){
        $permissions = true;
        // user and level role
        $idUser = Session::get('user')['id_user'];
        $idProfileType = Session::get('profile')['id_profile_type'];
        // condition role operative
        if ($idProfileType == ProfilesConstants::OPERATIVE) {
            $requirement = ActionPlansModel::with('users')->find($idActionPlan);
            $users = collect($requirement->users);
            $hasPermission = $users->contains('id_user', $idUser);
            $permissions = $hasPermission;
        }
        return $permissions;
    }
    /**
     * Get permission for task
     */
    public function getPermissionTask($idTask){
        $permissions['inTask'] = true;
        $permissions['inApproved'] = true;
        $permissions['inUpload'] = true;
        // user and level role
        $idUser = Session::get('user')['id_user'];
        $idProfileType = Session::get('profile')['id_profile_type'];
        // instance
        $relationship = ['users', 'action.users'];
        $tasks = TasksModel::with($relationship)->find($idTask);
        if ($idProfileType == ProfilesConstants::COORDINATOR) {
            $usersTask = collect($tasks->users)->contains('id_user', $idUser);
            $usersReq = collect($tasks->action->users)->contains('id_user', $idUser);
            $hasPermission = $usersReq || $usersTask;
            $permissions['inApproved'] = $usersReq;
            $permissions['inUpload'] = $usersTask;
        }
        // condition role operative
        if ($idProfileType == ProfilesConstants::OPERATIVE) {
            $usersTask = collect($tasks->users)->contains('id_user', $idUser);
            $usersReq = collect($tasks->action->users)->contains('id_user', $idUser);
            $hasPermission = $usersReq || $usersTask;
            $permissions['inTask'] = $hasPermission;
            $permissions['inApproved'] = $usersReq;
            $permissions['inUpload'] = $usersTask;
        }
        return $permissions;
    }
}