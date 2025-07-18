<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;
use App\Classes\StatusConstants;
use Illuminate\Database\QueryException as Exception;

class RequirementRecomendationsModel extends Model
{
    protected $table = 't_requirement_recomendations';
    protected $primaryKey = 'id_recomendation';
    /**
    * Return aspects
    */
    public function scopeGetRecomendationsByIdRequirement($query, $idRequirement) {
        $query->where('id_requirement', $idRequirement);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
    * Set Aspect from add catalogs module
    */
    public function scopeSetRecomendation($query, $recommendation, $idRequirement)
    {
        $response = null;
        $query
        ->select('id_recomendation')
        ->where([
            ['recomendation', $recommendation],
            ['id_requirement', $idRequirement],
        ]);
        $data = $query->first();
        if($data) $response = StatusConstants::ERROR;
        else {
            if(
                $query
                ->insert([
                    'recomendation' => $recommendation, 
                    'id_requirement' => $idRequirement
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
     * Get data recomendation
     */
    public function scopeGetRecomendation($query, $idRecomendation){
        $query->where('id_recomendation', $idRecomendation);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Update recomendation
     */
    public function scopeUpdateRecomendation($query, $recommendation, $idRecomendation) {
        $model = RequirementRecomendationsModel::findOrFail($idRecomendation);
        $model->recomendation = $recommendation;
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $th) {
            return StatusConstants::ERROR;
        }
    }
}