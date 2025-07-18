<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\ModulesModel;
use App\Models\Admin\SubModulesModel;
use App\Models\Admin\ProfilesModel;
use App\Models\Admin\CustomersModel;
use App\Models\Admin\CorporatesModel;
use App\Models\Admin\ProfilesPermissionModel;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\Session;

class PermissionController extends Controller
{
    public function __construct(){
		$this->middleware('auth');
	}

    /**
     * Main page
     */
    public function index(Request $request)
    {//dd(Session::all());
        switch (Session::get('profile')['id_profile_type'])
        {
            case 1: case 2:
                $customers = CustomersModel::getAllCustomers();
                $profiles = ProfilesModel::getAllProfiles();
                $corporates = null;
            break;

            case 3:
                $customers = CustomersModel::getCustomer(Session::get('customer')['id_customer']);
                $corporates = CorporatesModel::getAllCorporates(Session::get('customer')['id_customer']);
                $profiles = ProfilesModel::GetAllProfilesLower(Session::get('profile')['id_profile_type'], Session::get('customer')['id_customer'], Session::get('corporate')['id_corporate'] );
            break;

            case 4: case 5:
                $customers = CustomersModel::getCustomer(Session::get('customer')['id_customer']);
                $corporates = CorporatesModel::getCorporate(Session::get('corporate')['id_corporate']);
                $profiles = ProfilesModel::GetAllProfilesLower(Session::get('profile')['id_profile_type'], Session::get('customer')['id_customer'], Session::get('corporate')['id_corporate'] );
            break;
        }
        $arrays = ['profiles' => $profiles, 'customers' => $customers, 'corporates' => $corporates];
        return view('permissions.permissions', $arrays);
    }

    /**
     * Return roles
     */
    public function getPermissionsDT(Request $request) {
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $idProfile = $request->input('idProfile');
        if ($idProfile == 0) {
            $data['data'] = array();
            $data['recordsTotal'] = 0;
            $data['draw'] = intval($draw);
            $data['recordsFiltered'] = 0;
        }
        else {
            $restrict = TRUE;
            if($request->session()->get('isOwner', 0) == 1){
                $restrict = FALSE;
            }
            $data = ModulesModel::GetPermissions($page, $rows, $search, $draw, $order, $idProfile, $restrict);
        }
        return response($data);
    }
    /*
    Set permission
    */
    public function setPermission(Request $request)
    {
        $idModule = $request->input('idModule');
        $idSubmodule = $request->input('idSubmodule');
        $idProfile = $request->input('idProfile');
        $type = $request->input('type');
        $status = $request->input('status');

        /*Cannot drop read permission over permissions module to itself*/
        $logedIdProfile = Session::get('user')['id_profile'];
        $idSubmodulePermission = 6;
        if($logedIdProfile == $idProfile && $idSubmodule == $idSubmodulePermission && $type == 'visualize' && $status == 0){
            $response['status'] = StatusConstants::WARNING;
            $response['msg'] = 'No puedes quitarte permisos para este modulo';
            return response($response);
        }
        $haspermission = ProfilesPermissionModel::getModifyPermission(6, Session::get('profile')['id_profile']);
        if($haspermission !=NULL){
            $permission = ProfilesPermissionModel::setPermission($idModule, $idSubmodule, $idProfile, $type, $status);
        switch ($permission) {
                case StatusConstants::SUCCESS:
                    $response['status'] = StatusConstants::SUCCESS;
                    $response['msg'] = ($status == 0 ) ? "Permiso Removido": 'Permiso Asignado';
                break;
            case StatusConstants::ERROR:
                $response['status'] = StatusConstants::ERROR;
                $response['msg'] = 'Algo salio mal';
                break;
            default:
                # code...
                break;
                }
            }else{
                $response['status'] = StatusConstants::ERROR;
                $response['msg'] = 'No tienes permisos';
            }
        return response($response);
    }
    /**
     * Get permissions to actions in module
     */
    public function getPermissions(Request $request, $path){
        $idProfile = Session::get('user')['id_profile'];
        $idSubmodule = $path;
        $permission = ProfilesPermissionModel::getPermission($idSubmodule, $idProfile);
    }
     /**
     * Return roles
     */
    public function getPermissionsSubmoduleDT(Request $request)
    {
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $idProfile = $request->input('idProfile');
        $idModule = $request->input('idModule');
        if ($idModule == '') {
            $data['data'] = array();
            $data['recordsTotal'] = 0;
            $data['draw'] = intval($draw);
            $data['recordsFiltered'] = 0;
        }
        else{
            $restrict = TRUE;
            if($request->session()->get('isOwner', 0) == 1){
                $restrict = FALSE;
            }
            $data = subModulesModel::GetPermissions($page, $rows, $search, $draw, $order, $idProfile, $idModule, $restrict);
        }
        return response($data);
    }
}
