<?php

namespace App\Models\Risk;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;

class RiskHelpModel extends Model
{
    protected $table = 't_risk_help'; 
    protected $primaryKey = 'id_risk_help';
    /**
     * Return Risk Help for datatables
     */
    public function scopeGetRiskHelpDT($query, $page, $rows, $search, $draw, $order, $fName, $filterAttribute, $idRiskCategory){
        $query->join('c_status', 't_risk_help.id_status', 'c_status.id_status')
                ->where('t_risk_help.id_risk_attribute', $filterAttribute)
                ->where('t_risk_help.id_risk_category', $idRiskCategory);
        // Add filters
        if ( !is_null($fName) ) {
            $query->where('t_risk_help.risk_help', 'LIKE','%'.$fName.'%');
        }
        //Order by
        switch ($order[0]['column']) {
            case 0:
                $columnSwitch = 't_risk_help.risk_help';
                break;
            case 1:
                $columnSwitch = 't_risk_help.standard';
                break;
            case 2:
                $columnSwitch = 't_risk_help.value';
                break;
            case 3:
                $columnSwitch = 'c_status.status';
                break;
            default:
                $columnSwitch = 't_risk_help.id_risk_help';
                break;
        }
        $column = $columnSwitch;   
        $dir = (isset($order[0]['dir']) ? $order[0]['dir'] : 'desc');            
        $query->orderBy($column, $dir);
        
        /*Search*/
        $query->where(function($query) use ($search){
            $query->where('t_risk_help.risk_help','LIKE','%'.$search['value'].'%'); 
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
     * Set Help
     */
    public function scopeSetRiskHelp($query, $requestData) {
        $model = new RiskHelpModel();
        $model->risk_help = $requestData['riskHelp'];
        $model->standard = $requestData['standard'];
        $model->value = $requestData['value'];
        $model->id_risk_category = $requestData['idRiskCategory'];
        $model->id_risk_attribute = $requestData['idRiskAttribute'];
        $model->id_status = $requestData['idStatus'];
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Update help
     */
    public function scopeUpdateRiskHelp($query, $requestData) {
        try {
            $model = RiskHelpModel::findOrFail($requestData['idRiskHelp']);
        } catch (ModelNotFoundException $nf) {
            return StatusConstants::WARNING;
        }
        $model->risk_help = $requestData['riskHelp'];
        $model->standard = $requestData['standard'];
        $model->value = $requestData['value'];
        $model->id_risk_attribute = $requestData['idRiskAttribute'];
        $model->id_status = $requestData['idStatus'];
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Delete help
     */
    public function scopeDeleteRiskHelp($query, $requestData) {
        try {
            $model = RiskHelpModel::findOrFail($requestData['idRiskHelp']);
        } catch (ModelNotFoundException $nf) {
            return StatusConstants::WARNING;
        }
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Get Risk help data
     */
    public function scopeGetRiskHelp($query, $idRiskCategory, $idRiskAttribute, $idStatus) {
        $query->join('c_status', 't_risk_help.id_status', 'c_status.id_status')
            ->where([
                ['t_risk_help.id_risk_category', $idRiskCategory],
                ['t_risk_help.id_risk_attribute', $idRiskAttribute],
                ['t_risk_help.id_status', $idStatus]
            ])
            ->orderBy('t_risk_help.value', 'ASC');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get data Risk Help
     */
    public function scopeGetDataRiskHelp($query, $idRiskHelp){
        $query->where('t_risk_help.id_risk_help', $idRiskHelp);
        $data = $query->get()->toArray();
        return $data;
    }
}