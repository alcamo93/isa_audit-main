<?php

namespace App\Models\Risk;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;

class RiskInterpretationsModel extends Model
{
    protected $table = 't_risk_interpretations'; 
    protected $primaryKey = 'id_risk_interpretation';
    /**
     * Get intepretatiosn
     */
    public function scopeGetInterpretations($query, $idRiskCategory) {
        $query->where('id_risk_category', $idRiskCategory);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Set Intepretations
     */
    public function scopeSetInterpretations($query, $values) {
        foreach ($values as $k => $v) {
            // new or update
            if ($v['id_risk_interpretation'] != 0) {
                $model = RiskInterpretationsModel::findOrFail($v['id_risk_interpretation']);
            }
            else {
                $model = new RiskInterpretationsModel();
            }
            // set data
            $model->interpretation = $v['interpretation'];
            $model->interpretation_min = $v['interpretation_min'];
            $model->interpretation_max = $v['interpretation_max'];
            $model->id_risk_category = $v['id_risk_category'];
            // save new or update
            try {
                $model->save();
                $status = StatusConstants::SUCCESS;
            } catch (Exception $e) {
                $status = StatusConstants::ERROR;
                break;
            }
        }
        return $status;
    }
    /**
    * Return aspects
    */
    public function scopeGetInterpretationByRange($query, $idRiskCategory, $value) {
        $query->where('id_risk_category', $idRiskCategory)
            ->whereRaw($value.' BETWEEN interpretation_min AND interpretation_max');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get by min value
     */
    public function scopeGetInterpretationByMinValue($query ,$idRiskCategory, $value) {
        $query->select('id_risk_interpretation', 'interpretation', 
                'interpretation_min', 'interpretation_max', 'id_risk_category')
            ->where('id_risk_category', $idRiskCategory)
            ->where('interpretation_min', '>=', $value);
        $data = $query->get()->toArray();
        return $data;
    }
}