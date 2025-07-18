<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;

class EvidencesModel extends Model
{
       protected $table = 'c_evidences';
       protected $primaryKey = 'id_evidence';
       /**
        * Return audit types
        */
       public function scopeGetEvidences($query){
              $query->orderBy('id_evidence', 'ASC');
              $data = $query->get()->toArray();
       return $data;
       }
}