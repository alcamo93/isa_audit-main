<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Classes\StatusConstants;
use App\User;
use App\Models\V2\Admin\User as UserV2;
use App\Models\Admin\ModulesModel;
use App\Models\Admin\SubModulesModel;
use App\Models\Admin\CustomersModel;
use App\Models\Admin\CorporatesModel;
use App\Models\Admin\ProfilesPermissionModel;
use App\Models\Admin\ProfilesModel;
use App\Models\Admin\ContractsModel;
use App\Models\V2\Admin\Contract;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    use AuthenticatesUsers;
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest')->except('logout');
    }
    /**
     * Get login view
     */
    public function index() {
        return view('auth.login');
    }
    /**
     * Handle login request
     */
    public function login(Request $request) {
        $email = $request->input('email');
        $password = $request->input('password');
        $remember = $request->input('remember');
        if (Auth::attempt(['email' => $email, 'password' => $password], $remember)) {    
            $userInfo = User::GetUserInfo(Auth::id());
            $profileInfo = ProfilesModel::GetProfile($userInfo[0]['id_profile']);
            $contractInfo = Contract::where('id_corporate', $userInfo[0]['id_corporate'])
                ->where('id_status', 1)->get()->firstWhere('in_range_date', true);
            if ($profileInfo[0]['id_customer'] != 1) {
                if ( !is_null($contractInfo) ) {
                    if ($contractInfo->id_status != StatusConstants::ACTIVE) {
                        $data['msg'] = 'El contrato expiró'; 
                        $data['status'] = StatusConstants::ERROR;
                        Auth::logout();
                        return response($data);
                    } else {
                        $request->session()->put('contract', $contractInfo[0]);
                    }
                } else {
                    $data['msg'] = 'No existe un contrato activo para este usuario'; 
                    $data['status'] = StatusConstants::ERROR;
                    Auth::logout();
                    return response($data);
                }
            }
            $customerInfo = CustomersModel::getCustomer($userInfo[0]['id_customer']);
            $corporateInfo = CorporatesModel::getCorporate($userInfo[0]['id_corporate']);
            if(isset($userInfo[0]) && $userInfo[0]['id_status'] == StatusConstants::ACTIVE) { 
                // Getting the modules and submodules allowed for profile
                $modulesAllowed = ProfilesPermissionModel::GetViewPermissionByProfile($userInfo[0]['id_profile']);
                // Getting module information
                $activeModules = ModulesModel::GetModulesByIds($modulesAllowed, 'login');
                
                foreach($activeModules as $index => $module) {
                    $activeModules[$index]['submodules'] = [];
                    $activeModules[$index]['collapse'] = false;
                    $activeModules[$index]['active'] = ($module['path'] == 'dashboard') ? true : false;
                    $activeModules[$index]['submodules'] = SubModulesModel::GetGroupedSubModules($userInfo[0]['id_profile'], $module['id_module']);
                    foreach ($activeModules[$index]['submodules'] as $key => $s) {
                        if($module['has_submodule']) {
                            $activeModules[$index]['submodules'][$key]['active'] = false;
                        }
                    }
                }

                $userV2 = UserV2::find($userInfo[0]['id_user']);
                $image = !is_null($userV2->image) ? $userV2->image->full_path : url('/assets/img/faces/default.png');
                $request->session()->put('image', $image);
                
                $request->session()->put('menu', $activeModules);
                $request->session()->put('user',$userInfo[0]);
                $request->session()->put('customer', $customerInfo[0]);
                $request->session()->put('corporate', $corporateInfo[0]);
                $request->session()->put('profile', $profileInfo[0]);
                $request->session()->put('isOwner', $profileInfo[0]['owner']);
                $data['status'] = StatusConstants::SUCCESS;
                return response($data);
            } else {
                Auth::logout();
                $data['msg'] = 'Usuario inactivo, contacte al administrador'; 
                $data['status'] = StatusConstants::WARNING;
            }        
        }else {
            $data['msg'] = 'Usuario o contraseña incorrectos, por favor verifique su información'; 
            $data['status'] = StatusConstants::ERROR;
            return response($data);
        }
    }
    /**
     * Handle logout request
     */
    public function logout() {
        Auth::logout();
        return redirect('/');
    }
}
