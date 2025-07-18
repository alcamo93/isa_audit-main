<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\Session;

class ObligationUserModel extends Model
{
    protected $table = 't_obligation_user';
    protected $primaryKey = 'id_obligation_user';

    public function user() {
        return $this->hasOne('App\User', 'id_user', 'id_user');
    }

    public function obligation() {
        return $this->belongsTo('App\Models\Audit\ObligationsModel', 'id_obligation', 'id_obligation');
    }

    public function scopeSetUser($query, $info) {
        $model = new ObligationUserModel();
        $model->id_obligation = $info['id_obligation'];
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
        $model = ObligationUserModel::findOrFail($info['id_obligation_user']);
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
    
    public function scopeRemoveUser($query, $idObligationUser) {
        $model = ObligationUserModel::findOrFail($idObligationUser);
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (\Throwable $th) {
            return StatusConstants::ERROR;
        }
    }
}