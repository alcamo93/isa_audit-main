<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;

class ContactsModel extends Model
{
    protected $table = 't_contacts';
    protected $primaryKey = 'id_contact';
    /**
     * Return info contact by id_corporate
     */
    public function scopeGetContact($query, $idCorporate){
        $query->select('t_contacts.id_contact', 't_contacts.ct_email', 't_contacts.ct_phone_office', 
                't_contacts.ct_cell', 't_contacts.ct_first_name', 't_contacts.ct_second_name', 't_contacts.ct_last_name', 
                't_contacts.degree', 't_contacts.id_corporate', 't_contacts.ct_ext' )
            ->where('t_contacts.id_corporate', $idCorporate);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Set / Update contact
     */
    public function scopeSetContact($query, $data){
        if($data['idContact'] == 0){
            $model = new ContactsModel();
        }else{
            try {
                $model = ContactsModel::find($data['idContact']);
            } catch (ModelNotFoundException $th) {
                $data['status'] = StatusConstants::WARNING;
                return StatusConstants::WARNING;
            }
        }
        $model->id_corporate = $data['idCorporate'];
        $model->ct_email = $data['email'];
        $model->ct_phone_office = $data['phOffice'];
        $model->ct_ext = $data['phExtOffice'];
        $model->ct_cell = $data['cell'];
        $model->ct_first_name = $data['firstName'];
        $model->ct_second_name = $data['secondName'];
        $model->ct_last_name = $data['lastName'];
        $model->degree = $data['degree'];
        try {
            $model->save();
            $data['idContact'] = $model->id_contact;
            $data['idCorporate'] = $model->id_corporate;
            $data['status'] = StatusConstants::SUCCESS;
            return $data;
        } catch (Exception $e) {
            $data['status'] = StatusConstants::ERROR;
            return $data;
        }
    }
    /**
     * Delete contact
     */
    public function scopeDeleteContact($query, $idContact){
        try {
            $model = ContactsModel::findOrFail($idContact);
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
}