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
use App\User;

class AccountController extends Controller
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
        $idUser = Session::get('user')['id_user'];
        return view('account.account',[
            'idUser' => $idUser]
        );
    }
    /**
     * set info users 
     */
    public function setUser(Request $request){
        $requestUser = $request->input('user');
        $requestPerson = $request->input('person');
        DB::beginTransaction();
        $setPerson = PeopleModel::setPersonAccount($requestPerson);
        if ($setPerson == StatusConstants::SUCCESS) {
            $setUser = User::setUserAccount($requestUser);
            switch ($setUser) {
                case StatusConstants::SUCCESS:
                    DB::commit();
                    $user = User::getUserInfo($requestUser);
                    $request->session()->put('user', $user[0]);
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
        }elseif ($setPerson == StatusConstants::WARNING) {
            DB::rollBack();
            $status = StatusConstants::WARNING;
            $msg = 'El registro no fue encontrado, verifique nuevamente';
        }elseif ($setPerson == StatusConstants::DUPLICATE) {
            DB::rollBack();
            $status = StatusConstants::WARNING;
            $msg = 'El RFC ya es ocupado por otro usuario';
        }else{
            DB::rollBack();
            $status = StatusConstants::ERROR;
            $msg = 'Algo salio mal, intente nuevamente';
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return $data;
    }
}