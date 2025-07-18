<?php

namespace App\Models\Risk;

use Illuminate\Database\Eloquent\Model;
use App\Classes\StatusConstants;

class RiskProbabilitiesModel extends Model
{
    protected $table = 't_risk_probabilities'; 
    protected $primaryKey = 'id_risk_probability';

    /**
     * Return probabilities for datatables
     */
    public function scopeGetprobabilitiesDT($query, $page, $rows, $search, $draw, $order, $fName, $fIdStatus, $idRiskCategory){
        $query->join('c_status', 't_risk_probabilities.id_status', 'c_status.id_status')
            ->select('t_risk_probabilities.id_risk_probability', 't_risk_probabilities.probability', 
                    't_risk_probabilities.name_probability', 't_risk_probabilities.id_status', 'c_status.status')
            ->where('t_risk_probabilities.id_risk_category', $idRiskCategory);
        // Add filters
        if ( !is_null($fName) ) {
            $query->where('t_risk_probabilities.name_probability', 'LIKE','%'.$fName.'%');
        }
        if ( !is_null($fIdStatus) ) {
            $query->where('t_risk_probabilities.id_status', $fIdStatus);
        }
        //Order by
        switch ($order[0]['column']) {
            case 0:
                $columnSwitch = 't_risk_probabilities.probability';
                break;
            case 1:
                $columnSwitch = 't_risk_probabilities.name_probability';
                break;
            case 2:
                $columnSwitch = 'c_status.status';
                break;
            default:
                $columnSwitch = 't_risk_probabilities.id_risk_probability';
                break;
        }
        $column = $columnSwitch;   
        $dir = (isset($order[0]['dir']) ? $order[0]['dir'] : 'desc');            
        $query->orderBy($column, $dir);
        
        /*Search*/
        $query->where(function($query) use ($search){
            $query->where('t_risk_probabilities.name_probability','LIKE','%'.$search['value'].'%'); 
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
     * set new risk probability
     */
    public function scopeSetProbability($query, $data){
        $model = new RiskProbabilitiesModel();
        $model->probability = $data['numProbability'];
        $model->name_probability = $data['nameProbability'];
        $model->id_status = $data['idStatus'];
        $model->id_risk_category = $data['idRiskCategory'];
        try {
            $model->save();
            $data = StatusConstants::SUCCESS;
        } catch (Exception $e) {
            $data = StatusConstants::ERROR;
        }
        return $data;
    }
    /**
     * Update risk Probability
     */
    public function scopeUpdateProbability($query, $data){
        try {
            $model = RiskProbabilitiesModel::findOrFail($data['idProbability']);
        } catch (ModelNotFoundException $nf) {
            return StatusConstants::WARNING;
        }
        $model->probability = $data['numProbability'];
        $model->name_probability = $data['nameProbability'];
        $model->id_status = $data['idStatus'];
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Delete risk Probability
     */
    public function scopeDeleteProbability($query, $data){
        try {
            $model = RiskProbabilitiesModel::findOrFail($data['idProbability']);
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
    * Return probabilities by idRiskCategory
    */
    public function scopeGetRiskProbabilities($query, $idRiskCategory, $idStatus){
        $query->select('id_risk_probability', 'probability', 'name_probability', 'id_status', 'id_risk_category',
            \DB::raw('SUBSTRING(name_probability, 1, 25) AS name_probability_short'))
            ->where([
                ['id_risk_category', $idRiskCategory],
                ['id_status', $idStatus]
            ]);
        $data = $query->get()->toArray();
        return $data;
    }
}