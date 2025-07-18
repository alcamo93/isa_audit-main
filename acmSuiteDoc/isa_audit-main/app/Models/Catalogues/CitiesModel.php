<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;

class CitiesModel extends Model
{
    protected $table = 'c_cities';
    protected $primaryKey = 'id_city';
    /**
     * Return all cities for Country
    */
    public function scopeGetCities($query, $idState){
        $query->select('id_city', 'city')
            ->where('id_state', $idState)
            ->orderBy('city', 'ASC');
        $data = $query->get()->toArray();
        return $data;
	}
    public function scopeGetAllCities($query){
        $query->select('id_city', 'city')
            ->orderBy('city', 'ASC');
        $data = $query->get()->toArray();
        return $data;
	}
}