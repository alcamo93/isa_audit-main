<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;

class PeopleModel extends Model
{
    protected $table = 't_people';
    protected $primaryKey = 'id_person';

    public function user() {
        return $this->belongsTo('App\User', 'id_person', 'id_person');
    }
    /**
     * Get person data
     */
    public function scopeGetPerson($query, $idPerson){
        $query->select('t_people.id_person', 't_people.first_name', 't_people.second_name', 't_people.last_name',
                't_people.rfc', 't_people.gender', 't_people.phone', 
                \DB::raw('DATE_FORMAT(t_people.birthdate, "%Y-%m-%d") AS birthdate'))
            ->where('t_people.id_person', $idPerson);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Set person in register
     */
    public function scopeSetPerson($query, $data){
        $model = new PeopleModel;
        $model->first_name = $data['name'];
        $model->second_name = $data['secondName'];
        $model->last_name = ($data['lastName'] == null) ? '' : $data['lastName'];
        try {
            $model->save();
            $data['status'] = StatusConstants::SUCCESS;
            $data['idPerson'] = $model->id_person;
        } catch (Exception $e) {
            $data['status'] = StatusConstants::ERROR;
        }
        return $data;
    }
    /**
     * Update person in register
     */
    public function scopeUpdatePerson($query, $data){
        try {
            $model = PeopleModel::findOrFail($data['idPerson']);
            $model->first_name = $data['name'];
            $model->second_name = $data['secondName'];
            $model->last_name = ($data['lastName'] == null) ? '' : $data['lastName'];
            try {
                $model->save();
                return StatusConstants::SUCCESS;
            } catch (Exception $e) {
                return StatusConstants::WARNING;
            }
        } catch (ModelNotFoundException $th) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Delete person
     */
    public function scopeDeletePerson($query, $idUser){
        try {
            $model = PeopleModel::findOrFail($idUser);
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
     * Update data in account
     */
    public function scopeSetPersonAccount($query, $data){
        try {
            $model = PeopleModel::findOrFail($data['idPerson']);
            if ($data['name'] != null) {
                $model->first_name = $data['name'];
            }
            if ($data['secondName'] != null) {
                $model->second_name = $data['secondName'];
            }
            if ($data['lastName'] != null) {
                $model->last_name = $data['lastName'];
            }
            if ($data['rfc'] != null) {
                if ($model->rfc != $data['rfc']) {
                    $model->rfc = $data['rfc'];
                }
            }
            if ($data['gender'] != null) {
                $model->gender = $data['gender'];
            }
            if ($data['cell'] != null) {
                $model->phone = $data['cell'];
            }

            if ($data['birthdate'] != null) {
                $model->birthdate = $data['birthdate'].' 00:00:00';
            }
            try {
                $model->save();
                return StatusConstants::SUCCESS;
            } catch (Exception $e) {
                if ($e->errorInfo[1] == 1062) {
                    return StatusConstants::DUPLICATE; // Duplicate entry
                }else{
                    return StatusConstants::WARNING;
                }
            }
        } catch (ModelNotFoundException $th) {
            return StatusConstants::ERROR;
        }
    }
}