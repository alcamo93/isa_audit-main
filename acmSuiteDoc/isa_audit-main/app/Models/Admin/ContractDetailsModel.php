<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;

class ContractDetailsModel extends Model
{
    protected $table = 't_contract_details';
    protected $primaryKey = 'id_contract_detail';
    /**
     * Get contract detail
     */
    public function scopeGetContractDetail($query, $idContract){
        $query->join('t_users', 't_contract_details.id_user', 't_users.id_user')
            ->join('c_periods', 'c_periods.id_period', 't_contract_details.id_period')
            ->select('t_contract_details.license', 't_contract_details.usr_global', 't_contract_details.id_contract_detail',
                't_contract_details.usr_corporate', 't_contract_details.usr_operative', 'c_periods.period', 
                't_contract_details.id_period', 't_contract_details.id_contract', 't_users.id_user')
            ->where('t_contract_details.id_contract', $idContract);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Set info ContractDetails
     */
    public function scopeSetContractDetails($query, $idUser, $idContract, $dataHistorical){
        $model = new ContractDetailsModel();
        $model->license = $dataHistorical[0]['license'];
        $model->usr_global = $dataHistorical[0]['usr_global'];
        $model->usr_corporate = $dataHistorical[0]['usr_corporate'];
        $model->usr_operative = $dataHistorical[0]['usr_operative'];
        $model->id_period = $dataHistorical[0]['id_period'];
        $model->id_contract = $idContract;
        $model->id_user = $idUser;
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Update info customer
     */
    public function scopeUpdateContractDetails($query, $idUser, $data){
        try {
            $model = ContractDetailsModel::findOrFail($data['idContractDetail']);
            $model->usr_global = $data['usrGlobals'];
            $model->usr_corporate = $data['usrCorporates'];
            $model->usr_operative = $data['usrOperatives'];
            $model->id_period = $data['idPeriod'];
            $model->id_user = $idUser;
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
     * Update contract details by extension
     */
    public function scopeUpdateContractDetailsByExtension($query, $data){
        try {
            $model = ContractDetailsModel::findOrFail($data['id_contract']);
            $model->license = $data['license'];
            $model->usr_global = $data['usr_global'];
            $model->usr_corporate = $data['usr_corporate'];
            $model->usr_operative = $data['usr_operative'];
            $model->id_period = $data['id_period'];
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
     * Delete customer
     */
    public function scopeDeleteContractDetails($query, $idContract){
        $model = ContractDetailsModel::where('t_contract_details.id_contract', $idContract);
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;  //on cascade exception
        }
    }
}