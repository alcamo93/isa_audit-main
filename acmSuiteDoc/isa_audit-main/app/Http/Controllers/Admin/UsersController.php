<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Classes\StatusConstants;
use App\Classes\Source;
use App\Models\Admin\CustomersModel;
use App\Models\Admin\CorporatesModel;
use App\Models\Catalogues\StatusModel;
use App\Models\Admin\ProfilesModel;
use App\Models\Admin\PeopleModel;
use App\Models\Admin\ContractsModel;
use Intervention\Image\Facades\Image;
use App\Notifications\AccountNotifications;
use App\User;

class UsersController extends Controller
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
    public function index(){//dd(Session::all());
        $groupStatus = 1; // group status basic
        $status = StatusModel::getStatusByGroup($groupStatus);
        switch (Session::get('profile')['id_profile_type']){
            case 1: case 2:
                $customers = CustomersModel::getAllCustomers();
                $profiles = ProfilesModel::getAllProfiles(1);
                $corporates = null;
            break;
            
            case 3:
                $customers = CustomersModel::getCustomer(Session::get('customer')['id_customer']);
                $corporates = CorporatesModel::getAllCorporates(Session::get('customer')['id_customer']);
                $profiles = ProfilesModel::getAllProfiles(1, Session::get('customer')['id_customer']);
            break;
            
            case 4: case 5:
                $customers = CustomersModel::getCustomer(Session::get('customer')['id_customer']);
                $corporates = CorporatesModel::getCorporate(Session::get('corporate')['id_corporate']);
                $profiles = ProfilesModel::getAllProfiles( 1, Session::get('customer')['id_customer'], Session::get('corporate')['id_corporate'] );
            break;
        }

        return view('users.users',[
            'customers' => $customers,
            'corporates' => $corporates,
            'status' => $status,
            'profiles' => $profiles]
        );
    }
    /**
     * Get users to datatables
     */
    public function getUsersDT(Request $request){
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $fIdCustomer = $request->input('fIdCustomer');
        $fIdCorporate = $request->input('fIdCorporate');
        $data = User::getUsersDT($page, $rows, $search, $draw, $order, $fIdCustomer, $fIdCorporate);
        return response($data);
    }
    /**
     * Get users info by idUser
     */
    public function getUser(Request $request, $idUser){
        $data['user'] = User::getUserInfo($idUser);
        $idPerson = $data['user'][0]['id_person'];
        $data['person'] = PeopleModel::getPerson($idPerson);
        return response($data);
    }
    /**
     * Set info users 
     */
    public function setUser(Request $request){
        $requestUser = $request->input('user');
        $profile = ProfilesModel::getProfile($requestUser['idProfile']);
        if($profile[0]['profile_level'] > 2)
        {
            $contract = ContractsModel::GetContractByCorporate($requestUser['idCorporate']);
            if(count($contract) > 0)
            {
                $userProfileCount = User::getUsersCountByProfileID($requestUser['idProfile']);
                switch ($profile[0]['profile_level']) {
                    case 3:
                        if($userProfileCount < $contract[0]['usr_global']) $valid = true;
                        else $valid = false;
                    break;
                    case 4:
                        if($userProfileCount < $contract[0]['usr_corporate']) $valid = true;
                        else $valid = false;
                    break;
                    case 5:
                        if($userProfileCount < $contract[0]['usr_operative']) $valid = true;
                        else $valid = false;
                    break;
                }
                if(!$valid){
                    $status = StatusConstants::ERROR;
                    $msg = 'Ya se ha alcanzado el límite de usuarios para el perfil '.$profile[0]['profile_name'];
                }
            }
            else
            {
                $valid = false;
                $status = StatusConstants::ERROR;
                $msg = 'Error, no existe un contrato para esta planta';
            }
        }
        else $valid = true;
        if($valid)
        {
            $requestPerson = $request->input('person');
            DB::beginTransaction();
            $setPerson = PeopleModel::setPerson($requestPerson);
            if ($setPerson['status'] == StatusConstants::SUCCESS) {
                $source = new Source();
                $password = $source->randomPassword();
                $setUser = User::setUser($setPerson['idPerson'], $password, $requestUser);
                if ($setUser['status'] == StatusConstants::SUCCESS){
                    // Notification
                    $dataMail['info'] = [
                        'title' => 'Bienvenido',
                        'body' => 'El usuario <b>Administrador</b> cambio tu contraseña',
                        'description' => 'Su contraseña ha sido enviada a su correo electronico principal',
                        'link' => '/'
                    ];
                    $dataMail['mail'] = [
                        'user' => $requestUser['email'],
                        'name' => $requestPerson['name'].' '.$requestPerson['secondName'],
                        'password' => $password
                    ];
                    $templatePath = 'mails.welcome';
                    $notify = User::find($setUser['idUser']);
                    $notify->notify(new AccountNotifications($dataMail, $templatePath, 'Registro'));
                    DB::commit();
                    $status = StatusConstants::SUCCESS;
                    $msg = 'Registro actualizado';
                }elseif ($setUser['status'] == StatusConstants::DUPLICATE) {
                    DB::rollBack();
                    $status = StatusConstants::WARNING;
                    $msg = 'El correo ya esta en uso';
                }elseif ($setUser['status'] == StatusConstants::ERROR) {
                    DB::rollBack();
                    $status = StatusConstants::ERROR;
                    $msg = 'Algo salio mal, intente nuevamente';
                }
            }
            else{
                DB::rollBack();
                $status = StatusConstants::ERROR;
                $msg = 'Algo salio mal, intente nuevamente';
            }
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return $data;
    }
    /**
     * Update info users 
     */
    public function updateUser(Request $request){
        $requestUser = $request->input('user');
        $requestPerson = $request->input('person');
        DB::beginTransaction();
        $updatePerson = PeopleModel::updatePerson($requestPerson);
        if ($updatePerson == StatusConstants::SUCCESS) {
            $updateUser = User::updateUser($requestUser);
            switch ($updateUser) {
                case StatusConstants::SUCCESS:
                    DB::commit();
                    $status = StatusConstants::SUCCESS;
                    $msg = 'Registro actualizado';
                    break;
                case StatusConstants::DUPLICATE:
                    DB::rollBack();
                    $status = StatusConstants::WARNING;
                    $msg = 'El correo ya esta en uso';
                    break;
                case StatusConstants::WARNING:
                    DB::rollBack();
                    $status = StatusConstants::WARNING;
                    $msg = 'El registro no fue encontrado, verifique nuevamente';
                    break;
                case StatusConstants::ERROR:
                    DB::rollBack();
                    $status = StatusConstants::ERROR;
                    $msg = 'Algo salio mal, intente nuevamente';
                    break;
                default:
                    # code... 
                    break;
            }
        }elseif ($updatePerson == StatusConstants::WARNING) {
            DB::rollBack();
            $status = StatusConstants::WARNING;
            $msg = 'El registro no fue encontrado, verifique nuevamente';
        }else{
            DB::rollBack();
            $status = StatusConstants::ERROR;
            $msg = 'Algo salio mal, intente nuevamente';
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return $data;
    }
    /**
     * Delete users
     */
    public function deleteUser(Request $request){
        $idUser = $request->input('idUser');
        $person = PeopleModel::deletePerson($idUser); // Restrict Cascade  
        switch ($person) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                $msg = 'El registro ha sido eliminado exitosamente!';
                break;
            case StatusConstants::WARNING:
                $status = StatusConstants::WARNING;
                $msg = 'Imposible eliminar, otros elementos dependen de este.';
                break;
            case StatusConstants::ERROR:
                $status = StatusConstants::ERROR;
                $msg = 'Imposible eliminar, este cliente.';
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
     * Change password
     */
    public function setPassword(Request $request){
        $requestData = $request->all();
        $setPassword = User::setPassword($requestData);
        if ($setPassword['status'] == StatusConstants::SUCCESS){
            // Notification
            $info = User::GetUserInfo($setPassword['model']->id_user);
            $dataMail['info'] = [
                'title' => 'Cambio de contraseña Manual',
                'body' => 'El usuario <b>Administrador</b> cambio tu contraseña',
                'description' => 'Su contraseña ha sido enviada a su correo electronico principal',
                'link' => '/'
            ];
            $dataMail['mail'] = [
                'user' => $info[0]['email'],
                'name' => $info[0]['complete_name'],
                'password' => $requestData['newPassword']
            ];
            $templatePath = 'mails.password';
            $notify = User::find($info[0]['id_user']);
            $notify->notify(new AccountNotifications($dataMail, $templatePath, 'Cambio de contraseña'));
            DB::commit();
            $status = StatusConstants::SUCCESS;
            $msg = 'La contraseña se establecio exitosamente!';
        }elseif ($setPassword['status'] == StatusConstants::WARNING) {
                $status = StatusConstants::WARNING;
                $msg = 'No se encontro el registro';
        }elseif ($setPassword['status'] == StatusConstants::ERROR) {
            $status = StatusConstants::ERROR;
            $msg = 'Algo salio mas, intente nuevamente.';
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return $data;
    }
    /**
     * Change user profile image
     */
    public function setUserImg(Request $request){
        $idUser = $request->input('id');
        $user = User::getUserInfo($idUser);
        // store logo
        if( $request->file('logo') != null || !empty( $request->file('logo') ) ) {
            if($user[0]['picture'] != 'default.png' && file_exists(public_path().'/assets/img/faces/'.$user[0]['picture']))  unlink(public_path().'/assets/img/faces/'.$user[0]['picture']);
            $rnd = md5(microtime());
            $name = "profile_".$rnd.".".$request->file('logo')->getClientOriginalExtension();
            if($request->file('logo')->move(public_path().'/assets/img/faces/', $name))        
            {
                ini_set('memory_limit','256M');
                $image = Image::make(public_path().'/assets/img/faces/'.$name);
                $image->fit($request->input('imageH'), $request->input('imageW')); 
                $image->crop($request->input('Width'), $request->input('Height'), $request->input('Left'), $request->input('Top'));
                $image->fit(290);
                $image->save(); 
                $save = User::UpdateProfileImage($user, $name);
            }
        }
        switch ($save) {
            case StatusConstants::SUCCESS: 
                $user = User::getUserInfo($idUser);
                $request->session()->put('user', $user[0]);
                $status = StatusConstants::SUCCESS;
                $msg = 'La imagen se establecio exitosamente!';
                break;
            case StatusConstants::WARNING:
                $status = StatusConstants::WARNING;
                $msg = 'Algo salió mal, intentelo nuevamente';
                break;
            case StatusConstants::ERROR:
                $status = StatusConstants::ERROR;
                $msg = 'Imposible subir logo.';
                break;
            default:
                # code... 
                break;
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        $data['url'] = $name;
        return $data;
    }
    /**
     * Get users by corporation for selection
     */
    public function getUsers(Request $request)
    { 
        $idCorporate = $request->input('idCorporate');
        $data = User::getUsers($idCorporate);
        return response($data);
    }
}