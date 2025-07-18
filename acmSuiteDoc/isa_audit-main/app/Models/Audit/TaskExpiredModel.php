<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Audit\TasksModel;
use App\Classes\StatusConstants;

class TaskExpiredModel extends Model
{
    protected $table = 't_task_expired';
    protected $primaryKey = 'id_task_expired';
    
    protected $fillable = ['close_date', 'id_status', 'created_at', 'updated_at'];

    public function task() {
        return $this->belongsTo('App\Models\Audit\TasksModel', 'id_task', 'id_task');
    }

    public function status() {
        return $this->hasOne('App\Models\Catalogues\StatusModel', 'id_status', 'id_status');
    }
    /**
     * set
     */
    public function scopeSetExpired($query, $initDate, $info){
        $model = new TaskExpiredModel();
        $model->init_date = $initDate;
        $model->close_date = $info['endDate'];
        $model->id_status = TasksModel::NO_STARTED;
        $model->id_task = $info['idTask'];
        try {
            $model->save();
            $data['status'] = StatusConstants::SUCCESS;
            $data['id_task'] = $model->id_task;
        } catch (Exception $e) {
            if ($e->errorInfo[1] == 1062) {
                $data['status'] = StatusConstants::DUPLICATE;
            }
            else {
                $data['status'] = StatusConstants::ERROR;
            } 
        }
        return $data;
    }
}
