<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;

class RequirementTypesModel extends Model
{
       protected $table = 'c_requirement_types';
       protected $primaryKey = 'id_requirement_type';

       /**
        * Return all requirements type
        */
       public function scopeGetRequirementsTypeGroup($query, $group, $identification = [0,1]){
              if ($group >= 0) $query->where('group', $group);
              $query->whereIn('identification', $identification);
              $query->orderBy('requirement_type', 'ASC');
              $data = $query->get()->toArray();
              return $data;
       }
}