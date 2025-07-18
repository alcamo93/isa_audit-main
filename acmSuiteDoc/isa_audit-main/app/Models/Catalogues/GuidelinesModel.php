<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;
use App\Classes\StatusConstants;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GuidelinesModel extends Model
{
    protected $table = 't_guidelines';
    protected $primaryKey = 'id_guideline';
    /**
     * Set guideline
     */
    public function scopeSetGuideline($query, $guideline, $initialsGuideline, $applicationType, $legalClassification, $state, $city, $lastDate)
    {
        $response = StatusConstants::ERROR;
        $query
            ->select('id_guideline')
            ->where('guideline', $guideline);
        $data = $query->first();
        if($data) $response = StatusConstants::WARNING;
        else {
            if(
                $query
                ->insert([
                    'guideline' => $guideline,
                    'initials_guideline' => $initialsGuideline,
                    'id_application_type' => $applicationType,
                    'id_legal_c' => $legalClassification,
                    'id_state' => $state,
                    'id_city' => $city,
                    'last_date' => $lastDate
                ])
            ) $response = StatusConstants::SUCCESS;
        }
        return $response;
    }
    /**
     * Get guidelines to datatables
     */
    public function scopeGetGuidelinesDT($query, $page, $rows, $search, $draw, $order, $filterName, $applicationType, $legalClassification, $state, $city, $filterInitials) {
        $query->select(
            't_guidelines.*', 
            \DB::raw('DATE_FORMAT(t_guidelines.last_date, "%Y-%m-%d") AS last_date_format')
        );
        $where = [];
        if ($filterName != null) array_push($where, ['t_guidelines.guideline','LIKE','%'.$filterName.'%']);
        if ($applicationType != null) array_push($where, ['t_guidelines.id_application_type', $applicationType]);
        if ($legalClassification != null) array_push($where, ['t_guidelines.id_legal_c', $legalClassification]);
        if ($state != null) array_push($where, ['t_guidelines.id_state', $state]);
        if ($city != null && $city != '0') array_push($where, ['t_guidelines.id_city', $city]);
        if ($filterInitials != null) array_push($where, ['t_guidelines.initials_guideline','LIKE','%'.$filterInitials.'%']);
        if($where) $query->where($where);
        //Order by
        $arrayOrder = [
            0 => 't_guidelines.guideline',
            1 => 't_guidelines.initials_guideline'
        ];
        $column = $arrayOrder[$order[0]['column']];   
        $dir = (isset($order[0]['dir']) ? $order[0]['dir'] : 'desc');            
        $query->orderBy($column, $dir);
        // Paginate
        $totalRecords = $query->count('t_guidelines.guideline');
        $paginate = $query->skip($page)->take($rows)->get();
        $data['data'] = $paginate;
        $data['recordsTotal'] = $totalRecords;
        $data['draw'] = intval($draw);
        $data['recordsFiltered'] = $totalRecords;
        return $data;
    }
    /**
     * Get guidelines for select input
     */
    public function scopeGetGuidelinesSelection($query, $applicationType = null, $legalClassification = null , $state = null)
    {
        $query->select('t_guidelines.id_guideline', 't_guidelines.initials_guideline');

        $where = [];

        if ($applicationType != null) array_push($where, ['t_guidelines.id_application_type', $applicationType]);
        if ($legalClassification != null) array_push($where, ['t_guidelines.id_legal_c', $legalClassification]);
        if ($state != null) array_push($where, ['t_guidelines.id_state', $state]);
        if($where) $query->where($where);

        //Order by
        $query->orderBy('guideline', 'ASC');

        $data = $query->get()->toArray();

        return $data;
    }
    /**
     * Update Guideline
     */
    public function scopeUpdateGuideline($query, $idguideline, $guideline, $initialsGuideline, $applicationType, $legalClassification, $state, $city, $lastDate) {
        try {
            $model = GuidelinesModel::findOrFail($idguideline);
        } catch (ModelNotFoundException $nf) {
            return StatusConstants::WARNING;
        }
        $model->guideline = $guideline;
        $model->initials_guideline = $initialsGuideline;
        $model->id_application_type = $applicationType;
        $model->id_legal_c = $legalClassification;
        $model->id_state = $state;
        $model->id_city = $city;
        $model->last_date = $lastDate;
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * delete guideline
     */
    public function scopeDeleteGuideline($query, $idGuideline){
        $response = $response = StatusConstants::ERROR;
        try {
            $query
            ->where('id_guideline', $idGuideline)
            ->delete();
            $response = StatusConstants::SUCCESS;
        }
        catch (\Exception $e) {
            if($e->getCode() == '23000') $response =  StatusConstants::WARNING;
            else $response =  StatusConstants::ERROR;
        }
        return $response;
    }
    
}