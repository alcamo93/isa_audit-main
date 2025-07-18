<?php

namespace App\Models\Risk;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException as Exception;
use App\Classes\StatusConstants;

class RiskAttributesModel extends Model
{
    protected $table = 'c_risk_attributes'; 
    protected $primaryKey = 'id_c_risk_attribute';
    /**
     * set answer risk answer
     */
    public function scopeGetRiskAttributes($query){
        $data = $query->get()->toArray();
        return $data;
    }
}