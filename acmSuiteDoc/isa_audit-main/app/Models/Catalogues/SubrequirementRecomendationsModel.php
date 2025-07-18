<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;
use App\Classes\StatusConstants;

class SubrequirementRecomendationsModel extends Model
{
    protected $table = 't_subrequirement_recomendations';
    protected $primaryKey = 'id_recomendation';
    
    /**
     * Get data recomendation
     */
    public function scopeGetRecomendation($query, $idRecomendation){
        $query->where('id_recomendation', $idRecomendation);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
    * Return aspects
    */
    public function scopeGetRecomendationsByIdSubrequirement($query, $idSubrequirement){
        $query->where('id_subrequirement', $idSubrequirement);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
    * Set Aspect from add catalogs module
    */
    public function scopeSetRecomendation($query, $recommendation, $idSubrequirement)
    {
        $response = null;
        $query
        ->select('id_recomendation')
        ->where([
            ['recomendation', $recommendation],
            ['id_subrequirement', $idSubrequirement],
        ]);
        $data = $query->first();
        if($data) $response = StatusConstants::ERROR;
        else {
            if(
                $query
                ->insert([
                    'recomendation' => $recommendation, 
                    'id_subrequirement' => $idSubrequirement
                ])
            ) $response = StatusConstants::SUCCESS;
        }
        return $response;
    }
    /**
     * delete aspect from add catalogs module
    */
    public function scopeDeleteRecomendation($query, $idRecomendation){
    $response = StatusConstants::ERROR;
            if(
                $query
                ->where('id_recomendation', $idRecomendation)
                ->delete()
            ) $response = StatusConstants::SUCCESS;
            return $response;
    }
    /**
     * Update recomendation
     */
    public function scopeUpdateRecomendation($query, $recommendation, $idSubrecomendation) {
        $model = SubrequirementRecomendationsModel::findOrFail($idSubrecomendation);
        $model->recomendation = $recommendation;
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $th) {
            return StatusConstants::ERROR;
        }
    }
}