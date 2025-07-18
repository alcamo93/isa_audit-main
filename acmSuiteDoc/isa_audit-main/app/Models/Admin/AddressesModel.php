<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;

class AddressesModel extends Model
{
    protected $table = 't_addresses';
    protected $primaryKey = 'id_address';
    /**
     * Return info corporate by id_corporate
     */
    public function scopeGetAddress($query, $idCorporate){
        $query->select('t_addresses.id_address', 't_addresses.street', 't_addresses.ext_num', 
                't_addresses.int_num', 't_addresses.zip', 't_addresses.suburb', 't_addresses.type', 
                't_addresses.id_country', 't_addresses.id_state', 't_addresses.id_city', 't_addresses.id_corporate')
            ->where('t_addresses.id_corporate', $idCorporate);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Set / Update Addres
     */
    public function scopeSetAddress($query, $data){
        if($data['idAddress'] == 0){
            $model = new AddressesModel();
        }else{
            try {
                $model = AddressesModel::find($data['idAddress']);
            } catch (ModelNotFoundException $th) {
                $data['status'] = StatusConstants::WARNING;
                return StatusConstants::WARNING;
            }
        }
        $model->id_corporate = $data['idCorporate'];
        $model->street = $data['street'];
        $model->ext_num = $data['numExt'];
        $model->int_num = $data['numInt'];
        $model->zip = $data['zip'];
        $model->suburb = $data['suburb'];
        $model->type = $data['type'];
        $model->id_country = $data['idCountry'];
        $model->id_state = $data['idState'];
        $model->id_city = $data['idCity'];
        try {
            $model->save();
            $data['idAddress'] = $model->id_address;
            $data['idCorporate'] = $model->id_corporate;
            $data['type'] = $model->type;
            $data['status'] = StatusConstants::SUCCESS;
            return $data;
        } catch (Exception $e) {
            $data['status'] = StatusConstants::ERROR;
            return $data;
        }
    }
    /**
     * Delete address
     */
    public function scopeDeleteAddress($query, $idAddress){
        try {
            $model = AddressesModel::findOrFail($idAddress);
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
     * Return info corporate by id_corporate and type 
     */
    public function scopeGetAddressType($query, $idCorporate, $type){
        $query->join('c_countries', 'c_countries.id_country', 't_addresses.id_country')
            ->join('c_states', 'c_states.id_state', 't_addresses.id_state')
            ->join('c_cities', 'c_cities.id_city', 't_addresses.id_city')
            ->select('t_addresses.id_address', 't_addresses.street', 't_addresses.ext_num', 
                't_addresses.int_num', 't_addresses.zip', 't_addresses.suburb', 't_addresses.type', 
                't_addresses.id_country', 't_addresses.id_state', 't_addresses.id_city', 't_addresses.id_corporate',
                'c_countries.country', 'c_states.state', 'c_cities.city')
            ->where('t_addresses.id_corporate', $idCorporate)
            ->where('t_addresses.type', $type);
        $data = $query->get()->toArray();
        return $data;
    }
}