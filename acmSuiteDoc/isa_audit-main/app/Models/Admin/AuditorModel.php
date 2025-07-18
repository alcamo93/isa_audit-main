<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;

class AuditorModel extends Model
{
    protected $table = 't_auditor';
    protected $primaryKey = 'id_auditor';
    /**
     * Return info Auditors
     */
    public function scopeGetAuditor($query, $type){
        $query->where('t_auditor.leader', $type);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get auditors
     */
    public function scopeGetAuditorsByProcess($query, $idProcess, $type){
        $query->where([
            ['t_auditor.id_audit_processes', $idProcess],
            ['t_auditor.leader', $type]
        ]);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Set
     */
    public function scopeSetLeader($query, $info, $idProcesses){
        $model = new AuditorModel();
        $model->id_user = $info['idLeader'];
        $model->id_audit_processes = $idProcesses;
        $model->leader = StatusConstants::LEADER;
        try {
            $model->save();
            $data['status'] = StatusConstants::SUCCESS;
            $data['idLeader'] = $model->id_auditor;
        } catch (Exception $e) {
            $data['status'] = StatusConstants::ERROR;
        }
        return $data;
    }
    /**
     * Update
     */
    public function scopeUpdateLeader($query, $idUserNew, $idProcesses) {
        try {
            $model = AuditorModel::where([
                ['id_audit_processes', $idProcesses],
                ['leader', StatusConstants::LEADER],
            ])->firstOrFail();
        } catch (ModelNotFoundException $th) {
            $data['status'] = StatusConstants::WARINING;
            return $data;
        }
        $model->id_user = $idUserNew;
        try {
            $model->save();
            $data['status'] = StatusConstants::SUCCESS;
            $data['idLeader'] = $model->id_auditor;
        } catch (Exception $e) {
            $data['status'] = StatusConstants::ERROR;
        }
        return $data;
    }
    /**
     * Set auditors
     */
    public function scopeSetAuditor($query, $auditor, $idProcesses){
        $model = new AuditorModel();
        $model->id_user = $auditor;
        $model->id_audit_processes = $idProcesses;
        $model->leader = StatusConstants::NO_LEADER;
        try {
            $model->save();
            $data['status'] = StatusConstants::SUCCESS;
            $data['idAuditor'] = $model->id_auditor;
        } catch (Exception $e) {
            $data['status'] = StatusConstants::ERROR;
        }
        return $data;
    }
    /**
     * Delete auditor by process
     */
    public function scopeDeleteAuditors($query, $idProcess){
        try {
            $model = AuditorModel::where([
                ['id_audit_processes', $idProcess],
                ['leader', StatusConstants::NO_LEADER]
            ]);
        } catch (ModelNotFoundException $th) {
            return StatusConstants::WARNING;
        }
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (QueryException $th) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Delete auditor
     */
    public function scopeDeleteAuditor($query, $idAuditor){
        try {
            $model = AuditorModel::findOrFail($idAuditor);
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
     * Get auditors
     */
    public function scopeGetAuditorsForAP($query, $idProcess, $types){
        $query->join('t_users', 't_users.id_user', 't_auditor.id_user')
            ->join('t_people', 't_people.id_person', 't_users.id_person')
            ->select(
                't_auditor.*',
                \DB::raw('CONCAT(t_people.first_name,\' \',t_people.second_name) as complete_name')
            )
            ->where('t_auditor.id_audit_processes', $idProcess)
            ->whereIn('t_auditor.leader', $types);
        $data = $query->get()->toArray();
        return $data;
    }
}