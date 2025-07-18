<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;

class LegalClassificationModel extends Model
{
    protected $table = 'c_legal_classification';
    protected $primaryKey = 'id_legal_c';
    /**
     * Return all states for Country
    */
    public function scopeGetLegalClassifications($query){
        $query->select('id_legal_c', 'legal_classification')
            ->orderBy('legal_classification', 'ASC');
        $data = $query->get()->toArray();
        return $data;
	}
}