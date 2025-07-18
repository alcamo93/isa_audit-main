<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Classes\StatusConstants;

class ModulesModel extends Model
{
     protected $table = 'c_modules';
     protected $primaryKey = 'id_module';

     protected $fillable = [  
          'name_module',
          'pseud_module',
          'path',
          'color_module',
          'icon_module',
          'id_status',
          'has_submodule',
          'sequence',
          'owner',
          'description',
     ];

     public function submodules(){
          return $this->hasMany('App\Models\Admin\SubModulesModel', 'id_module', 'id_module');
     }
     /**
      * Return active modules for current rolId
      */
     public function scopeGetModules($query)
     {
          $query->select(
               'c_modules.id_module',
               'c_modules.name_module',
               'c_modules.sequence',
               'c_modules.path',
               'c_modules.icon_module',
               'c_modules.color_module',
               'c_modules.description',
               'c_modules.has_submodule'
          )
          ->where('c_modules.id_status', '=', 1)
          ->orderBy('c_modules.sequence', 'ASC');
          $data = $query->get()->toArray();        
          return $data;  
     }
     /**
      * Return module info
      */
     public function scopeGetModulePath($query, $path)
     {
          $data = $query->select('id_module')->where('path', $path)->get()->toArray();
          return ( sizeof($data) > 0 ) ? $data[0] : array();
     }
     /**
      * Return modules info selected by array of ids // used for menu creation
      */
      public function scopeGetModulesByIds($query, $ids, $method = null){
          $whereIn = [];
          foreach($ids as $id) if(!in_array($id['id_module'], $whereIn)) array_push($whereIn, $id['id_module']);
          if ( !is_null($method) && $method == 'login' ) {
               array_push($whereIn, 1);
          }
          $query->select(
               'c_modules.id_module',
               'c_modules.name_module',
               'c_modules.sequence',
               'c_modules.path',
               'c_modules.icon_module',
               'c_modules.color_module',
               'c_modules.description',
               'c_modules.has_submodule'
          )
          ->where('c_modules.id_status', StatusConstants::ACTIVE)
          ->whereIn('c_modules.id_module', $whereIn)
          ->orderBy('c_modules.sequence', 'ASC');
          $data = $query->get()->toArray();
          return $data; 
     }
     /**
      * Return submodules
      */
      public function scopeGetAllModules($query, $restrict){
          $query
          ->select(
               'c_modules.id_module',
               'c_modules.name_module', 
               'c_modules.sequence',
               'c_modules.path', 
               'c_modules.icon_module', 
               'c_modules.color_module', 
               'c_modules.description'
          );
          if($restrict) $query->where('c_modules.owner', '!=', 1);
          $submodules = $query->get()->toArray();
          return $submodules;
     }
     /**
      * Return submodules
      */
      public function scopeGetPermissions($query, $page, $rows, $search, $draw, $order, $idProfile, $restrict) {
          $query             
               ->select(
                    'c_modules.id_module',
                    'c_modules.id_module as module',
                    'c_modules.name_module',
                    'c_modules.pseud_module',
                    'c_modules.has_submodule',
                    \DB::raw('(SELECT t_profiles_permissions.visualize FROM  t_profiles_permissions INNER JOIN c_modules ON t_profiles_permissions.id_module = c_modules.id_module where t_profiles_permissions.id_module = module AND t_profiles_permissions.id_submodule IS NULL AND t_profiles_permissions.id_profile = '.$idProfile.') as visualize_c'),
                    \DB::raw('(SELECT t_profiles_permissions.create FROM  t_profiles_permissions INNER JOIN c_modules ON t_profiles_permissions.id_module = c_modules.id_module where t_profiles_permissions.id_module = module AND t_profiles_permissions.id_submodule IS NULL AND t_profiles_permissions.id_profile = '.$idProfile.') as create_c'),
                    \DB::raw('(SELECT t_profiles_permissions.modify FROM  t_profiles_permissions INNER JOIN c_modules ON t_profiles_permissions.id_module = c_modules.id_module where t_profiles_permissions.id_module = module AND t_profiles_permissions.id_submodule IS NULL AND t_profiles_permissions.id_profile = '.$idProfile.') as modify_c'),
                    \DB::raw('(SELECT t_profiles_permissions.delete FROM  t_profiles_permissions INNER JOIN c_modules ON t_profiles_permissions.id_module = c_modules.id_module where t_profiles_permissions.id_module = module AND t_profiles_permissions.id_submodule IS NULL AND t_profiles_permissions.id_profile = '.$idProfile.') as delete_c')                         
               );
          $where = [];
          array_push( $where ,['c_modules.owner', '<>', 2]);
          if($restrict) array_push( $where ,['c_modules.owner', '<>', 1]);
          $query->where($where);
          $query->orderBy('pseud_module', $order[0]['dir']);
          // Paginate
          $totalRecords = $query->count('id_module');
          $paginate = $query->skip($page)->take($rows)->get();
          $data['data'] = $paginate;
          $data['recordsTotal'] = $totalRecords;
          $data['draw'] = intval($draw);
          $data['recordsFiltered'] = $totalRecords;
          return $data;
     }     
}