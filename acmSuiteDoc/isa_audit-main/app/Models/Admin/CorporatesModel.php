<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;

class CorporatesModel extends Model
{
    protected $table = 't_corporates';
    protected $primaryKey = 'id_corporate';

    public function users() {
        return $this->hasMany('App\User', 'id_corporate', 'id_corporate');
    }

    /**
     * Get all of the address for the CorporatesModel
     */
    public function addresses()
    {
        return $this->hasMany('App\Models\Admin\AddressesModel', 'id_corporate', 'id_corporate');
    }
    /**
     * Return info corporate by id_corporate
     */
    public function scopeGetCorporate($query, $idCorporate){
        $query->join('c_status', 't_corporates.id_status', 'c_status.id_status')
            ->join('c_industries', 't_corporates.id_industry', 'c_industries.id_industry')
            ->select('t_corporates.id_corporate', 't_corporates.corp_tradename', 't_corporates.corp_trademark', 
                't_corporates.rfc', 't_corporates.id_customer', 'c_status.status', 'c_industries.industry',
                't_corporates.id_status', 't_corporates.id_industry', 't_corporates.type')
            ->where('t_corporates.id_corporate', $idCorporate);
        $data = $query->get()->toArray();        
        return $data;
    }

    public function scopeGetAllCorporatesDT($query, $idCorporate = null)
    {
        $where = [];
        if($idCorporate) array_push($where, ['t_corporates.id_corporate', $idCorporate]);
        $query->where($where);

        $query->select('t_corporates.id_corporate', 't_corporates.corp_tradename', 't_corporates.corp_trademark', 
                 'id_customer');
        $data = $query->get()->toArray();        
        return $data;
    }
    /**
     * Get corporates to datatables
     */
    public function scopeGetCorporatesDT($query, $page, $rows, $search, $draw, $order, $idCustomer,
                $fTradename, $fTrademark, $fIdStatus, $fIdIndustry, $fRFC){
        $query->join('c_status', 't_corporates.id_status', 'c_status.id_status')
            ->join('c_industries', 't_corporates.id_industry', 'c_industries.id_industry')
            ->select('t_corporates.id_corporate', 't_corporates.corp_tradename', 't_corporates.corp_trademark', 
                't_corporates.rfc', 't_corporates.id_customer', 'c_status.status', 'c_industries.industry', 't_corporates.type')
            ->where('t_corporates.id_customer', $idCustomer);
            // Add filters
            if ($fTradename != null) {
                $query->where('t_corporates.corp_tradename','LIKE','%'.$fTradename.'%');
            }
            if ($fTrademark != null) {
                $query->where('t_corporates.corp_trademark','LIKE','%'.$fTrademark.'%');
            }
            if ($fRFC != null) {
                $query->where('t_corporates.rfc','LIKE','%'.$fRFC.'%');
            }
            if ($fIdStatus != 0) {
                $query->where('t_corporates.id_status', $fIdStatus);
            }
            if ($fIdIndustry != 0) {
                $query->where('t_corporates.id_industry', $fIdIndustry);
            }
        //Order by
        switch ($order[0]['column']) {
            case 0:
                $columnSwitch = 't_corporates.corp_trademark';
                break;
            case 1:
                $columnSwitch = 't_corporates.corp_tradename';
                break;
            case 2:
                $columnSwitch = 't_corporates.rfc';
                break;
            case 3:
                $columnSwitch = 'c_status.status';
                break;
            case 4:
                $columnSwitch = 'c_industries.industry';
                break;
            default:
                $columnSwitch = 't_corporates.id_corporate';
                break;
        }
        $column = $columnSwitch;
        $dir = (isset($order[0]['dir']) ? $order[0]['dir'] : 'desc');
        $query->orderBy($column, $dir);
        
        /*Search*/
        $query->where(function($query) use ($search){
            $query->where('t_corporates.corp_tradename','LIKE','%'.$search['value'].'%')
            ->orWhere('t_corporates.corp_trademark','LIKE','%'.$search['value'].'%'); 
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
     * Set info corporate
     */
    public function scopeSetCorporate($query, $idCustomer, $tradename, $trademark, $rfc, $idStatus, $idIndustry, $type){
        $model = new CorporatesModel;
        $model->id_customer = $idCustomer;
        $model->corp_tradename = $tradename;
        $model->corp_trademark = $trademark;
        $model->rfc = $rfc;
        $model->id_status = $idStatus;
        $model->id_industry = $idIndustry;
        $model->type = $type;
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Update info corporate
     */
    public function scopeUpdateCorporate($query, $idCorporate, $idCustomer, $tradename, $trademark, $rfc, $idStatus, $idIndustry, $type){
        try {
            $model = CorporatesModel::findOrFail($idCorporate);
            $model->id_customer = $idCustomer;
            $model->corp_tradename = $tradename;
            $model->corp_trademark = $trademark;
            $model->rfc = $rfc;
            $model->id_status = $idStatus;
            $model->id_industry = $idIndustry;
            $model->type = $type;
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
     * Delete corporate
     */
    public function scopeDeleteCorporate($query, $idCorporate){
        try {
            $model = CorporatesModel::findOrFail($idCorporate);
            try {
                $model->delete();
                return StatusConstants::SUCCESS;
            } catch (Exception $e) {
                return StatusConstants::WARNING;  //on cascade exception
            }
        } catch (ModelNotFoundException $th) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Delete all corporates
     */
    public function scopeDeleteCorporatesTransaction($query, $idCustomer){
        $model = CorporatesModel::where('t_corporates.id_customer', $idCustomer);
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::WARNING;  //on cascade exception
        }
        return $model;
    }
    /**
     * Get all corporates by customer
     */
    public function scopeGetAllCorporates($query, $idCustomer){
        $query->join('c_status', 't_corporates.id_status', 'c_status.id_status')
            ->join('c_industries', 't_corporates.id_industry', 'c_industries.id_industry')
            ->select('t_corporates.id_corporate', 't_corporates.corp_tradename', 't_corporates.corp_trademark', 
                't_corporates.rfc', 't_corporates.id_customer', 'c_status.status', 'c_industries.industry', 't_corporates.type')
            ->where('t_corporates.id_customer', $idCustomer);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get all corporates with active contract
     */
    public function scopeGetCorporateActiveContract($query, $idCustomer){
        $query->join('t_contracts', 't_corporates.id_corporate', 't_contracts.id_corporate')
            ->select('t_contracts.id_contract', 't_corporates.id_corporate',
                't_corporates.corp_tradename', 't_corporates.corp_trademark')
            ->where([
                ['t_contracts.id_customer', $idCustomer],
                ['t_corporates.id_status', StatusConstants::ACTIVE],
                ['t_contracts.id_status', StatusConstants::ACTIVE],
            ]);
        $data = $query->get()->toArray();
        return $data;
    }
}