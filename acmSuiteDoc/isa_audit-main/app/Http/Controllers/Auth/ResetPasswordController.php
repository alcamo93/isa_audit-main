<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\Classes\StatusConstants;
use App\Classes\Mailing;
use App\Notifications\AccountNotifications;
use App\User;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
    /**
     * Send security token 
     */
    public function resetToken(Request $request){
        $email = $request->input('email');
        $user = User::getUserInfoByEmail($email);
        $data = array();
        if(is_array($user) && isset($user[0]['id_status'])) {
            if($user[0]['id_status'] == StatusConstants::ACTIVE) {
                $token = bin2hex(random_bytes(16));
                User::setResetToken($user[0]['id_user'], $token);
                // Notification
                $dataMail['info'] = [
                    'title' => 'Recuperación de acceso',
                    'body' => 'Revise su correo electrónico para recuperar contraseña',
                    'description' => 'Siga los pasos en el orreo electrónico para recuperar su contraseña',
                    'link' => '/'
                ];
                $dataMail['mail'] = [
                    'user' => $user[0]['email'],
                    'resetPath' => asset('login/reset/'.$token)
                ];
                $templatePath = 'mails.reset';
                $notify = User::find($user[0]['id_user']);
                $notify->notify(new AccountNotifications($dataMail, $templatePath, 'Recuperación de acceso'));
                $message = 'Se ha enviado a su correo las instrucciones';
                $status = StatusConstants::SUCCESS;
            }else{
                $message = 'Usuario inactivo, contacte al administrador del sistema.';
                $status = StatusConstants::WARNING;
            }
        }else{
            $message = 'El correo ingresado no tiene relación a una cuenta.';
            $status = StatusConstants::ERROR;
        }
        $data['msg'] = $message;
        $data['status'] = $status;
        return response($data);
    }
    /**
     * Change password view
     */
    public function index(Request $request, $token){
        $user = User::getUserInfoByToken($token);
        if(!is_array($user) || !isset($user[0])){
            return view('errors.404');         
        }
        return view('auth.reset', ['resetToken' => $token]);
    }
    /**
     * Set the new password
     */
    public function setReset(Request $request){
        $newPassword = $request->input('newPassword');
        $repitPassword = $request->input('repitPassword');
        $token = $request->input('resetToken');
        if(!isset($token) || !isset($newPassword) || !isset($repitPassword)){
            $data['msg'] = "Ambas contraseñas son necesarias"; 
            $data['status'] = 'warning'; 
            return response($data);           
        }
        if($newPassword != $repitPassword){
            $data['msg'] = "Las contraseñas no coinciden";
            $data['status'] = 'error';
            return response($data);
        }
        //Check if user exists with token
        $user = User::getUserInfoByToken($token);
        $idUser = $user[0]['id_user'];
        if(!is_array($user) || !isset($user[0])){
            $data['msg'] = "Problemas con reestablecer contraseña, contacte a su administrador";
            $data['status'] = 'error';
        }else{
            User::UpdatePassword($newPassword, $idUser);
            $data['msg'] = "Contraseña reestablecida, ingrese nuevamente.";
            $data['status'] = 'success';            
        }                
        return response($data);
    }
}
