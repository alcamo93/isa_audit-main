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
use App\Models\Admin\CorporatesModel;
use App\Models\Catalogues\StatusModel;
use App\Models\Catalogues\IndustriesModel;
use App\Models\Catalogues\CountriesModel;
use App\Models\Admin\AddressesModel;
use App\Models\Admin\ContactsModel;

class CorporatesController extends Controller
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
     * Main view in corporate module
     */
    public function index(Request $request, $idCustomer){
        $groupStatus = 1; // group status basic
        $countries = CountriesModel::getCountries();
        $status = StatusModel::getStatusByGroup($groupStatus);
        $industries = IndustriesModel::getIndustries();
        return view('customers.corporates.corporates', [
            'idCustomer' => $idCustomer, 
            'countries' => $countries,
            'status' => $status,
            'industries' => $industries]
        );
    }
    /**
     * Get corporate info by idCorporate
     */
    public function getCorporate(Request $request, $idCorporate){
        $data = CorporatesModel::getCorporate($idCorporate);
        return response($data);
    }
    /**
     * Get corporates to datatables
     */
    public function getCorporatesDT(Request $request){
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $idCustomer = $request->input('idCustomer');
        $fTradename = $request->input('filterTradename');
        $fTrademark = $request->input('filterTrademark');
        $fIdStatus = $request->input('filterIdStatus');
        $fIdIndustry = $request->input('filterIdIndustry');
        $fRFC = $request->input('filterRFC');
        $data = CorporatesModel::getCorporatesDT($page, $rows, $search, $draw, $order, $idCustomer,
                    $fTradename, $fTrademark, $fIdStatus, $fIdIndustry, $fRFC);
        return response($data);
    }
    /**
     * Set info corporate 
     */
    public function setCorporate(Request $request){
        if($request->input('sNewIndustry')){
            $idIndustry = IndustriesModel::setIndustry($request->input('sNewIndustry'));
            $data['newIndustry'] = $idIndustry;
        }
        else $idIndustry = $request->input('sIdIndustry');
        $idCustomer = $request->input('idCustHidden');
        $tradename = $request->input('sTradename');
        $trademark = $request->input('sTrademark');
        $rfc = $request->input('sRfc');
        $idStatus = $request->input('sIdStatus');
        $type = $request->input('sType');
        
        $setCorporate = CorporatesModel::setCorporate($idCustomer, $tradename, $trademark, $rfc, $idStatus, $idIndustry, $type);
        switch ($setCorporate) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                $msg = 'Registro actualizado';
                break;
            case StatusConstants::ERROR:
                $status = StatusConstants::ERROR;
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
     * Update info corporate 
     */
    public function updateCorporate(Request $request){
        $idCustomer = $request->input('idCustHidden');
        $idCorporate = $request->input('idCorpHidden');
        $tradename = $request->input('uTradename');
        $trademark = $request->input('uTrademark');
        $rfc = $request->input('uRfc');
        $idStatus = $request->input('uIdStatus');
        $idIndustry = $request->input('uIdIndustry');
        $type = $request->input('uType');

        $updateCorporate = CorporatesModel::updateCorporate($idCorporate, $idCustomer, $tradename, $trademark, $rfc, $idStatus, $idIndustry, $type);
        switch ($updateCorporate) {
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
     * Delete corporate
     */
    public function deleteCorporate(Request $request){
        $idCorporate = $request->input('idCorporate');
        $model = CorporatesModel::deleteCorporate($idCorporate);
        switch ($model) {
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
                $msg = 'Imposible eliminar, este corporativo.';
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
     * Get address for corporates
     */
    public function getAddressCorporate(Request $request, $idCorporate){
        $data = AddressesModel::getAddress($idCorporate);
        return $data;
    }
    /**
     * Set info address for each corporates
     */
    public function setAddressCorporate(Request $request){
        $data = $request->all();
        DB::beginTransaction();
        $addressResponse = ['status' => '', 'physical' => [], 'fiscal' => []];
        $setAddressPhysical = AddressesModel::setAddress($data['addresses']['physical']);
        if($setAddressPhysical['status'] == StatusConstants::SUCCESS){
            $addressResponse['status'] = $setAddressPhysical['status'];
            $addressResponse['physical'] =
                ['idAddress' => $setAddressPhysical['idAddress'],
                 'idCorporate' => $setAddressPhysical['idCorporate'],
                 'type' => $setAddressPhysical['type']];

            $setAddressFiscal = AddressesModel::setAddress($data['addresses']['fiscal']);
            if($setAddressFiscal['status'] == StatusConstants::SUCCESS) {
                $addressResponse['status'] = $setAddressFiscal['status'];
                $addressResponse['fiscal'] =
                    ['idAddress' => $setAddressFiscal['idAddress'],
                     'idCorporate' => $setAddressFiscal['idCorporate'],
                     'type' => $setAddressFiscal['type']];
                DB::commit();
            } else {
                DB::rollBack();
                $addressResponse['status'] = $setAddressFiscal['status'];
            }
        } else {
            DB::rollBack();
            $addressResponse['status'] = $setAddressPhysical['status'];
        }

        switch ($addressResponse['status']) {
            case StatusConstants::SUCCESS:
                $data['addressesResponse'] = $addressResponse;
                $status = StatusConstants::SUCCESS;
                $msg = 'Registro actualizado';
                break;
            case StatusConstants::WARNING:
                $status = StatusConstants::WARNING;
                $msg = 'Algo salio mal, intente nuevamente';
                break;
            case StatusConstants::ERROR:
                $status = StatusConstants::ERROR;
                $msg = 'Algo salio mal';
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
     * Delete address
     */
    public function deleteAddressCorporate(Request $request){
        $idAddress = $request->input('idAddress');
        $model = AddressesModel::deleteAddress($idAddress);
        switch ($model) {
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
                $msg = 'Imposible eliminar, intente de nuevo.';
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
     * Get contact for corporates
     */
    public function getContactCorporate(Request $request, $idCorporate){
        $data = ContactsModel::getContact($idCorporate);
        return $data;
    }
    /**
     * Set info contact for each corporates
     */
    public function setContactCorporate(Request $request){
        $data = $request->all();
        $setContact = ContactsModel::setContact($data);
        switch ($setContact['status']) {
            case StatusConstants::SUCCESS:
                $data['idContact'] = $setContact['idContact'];
                $data['idCorporate'] = $setContact['idCorporate'];
                $status = StatusConstants::SUCCESS;
                $msg = 'Registro actualizado';
                break;
            case StatusConstants::WARNING:
                $status = StatusConstants::WARNING;
                $msg = 'Algo salio mal, intente nuevamente';
                break;
            case StatusConstants::ERROR:
                $status = StatusConstants::ERROR;
                $msg = 'Algo salio mal';
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
     * Delete contact
     */
    public function deleteContactCorporate(Request $request){
        $idContact = $request->input('idContact');
        $model = ContactsModel::deleteContact($idContact);
        switch ($model) {
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
                $msg = 'Imposible eliminar, intente de nuevo.';
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
     * Get corporates by id customer 
     */
    public function getAllCorporates(Request $request, $idCustomer){
        $corporates = CorporatesModel::GetAllCorporates($idCustomer);
        return response($corporates);
    }
    /**
     * Get corporates with active contract
     */
    public function getActiveCorporates(Request $request, $idCustomer){
        $corporates = CorporatesModel::GetCorporateActiveContract($idCustomer);
        return response($corporates);
    }
}
