<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;

class ProfileTypeModel extends Model
{
     protected $table = 't_profile_types';
     protected $primaryKey = 'id_profile_type';
     /**
      * Return all profiles type
      */
     public function scopeGetProfilesTypes($query, $profileLevel = null){
          $query->select('id_profile_type','type');
          if($profileLevel) $query->where('profile_level', '>=', $profileLevel);
          $data = $query->get()->toArray();
          return $data;
	}
    
}