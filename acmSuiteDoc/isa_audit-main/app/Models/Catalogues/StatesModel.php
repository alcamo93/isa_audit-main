<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;

class StatesModel extends Model
{
    protected $table = 'c_states';
    protected $primaryKey = 'id_state';
    /**
     * Return all states for Country
    */
    public function scopeGetStates($query, $idCountry){
        $query->select('id_state', 'state')
            ->where('id_country', $idCountry)
            ->orderBy('state', 'ASC');
        $data = $query->get()->toArray();
        return $data;
	}
}