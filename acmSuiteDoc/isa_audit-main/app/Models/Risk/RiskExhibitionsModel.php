<?php

namespace App\Models\Risk;

use Illuminate\Database\Eloquent\Model;
use App\Classes\StatusConstants;

class RiskExhibitionsModel extends Model
{
    protected $table = 't_risk_exhibitions'; 
    protected $primaryKey = 'id_risk_exhibition';

    /**
     * Return exhibitions for datatables
     */
    public function scopeGetexhibitionsDT($query, $page, $rows, $search, $draw, $order, $fName, $fIdStatus, $idRiskCategory){
        $query->join('c_status', 't_risk_exhibitions.id_status', 'c_status.id_status')
            ->select('t_risk_exhibitions.id_risk_exhibition', 't_risk_exhibitions.exhibition', 
                    't_risk_exhibitions.name_exhibition', 't_risk_exhibitions.id_status', 'c_status.status')
            ->where('t_risk_exhibitions.id_risk_category', $idRiskCategory);
        // Add filters
        if ( !is_null($fName) ) {
            $query->where('t_risk_exhibitions.name_exhibition', 'LIKE','%'.$fName.'%');
        }
        if ( !is_null($fIdStatus) ) {
            $query->where('t_risk_exhibitions.id_status', $fIdStatus);
        }
        //Order by
        switch ($order[0]['column']) {
            case 0:
                $columnSwitch = 't_risk_exhibitions.exhibition';
                break;
            case 1:
                $columnSwitch = 't_risk_exhibitions.name_exhibition';
                break;
            case 2:
                $columnSwitch = 'c_status.status';
                break;
            default:
                $columnSwitch = 't_risk_exhibitions.id_risk_exhibition';
                break;
        }
        $column = $columnSwitch;   
        $dir = (isset($order[0]['dir']) ? $order[0]['dir'] : 'desc');            
        $query->orderBy($column, $dir);
        
        /*Search*/
        $query->where(function($query) use ($search){
            $query->where('t_risk_exhibitions.name_exhibition','LIKE','%'.$search['value'].'%'); 
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
     * set new risk exhibition
     */
    public function scopeSetExhibition($query, $data){
        $model = new RiskExhibitionsModel();
        $model->exhibition = $data['numExhibition'];
        $model->name_exhibition = $data['nameExhibition'];
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
     * Update risk Exhibition
     */
    public function scopeUpdateExhibition($query, $data){
        try {
            $model = RiskExhibitionsModel::findOrFail($data['idExhibition']);
        } catch (ModelNotFoundException $nf) {
            return StatusConstants::WARNING;
        }
        $model->exhibition = $data['numExhibition'];
        $model->name_exhibition = $data['nameExhibition'];
        $model->id_status = $data['idStatus'];
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Delete risk Exhibition
     */
    public function scopeDeleteExhibition($query, $data){
        try {
            $model = RiskExhibitionsModel::findOrFail($data['idExhibition']);
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
    * Return exhibitions by idRiskCategory
    */
    public function scopeGetRiskExhibitions($query, $idRiskCategory, $idStatus){
        $query->select('id_risk_exhibition', 'exhibition', 'name_exhibition', 'id_status', 'id_risk_category',
            \DB::raw('SUBSTRING(name_exhibition, 1, 25) AS name_exhibition_short'))
            ->where([
                ['id_risk_category', $idRiskCategory],
                ['id_status', $idStatus]
            ]);
        $data = $query->get()->toArray();
        return $data;
    }
}