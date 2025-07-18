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
use App\Models\Admin\LicensesModel;
use App\Models\Catalogues\StatusModel;
use App\Models\Catalogues\PeriodModel;

class LicensesController extends Controller
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
        $periods = PeriodModel::GetPeriods();
        return view('licenses.licenses',[
            'status' => $status,
            'periods' => $periods]
        );
    }
    /**
     * Get licenses to datatables
     */
    public function getLicensesDT(Request $request){
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $fLicense = $request->input('fLicense');
        $fIdStatus = $request->input('fIdStatus');
        $data = LicensesModel::getLicensesDT($page, $rows, $search, $draw, $order, $fLicense, $fIdStatus);
        return response($data);
    }
    /**
     * Get license info by idLicense
     */
    public function getLicense(Request $request, $idLicense){
        $data = LicensesModel::getLicense($idLicense);
        return response($data);
    }
    /**
     * Set info license 
     */
    public function setLicense(Request $request){
        $data = $request->all();
        $setLicense = LicensesModel::setLicense($data);
        switch ($setLicense) {
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
     * Update info license 
     */
    public function updateLicense(Request $request){
        $data = $request->all();
        $updateLicense = LicensesModel::updateLicense($data);
        switch ($updateLicense) {
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
    public function deleteLicense(Request $request){
        $idLicense = $request->input('idLicense');
        $model = LicensesModel::deleteLicense($idLicense);
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
                $msg = 'Imposible eliminar, esta licencia.';
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
     * Get licenses by filter select2
     */
    public function getFilterLicenses(Request $request){
        $filter = $request->input('filter');
        $data = array();
        $licenses = LicensesModel::getFilterLicenses($filter);
        for ($i=0; $i <sizeof($licenses); $i++) { 
            $data[] = ['id' => $licenses[$i]['id_license'], 'text' => $licenses[$i]['license']];
        }
        return response($data);
    }
}