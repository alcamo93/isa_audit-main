<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;

class LicensesModel extends Model
{
    protected $table = 't_licenses';
    protected $primaryKey = 'id_license';
    /**
     * Return info customer by id_license
     */
    public function scopeGetLicense($query, $idLicense){
        $query->join('c_status', 't_licenses.id_status', 'c_status.id_status')
            ->join('c_periods', 'c_periods.id_period', 't_licenses.id_period')
            ->select('t_licenses.id_license', 't_licenses.license', 't_licenses.usr_global', 
                't_licenses.usr_corporate', 't_licenses.usr_operative', 
                't_licenses.id_status', 'c_status.status', 'c_periods.period', 't_licenses.id_period')
            ->where('t_licenses.id_license', $idLicense);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get licenses to datatables
     */
    public function scopeGetLicensesDT($query, $page, $rows, $search, $draw, $order, $fLicense, $fIdStatus){
        $query->join('c_status', 't_licenses.id_status', 'c_status.id_status')
            ->join('c_periods', 'c_periods.id_period', 't_licenses.id_period')
            ->select('t_licenses.id_license', 't_licenses.license', 't_licenses.usr_global', 
                't_licenses.usr_corporate', 't_licenses.usr_operative', 
                't_licenses.id_status', 'c_status.status', 'c_periods.period', 't_licenses.id_period');
        // Add filters
        if ($fLicense != null) {
            $query->where('t_licenses.license','LIKE','%'.$fLicense.'%');
        }
        if ($fIdStatus != 0) {
            $query->where('t_licenses.id_status', $fIdStatus);
        }
        //Order by
        switch ($order[0]['column']) {
            case 0:
                $columnSwitch = 't_licenses.license';
                break;
            default:
                $columnSwitch = 't_licenses.id_license';
                break;
        }
        $column = $columnSwitch;   
        $dir = (isset($order[0]['dir']) ? $order[0]['dir'] : 'desc');            
        $query->orderBy($column, $dir);
        
        /*Search*/
        $query->where(function($query) use ($search){
            $query->where('t_licenses.license','LIKE','%'.$search['value'].'%'); 
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
     * Set info License
     */
    public function scopeSetLicense($query, $data){
        $model = new LicensesModel;
        $model->license = $data['license'];
        $model->usr_global = $data['usrGlobals'];
        $model->usr_corporate = $data['usrCorporates'];
        $model->usr_operative = $data['usrOperatives'];
        $model->id_status = $data['idStatus'];
        $model->id_period = $data['idPeriod'];
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Update info customer
     */
    public function scopeUpdateLicense($query, $data){
        try {
            $model = LicensesModel::findOrFail($data['idLicense']);
            $model->license = $data['license'];
            $model->usr_global = $data['usrGlobals'];
            $model->usr_corporate = $data['usrCorporates'];
            $model->usr_operative = $data['usrOperatives'];
            $model->id_status = $data['idStatus'];
            $model->id_period = $data['idPeriod'];
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
    public function scopeDeleteLicense($query, $idLicense){
        $model = LicensesModel::findOrFail($idLicense);
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;  //on cascade exception
        }
    }
    /**
     * Get all licenses
     */
    public function scopeGetAllLicenses($query){
        $query->join('c_status', 't_licenses.id_status', 'c_status.id_status')
            ->join('c_periods', 'c_periods.id_period', 't_licenses.id_period')
            ->select('t_licenses.id_license', 't_licenses.license', 't_licenses.usr_global', 
                't_licenses.usr_corporate', 't_licenses.usr_operative', 
                't_licenses.id_status', 'c_status.status', 'c_periods.period', 't_licenses.id_period');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get licenses filtering name
     */
    public function scopeGetFilterLicenses($query, $filter){
        $query->join('c_status', 't_licenses.id_status', 'c_status.id_status')
            ->join('c_periods', 'c_periods.id_period', 't_licenses.id_period')
            ->select('t_licenses.id_license', 't_licenses.license', 't_licenses.usr_global', 
                't_licenses.usr_corporate', 't_licenses.usr_operative', 
                't_licenses.id_status', 'c_status.status', 'c_periods.period', 't_licenses.id_period')
            ->where('t_licenses.license','LIKE','%'.$filter.'%');
        $data = $query->limit(5)->get()->toArray();
        return $data;
    }
}