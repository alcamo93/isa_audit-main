<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\Session;

class TasksModel extends Model
{
    protected $table = 't_tasks';
    protected $primaryKey = 'id_task';

    protected $fillable = ['id_status', 'block', 'stage', 'created_at', 'updated_at'];

    // Status Group 4
    const NO_TASKS = 0; // Extra value
    const NO_STARTED = 11;
    const PROGRESS = 12;
    const EXPIRED = 14;
    const REVIEW = 15;
    const APPROVED = 19;
    const REJECTED = 26;
    // blocking values
    const BLOCK_ENABLED = 1;
    const BLOCK_DISABLED = 0;
    // stage values
    const NORMAL_STAGE = 1;
    const EXPIRED_STAGE = 2;

    public function users() {
        return $this->hasMany('App\Models\Audit\TaskUserModel', 'id_task', 'id_task');
    }

    public function status() {
        return $this->hasOne('App\Models\Catalogues\StatusModel', 'id_status', 'id_status');
    }

    public function action() {
        return $this->belongsTo('App\Models\Audit\ActionPlansModel', 'id_action_plan', 'id_action_plan');
    }

    public function actionExpired() {
        return $this->hasOne('App\Models\Audit\ActionExpiredModel', 'id_action_plan', 'id_action_plan');
    }

    public function taskExpired() {
        return $this->hasOne('App\Models\Audit\TaskExpiredModel', 'id_task', 'id_task');
    }

    public function file() {
        return $this->hasOne('App\Models\Files\FilesModel', 'id_task', 'id_task');
    }

    /**
     * Set task
     */
    public function scopeSetTask($query, $data){
        $model = new TasksModel;
        $model->title = $data['title'];
        $model->task = $data['task'];
        $model->init_date = $data['initDate'];
        $model->close_date = $data['endDate'];
        $model->id_action_plan = $data['idActionPlan'];
        $model->id_status = $data['idStatus'];
        $model->stage = $data['stage'];
        $model->save();
        $data['status'] = StatusConstants::SUCCESS;
        $data['id_task'] = $model->id_task;
        $data['block'] = $model->block;
        $data['stage'] = $model->stage;
        return $data;
    }
    /**
     * Update task
     */
    public function scopeUpdateTask($query, $data){
        try {
            $model = TasksModel::findOrFail($data['idTask']);
            $model->title = $data['title'];
            $model->task = $data['task'];
            $model->init_date = $data['initDate'];
            $model->id_status = $data['idStatus'];
            $model->close_date = $data['endDate'];
            try {
                $model->save();
                $data['status'] = StatusConstants::SUCCESS;
                $data['id_task'] = $model->id_task;
            } catch (Exception $e) {
                $data['status'] = StatusConstants::ERROR;
            }
        } catch (ModelNotFoundException $th) {
            $data['status'] = StatusConstants::ERROR;
        }
        return $data;
    }
    /**
     * Delete task
     */
    public function scopeDeleteTask($query, $idTask){
        try {
            $model = TasksModel::findOrFail($idTask);
            try {
                $model->delete();
                return StatusConstants::SUCCESS;
            } catch (Exception $e) {
                return StatusConstants::WARNING;  //on cascade exception
            }
        } catch (ModelNotFoundException $th) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * update task status
     */
    public function scopeUpdateStatusTask($query, $idStatus, $idTask){
        try {
            $model = TasksModel::findOrFail($idTask);
            try {
                $model->id_status = $idStatus;
                $model->save();
                $data['status'] = StatusConstants::SUCCESS;
                $data['task'] = $model;
                return $data;
            } catch (Exception $e) {
                $data['status'] = StatusConstants::WARNING;  //on cascade exception
                return $data;
            }
        } catch (ModelNotFoundException $th) {
            $data['status'] = StatusConstants::ERROR;
            return $data;
        }
    }
    /**
     * Delete tasks
     */
    public function scopeDeleteTasks($query, $idActionPlan){
        $model = TasksModel::where('id_action_plan', $idActionPlan);
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::WARNING;  //on cascade exception
        }
    }
    
}