<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;

class ScopeModel extends Model
{
    protected $table = 'c_scope';
    protected $primaryKey = 'id_scope';
    /**
     * Return all cities for Country
    */
    public function scopeGetScopes($query){
        $query->select('id_scope', 'scope')
            ->orderBy('id_scope', 'ASC');
        $data = $query->get()->toArray();
        return $data;
	}
}