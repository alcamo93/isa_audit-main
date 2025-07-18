<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;

class AuditMattersModel extends Model
{
    protected $table = 'r_audit_matters';
    protected $primaryKey = 'id_audit_matter';

    /**
     * Get all of the comments for the AuditMattersModel
     */
    public function aspects()
    {
        return $this->hasMany('App\Models\Audit\AuditAspectsModel', 'id_audit_matter', 'id_audit_matter');
    }

    /**
     * Get the user that owns the AuditMattersModel
     */
    public function matter()
    {
        return $this->belongsTo('App\Models\Catalogues\MattersModel', 'id_matter', 'id_matter');
    }
    /**
     * Get the user that owns the AuditMattersModel
     */
    public function status()
    {
        return $this->belongsTo('App\Models\Catalogues\StatusModel', 'id_status', 'id_status');
    }

    public function scopeGetContractMatters($query, $idAuditRegister){
        $query->join('c_matters', 'c_matters.id_matter', 'r_audit_matters.id_matter')
            ->where('r_audit_matters.id_audit_register', $idAuditRegister);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Set current status quiz by apsect
     */
    public function scopeSetStatusMatter($query, $idAuditMatter, $idStatus){
        try {
            $model = AuditMattersModel::find($idAuditMatter);
            try {
                $model->id_status = $idStatus;
                $model->save();
                return StatusConstants::SUCCESS;
            } catch (Exception $e) {
                return StatusConstants::ERROR;
            }
        } catch (ModelNotFoundException $th) {
            return StatusConstants::WARNING;
        }
    }
    /**
     * Set current status matter
     */
    public function scopeSetFinishedMatters($query, $idAuditRegister){
        $model = AuditMattersModel::where([
            ['r_audit_matters.id_audit_register', $idAuditRegister],
            ['r_audit_matters.id_status', StatusConstants::AUDITED]
        ]);
        try {
            $model->update(['r_audit_matters.id_status' => StatusConstants::FINISHED_AUDIT]);
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Set audit matters
     */
    public function scopeSetAuditMatters($query, $matter, $idAuditProcess, $idAuditRegister){
        // Search matter
        try {
            $model = AuditMattersModel::where([
                ['id_audit_register', $idAuditRegister],
                ['id_audit_processes', $idAuditProcess],
                ['id_contract', $matter['id_contract']],
                ['id_matter', $matter['id_matter']],
            ])->firstOrFail();
        // New Matter
        } catch (ModelNotFoundException $th) {
            $model = new AuditMattersModel();
            $model->self_audit = $matter['self_audit'];
            $model->id_audit_register = $idAuditRegister;
            $model->id_audit_processes = $idAuditProcess;
            $model->id_contract = $matter['id_contract'];
            $model->id_matter =  $matter['id_matter'];
            $model->id_status = StatusConstants::NOT_AUDITED;
        }
        // Save New Matter
        try {
            $model->save();
            $data['idAuditMatter'] = $model->id_audit_matter;
            $data['idMatter'] = $model->id_matter;
            $data['status'] = StatusConstants::SUCCESS;
            return $data;
        } catch (Exception $e) {
            $data['status'] = StatusConstants::ERROR;
            return $data;
        }
        
    }
    /**
     * Delete Matters by id audit matter
     */
    public function scopeDeleteAuditMatter($query, $idAuditMatter){
        try {
            $model = AuditMattersModel::findOrFail($idAuditMatter);
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
     * Count matter by status
     */
    public function scopeCountMattersByStataus($query, $idAuditRegister, $idStatus){
        $count = AuditMattersModel::where([
            ['id_audit_register', $idAuditRegister],
            ['id_status', $idStatus]
        ])->count();
        return $count;
    }
    /**
     * Get Audit Matters By Contract
     */
    public function scopeGetAuditMattersByContract($query, $idContract){
        $query->join('c_matters', 'c_matters.id_matter', 'r_audit_matters.id_matter')
            ->select('r_audit_matters.*', 'c_matters.matter')
            ->where('r_audit_matters.id_contract' ,$idContract);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get Matter by status
     */
    public function scopeGetContractMattersByStatus($query, $idAuditRegister, $idStatus){
        $query->join('c_matters', 'c_matters.id_matter', 'r_audit_matters.id_matter')
            ->select('r_audit_matters.*', 'c_matters.matter')
            ->where('r_audit_matters.id_status', $idStatus)
            ->where('r_audit_matters.id_audit_register', $idAuditRegister);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get Matter by status
     */
    public function scopeGetMattersByStatusAuditProcess($query, $idAuditProcess, $idStatus){
        $query->join('c_matters', 'c_matters.id_matter', 'r_audit_matters.id_matter')
            // ->select('r_audit_matters.*', 'c_matters.matter')
            ->where('r_audit_matters.id_status', $idStatus)
            ->where('r_audit_matters.id_audit_processes', $idAuditProcess);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * update total percent
     */
    public function scopeUpdatePercent($query, $idAuditMatter, $percent){
        try {
            $model = AuditMattersModel::find($idAuditMatter);
            try {
                $model->total = $percent;
                $model->save();
                $data['status'] = StatusConstants::SUCCESS;
                $data['idAuditRegister'] = $model->id_audit_register;
                return $data;
            } catch (Exception $e) {
                $data['status'] = StatusConstants::ERROR;
                return $data;
            }
        } catch (ModelNotFoundException $th) {
            $data['status'] = StatusConstants::WARNING;
            return $data;
        }
    }
    public function scopeGetMatterId($query, $idAuditRegister){
        $query->select('r_audit_matters.*')
            ->where('r_audit_matters.id_audit_register' ,$idAuditRegister);
        $data = $query->get()->toArray();
        return $data;
    }
}