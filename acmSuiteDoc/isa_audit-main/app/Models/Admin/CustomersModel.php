<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;

class CustomersModel extends Model
{
    protected $table = 't_customers';
    protected $primaryKey = 'id_customer';
    /**
     * Return info customer by id_customer
     */
    public function scopeGetCustomer($query, $idCustomer){
        $query->select('t_customers.id_customer', 't_customers.cust_tradename', 't_customers.cust_trademark', 
                't_customers.logo', 't_customers.sm_logo', 't_customers.lg_logo', 't_customers.owner')
            ->where('t_customers.id_customer', $idCustomer);
        $data = $query->get()->toArray();        
        return $data;
    }
    /**
     * Return info customer by id_customer
     */
    public function scopeGetAllCustomers($query, $idCustomer = null)
    {
        $where = [];
        if($idCustomer) array_push($where, ['t_customers.id_customer', $idCustomer]);
        $query->where($where);

        $query->select('t_customers.id_customer', 't_customers.cust_tradename', 't_customers.cust_trademark', 
                't_customers.logo', 't_customers.sm_logo', 't_customers.lg_logo', 't_customers.owner');
        $data = $query->get()->toArray();        
        return $data;
    }
    /**
     * Get customers to datatables
     */
    public function scopeGetCustomersDT($query, $page, $rows, $search, $draw, $order, $filterName){
        $query->select('t_customers.id_customer', 't_customers.cust_tradename', 't_customers.cust_trademark', 
                't_customers.logo','t_customers.sm_logo', 't_customers.lg_logo', 't_customers.owner');
        if ($filterName != null) {
            $query->where(function($query) use ($filterName){
                $query->where('t_customers.cust_tradename','LIKE','%'.$filterName.'%')
                ->orWhere('t_customers.cust_trademark','LIKE','%'.$filterName.'%'); 
            });
        }
        //Order by
        switch ($order[0]['column']) {
            case 1:
                $columnSwitch = 't_customers.cust_trademark';
                break;
            default:
                $columnSwitch = 't_customers.id_customer';
                break;
        }
        $column = $columnSwitch;   
        $dir = (isset($order[0]['dir']) ? $order[0]['dir'] : 'desc');            
        $query->orderBy($column, $dir);
        
        /*Search*/
        $query->where(function($query) use ($search){
            $query->where('t_customers.cust_tradename','LIKE','%'.$search['value'].'%')
            ->orWhere('t_customers.cust_trademark','LIKE','%'.$search['value'].'%'); 
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
     * Set info Customer
     */
    public function scopeSetCustomer($query, $tradename, $trademark){
        $model = new CustomersModel;
        $model->cust_tradename = $tradename;
        $model->cust_trademark = $trademark;
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
    public function scopeUpdateCustomer($query, $idCustomer, $tradename, $trademark){
        try {
            $model = CustomersModel::findOrFail($idCustomer);
            $model->cust_tradename = $tradename;
            $model->cust_trademark = $trademark;
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
    public function scopeDeleteCustomer($query, $idCustomer){
        $model = CustomersModel::findOrFail($idCustomer);
        if ($model->owner == 1) {
            return StatusConstants::ERROR;
        }else{
            try {
                $model->delete();
                return StatusConstants::SUCCESS;
            } catch (Exception $e) {
                return StatusConstants::WARNING;  //on cascade exception
            }
        }
    }
    /**
     * Update name logo
     */
    public function scopeUpdateLogo($query, $idCustomer, $logo){
        try {
            $model = CustomersModel::findOrFail($idCustomer);
            $model->logo = $logo;
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
     * Update name logo
     */
    public function scopeUpdateLogoSM($query, $idCustomer, $logo){
        try {
            $model = CustomersModel::findOrFail($idCustomer);
            $model->sm_logo = $logo;
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
     * Update name large logo
     */
    public function scopeUpdateLogoLG($query, $idCustomer, $logo){
        try {
            $model = CustomersModel::findOrFail($idCustomer);
            $model->lg_logo = $logo;
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
     * Return info customer by id_customer
     */
    public function scopeGetCustomers($query){
        $query->select('t_customers.id_customer', 't_customers.cust_tradename', 't_customers.cust_trademark', 
                't_customers.logo', 't_customers.sm_logo', 't_customers.lg_logo', 't_customers.owner')
            ->where('t_customers.owner', StatusConstants::OWNER_INACTIVE);
        $data = $query->get()->toArray();        
        return $data;
    }
    /**
     * Get all customers with active contract
     */
    public function scopeGetCustomerActiveContract($query, $arrayCustomers){
        $query->whereIn('t_customers.id_customer', $arrayCustomers);
        $data = $query->get()->toArray();
        return $data;
    }
}