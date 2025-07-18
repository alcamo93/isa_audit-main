<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\DB;

class AplicabilityRegistersModel extends Model
{
    protected $table = 't_aplicability_registers';
    protected $primaryKey = 'id_aplicability_register';

    public function matters()
    {
        return $this->hasMany('App\Models\Audit\ContractMattersModel', 'id_aplicability_register', 'id_aplicability_register');
    }

    public function auditRegisters()
    {
        return $this->hasMany('App\Models\Audit\AuditRegistersModel', 'id_aplicability_register', 'id_aplicability_register');
    }
    /**
     * Get aplicability resgisters
     */
    public function scopeAplicabilityRegistersDT(
        $query,
        $page,
        $rows,
        $search,
        $draw,
        $order,
        $fIdStatus,
        $fIdCustomer,
        $fIdCorporate
    ) {
        $query->join('t_contracts', 't_contracts.id_contract', 't_aplicability_registers.id_contract')
            ->join('c_status', 'c_status.id_status', 't_aplicability_registers.id_status')
            ->join('t_corporates', 't_corporates.id_corporate', 't_aplicability_registers.id_corporate')
            ->join('t_customers', 't_customers.id_customer', 't_corporates.id_customer')
            ->select(
                't_aplicability_registers.id_aplicability_register',
                'c_status.status',
                't_contracts.contract',
                't_contracts.id_contract',
                't_corporates.corp_tradename',
                't_customers.cust_trademark'
            );
        // add filters
        if ($fIdStatus != 0) {
            $query->where('t_aplicability_registers.id_status', $fIdStatus);
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
                $columnSwitch = 'c_status.status';
                break;
            default:
                $columnSwitch = 't_customers.id_aplicability_register';
                break;
        }
        $column = $columnSwitch;
        $dir = (isset($order[0]['dir']) ? $order[0]['dir'] : 'desc');
        $query->orderBy($column, $dir);

        /*Search*/
        $query->where(function ($query) use ($search) {
            $query->where('t_corporates.corp_tradename', 'LIKE', '%' . $search['value'] . '%')
                ->orWhere('t_customers.cust_trademark', 'LIKE', '%' . $search['value'] . '%');
        });

        $queryCount = $query->get();
        $result = $query->limit($rows)->offset($page)->get()->toArray();
        $total = $queryCount->count();
        $data['data'] = (sizeof($result) > 0) ? $result : array();
        $data['recordsTotal'] = $total;
        $data['draw'] = (int) $draw;
        $data['recordsFiltered'] = $total;
        return $data;
    }
    /**
     * Set current status in general aplicability
     */
    public function scopeSetStatusAplicability($query, $idAplicabilityRegister, $idStatus)
    {
        try {
            $model = AplicabilityRegistersModel::find($idAplicabilityRegister);
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
     * Get aplicability register
     */
    public function scopeGetAplicabilityRegister($query, $idAplicabilityRegister)
    {
        $query->join('t_contracts', 't_contracts.id_contract', 't_aplicability_registers.id_contract')
            ->join('c_status', 'c_status.id_status', 't_aplicability_registers.id_status')
            ->join('t_corporates', 't_corporates.id_corporate', 't_aplicability_registers.id_corporate')
            ->join('t_customers', 't_customers.id_customer', 't_corporates.id_customer')
            ->join('t_audit_processes', 't_audit_processes.id_audit_processes', 't_aplicability_registers.id_audit_processes')
            ->join('c_scope', 'c_scope.id_scope', 't_audit_processes.id_scope')
            ->select(
                't_aplicability_registers.id_aplicability_register',
                'c_status.status',
                't_contracts.contract',
                't_contracts.id_contract',
                't_aplicability_registers.id_corporate',
                't_corporates.corp_tradename',
                't_corporates.corp_trademark',
                't_customers.cust_tradename',
                't_customers.cust_trademark',
                't_aplicability_registers.id_status',
                't_aplicability_registers.id_audit_processes',
                't_audit_processes.audit_processes',
                't_audit_processes.specification_scope',
                't_audit_processes.id_scope',
                'c_scope.scope'
            )
            ->where('t_aplicability_registers.id_aplicability_register', $idAplicabilityRegister);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get aplicability register by contract
     */
    public function scopeGetAplicabilityRegisterByContract($query, $idContract)
    {
        $query->join('t_contracts', 't_contracts.id_contract', 't_aplicability_registers.id_contract')
            ->join('c_status', 'c_status.id_status', 't_aplicability_registers.id_status')
            ->join('t_corporates', 't_corporates.id_corporate', 't_aplicability_registers.id_corporate')
            ->join('t_customers', 't_customers.id_customer', 't_corporates.id_customer')
            ->select(
                't_aplicability_registers.id_aplicability_register',
                'c_status.status',
                't_contracts.contract',
                't_contracts.id_contract',
                't_aplicability_registers.id_corporate',
                't_corporates.corp_tradename',
                't_customers.cust_trademark',
                't_aplicability_registers.id_status'
            )
            ->where('t_aplicability_registers.id_contract', $idContract);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Set aplicability
     */
    public function scopeSetAplicability($query, $idContract, $idCorporate, $idProcesses)
    {
        $model = new AplicabilityRegistersModel();
        try {
            $model->id_contract = $idContract;
            $model->id_corporate = $idCorporate;
            $model->id_audit_processes = $idProcesses;
            $model->id_status = StatusConstants::NOT_CLASSIFIED;
            $model->save();
            $data['status'] = StatusConstants::SUCCESS;
            $data['idAplicabilityRegister'] = $model->id_aplicability_register;
            return $data;
        } catch (Exception $e) {
            $data['status'] = StatusConstants::ERROR;
            return $data;
        }
    }
    /***
     * Obtener Aplicability by process
     */
    public function scopeGetAplicabilityRegisterProcess($query, $idProcesses)
    {
        $query->where('t_aplicability_registers.id_audit_processes', $idProcesses);
        $data = $query->get()->toArray();
        return $data;
    }
    public function scopeGetAplicabilityRegisterStatus($query, $idProcesses)
    {
        $query->select('t_aplicability_registers.id_status', 't_aplicability_registers.id_aplicability_register')
            ->whereIn('t_aplicability_registers.id_audit_processes', $idProcesses);
        $data = $query->get()->toArray();
        return $data;
    }
    public function scopeGetAplicabilityRegisterId($query, $idCorporate)
    {
        $query->select('t_aplicability_registers.id_aplicability_register', 't_aplicability_registers.id_contract')
            ->where('t_aplicability_registers.id_corporate', $idCorporate);
        $data = $query->get()->toArray();
        return $data;
    }

    public function scopeGetAplicabilityRegisterForReport($query, $idAplicabilityRegister)
    {
        $query->select([
            't_aplicability_registers.*',
            'status_aplicability_registers.status',
            't_corporates.corp_tradename',
            't_corporates.corp_trademark',
            't_corporates.rfc',
            't_corporates.type',
            'status_corporate.status as status_corporate',
            'c_industries.industry',
            't_audit_processes.audit_processes',
            'c_scope.scope',
            DB::raw('concat(t_people.first_name," ", t_people.second_name) as responsible')
        ])
            ->join('c_status AS status_aplicability_registers', 't_aplicability_registers.id_status', 'status_aplicability_registers.id_status')
            ->join('t_corporates', 't_aplicability_registers.id_corporate', 't_corporates.id_corporate')
            ->join('c_status AS status_corporate', 't_corporates.id_status', 'status_corporate.id_status')
            ->join('c_industries', 't_corporates.id_industry', 'c_industries.id_industry')
            ->join('t_audit_processes', 't_aplicability_registers.id_audit_processes', 't_audit_processes.id_audit_processes')
            ->join('c_scope', 'c_scope.id_scope', 't_audit_processes.id_scope')
            ->join('t_auditor', 't_audit_processes.id_audit_processes', 't_auditor.id_audit_processes')
            ->join('t_users', 't_users.id_user', 't_auditor.id_user')
            ->join('t_people', 't_users.id_person', 't_people.id_person')
            ->where('t_aplicability_registers.id_aplicability_register', $idAplicabilityRegister)
            ->where('t_auditor.leader', StatusConstants::OWNER_ACTIVE);

        return $query->first();
    }
}
