<?php

namespace App\Models\Risk;

use Illuminate\Database\Eloquent\Model;
use App\Classes\StatusConstants;

class RiskSpecificationsModel extends Model
{
    protected $table = 't_risk_specifications'; 
    protected $primaryKey = 'id_risk_specification';
    /**
    * Return aspects
    */
    public function scopeGetSpecifications($query, $idProbability, $idExhibition, $idConsequence, $idStatus){
        $query->select('t_risk_specifications.id_risk_specification', 't_risk_specifications.specification', 
                    't_risk_specifications.id_risk_consequence', 't_risk_specifications.id_status',
                    't_risk_specifications.id_risk_probability', 't_risk_specifications.id_risk_exhibition', 
                    't_risk_specifications.id_risk_consequence')
            ->orWhere([
                ['t_risk_specifications.id_risk_probability', $idProbability],
                ['t_risk_specifications.id_risk_exhibition', $idExhibition],
                ['t_risk_specifications.id_risk_consequence', $idConsequence]
            ])
            ->where('id_status', $idStatus);
        $data = $query->get()->toArray();
        return $data;
    }


    /**
     * Return Specifications for datatables
     */
    public function scopeGetSpecificationsDT($query, $page, $rows, $search, $draw, $order, $fName, $fIdStatus, $idProbability, $idExhibition, $idConsequence){
        $query->join('c_status', 't_risk_specifications.id_status', 'c_status.id_status')
            ->select('t_risk_specifications.id_risk_specification', 't_risk_specifications.specification', 
                    't_risk_specifications.id_status', 'c_status.status')
            ->orWhere([
                ['t_risk_specifications.id_risk_probability', $idProbability],
                ['t_risk_specifications.id_risk_exhibition', $idExhibition],
                ['t_risk_specifications.id_risk_consequence', $idConsequence]
            ]);
        // Add filters
        if ( !is_null($fName) ) {
            $query->where('t_risk_specifications.specification', 'LIKE','%'.$fName.'%');
        }
        if ( !is_null($fIdStatus) ) {
            $query->where('t_risk_specifications.id_status', $fIdStatus);
        }
        //Order by
        switch ($order[0]['column']) {
            case 0:
                $columnSwitch = 't_risk_specifications.specification';
                break;
            case 1:
                $columnSwitch = 'c_status.status';
                break;
            default:
                $columnSwitch = 't_risk_specifications.id_risk_probability';
                break;
        }
        $column = $columnSwitch;   
        $dir = (isset($order[0]['dir']) ? $order[0]['dir'] : 'desc');            
        $query->orderBy($column, $dir);
        
        /*Search*/
        $query->where(function($query) use ($search){
            $query->where('t_risk_specifications.specification','LIKE','%'.$search['value'].'%'); 
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
     * set new risk Specification
     */
    public function scopeSetSpecification($query, $data){
        $model = new RiskSpecificationsModel();
        $model->specification = $data['specification'];
        $model->id_status = $data['idStatus'];
        $model->id_risk_probability = $data['idProbability'];
        $model->id_risk_exhibition = $data['idExhibition'];
        $model->id_risk_consequence = $data['idConsequence'];
        try {
            $model->save();
            $data = StatusConstants::SUCCESS;
        } catch (Exception $e) {
            $data = StatusConstants::ERROR;
        }
        return $data;
    }
    /**
     * Update risk Specification
     */
    public function scopeUpdateSpecification($query, $data){
        try {
            $model = RiskSpecificationsModel::findOrFail($data['idSpecification']);
        } catch (ModelNotFoundException $nf) {
            return StatusConstants::WARNING;
        }
        $model->specification = $data['specification'];
        $model->id_status = $data['idStatus'];
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Delete risk Specification
     */
    public function scopeDeleteSpecification($query, $data){
        try {
            $model = RiskSpecificationsModel::findOrFail($data['idSpecification']);
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
}