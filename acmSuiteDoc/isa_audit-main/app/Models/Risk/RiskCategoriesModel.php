<?php

namespace App\Models\Risk;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;

class RiskCategoriesModel extends Model
{
    protected $table = 'c_risk_categories'; 
    protected $primaryKey = 'id_risk_category';
    /**
    * Return aspects
    */
    public function scopeGetRiskCategories($query, $idStatus){
        $query->select('id_risk_category', 'risk_category', 'id_status')
            ->where('id_status', $idStatus);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Return categories for datatables
     */
    public function scopeGetCategoriesDT($query, $page, $rows, $search, $draw, $order, $fName, $fIdStatus){
        $query
            ->join('c_status', 'c_risk_categories.id_status', 'c_status.id_status')
            ->select('c_risk_categories.id_risk_category', 'c_risk_categories.risk_category',
                    'c_risk_categories.id_status', 'c_status.status');
        // Add filters
        if ( !is_null($fName) ) {
            $query->where('c_risk_categories.risk_category', 'LIKE','%'.$fName.'%');
        }
        if ( !is_null($fIdStatus) ) {
            $query->where('c_risk_categories.id_status', $fIdStatus);
        }
        //Order by
        switch ($order[0]['column']) {
            case 0:
                $columnSwitch = 'c_risk_categories.risk_category';
                break;
            case 1:
                $columnSwitch = 'c_status.status';
                break;
            default:
                $columnSwitch = 'c_risk_categories.id_risk_category';
                break;
        }
        $column = $columnSwitch;   
        $dir = (isset($order[0]['dir']) ? $order[0]['dir'] : 'desc');            
        $query->orderBy($column, $dir);
        
        /*Search*/
        $query->where(function($query) use ($search){
            $query->where('c_risk_categories.risk_category','LIKE','%'.$search['value'].'%'); 
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
     * set new risk category
     */
    public function scopeSetCategory($query, $data){
        $model = new RiskCategoriesModel();
        $model->risk_category = $data['name'];
        $model->id_status = $data['idStatus'];
        try {
            $model->save();
            $data['status'] = StatusConstants::SUCCESS;
            $data['idRiskCategory'] = $model->id_risk_category;
            $data['riskCategory'] = $model->risk_category;
        } catch (Exception $e) {
            $data['status'] = StatusConstants::ERROR;
        }
        return $data;
    }
    /**
     * Update risk category
     */
    public function scopeUpdateCategory($query, $data){
        try {
            $model = RiskCategoriesModel::findOrFail($data['idCategory']);
        } catch (ModelNotFoundException $nf) {
            return StatusConstants::WARNING;
        }
        $model->risk_category = $data['name'];
        $model->id_status = $data['idStatus'];
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Delete risk category
     */
    public function scopeDeleteCategory($query, $data){
        try {
            $model = RiskCategoriesModel::findOrFail($data['idCategory']);
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