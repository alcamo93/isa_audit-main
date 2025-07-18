<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Classes\StatusConstants;
use App\Models\Admin\ProfilesModel;
use App\Models\Catalogues\StatusModel;
use App\Models\Catalogues\ProfileTypeModel;
use App\Models\Admin\CustomersModel;
use App\Models\Admin\CorporatesModel;
use App\Models\Admin\ProfilesPermissionModel;

class ProfilesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Main view in custumers module
     */
    public function index(){
        $groupStatus = 1; // group status basic
        $status = StatusModel::getStatusByGroup($groupStatus);

        switch (Session::get('profile')['id_profile_type']){
            case 1: case 2:
                $profilesType = ProfileTypeModel::getProfilesTypes();
                $customers = CustomersModel::getAllCustomers();
                $corporates = null;
            break;

            case 3:
                $profilesType = ProfileTypeModel::getProfilesTypes(3);// profile_level 3 customer admin
                $customers = CustomersModel::getCustomer(Session::get('customer')['id_customer']);
                $corporates = CorporatesModel::getAllCorporates(Session::get('customer')['id_customer']);
            break;

            case 4: case 5:
                $profilesType = ProfileTypeModel::getProfilesTypes(4);// profile_level 4 corporative
                $customers = CustomersModel::getCustomer(Session::get('customer')['id_customer']);
                $corporates = CorporatesModel::getCorporate(Session::get('corporate')['id_corporate']);
            break;
        }
        return view('profiles.profiles',[
            'status' => $status,
            'profilesType' => $profilesType,
            'customers' => $customers,
            'corporates' => $corporates,
            ]
        );
    }
    /**
     * Get licenses to datatables
     */
    public function getProfilesDT(Request $request){
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $fIdCustomer = $request->input('fIdCustomer');
        $fIdCorporate = $request->input('fIdCorporate');
        $data = ProfilesModel::getProfilesDT($page, $rows, $search, $draw, $order, $fIdCustomer, $fIdCorporate, Session::get('profile')['id_profile_type']);
        return response($data);
    }
    /**
     * Get license info by idProfile
     */
    public function getProfile(Request $request, $idProfile){
        $data = ProfilesModel::getProfile($idProfile);
        return response($data);
    }
    /**
     * Set info license
     */
    public function setProfile(Request $request){
        $data = $request->all();
        DB::beginTransaction();
        $setProfile = ProfilesModel::setProfile($data);
        switch ($setProfile['status']) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                break;
            case StatusConstants::ERROR:
                DB::rollBack();
                $status = StatusConstants::WARNING;
                $msg = 'Algo salio mal, intente nuevamente';
                break;
            default:
                # code...
                break;
        }
        if($status == StatusConstants::SUCCESS){   //Set permissions by profile ### NEED TO ADD NEW MODULE PERMISSIONS
            if($data['typeProfile']=='1'){
                $permission = ProfilesPermissionModel::setPermission(1, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(1, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(1, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(1, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(2, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(2, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(2, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(2, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(3, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(3, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(3, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(3, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(4, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(4, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(4, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(4, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(5, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(5, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(5, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(5, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(6, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(6, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(6, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(6, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(7, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(7, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(7, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(7, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(9, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(9, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(9, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(9, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(10, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(10, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(10, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(10, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(11, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(11, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(11, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(11, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(12, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(12, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(12, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(12, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(13, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(13, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(13, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(13, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(14, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(14, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(14, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(14, null, $setProfile['data'], 'delete', 1);                    
            }elseif($data['typeProfile']=='2'){
                $permission = ProfilesPermissionModel::setPermission(1, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(1, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(1, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(1, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(2, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(2, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(2, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(2, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(3, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(3, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(3, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(3, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(4, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(4, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(4, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(4, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(5, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(5, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(5, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(5, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(6, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(6, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(6, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(6, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(7, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(7, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(7, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(7, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(9, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(9, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(9, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(9, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(10, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(10, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(10, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(10, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(11, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(11, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(11, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(11, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(12, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(12, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(12, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(12, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(13, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(13, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(13, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(13, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(14, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(14, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(14, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(14, null, $setProfile['data'], 'delete', 1);                  
            }elseif($data['typeProfile']=='3'){
                $permission = ProfilesPermissionModel::setPermission(1, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(1, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(1, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(1, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(5, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(5, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(5, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(5, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(6, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(6, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(6, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(6, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(7, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(7, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(7, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(7, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(9, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(9, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(9, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(9, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(10, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(10, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(10, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(10, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(11, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(11, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(11, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(11, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(12, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(12, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(12, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(12, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(13, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(13, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(13, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(13, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(14, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(14, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(14, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(14, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(15, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(15, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(15, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(15, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(16, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(16, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(16, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(16, null, $setProfile['data'], 'delete', 1);
            }elseif($data['typeProfile']=='4'){
                $permission = ProfilesPermissionModel::setPermission(1, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(1, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(1, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(1, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(5, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(5, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(5, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(5, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(6, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(6, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(6, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(6, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(7, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(7, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(7, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(7, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(9, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(9, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(9, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(9, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(10, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(10, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(10, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(10, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(11, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(11, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(11, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(11, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(12, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(12, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(12, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(12, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(13, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(13, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(13, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(13, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(14, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(14, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(14, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(14, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(15, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(15, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(15, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(15, null, $setProfile['data'], 'delete', 1);
                $permission = ProfilesPermissionModel::setPermission(16, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(16, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(16, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(16, null, $setProfile['data'], 'delete', 1);
            }elseif($data['typeProfile']=='5'){
                $permission = ProfilesPermissionModel::setPermission(9, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(9, null, $setProfile['data'], 'create', 0);
                $permission = ProfilesPermissionModel::setPermission(9, null, $setProfile['data'], 'modify', 0);
                $permission = ProfilesPermissionModel::setPermission(9, null, $setProfile['data'], 'delete', 0);
                $permission = ProfilesPermissionModel::setPermission(10, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(10, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(10, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(11, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(11, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(11, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(11, null, $setProfile['data'], 'delete', 0);
                $permission = ProfilesPermissionModel::setPermission(12, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(12, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(12, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(12, null, $setProfile['data'], 'delete', 0);
                $permission = ProfilesPermissionModel::setPermission(13, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(13, null, $setProfile['data'], 'create', 0);
                $permission = ProfilesPermissionModel::setPermission(13, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(13, null, $setProfile['data'], 'delete', 0);
                $permission = ProfilesPermissionModel::setPermission(14, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(14, null, $setProfile['data'], 'create', 0);
                $permission = ProfilesPermissionModel::setPermission(14, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(14, null, $setProfile['data'], 'delete', 0);
                $permission = ProfilesPermissionModel::setPermission(15, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(15, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(15, null, $setProfile['data'], 'modify', 1);
                $permission = ProfilesPermissionModel::setPermission(16, null, $setProfile['data'], 'visualize', 1);
                $permission = ProfilesPermissionModel::setPermission(16, null, $setProfile['data'], 'create', 1);
                $permission = ProfilesPermissionModel::setPermission(16, null, $setProfile['data'], 'modify', 1);
            }
            switch ($permission) {
                case StatusConstants::SUCCESS:
                    DB::commit();
                    $status =  StatusConstants::SUCCESS;
                    $msg = 'Registro actualizado';
                    break;
                case StatusConstants::ERROR:
                    DB::rollBack();
                    $response['status'] = StatusConstants::ERROR;
                    $response['msg'] = 'Algo salio mal';
                    break;
                default:
                    # code...
                    break;
            }
        }


        $data['status'] = $status;
        $data['msg'] = $msg;
        return $data;
    }
    /**
     * Update info license
     */
    public function updateProfile(Request $request){
        $data = $request->all();
        $updateProfile = ProfilesModel::updateProfile($data);
        switch ($updateProfile) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                $msg = 'Registro actualizado';
                break;
            case StatusConstants::WARNING:
                $status = StatusConstants::WARNING;
                $msg = 'Algo salio mal, intente nuevamente';
                break;
            case StatusConstants::ERROR:
                $status = StatusConstants::ERROR;
                $msg = 'El registro no fue encontrado, verifique';
                break;
            default:
                # code...
                break;
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return $data;
    }
    /**
     * Delete license
     */
    public function deleteProfile(Request $request){
        $idProfile = $request->input('idProfile');
        $model = ProfilesModel::deleteProfile($idProfile);
        switch ($model) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                $msg = 'El registro ha sido eliminado exitosamente!';
                break;
            case StatusConstants::WARNING:
                $status = StatusConstants::WARNING;
                $msg = 'El registro está en uso, no se puede eliminar';
                break;
            case StatusConstants::ERROR:
                $status = StatusConstants::WARNING;
                $msg = 'Algo salio mal, intente nuevamente';
                break;
            default:
                # code...
                break;
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return $data;
    }
    /**
     * Get profiles by idCorporate
     */
    public function getProfilesFilter(Request $request, $idCorporate){
        $data = ProfilesModel::getAllProfilesFilter($idCorporate);
        return response($data);
    }
}
