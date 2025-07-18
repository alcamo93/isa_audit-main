<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\Session;

class ActionExpiredModel extends Model
{
    protected $table = 't_action_expired';
    protected $primaryKey = 'id_action_expired';

    protected $fillable = ['cause', 'real_close_date', 'id_status', 'created_at', 'updated_at'];
    
    public function action() {
        return $this->belongsToMany('App\Models\Audit\ActionPlanModel', 'id_action_plan', 'id_action_plan');
    }

    public function tasks() {
        return $this->belongsTo('App\Models\Audit\TasksModel', 'id_action_plan', 'id_action_plan');
    }

    public function tasksExpired()
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

    public function status() {
        return $this->hasOne('App\Models\Catalogues\StatusModel', 'id_status', 'id_status');
    }
    /**
     * set expired
     */
    public function scopeSetExpired($query, $info){
        $model = new ActionExpiredModel();
        $model->cause = $info['cause'];
        $model->real_close_date = $info['realCloseDate'];
        $model->id_status = ActionPlansModel::UNASSIGNED_AP;
        $model->id_action_plan = $info['idActionPlan'];
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            if ($e->errorInfo[1] == 1062) {
                return StatusConstants::DUPLICATE;
            }else{
                return StatusConstants::ERROR;
            } 
        }
    }
}
