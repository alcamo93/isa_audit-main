<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\Session;

class CommentsModel extends Model
{
    protected $table = 't_comments';
    protected $primaryKey = 'id_comment';

    public function user(){
        return $this->hasOne('App\User', 'id_user', 'id_user');
    }

    public function task(){
        return $this->belongsTo('App\Models\Audit\TasksModel', 'id_task', 'id_task');
    }
    /**
    *  save comments
    */
    public function scopeSetComment($query, $idComment, $comment, $idUser, $idActionPlan, $idTask, $idObligation, $stage) {
        if ($idComment != null) {
            $model = CommentsModel::findOrFail($idComment);
        }
        else {
            $model = new CommentsModel();
            $model->id_action_plan = $idActionPlan; 
            $model->id_task = $idTask;
            $model->id_obligation = $idObligation;
            $model->stage = $stage;
        }
        $model->comment = $comment;
        $model->id_user = $idUser;
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (\Exception $e) {
            return StatusConstants::ERROR;   
        }
    }
    /**
     * delete comment from obligations/
    */
    public function scopeDeleteComment($query, $idComment){
        $model = CommentsModel::findOrFail($idComment);
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (\Exception $e) {
            return StatusConstants::ERROR;
        }
    }
}