<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;

class PlanUserModel extends Model
{
    protected $table = 't_plan_user';
    protected $primaryKey = 'id_plan_user';

    public function user() {
        return $this->hasOne('App\User', 'id_user', 'id_user');
    }

    public function action() {
        return $this->belongsTo('App\Models\Audit\ActionPlansModel', 'id_action_plan', 'id_action_plan');
    }
    
    public function scopeSetUser($query, $info) {
        $model = new PlanUserModel();
        $model->id_action_plan = $info['id_action_plan'];
        $model->id_user = $info['id_user'];
        $model->days = $info['days'];
        $model->level = $info['level'];
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (\Throwable $th) {
            return StatusConstants::ERROR;
        }
    }
    
    public function scopeUpdateUser($query, $info) {
        $model = PlanUserModel::findOrFail($info['id_plan_user']);
        $model->id_user = $info['id_user'];
        $model->days = $info['days'];
        $model->level = $info['level'];
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (\Throwable $th) {
            return StatusConstants::ERROR;
        }
    }
    
    public function scopeRemoveUser($query, $idPlanUser) {
        $model = PlanUserModel::findOrFail($idPlanUser);
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (\Throwable $th) {
            return StatusConstants::ERROR;
        }
    }
}