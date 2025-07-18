<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;

class ApplicationTypesModel extends Model
{
    protected $table = 'c_application_types';
    protected $primaryKey = 'id_application_type';
    /**
     * Return all states for Country
    */
    public function scopeGetApplicationTypes($query, $group){
        $query->select('id_application_type', 'application_type')
            ->whereIn('group', $group)
            ->orderBy('application_type', 'ASC');
        $data = $query->get()->toArray();
        return $data;
	}
}