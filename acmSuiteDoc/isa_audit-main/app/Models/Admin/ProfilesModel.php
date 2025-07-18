<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;

class ProfilesModel extends Model
{
    protected $table = 't_profiles';
    protected $primaryKey = 'id_profile';
    
    public function modules(){
        return $this->belongsToMany(
            'App\Models\Admin\ModulesModel', 
            't_profiles_permissions',
            'id_profile',
            'id_module'
        )->wherePivotNull('id_submodule');
    }
    /**
     * Return info customer by id_profile
     */
    public function scopeGetProfile($query, $idProfile){
        $query->join('c_status', 't_profiles.id_status', 'c_status.id_status')
            ->join('t_profile_types', 't_profiles.id_profile_type', 't_profile_types.id_profile_type')
            ->select('t_profiles.id_profile', 't_profiles.profile_name', 't_profiles.id_customer',
                    't_profiles.id_status', 'c_status.status', 't_profiles.id_corporate', 
                    't_profiles.id_profile_type', 't_profile_types.type', 't_profile_types.owner', 't_profile_types.profile_level')
            ->where('t_profiles.id_profile', $idProfile);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get profiles to datatables
     */
    public function scopeGetProfilesDT($query, $page, $rows, $search, $draw, $order, $fIdCustomer, $fIdCorporate, $profileLevel){
        $query
            ->join('c_status', 't_profiles.id_status', 'c_status.id_status')
            ->join('t_profile_types', 't_profiles.id_profile_type', 't_profile_types.id_profile_type')
            ->join('t_corporates', 't_profiles.id_corporate', 't_corporates.id_corporate')
            ->select('t_profiles.id_profile', 't_profiles.profile_name', 't_profiles.id_customer',
                    't_profiles.id_status', 'c_status.status', 't_profiles.id_corporate', 't_corporates.corp_tradename',
                    't_profiles.id_profile_type', 't_profile_types.type');
        // Add filters
        $where = [];
        if ($fIdCustomer != 0) array_push($where, ['t_profiles.id_customer', $fIdCustomer]);
        if ($fIdCorporate != 0) array_push($where, ['t_profiles.id_corporate', $fIdCorporate]);
        array_push($where, ['t_profile_types.profile_level', '>=', $profileLevel]);
        
        $query->where($where);

        //Order by
        switch ($order[0]['column']) {
            case 0:
                $columnSwitch = 't_profiles.profile_name';
                break;
            case 1:
                $columnSwitch = 'c_status.status';
                break;
            case 2:
                $columnSwitch = 't_profile_types.type';
                break;
            default:
                $columnSwitch = 't_profiles.id_profile';
                break;
        }
        $column = $columnSwitch;   
        $dir = (isset($order[0]['dir']) ? $order[0]['dir'] : 'desc');            
        $query->orderBy($column, $dir);
        
        /*Search*/
        $query->where(function($query) use ($search){
            $query->where('t_profiles.profile_name','LIKE','%'.$search['value'].'%'); 
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
     * Set info Profile
     */
    public function scopeSetProfile($query, $data){
        $model = new ProfilesModel;
        $model->profile_name = $data['profile'];
        $model->id_status = $data['idStatus'];
        $model->id_profile_type = $data['typeProfile'];
        $model->id_customer = $data['idCustomer'];
        $model->id_corporate = $data['idCorporate'];
        try {
            $model->save();
            $data['status'] = StatusConstants::SUCCESS;
            $data['data'] = $model->id_profile;
            $data['typeProfile'] = $model->id_profile_type;
            return $data;
        } catch (Exception $e) {
            $data['status'] = StatusConstants::ERROR;
            return $data;
        }
    }
    /**
     * Update info customer
     */
    public function scopeUpdateProfile($query, $data){
        try {
            $model = ProfilesModel::findOrFail($data['idProfile']);
            $model->profile_name = $data['profile'];
            $model->id_status = $data['idStatus'];
            $model->id_profile_type = $data['typeProfile'];
            $model->id_customer = $data['idCustomer'];
            $model->id_corporate = $data['idCorporate'];
            try {
                $model->save();
                return StatusConstants::SUCCESS;
            } catch (Exception $e) {
                return StatusConstants::WARNING;
            }
        } catch (ModelNotFoundException $th) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Delete customer
     */
    public function scopeDeleteProfile($query, $idProfile){
        $model = ProfilesModel::findOrFail($idProfile);
        try {
            $model->delete();
            $response = StatusConstants::SUCCESS;
        } catch (Exception $e) {
            if($e->getCode() == '23000') $response =  StatusConstants::WARNING;
            else $response =  StatusConstants::ERROR;
        }
        return $response;
    }
    /**
     * Get all profiles
     */
    public function scopeGetAllProfiles($query, $idStatus = null, $idCustomer = null, $idCorporate = null)
    {
        $query->join('c_status', 't_profiles.id_status', 'c_status.id_status')
            ->join('t_profile_types', 't_profiles.id_profile_type', 't_profile_types.id_profile_type')
            ->select('t_profiles.id_profile', 't_profiles.profile_name', 't_profiles.id_customer',
                    't_profiles.id_status', 'c_status.status', 't_profiles.id_corporate',
                    't_profiles.id_profile_type', 't_profile_types.type');
        $where = [];
        if($idStatus) array_push($where, ['t_profiles.id_status', $idStatus]);
        if($idCustomer) array_push($where, ['t_profiles.id_customer', $idCustomer]);
        if($idCorporate) array_push($where, ['t_profiles.id_corporate', $idCorporate]);
        $query->where($where);
            
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get profiles filtering name
     */
    public function scopeGetFilterProfiles($query, $filter){
        $query->join('c_status', 't_profiles.id_status', 'c_status.id_status')
            ->select('t_profiles.id_profile', 't_profiles.profile', 't_profiles.usr_global', 
                't_profiles.usr_corporate', 't_profiles.usr_operative', 't_profiles.num_periods',
                 't_profiles.type_period', 't_profiles.id_status', 'c_status.status')
            ->where('t_profiles.profile','LIKE','%'.$filter.'%');
        $data = $query->limit(5)->get()->toArray();
        return $data;
    }
     /**
     * Get all profiles by idCorporate
     */
    public function scopeGetAllProfilesFilter($query, $fIdCorporate){
        $query->join('c_status', 't_profiles.id_status', 'c_status.id_status')
            ->join('t_profile_types', 't_profiles.id_profile_type', 't_profile_types.id_profile_type')
            ->select('t_profiles.id_profile', 't_profiles.profile_name', 't_profiles.id_customer',
                    't_profiles.id_status', 'c_status.status', 't_profiles.id_corporate',
                    't_profiles.id_profile_type', 't_profile_types.type')
            ->where('t_profiles.id_corporate', $fIdCorporate);
        $data = $query->get()->toArray();
        return $data;
    }
    public function scopeGetAllProfilesLower($query, $idType = null, $idCustomer = null, $idCorporate = null)
    {
        $query->join('c_status', 't_profiles.id_status', 'c_status.id_status')
            ->join('t_profile_types', 't_profiles.id_profile_type', 't_profile_types.id_profile_type')
            ->select('t_profiles.id_profile', 't_profiles.profile_name', 't_profiles.id_customer',
                    't_profiles.id_status', 'c_status.status', 't_profiles.id_corporate',
                    't_profiles.id_profile_type', 't_profile_types.type');
        $where = [];
        if($idType) array_push($where, ['t_profiles.id_profile_type', '>=', $idType]);
        if($idCustomer) array_push($where, ['t_profiles.id_customer', $idCustomer]);
        if($idCorporate) array_push($where, ['t_profiles.id_corporate', $idCorporate]);
        $query->where($where);
            
        $data = $query->get()->toArray();
        return $data;
    }
} 