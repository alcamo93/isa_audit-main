<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;

class ConditionsModel extends Model
{
       protected $table = 'c_conditions';
       protected $primaryKey = 'id_condition';
       /**
        * Return conditions
        */
       public function scopeGetConditions($query)
       {
              $query->select('id_condition', 'condition')
                     ->orderBy('condition', 'ASC');    		
              $data = $query->get()->toArray();
              return $data;
       }
}