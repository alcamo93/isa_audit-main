<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\DB;

class ContractsModel extends Model
{
    protected $table = 't_contracts';
    protected $primaryKey = 'id_contract';
    /**
     * Return info contract by id_contract
     */
    public function scopeGetContract($query, $idContract){
        $query
            ->join('c_status', 't_contracts.id_status', 'c_status.id_status')
            ->join('t_customers', 't_contracts.id_customer', 't_customers.id_customer')
            ->join('t_corporates', 't_contracts.id_corporate', 't_corporates.id_corporate')
            ->select(
                't_contracts.id_contract',
                't_contracts.contract', 
                DB::raw('DATE_FORMAT(t_contracts.start_date, "%Y-%m-%d") AS start_date'),
                DB::raw('DATE_FORMAT(t_contracts.end_date, "%Y-%m-%d") AS end_date'),
                't_contracts.id_customer',
                't_customers.cust_tradename',
                't_customers.cust_trademark',
                't_contracts.id_corporate',
                't_corporates.corp_tradename',
                't_corporates.corp_trademark',
                't_contracts.id_status',
                'c_status.status',
                't_contracts.id_contract'
            )
            ->where('t_contracts.id_contract', $idContract);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get contracts to datatables
     */
    public function scopeGetContractsDT($query, $page, $rows, $search, $draw, $order, 
            $fContract, $fIdStatus, $fIdCustomer, $fIdCorporate){
        $query->join('c_status', 't_contracts.id_status', 'c_status.id_status')
            ->join('t_customers', 't_contracts.id_customer', 't_customers.id_customer')
            ->join('t_corporates', 't_contracts.id_corporate', 't_corporates.id_corporate')
            ->select('t_contracts.id_contract', 't_contracts.contract', 
                DB::raw('DATE_FORMAT(t_contracts.start_date, "%d/%m/%Y") as start_date'),
                DB::raw('DATE_FORMAT(t_contracts.end_date, "%d/%m/%Y") as end_date'),
                't_contracts.id_customer', 't_customers.cust_tradename', 't_customers.cust_trademark',
                't_contracts.id_corporate', 't_corporates.corp_tradename', 't_corporates.corp_trademark',
                't_contracts.id_status', 'c_status.status', 't_contracts.id_contract');
        // Add filters
        if ($fContract != null) {
            $query->where('t_contracts.contract','LIKE','%'.$fContract.'%');
        }
        if ($fIdStatus != 0) {
            $query->where('t_contracts.id_status', $fIdStatus);
        }
        if ($fIdCustomer != 0) {
            $query->where('t_contracts.id_customer', $fIdCustomer);
        }
        if ($fIdCorporate != 0) {
            $query->where('t_contracts.id_corporate', $fIdCorporate);
        }
        //Order by
        switch ($order[0]['column']) {
            case 1:
                $columnSwitch = 't_contracts.contract';
                break;
            default:
                $columnSwitch = 't_contracts.id_contract';
                break;
        }

        $column = $columnSwitch;   
        $dir = (isset($order[0]['dir']) ? $order[0]['dir'] : 'desc');            
        $query->orderBy($column, $dir);
        
        /*Search*/
        $query->where(function($query) use ($search){
            $query->where('t_contracts.contract','LIKE','%'.$search['value'].'%'); 
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
     * Set info Contract
     */
    public function scopeSetContract($query, $data){
        $model = null;
        if(!array_key_exists('idContract', $data) || $data['idContract'] == 0){
            $contractExist = ContractsModel::where('id_corporate', $data['idCorporate'])->where('id_customer', $data['idCustomer'])->first();
            if($contractExist == null){
                $model = new ContractsModel();
                $model->contract = $data['contract'];
                $model->id_status = StatusConstants::INACTIVE;
                $model->id_customer = $data['idCustomer'];
                $model->id_corporate = $data['idCorporate'];
                $model->start_date = $data['dateStart'].' 00:00:00';
                $model->end_date = $data['dateEnd'].' 23:59:59';

                try {
                    $model->save();
                    $data['status'] = StatusConstants::SUCCESS;
                    $data['idContract'] = $model->id_contract;
                } catch (Exception $e) {
                    $data['status'] = StatusConstants::ERROR;
                }
            } else {
                $data['status'] = StatusConstants::WARNING;
            }
        } else {
            $model = ContractsModel::find($data['idContract']);
            $model->contract = $data['contract'];

            try {
                $model->save();
                $data['status'] = StatusConstants::SUCCESS;
                $data['idContract'] = $model->id_contract;
            } catch (Exception $e) {
                $data['status'] = StatusConstants::ERROR;
            }
        }

        return $data;
    }
    /**
     * Update info contract
     */
    public function scopeUpdateContract($query, $data){
        try {
            $model = ContractsModel::findOrFail($data['idContract']);
            $model->start_date = $data['dateStart'].' 00:00:00';
            $model->end_date = $data['dateEnd'].' 23:59:59';
            if(array_key_exists('contract', $data))
                $model->contract = $data['contract'];
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
     * Delete contract
     */
    public function scopeDeleteContract($query, $idContract){
        $model = ContractsModel::findOrFail($idContract);
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;  //on cascade exception
        }
    }
    /**
     * Return info contract by id_corporate
     */
    public function scopeGetContractActive($query, $idCorporate){
        $today = date("Y-m-d").' 00:00:00';
        $query
            ->join('c_status', 't_contracts.id_status', 'c_status.id_status')
            ->join('t_customers', 't_contracts.id_customer', 't_customers.id_customer')
            ->join('t_corporates', 't_contracts.id_corporate', 't_corporates.id_corporate')
            ->join('t_contract_details', 't_contracts.id_contract', 't_contract_details.id_contract')
            ->select(
                't_contracts.id_contract',
                't_contracts.contract', 
                \DB::raw('DATE_FORMAT(t_contracts.start_date, "%d/%m/%Y") AS start_date'),
                \DB::raw('DATE_FORMAT(t_contracts.end_date, "%d/%m/%Y") AS end_date'),
                't_contracts.id_customer',
                't_contract_details.usr_global',
                't_contract_details.usr_corporate',
                't_contract_details.usr_operative',
                't_customers.cust_tradename',
                't_customers.cust_trademark',
                't_contracts.id_corporate',
                't_corporates.corp_tradename', 
                't_corporates.corp_trademark',
                't_contracts.id_status',
                'c_status.status'
            )
            ->where([
                ['t_contracts.id_status', StatusConstants::ACTIVE],
                ['t_contracts.id_corporate', $idCorporate],
                ['t_contracts.start_date', '<=', $today],
                ['t_contracts.end_date', '>=', $today]
            ]);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get contract activation viabily
     */
    public function scopeGetIsContractActivable($query, $idContract)
    {
        $today = date("Y-m-d").' 00:00:00';
        $query
            ->select(
                't_contracts.id_contract'
            )
            ->where([
                ['t_contracts.id_contract', $idContract],
                ['t_contracts.id_status', StatusConstants::INACTIVE],
                ['t_contracts.end_date', '>=', $today]
            ]);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Active contract by extension
     */
    public function scopeExtendContract($query, $data, $status)
    {
        try {
            $model = ContractsModel::findOrFail($data['id_contract']);
            $model->id_status = $status;
            $model->end_date = $data['end_date'];
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
     * Update status contract
     */
    public function scopeUpdateContractStatus($query, $idContract, $idStatus){
        try {
            $model = ContractsModel::findOrFail($idContract);
            $model->id_status = $idStatus;
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
     * Get contracts by dates
     */
    public function scopeGetContractsByDate($query, $today, $idStatus){
        $query->select('t_contracts.id_contract');
        if ($idStatus == StatusConstants::ACTIVE) {
            $query->where('t_contracts.end_date', '<', $today);
        }
        else {
            $query->where('t_contracts.start_date', $today);
        }
        $query->where('t_contracts.id_status', $idStatus);
        $data = $query->distinct()->get()->toArray();
        return $data;
    }
    /**
     * Update status contracts
     */
    public function scopeUpdateStatusByGroup($query, $idContracts, $idStatus){
        $model = ContractsModel::whereIn('t_contracts.id_contract', $idContracts);
        try {
            $model->update(['t_contracts.id_status' => $idStatus]);
            $data['status'] = StatusConstants::SUCCESS;
            $data['msg'] = 'ok';
            return $data;
        } catch (Exception $e) {
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = $e->errorInfo;
            return $data;
        }
    }
    /**
     * Return info contract active
     */
    public function scopeGetAllContractActive($query){
        $query->select('t_contracts.id_contract', 't_contracts.id_corporate')
            ->where('t_contracts.id_status', StatusConstants::ACTIVE);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get Contract by corporate
     */
    public function scopeGetContractByCorporate($query, $idCorporate){
        $query->join('t_contract_details', 't_contracts.id_contract', 't_contract_details.id_contract')
            ->join('t_corporates', 't_corporates.id_corporate', 't_contracts.id_corporate')
            ->where('t_contracts.id_corporate', $idCorporate);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get id customers whit actives contracts
     */
    public function scopeGetArrayCustomerActiveContract($query){
        $query->where('t_contracts.id_status', StatusConstants::ACTIVE);
        $data = $query->distinct()->pluck('id_customer')->toArray();
        return $data;
    }
}
