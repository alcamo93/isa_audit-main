<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;

class ContractsExtensionModel extends Model
{
    protected $table = 't_contracts_extends';
    protected $primaryKey = 'id_contract_extend';  
    /**
     *  Set Contract Extension
     */
    public function scopeSetContractExtension($query, $type, $startDate, $endDate, $idContract)
    {
        try {
            $model = ContractsExtensionModel::where([
                ['id_contract', $idContract],
                ['status', 1]
            ])->firstOrFail();
            $data['status'] = StatusConstants::WARNING;
        }
        catch (ModelNotFoundException $ex) {
            $model = new ContractsExtensionModel();
            $model->id_contract = $idContract;
            $model->type = $type;
            $model->start_date = $startDate.' 00:00:00';
            $model->end_date = $endDate.' 23:59:59';
            try {
                $model->save();
                $data['status'] = StatusConstants::SUCCESS;
                $data['idContract'] = $model->id_contract;
            }
            catch (Exception $e) {
                $data['status'] = StatusConstants::ERROR;
            }
        }
        return $data;
    }

    /**
     * Return the list of contracts that should be activated today
     */
    public function scopeGetContractsExtensionsByDate($query, $date)
    {
        $query
            ->where([
                ['start_date', $date],
                ['status', 1]
            ]);
        return $query->get()->toArray();        
    }

    /**
     * Update used extensions
     */
    public function scopeUpdateUsedExtensions($query, $extesions)
    {
        try{
            $query
            ->whereIn('id_contract_extend', $extesions)
            ->update([
                'status' => 0
            ]);
            $response = StatusConstants::SUCCESS;
        }
        catch (\Exception $e) {
            $response =  StatusConstants::ERROR;
        }
        return $response;
    }
}
