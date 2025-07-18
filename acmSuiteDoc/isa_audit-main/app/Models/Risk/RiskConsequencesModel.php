<?php

namespace App\Models\Risk;

use Illuminate\Database\Eloquent\Model;
use App\Classes\StatusConstants;

class RiskConsequencesModel extends Model
{
    protected $table = 't_risk_consequences'; 
    protected $primaryKey = 'id_risk_consequence';
    /**
    * Return consequences by idRiskCategory
    */
    public function scopeGetRiskConsequences($query, $idRiskCategory, $idStatus){
        $query->select('id_risk_consequence', 'consequence', 'name', 'name_consequence', 'id_status', 'id_risk_category',
            \DB::raw('SUBSTRING(name_consequence, 1, 25) AS name_consequence_short'))
            ->where([
                ['id_risk_category', $idRiskCategory],
                ['id_status', $idStatus]
            ]);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get risk level consequence by id
     */
    public function scopeGetRiskConsequence($query, $idRiskConsequence){
        $query->select('id_risk_consequence', 'consequence', 'name', 'name_consequence', 'id_status', 'id_risk_category',
            \DB::raw('SUBSTRING(name_consequence, 1, 25) AS name_consequence_short'))
            ->where('id_risk_consequence', $idRiskConsequence);
        $data = $query->get()->toArray();
        return $data;
    }

    /************** Catalogue risk ***************/

    /**
     * Return consequences for datatables
     */
    public function scopeGetConsequencesDT($query, $page, $rows, $search, $draw, $order, $fName, $fIdStatus, $idRiskCategory){
        $query->join('c_status', 't_risk_consequences.id_status', 'c_status.id_status')
            ->select('t_risk_consequences.id_risk_consequence', 't_risk_consequences.consequence', 
                    't_risk_consequences.name_consequence', 't_risk_consequences.id_status', 'c_status.status')
            ->where('t_risk_consequences.id_risk_category', $idRiskCategory);
        // Add filters
        if ( !is_null($fName) ) {
            $query->where('t_risk_consequences.name_consequence', 'LIKE','%'.$fName.'%');
        }
        if ( !is_null($fIdStatus) ) {
            $query->where('t_risk_consequences.id_status', $fIdStatus);
        }
        //Order by
        switch ($order[0]['column']) {
            case 0:
                $columnSwitch = 't_risk_consequences.consequence';
                break;
            case 1:
                $columnSwitch = 't_risk_consequences.name_consequence';
                break;
            case 2:
                $columnSwitch = 'c_status.status';
                break;
            default:
                $columnSwitch = 't_risk_consequences.id_risk_consequence';
                break;
        }
        $column = $columnSwitch;   
        $dir = (isset($order[0]['dir']) ? $order[0]['dir'] : 'desc');            
        $query->orderBy($column, $dir);
        
        /*Search*/
        $query->where(function($query) use ($search){
            $query->where('t_risk_consequences.name_consequence','LIKE','%'.$search['value'].'%'); 
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
     * set new risk Consequence
     */
    public function scopeSetConsequence($query, $data){
        $model = new RiskConsequencesModel();
        $model->consequence = $data['numConsequence'];
        $model->name_consequence = $data['nameConsequence'];
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
     * Update risk Consequence
     */
    public function scopeUpdateConsequence($query, $data){
        try {
            $model = RiskConsequencesModel::findOrFail($data['idConsequence']);
        } catch (ModelNotFoundException $nf) {
            return StatusConstants::WARNING;
        }
        $model->consequence = $data['numConsequence'];
        $model->name_consequence = $data['nameConsequence'];
        $model->id_status = $data['idStatus'];
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Delete risk Consequence
     */
    public function scopeDeleteConsequence($query, $data){
        try {
            $model = RiskConsequencesModel::findOrFail($data['idConsequence']);
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