<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Classes\StatusConstants;

class ProcessesModel extends Model
{
    protected $table = 't_audit_processes';
    protected $primaryKey = 'id_audit_processes';

    const NO_EVALUATION_RISK = 0;
    const EVALUATION_RISK = 1;

    public function customer()
    {
        return $this->belongsTo('App\Models\Admin\CustomersModel', 'id_customer', 'id_customer');
    }

    public function corporate()
    {
        return $this->belongsTo('App\Models\Admin\CorporatesModel', 'id_corporate', 'id_corporate');
    }

    public function auditors()
    {
        return $this->hasMany('App\Models\Admin\AuditorModel', 'id_audit_processes', 'id_audit_processes');
    }

    public function audit()
    {
        return $this->hasOne('App\Models\Audit\AuditRegistersModel', 'id_audit_processes', 'id_audit_processes');
    }

    public function scope()
    {
        return $this->hasOne('App\Models\Catalogues\ScopeModel', 'id_scope', 'id_scope');
    }

    public function aplicabilityRegister()
    {
        return $this->hasOne('App\Models\Audit\AplicabilityRegistersModel', 'id_audit_processes', 'id_audit_processes');
    }
    /**
     * Return info  process
     */
    public function scopeGetProcesses($query, $idProcesses)
    {
        $query->join('t_customers', 't_audit_processes.id_customer', 't_customers.id_customer')
            ->join('t_corporates', 't_audit_processes.id_corporate', 't_corporates.id_corporate')
            ->join('t_aplicability_registers', 't_audit_processes.id_audit_processes', 't_aplicability_registers.id_audit_processes')
            ->join('c_scope', 'c_scope.id_scope', 't_audit_processes.id_scope')
            ->select(
                't_customers.cust_trademark',
                't_customers.id_customer',
                't_corporates.corp_tradename',
                't_corporates.id_corporate',
                't_audit_processes.audit_processes',
                't_audit_processes.id_audit_processes',
                't_audit_processes.evaluate_risk',
                't_aplicability_registers.id_aplicability_register',
                't_aplicability_registers.id_status',
                't_aplicability_registers.id_aplicability_register',
                'c_scope.id_scope',
                'c_scope.scope',
                't_audit_processes.specification_scope'
            )
            ->where('t_audit_processes.id_audit_processes', $idProcesses);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Return info process for DT
     */
    public function scopeGetProcessesDT($query, $idCustomer)
    {
        $query->join('t_corporates', 't_audit_processes.id_corporate', 't_corporates.id_corporate')
            ->join('t_aplicability_registers', 't_audit_processes.id_audit_processes', 't_aplicability_registers.id_audit_processes')
            ->select(
                't_audit_processes.id_audit_processes',
                't_audit_processes.audit_processes',
                't_audit_processes.evaluate_risk',
                't_aplicability_registers.id_status',
                't_aplicability_registers.id_aplicability_register',
                't_corporates.corp_tradename',
                't_corporates.corp_trademark'
            )
            ->where('t_corporates.id_customer', $idCustomer);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get processs by id
     */
    public function scopeGetProcessesId($query, $idCorporate)
    {
        $query->select('t_audit_processes.id_audit_processes')
            ->where('t_audit_processes.id_corporate', $idCorporate);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Datatable DT
     */
    public function scopeProcessesRegistersDT($query, $page, $rows, $search, $draw, $order, $fIdCustomer, $fIdCorporate, $fProcess, $filterIdUser)
    {
        $query->join('t_customers', 't_audit_processes.id_customer', 't_customers.id_customer')
            ->join('t_corporates', 't_audit_processes.id_corporate', 't_corporates.id_corporate')
            ->join('t_aplicability_registers', 't_audit_processes.id_audit_processes', 't_aplicability_registers.id_audit_processes')
            ->join('c_status as c_status_aplicability', 't_aplicability_registers.id_status', 'c_status_aplicability.id_status')
            ->join('c_scope', 'c_scope.id_scope', 't_audit_processes.id_scope')
            ->leftJoin('t_audit_registers', 't_audit_processes.id_audit_processes', 't_audit_registers.id_audit_processes')
            ->leftJoin('c_status as c_status_audit', 't_audit_registers.id_status', 'c_status_audit.id_status')
            ->select(
                't_audit_processes.audit_processes',
                't_corporates.corp_tradename',
                't_audit_processes.id_audit_processes',
                't_customers.cust_trademark',
                't_audit_processes.evaluate_risk',
                't_aplicability_registers.id_aplicability_register',
                't_aplicability_registers.id_status',
                't_aplicability_registers.id_aplicability_register',
                't_audit_registers.id_audit_register',
                'c_status_aplicability.status as status_aplicability',
                'c_status_audit.status as status_audit',
                'c_scope.id_scope',
                'c_scope.scope',
                't_audit_processes.specification_scope'
            );
        // add filters
        if ($fIdCustomer != 0) {
            $query->where('t_corporates.id_customer', $fIdCustomer);
        }
        if ($fIdCorporate != 0) {
            $query->where('t_corporates.id_corporate', $fIdCorporate);
        }
        if ($fProcess != '') {
            $query->where('t_audit_processes.audit_processes', 'LIKE', '%' . $fProcess . '%');
        }
        if ($filterIdUser != 0) {
            $query->join('t_auditor', 't_audit_processes.id_audit_processes', 't_auditor.id_audit_processes')
                ->where('t_auditor.id_user', $filterIdUser);
        }
        //Order by
        switch ($order[0]['column']) {
            case 0:
                $columnSwitch = 't_audit_processes.audit_processes';
                break;
            case 1:
                $columnSwitch = 't_corporates.corp_tradename';
                break;
        }
        $column = $columnSwitch;
        $dir = (isset($order[0]['dir']) ? $order[0]['dir'] : 'desc');
        $query->orderBy($column, $dir);

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
     * Set Processs
     */
    public function scopeSetProcesses($query, $info)
    {
        $model = new ProcessesModel();
        $model->audit_processes = $info['processes'];
        $model->id_corporate = $info['idCorporate'];
        $model->id_customer = $info['idCustomer'];
        $model->id_scope = $info['idScope'];
        $model->evaluate_risk = ($info['setRisk'] == 'true') ? 1 : 0;
        if ($info['idScope'] == 2) {
            $model->specification_scope = $info['specification'];
        }
        try {
            $model->save();
            $data['status'] = StatusConstants::SUCCESS;
            $data['idProcesses'] = $model->id_audit_processes;
        } catch (Exception $e) {
            $data['status'] = StatusConstants::ERROR;
        }
        return $data;
    }
    /**
     * Update Processs
     */
    public function scopeUpdateProcess($query, $info)
    {
        try {
            $model = ProcessesModel::findOrFail($info['idProcess']);
        } catch (ModelNotFoundException $th) {
            $data['status'] = StatusConstants::WARNING;
            return $data;
        }
        $model->audit_processes = $info['process'];
        $model->id_corporate = $info['idCorporate'];
        $model->id_customer = $info['idCustomer'];
        $model->id_scope = $info['idScope'];
        $model->evaluate_risk = ($info['setRisk'] == 'true') ? 1 : 0;
        if ($info['idScope'] == 2) {
            $model->specification_scope = $info['specification'];
        } else {
            $model->specification_scope = NULL;
        }
        try {
            $model->save();
            $data['status'] = StatusConstants::SUCCESS;
            $data['idProcesses'] = $model->id_audit_processes;
        } catch (Exception $e) {
            $data['status'] = StatusConstants::ERROR;
        }
        return $data;
    }
    /**
     * Delete processes
     */
    public function scopeDeleteProcesses($query, $idProcesses)
    {
        $model = ProcessesModel::findOrFail($idProcesses);
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::WARNING;  //on cascade exception
        }
    }

    public function scopeGetProcessByCustomer($query, $idCustomer = null)
    {
        $query->join('t_customers', 't_audit_processes.id_customer', 't_customers.id_customer')
            ->join('t_corporates', 't_audit_processes.id_corporate', 't_corporates.id_corporate')
            ->join('c_scope', 'c_scope.id_scope', 't_audit_processes.id_scope')
            ->leftJoin('t_audit_registers', 't_audit_processes.id_audit_processes', 't_audit_registers.id_audit_processes')
            ->leftJoin('c_status', 't_audit_registers.id_status', 'c_status.id_status')
            ->leftJoin('t_action_registers', 't_audit_processes.id_audit_processes', 't_action_registers.id_audit_processes')
            ->select(
                't_customers.cust_trademark',
                't_corporates.corp_tradename',
                'c_scope.id_scope',
                'c_scope.scope',
                't_audit_processes.audit_processes',
                't_audit_processes.id_audit_processes',
                't_audit_processes.evaluate_risk',
                't_audit_processes.specification_scope',
                't_audit_registers.id_audit_register',
                't_audit_registers.total as total_audit',
                DB::raw('DATE_FORMAT(t_audit_registers.end_date, "%Y-%m-%d") AS end_date'),
                'c_status.status as status_audit',
                'c_status.id_status as id_status_audit',
                't_action_registers.id_action_register'
            );
        if ($idCustomer) {
            $query->where('t_audit_processes.id_customer', $idCustomer);
        }
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get data by process dashboard
     */
    public function scopeGetProcessByCustomerDashboard($query, $idCustomer = null, $idCorporate = null, $idUser = null)
    {
        $query->join('t_customers', 't_audit_processes.id_customer', 't_customers.id_customer')
            ->join('t_corporates', 't_audit_processes.id_corporate', 't_corporates.id_corporate')
            ->join('c_scope', 'c_scope.id_scope', 't_audit_processes.id_scope')
            ->join('t_audit_registers', 't_audit_processes.id_audit_processes', 't_audit_registers.id_audit_processes')
            ->join('c_status', 't_audit_registers.id_status', 'c_status.id_status')
            ->leftJoin('t_action_registers', 't_audit_processes.id_audit_processes', 't_action_registers.id_audit_processes')
            ->select(
                't_customers.cust_trademark',
                't_corporates.corp_tradename',
                'c_scope.id_scope',
                'c_scope.scope',
                't_audit_processes.audit_processes',
                't_audit_processes.id_audit_processes',
                't_audit_processes.evaluate_risk',
                't_audit_processes.specification_scope',
                't_audit_registers.id_audit_register',
                't_audit_registers.total as total_audit',
                DB::raw('DATE_FORMAT(t_audit_registers.end_date, "%d-%m-%Y") AS end_date'),
                'c_status.status as status_audit',
                'c_status.id_status as id_status_audit',
                't_action_registers.id_action_register'
            );
        if ($idCustomer) {
            $query->where('t_audit_processes.id_customer', $idCustomer);
        }
        if ($idCorporate) {
            $query->where('t_audit_processes.id_corporate', $idCorporate);
        }
        if ($idUser) {
            $query->join('t_auditor', 't_audit_processes.id_audit_processes', 't_auditor.id_audit_processes')
                ->where('t_auditor.id_user', $idUser);
        }
        $data = $query->get()->toArray();
        return $data;
    }
}
