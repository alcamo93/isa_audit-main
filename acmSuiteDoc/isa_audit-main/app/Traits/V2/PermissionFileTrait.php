<?php

namespace App\Traits\V2;

use Illuminate\Support\Facades\Session;
use App\Models\V2\Catalogs\ProfileType;
use Illuminate\Support\Facades\Auth;

trait PermissionFileTrait 
{    
  /**
   * Get permission for requirements in section
   */
  public function getPermissionRequirement($model, $section)
  {
    $permissions['can_approve'] = true;
    $permissions['can_upload'] = true;
    // no has user auth
    if ( !Auth::check() ) return $permissions;
    // user and level role
    $idUser = isset(Session::get('user')['id_user']);
    $idProfileType = Session::get('profile')['id_profile_type'] ?? 1;
    if ($idProfileType != ProfileType::OPERATIVE) return $permissions;
    
    // condition role operative in obligations
    if ($section == 'obligation') {
      $hasAuditor = !is_null($model->auditor);
      $hasPermission = $hasAuditor ? $model->auditor->id_user == $idUser : false;
      $permissions['can_approve'] = $hasPermission;
      $permissions['can_upload'] = $hasPermission;
      return $permissions;
    }
    // condition role operative in action plan
    if ($section == 'action') {
      $auditors = $model->auditors;
      $permissions['can_approve'] = $auditors->contains('id_user', $idUser);
      return $permissions;
    } 
  }
  /**
   * Get permission for task
   */
  public function getPermissionTask($model)
  {
    $permissions['can_approve'] = true;
    $permissions['can_upload'] = true;
    // no has user auth
    if ( !Auth::check() ) return $permissions;
    // user and level role
    $idUser = Session::get('user')['id_user'];
    $idProfileType = Session::get('profile')['id_profile_type'];
    // check permission for profile type
    if ($idProfileType == ProfileType::OPERATIVE) {
      // search user in 
      $auditorsTask = collect($model->auditors)->contains('id_user', $idUser);
      $auditorsReq = collect($model->action->auditors)->contains('id_user', $idUser);
      $auditorApprove = ($auditorsReq || $auditorsTask) && !$auditorsTask;
      $permissions['can_approve'] = $auditorApprove;
      $permissions['can_upload'] = $auditorsTask;
    }
    return $permissions;
  }
}
