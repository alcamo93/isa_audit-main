<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;

class ProfilesPermissionModel extends Model
{
    protected $table = 't_profiles_permissions';
    protected $primaryKey = 'id_profile_permission';

    /*
    Save permission
    */
    public function scopeSetPermission($query, $idModule, $idSubmodule, $idProfile, $type, $status){
        try{
            if($idSubmodule) $model = ProfilesPermissionModel::where([
                    ['id_module', '=', $idModule], 
                    ['id_submodule', '=', $idSubmodule], 
                    ['id_profile', '=', $idProfile]
                ])->firstOrFail();
            else $model = ProfilesPermissionModel::where([
                    ['id_module', '=', $idModule], 
                    ['id_profile', '=', $idProfile]
                ])
                ->whereNull('id_submodule')
                ->firstOrFail();
        }
        catch(ModelNotFoundException $ex){
            $model = new ProfilesPermissionModel;
            $model->id_module = $idModule;
            $model->id_submodule = $idSubmodule;
            $model->id_profile = $idProfile;               
        }
        switch ($type) {
            case 'visualize':
                $model->visualize = $status;
                break;
            case 'create':
                $model->create = $status;
                break;
            case 'modify':
                $model->modify = $status;
                break;
            case 'delete':
                $model->delete = $status;
                break;
            default:
                # code...
                break;
        }
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Get Permisssions
     */
    public function scopeGetPermission($query, $idmodule, $idProfile){
        $query->select('visualize', 'create', 'modify', 'delete')
            ->where([
                ['id_profile', '=', $idProfile], 
                ['id_module', '=', $idmodule]
            ]);
        $data = $query->get()->toArray();
        return $data;
    }

    public function scopeGetModifyPermission($query, $idmodule, $idProfile){
        $query->select('visualize', 'create','delete')
            ->where([
                ['id_profile', '=', $idProfile], 
                ['id_module', '=', $idmodule],
                ['modify', '=', 1]
            ]);
        $data = $query->get()->toArray();
        return $data;
    }
      /**
     * Get submodule permisssions
     */
    public function scopeGetSubmodulePermission($query, $idsubmodule, $idProfile){
        $query->select('visualize', 'create', 'modify', 'delete')
            ->where([
                ['id_profile', '=', $idProfile], 
                ['id_submodule', '=', $idsubmodule]
            ]);
        $data = $query->get()->toArray();
        return $data;
     }
     /**
     * Get view permisssions by profile // used for menu creation
     */
    public function scopeGetViewPermissionByProfile($query, $idProfile){
        $query->select('id_module', 'id_submodule')
            ->where([
                ['id_profile', '=', $idProfile],
                ['visualize', '=', 1]
            ]);
        $data = $query->get()->toArray();
        return $data;
     }
}