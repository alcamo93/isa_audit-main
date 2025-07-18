<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;

class CountriesModel extends Model
{
    protected $table = 'c_countries';
    protected $primaryKey = 'id_country';
    /**
     * Return all countries
    */
    public function scopeGetCountries($query){
        $query->select('id_country', 'country')
            ->orderBy('country', 'ASC');
        $data = $query->get()->toArray();
        return $data;
	}
    
}