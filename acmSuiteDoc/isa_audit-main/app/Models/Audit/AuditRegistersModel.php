<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuditRegistersModel extends Model
{
    protected $table = 't_audit_registers';
    protected $primaryKey = 'id_audit_register';

    public function process(){
        return $this->belongsTo('App\Models\Admin\ProcessesModel', 'id_audit_processes', 'id_audit_processes');
    }

    /**
     * Get all of the comments for the AuditRegistersModel
     */
    public function matters()
    {
        return $this->hasMany('App\Models\Audit\AuditMattersModel', 'id_audit_register', 'id_audit_register');
    }

    /**
     * Get the user that owns the AuditRegistersModel
     */
    public function status()
    {
        return $this->belongsTo('App\Models\Catalogues\StatusModel', 'id_status', 'id_status');
    }

    public function scopeSetAuditRegister($query, $data){
        try {
            $model = AuditRegistersModel::where([
                ['id_contract', $data['id_contract']],
                ['id_corporate', $data['id_corporate']],
                ['id_audit_processes', $data['id_audit_processes']]
            ])->firstOrFail();
        } catch (ModelNotFoundException $th) {
            $model = new AuditRegistersModel();
            $model->id_contract = $data['id_contract'];
            $model->id_corporate = $data['id_corporate'];
            $model->id_audit_processes = $data['id_audit_processes'];
            $model->id_status = StatusConstants::NOT_AUDITED;
        }
        try {
            $model->save();
            $response['idAuditRegister'] = $model->id_audit_register;
            $response['status'] = StatusConstants::SUCCESS;
            return $response;
        } catch (Exception $e) {
            $response['status'] = StatusConstants::ERROR;
            return $response;
        }
    }
    /**
     * Get audit registers to DT
     */
    public function scopeAuditRegistersDT($query, $page, $rows, $search, $draw, $order,
                $fIdStatus, $fIdCustomer, $fIdCorporate){
        $query->join('t_contracts', 't_contracts.id_contract', 't_audit_registers.id_contract')
            ->join('c_status', 'c_status.id_status', 't_audit_registers.id_status')
            ->join('t_corporates', 't_corporates.id_corporate', 't_audit_registers.id_corporate')
            ->join('t_customers', 't_customers.id_customer', 't_corporates.id_customer')
            ->select('t_audit_registers.id_audit_register', 'c_status.status',
                    't_contracts.contract', 't_contracts.id_contract', 't_audit_registers.total',
                    't_corporates.corp_tradename', 't_customers.cust_trademark',
                    't_audit_registers.id_corporate');
        // add filters
        if ($fIdStatus != 0) {
            $query->where('t_audit_registers.id_status', $fIdStatus);
        }
        if ($fIdCustomer != 0) {
            $query->where('t_customers.id_customer', $fIdCustomer);
        }
        if ($fIdCorporate != 0) {
            $query->where('t_corporates.id_corporate', $fIdCorporate);
        }
        //Order by
        switch ($order[0]['column']) {
            case 0:
                $columnSwitch = 't_customers.cust_trademark';
                break;
            case 1:
                $columnSwitch = 't_corporates.corp_tradename';
                break;
            case 2:
                $columnSwitch = 't_contracts.contract';
                break;
            case 3:
                $columnSwitch = 't_audit_registers.total';
                break;
            case 4:
                $columnSwitch = 'c_status.status';
                break;
            default:
                $columnSwitch = 't_customers.id_audit_register';
                break;
        }
        $column = $columnSwitch;
        $dir = (isset($order[0]['dir']) ? $order[0]['dir'] : 'desc');
        $query->orderBy($column, $dir);

        /*Search*/
        $query->where(function($query) use ($search){
            $query->where('t_corporates.corp_tradename','LIKE','%'.$search['value'].'%')
            ->orWhere('t_customers.cust_trademark','LIKE','%'.$search['value'].'%');
        });

        $queryCount = $query->get();
        $result = $query->limit($rows)->offset($page)->get()->toArray();
        $total = $queryCount->count();
        $data['data'] = ( sizeof($result) > 0) ? $result : array();
        $data['recordsTotal'] = $total;
        $data['draw'] = (int) $draw;
        $data['recordsFiltered'] = $total;
        return $data;
    }
    /**
     * Set current status in general audit
     */
    public function scopeSetStatusAudit($query, $idAuditRegister, $idStatus){
        // $today = Carbon::now('America/Mexico_City')->toDateString();
        try {
            $model = AuditRegistersModel::find($idAuditRegister);
            try {
                $model->id_status = $idStatus;
                // $model->end_date = $today;
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
     * Get audit register
     */
    public function scopeGetAuditRegister($query, $idAuditRegister){
        $query
            ->join('t_contracts', 't_contracts.id_contract', 't_audit_registers.id_contract')
            ->join('c_status', 'c_status.id_status', 't_audit_registers.id_status')
            ->join('t_corporates', 't_corporates.id_corporate', 't_audit_registers.id_corporate')
            ->join('c_industries', 't_corporates.id_industry', 'c_industries.id_industry')
            ->join('t_customers', 't_customers.id_customer', 't_corporates.id_customer')
            ->join('t_audit_processes', 't_audit_processes.id_audit_processes', 't_audit_registers.id_audit_processes')
            ->join('c_scope', 'c_scope.id_scope', 't_audit_processes.id_scope')
            ->where('t_audit_registers.id_audit_register', $idAuditRegister);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get aplicability register by contract
     */
    public function scopeGetAuditRegisterByContract($query, $idContract){
        $query->select('t_audit_registers.id_audit_register')
            ->where('t_audit_registers.id_contract', $idContract);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * update total percent
     */
    public function scopeUpdatePercent($query, $idAuditRegister, $percent){
        try {
            $model = AuditRegistersModel::find($idAuditRegister);
            try {
                $model->total = $percent;
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
     * Get percent
     */
    public function scopeGetAuditRegisterPercent($query, $idAuditRegister)
    {
        $query
            ->select('t_audit_registers.total')
            ->where('t_audit_registers.id_audit_register', $idAuditRegister);
        $data = $query->first();
        return $data;
    }

    public function scopeGetAuditByContract($query, $idContract){
        $query->select('t_audit_registers.*')
            ->where('t_audit_registers.id_contract', $idContract);
        $data = $query->get()->toArray();
        return $data;
    }

    public function scopeGetAuditRegisterByContractData($query, $idContract){
        $query->select('t_audit_registers.*')
            ->where('t_audit_registers.id_contract', $idContract);
        $data = $query->get()->toArray();
        return $data;
    }
    public function scopeGetAuditRegisterByCorporateData($query, $idCorporate){
        $query->select('t_audit_registers.*')
            ->where('t_audit_registers.id_corporate', $idCorporate);
        $data = $query->get()->toArray();
        return $data;
    }

    public function scopeGetAuditStatus($query, $idContract){
        $query->select('t_audit_registers.*')
            ->where('t_audit_registers.id_contract', $idContract);
        $data = $query->get()->toArray();
        return $data;
    }
    
    public function scopeGetAuditId($query, $idCorporate){
        $query->select('t_audit_registers.*')
            ->where('t_audit_registers.id_corporate', $idCorporate);
        $data = $query->get()->toArray();
        return $data;
    }

    public function scopeGetAuditosByIdAuditRegister($query, $idAuditRegister){
        $query->select(DB::raw('concat(t_people.first_name," ", t_people.second_name) as responsible'))
            ->join('t_audit_processes', 't_audit_registers.id_audit_processes', 't_audit_processes.id_audit_processes')
            ->join('t_auditor', 't_audit_processes.id_audit_processes', 't_auditor.id_audit_processes')
            ->join('t_users', 't_users.id_user', 't_auditor.id_user')
            ->join('t_people', 't_users.id_person', 't_people.id_person')
            ->where('t_audit_registers.id_audit_register', $idAuditRegister);

        return $query->get()->toArray();
    }

}
