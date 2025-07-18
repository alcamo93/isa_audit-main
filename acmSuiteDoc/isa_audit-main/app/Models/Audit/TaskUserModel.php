<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\Session;

class TaskUserModel extends Model
{
    protected $table = 't_task_user';
    protected $primaryKey = 'id_task_user';

    public function user() {
        return $this->hasOne('App\User', 'id_user', 'id_user');
    }

    public function task() {
        return $this->belongsTo('App\Models\Audit\TasksModel', 'id_task', 'id_task');
    }

    public function scopeSetUser($query, $info) {
        $model = new TaskUserModel();
        $model->id_task = $info['id_task'];
        $model->id_user = $info['id_user'];
        $model->days = $info['days'];
        $model->level = $info['level'];
        $model->save();
        $data['id_user'] = $model->id_user;
        $data['status'] = StatusConstants::SUCCESS;
        return $data;
    }
    
    public function scopeUpdateUser($query, $info) {
        $model = TaskUserModel::findOrFail($info['id_task_user']);
        $data['id_user_old'] = $model->id_user;
        $model->id_user = $info['id_user'];
        $model->days = $info['days'];
        $model->level = $info['level'];
        $model->save();
        $data['id_user'] = $model->id_user;
        $data['status'] = StatusConstants::SUCCESS;
        return $data;
    }
    
    public function scopeRemoveUser($query, $idTaskUser) {
        $model = TaskUserModel::findOrFail($idTaskUser);
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (\Throwable $th) {
            return StatusConstants::ERROR;
        }
    }
}