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
use App\Models\Admin\CustomersModel;
use App\Models\Admin\CorporatesModel;
use Intervention\Image\Facades\Image;

class CustomersController extends Controller
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
    public function index(Request $request){
        $data['idOwnCustomer'] = $request->session()->get('customer')['id_customer'];
        return view('customers.customers', $data);
    }
    /**
     * Get customers to datatables
     */
    public function getCustomersDT(Request $request){
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $filterName = $request->input('filterName');
        $data = CustomersModel::getCustomersDT($page, $rows, $search, $draw, $order, $filterName);
        return response($data);
    }
    /**
     * Get customer info by idCustomer
     */
    public function getCustomer(Request $request, $idCustomer){
        $data = CustomersModel::getCustomer($idCustomer);
        return response($data);
    }
    /**
     * Set info customer 
     */
    public function setCustomer(Request $request){
        $tradename = $request->input('sTradename');
        $trademark = $request->input('sTrademark');
        $setCustomer = CustomersModel::setCustomer($tradename, $trademark);
        switch ($setCustomer) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                $msg = 'Registro actualizado';
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
     * Update info customer 
     */
    public function updateCustomer(Request $request){
        $idCustomer = $request->input('idCustomer');
        $tradename = $request->input('uTradename');
        $trademark = $request->input('uTrademark');
        $updateCustomer = CustomersModel::updateCustomer($idCustomer, $tradename, $trademark);
        switch ($updateCustomer) {
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
     * Delete customer
     */
    public function deleteCustomer(Request $request){
        $idCustomer = $request->input('idCustomer');
        DB::beginTransaction();
        $corporates = CorporatesModel::deleteCorporatesTransaction($idCustomer);
        $model = CustomersModel::deleteCustomer($idCustomer);
        switch ($model) {
            case StatusConstants::SUCCESS:
                DB::commit();
                $status = StatusConstants::SUCCESS;
                $msg = 'El registro ha sido eliminado exitosamente!';
                break;
            case StatusConstants::WARNING:
                DB::rollBack();
                $status = StatusConstants::WARNING;
                $msg = 'Imposible eliminar, otros elementos dependen de este.';
                break;
            case StatusConstants::ERROR:
                DB::rollBack();
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
    /************************ LOGOS ******************************/
    /**
     * Set logo on customer
     */
    public function setCustomerLogo(Request $request){
        $idCustomer = $request->input('id');
        $imgLogo = $request->file("imgLogo");

        $customer = CustomersModel::getCustomer($idCustomer);
        $save = StatusConstants::ERROR;

        // store logo
        if( $imgLogo != null || !empty( $imgLogo ) ) {
            if( $customer[0]['logo'] != 'default.png' && file_exists(public_path().'/assets/img/customers/'.$customer[0]['logo']))
                unlink(public_path().'/assets/img/customers/'.$customer[0]['logo']);

            $rnd = md5(microtime());
            $name = "logo_".$rnd.".".$imgLogo->getClientOriginalExtension();

            if($imgLogo->move(public_path().'/assets/img/customers/', $name))
            {
                $save = CustomersModel::UpdateLogo($idCustomer, $name);
            }
        }

        switch ($save) {
            case StatusConstants::SUCCESS:
                if( $idCustomer == $request->session()->get('customer')['id_customer'] )
                {
                    $customer = CustomersModel::getCustomer($idCustomer);
                    $request->session()->put('customer', $customer[0]);
                }
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
        return $data;
    }
    /**
     * Set smal logo on customer
     */
    public function setCustomerLogoSM(Request $request){
        $idCustomer = $request->input('id');
        $customer = CustomersModel::getCustomer($idCustomer);
        // store logo
        if( $request->file('logo') != null || !empty( $request->file('logo') ) ) {
            if( $customer[0]['sm_logo'] != 'default.png' && file_exists(public_path().'/assets/img/customers/'.$customer[0]['sm_logo']))  unlink(public_path().'/assets/img/customers/'.$customer[0]['sm_logo']);
            $rnd = md5(microtime());
            $name = "sm_".$rnd.".".$request->file('logo')->getClientOriginalExtension();
            if($request->file('logo')->move(public_path().'/assets/img/customers/', $name))        
            {
                ini_set('memory_limit','256M');
                $image = Image::make(public_path().'/assets/img/customers/'.$name);
                $image->fit($request->input('imageH'), $request->input('imageW')); 
                $image->crop($request->input('Width'), $request->input('Height'), $request->input('Left'), $request->input('Top'));
                $image->fit(290);
                $image->save(); 
                $save = CustomersModel::UpdateLogoSM($idCustomer, $name);
            }
        }
        switch ($save) {
            case StatusConstants::SUCCESS:
                if( $idCustomer == $request->session()->get('customer')['id_customer'] )
                {
                    $customer = CustomersModel::getCustomer($idCustomer);
                    $request->session()->put('customer', $customer[0]);
                }
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
        return $data;
    }
    /**
     * Set large logo on customer
     */
    public function setCustomerLogoLG(Request $request){
        $idCustomer = $request->input('id');
        $customer = CustomersModel::getCustomer($idCustomer);
        // store logo
        if( $request->file('logo') != null || !empty( $request->file('logo') ) ) {
            if( $customer[0]['lg_logo'] != 'default.png' && file_exists(public_path().'/assets/img/customers/'.$customer[0]['lg_logo']))unlink(public_path().'/assets/img/customers/'.$customer[0]['lg_logo']);
            $rnd = md5(microtime());
            $name = "lg_".$rnd.".".$request->file('logo')->getClientOriginalExtension();
            if($request->file('logo')->move(public_path().'/assets/img/customers/', $name))        
            {
                ini_set('memory_limit','256M');
                $image = Image::make(public_path().'/assets/img/customers/'.$name);
                $image->fit($request->input('imageH'), $request->input('imageW')); 
                $image->crop($request->input('Width'), $request->input('Height'), $request->input('Left'), $request->input('Top'));
                $image->fit(900, 290);
                $image->save();
                $save = CustomersModel::UpdateLogoLG($idCustomer, $name);
            }
        }
        switch ($save) {
            case StatusConstants::SUCCESS:
                if( $idCustomer == $request->session()->get('customer')['id_customer'] )
                {
                    $customer = CustomersModel::getCustomer($idCustomer);
                    $request->session()->put('customer', $customer[0]);
                }
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
        return $data;
    }
}
