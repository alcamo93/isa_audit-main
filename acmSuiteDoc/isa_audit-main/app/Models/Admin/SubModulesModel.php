<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Classes\StatusConstants;

class SubModulesModel extends Model
{
     protected $table = 'c_submodules';
     protected $primaryKey = 'id_submodule';
     protected $fillable = ['path', 'id_status', 'name_submodule', 'initials_submodule'];
     /**
      * Return active submodules for current rolId and modules ids
      */
     public function scopeGetGroupedSubModules($query, $idProfile, $idModule){ 
          $query->join('c_modules', 'c_modules.id_module', 'c_submodules.id_module')
               ->join('t_profiles_permissions', 't_profiles_permissions.id_submodule', 'c_submodules.id_submodule')                         
               ->select(
                    'c_submodules.id_submodule',
                    'c_submodules.id_module',
                    'c_submodules.name_submodule',
                    'c_submodules.initials_submodule',
                    'c_submodules.path',
                    'c_submodules.description'
               )
               ->where('t_profiles_permissions.id_profile', $idProfile)
               ->where('c_submodules.id_status', StatusConstants::ACTIVE) //active modules
               ->where('c_submodules.id_module', $idModule);
          $data = $query->get()->toArray();
          return $data;
	}
     /**
      * Return submodules
      */
     public function scopeGetPermissions($query, $page, $rows, $search, $draw, $order, $idProfile, $idModule, $restrict) {
          $query->join('c_modules', 'c_modules.id_module', '=', 'c_submodules.id_module')              
               ->select('c_submodules.id_submodule',
                         'c_submodules.id_module',
                         'c_submodules.name_submodule', 
                         'c_modules.name_module',
                         \DB::raw('(SELECT t_profiles_permissions.visualize FROM t_profiles_permissions WHERE t_profiles_permissions.id_submodule = c_submodules.id_submodule AND t_profiles_permissions.id_profile = '.$idProfile.') as visualize_c'),
                         \DB::raw('(SELECT t_profiles_permissions.create FROM t_profiles_permissions WHERE t_profiles_permissions.id_submodule = c_submodules.id_submodule AND t_profiles_permissions.id_profile = '.$idProfile.') as create_c'),
                         \DB::raw('(SELECT t_profiles_permissions.modify FROM t_profiles_permissions WHERE t_profiles_permissions.id_submodule = c_submodules.id_submodule AND t_profiles_permissions.id_profile = '.$idProfile.') as modify_c'),
                         \DB::raw('(SELECT t_profiles_permissions.delete FROM t_profiles_permissions WHERE t_profiles_permissions.id_submodule = c_submodules.id_submodule AND t_profiles_permissions.id_profile = '.$idProfile.') as delete_c')                         
                    )
               ->where('c_submodules.id_module', $idModule);
          if($restrict){
               $query->where('c_submodules.owner', '!=', 1);
          }
          // $query->orderBy('id_submodule', 'DESC');
          $queryCount = $query->get();
          $result = $query->limit($rows)->offset($page)->get()->toArray();
          $total = $queryCount->count();
          $data['data']=(sizeof($result) > 0)?$result:array();
          $data['recordsTotal'] = $total;
          $data['draw'] = (int)$draw;
          $data['recordsFiltered'] = $total;                
          return $data;    
               
     }
     /**
      * Return submodules
      */
     public function scopeGetAllSubModules($query, $restrict){
          $query
          ->select(
               'c_submodules.id_submodule', 
               'c_submodules.id_module', 
               'c_submodules.name_submodule',
               'c_submodules.path',
               'c_submodules.description'
          );
          $submodules = $query->get()->toArray();
          if($restrict) $query->where('c_submodules.owner', '!=', 1);
          return $submodules;
     }
     /**
      * Return submodule info
      */
    public function scopeGetSubModulePath($query, $path){
         $data = SubModulesModel::select('id_submodule')->where('path', $path)->get()->toArray();
         return ( sizeof($data) > 0 ) ? $data[0] : array();
    }
     /**
      * Return active submodule by id // used for menu creation
      */
     public function scopeGetSubModuleById($query, $idProfile, $idSubModule){ 
          $query->join('c_modules', 'c_modules.id_module', 'c_submodules.id_module')
               ->join('t_profiles_permissions', 't_profiles_permissions.id_submodule', 'c_submodules.id_submodule')                         
               ->select(
                    'c_submodules.id_submodule',
                    'c_submodules.id_module',
                    'c_submodules.name_submodule',
                    'c_submodules.initials_submodule',
                    'c_submodules.path',
                    'c_submodules.description'
               )
               ->where('t_profiles_permissions.id_profile', $idProfile)
               ->where('c_submodules.id_status', StatusConstants::ACTIVE) //active modules
               ->where('c_submodules.id_submodule', $idSubModule);
          $data = $query->get()->toArray();
          return $data;
	}
}