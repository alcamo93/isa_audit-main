<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;

class ObligationTypesModel extends Model
{
    protected $table = 'c_obligation_types';
       protected $primaryKey = 'id_obligation_type';
       /**
        * Return periods
        */
       public function scopeGetObligationTypes($query){
              $query->select('id_obligation_type', 'obligation_type');
              $data = $query->get()->toArray();
              return $data;
       }
}
