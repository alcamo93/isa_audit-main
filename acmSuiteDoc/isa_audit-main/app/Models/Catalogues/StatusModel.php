<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;

class StatusModel extends Model
{
       protected $table = 'c_status';
       protected $primaryKey = 'id_status';
       /**
        * Return status selected
        */
       public function scopeGetStatus($query, $ids){
              $query->select('id_status', 'status')               
                     ->whereIn('id_status', $ids)
                     ->orderBy('status', 'ASC');    		
              $data = $query->get()->toArray();
       return $data;
       }
       /**
        * Return all status for group
        */
       public function scopeGetStatusByGroup($query, $group){
              $query->select('id_status', 'status')               
                     ->where('group', $group)
                     ->orderBy('status', 'ASC');
              $data = $query->get()->toArray();
       return $data;
       }
}