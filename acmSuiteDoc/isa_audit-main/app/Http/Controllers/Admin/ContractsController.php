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
use App\Classes\StatusAplicability;
use App\Models\Admin\ContractsModel;
use App\Models\Admin\ContractDetailsModel;
use App\Models\Admin\ContractsExtensionModel;
use App\Models\Catalogues\StatusModel;
use App\Models\Admin\LicensesModel;
use App\Models\Admin\CustomersModel;
use App\Models\Catalogues\PeriodModel;
use App\Models\Catalogues\MattersModel;
use App\Models\Catalogues\AspectsModel;
use App\Models\Audit\AplicabilityRegistersModel;
use App\Models\Audit\ContractMattersModel;
use App\Models\Audit\ContractAspectsModel;
use App\Models\Admin\AddressesModel;
use Carbon\Carbon;

class ContractsController extends Controller
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
     * Main view in contracts module
     */
    public function index(){
        $groupStatus = 1; // group status basic
        $status = StatusModel::getStatusByGroup($groupStatus);
        $customers = CustomersModel::getAllCustomers();
        $licenses = LicensesModel::getAllLicenses();
        $periods = PeriodModel::GetPeriods();
        $matters = MattersModel::GetMatters();
        return view('contracts.contracts',[
            'status' => $status,
            'customers' => $customers,
            'licenses' => $licenses,
            'periods' => $periods,
            'matters' => $matters]
        );
    }
    /**
     * Get contracts to datatables
     */
    public function getContractsDT(Request $request){
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $fContract = $request->input('fContract');
        $fIdStatus = $request->input('fIdStatus');
        $fIdCustomer = $request->input('fIdCustomer');
        $fIdCorporate = $request->input('fIdCorporate');
        $data = ContractsModel::getContractsDT($page, $rows, $search, $draw, $order, 
            $fContract, $fIdStatus, $fIdCustomer, $fIdCorporate);
        return response($data);
    }
    /**
     * Get contract info by idContract
     */
    public function getContract(Request $request, $idContract){
        $data['contract'] = ContractsModel::getContract($idContract);
        $data['details'] = ContractDetailsModel::getContractDetail($idContract);
        return response($data);
    }
    /**
     * Set info contract 
     */
    public function setContract(Request $request){
        $requestData = $request->all();
        $idContract = intval($requestData['idContract']);
        $idLicense = $requestData['idLicense'];
        $idUser = Session::get('user')['id_user'];
        DB::beginTransaction();
        $setContract = ContractsModel::SetContract($requestData);
        // Validate Set contract
        if ($idContract == 0 && $setContract['status'] == StatusConstants::SUCCESS) {
            // set contracts details
            $idContract = $setContract['idContract'];
            $dataHistorical = LicensesModel::getLicense($idLicense);
            $setDetailsContract = ContractDetailsModel::SetContractDetails($idUser, $idContract, $dataHistorical);
            if ($setDetailsContract == StatusConstants::SUCCESS) {
                DB::commit();
                $status = StatusConstants::SUCCESS;
                $msg = 'Registro actualizado';
            } else {
                DB::rollBack();
                $status = StatusConstants::ERROR;
                $msg = 'Algo salio mal, intente nuevamente';
            }
        } else if($idContract > 0 && $setContract['status'] == StatusConstants::SUCCESS){
            DB::commit();
            $status = StatusConstants::SUCCESS;
            $msg = 'Registro actualizado';
        } else if($setContract['status'] == StatusConstants::WARNING) {
            DB::rollBack();
            $status = StatusConstants::WARNING;
            $msg = 'No es posible realizar el registro, el contrato ya existe';
        } else{
            DB::rollBack();
            $status = StatusConstants::ERROR;
            $msg = 'Algo salio mal, intente nuevamente';
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return response($data);
    }
    /**
     * Update info contract 
     */
    public function updateContract(Request $request){
        $contractData = $request->input('contract');
        $detailData = $request->input('detail');
        $idUser = Session::get('user')['id_user'];
        DB::beginTransaction();
        $updateContract = ContractsModel::UpdateContract($contractData);
        // Validate Update contract
        if ($updateContract == StatusConstants::SUCCESS) {
            $updateContractDetail = ContractDetailsModel::UpdateContractDetails($idUser, $detailData);
            switch ($updateContractDetail) {
                case StatusConstants::SUCCESS:
                    DB::commit();
                    $status = StatusConstants::SUCCESS;
                    $msg = 'Registro actualizado';
                    break;
                case StatusConstants::WARNING:
                    DB::rollBack();
                    $status = StatusConstants::WARNING;
                    $msg = 'Algo salio mal, intente nuevamente';
                    break;
                case StatusConstants::ERROR:
                    DB::rollBack();
                    $status = StatusConstants::ERROR;
                    $msg = 'El registro no fue encontrado, verifique';
                    break;
                default:
                    # code... 
                    break;
            }
        }elseif ($updateContract == StatusConstants::WARNING) {
            DB::rollBack();
            $status = StatusConstants::WARNING;
            $msg = 'Algo salio mal, intente nuevamente';
        }else{
            DB::rollBack();
            $status = StatusConstants::ERROR;
            $msg = 'El registro no fue encontrado, verifique';
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return $data;
    }
    /**
     * Delete contract
     */
    public function deleteContract(Request $request){
        $idContract = $request->input('idContract');
        DB::beginTransaction();
        $details = ContractDetailsModel::deleteContractDetails($idContract);
        if ($details == StatusConstants::SUCCESS) {
            $contract = ContractsModel::deleteContract($idContract);
            switch ($contract) {
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
                    $msg = 'Imposible eliminar, este Contrato.';
                    break;
                default:
                    # code... 
                    break;
            }    
        }else{
            DB::rollBack();
            $status = StatusConstants::WARNING;
            $msg = 'Imposible eliminar, otros elementos dependen de este.';
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return $data;
    }
    /**
     * 
     */
    public function setContractExtension(Request $request)
    {
        $type = $request->input('type');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $idContract = $request->input('idContract');
        //$idLicense = $request->input('idLicense');
        DB::beginTransaction();
        $setContractExtension = ContractsExtensionModel::setContractExtension($type, $startDate, $endDate, $idContract);
        // Validate Set contract
        switch ($setContractExtension['status']) {
            case StatusConstants::SUCCESS:
                if($type == 1){
                    $contract = ContractsModel::updateContract([
                        'idContract' => $idContract,
                        'dateStart' => $startDate,
                        'dateEnd' => $endDate]);
                    if($contract == StatusConstants::SUCCESS){
                        $status = StatusConstants::SUCCESS;
                        $msg = 'Registro actualizado';
                        DB::commit();
                    } else {
                        $status = StatusConstants::ERROR;
                        $msg = 'Algo salio mal, intente nuevamente';
                        DB::rollBack();
                    }
                } else {
                    $status = StatusConstants::SUCCESS;
                    $msg = 'Registro actualizado';
                    DB::commit();
                }
                break;
            case StatusConstants::ERROR:
                DB::rollBack();
                $status = StatusConstants::ERROR;
                $msg = 'Algo salio mal, intente nuevamente';
                break;
            case StatusConstants::WARNING:
                DB::rollBack();
                $status = StatusConstants::WARNING;
                $typeW = ($type == 0) ? 'extensión' : 'renovación';
                $msg = 'Ya existe una '.$typeW.' programada para este contrato';
                break;
            
            default:
                # code...
                break;
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return response($data);
        
    }
    /**
     * Calculate Date End
     */
    public function calculateDateEnd(Request $request){
        $date = $request->input('dateStart');
        $idLicense = $request->input('idLicense');
        $infoLicense = LicensesModel::getLicense($idLicense);
        $infoPeriod = PeriodModel::GetPeriod($infoLicense[0]['id_period']);
        if ($infoPeriod[0]['lastMonth'] == 0 && $infoPeriod[0]['lastYear'] == 0 && $infoPeriod[0]['lastDay'] == 0) {
            $endDate = StatusConstants::ERROR;
        }else{
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $date.' 00:00:00', 'America/Mexico_City');
            if ($infoPeriod[0]['lastMonth'] != 0) {
                $endDate = $startDate->addMonths($infoPeriod[0]['lastMonth']);
            }
            if($infoPeriod[0]['lastYear'] != 0){
                $endDate = $startDate->addYears($infoPeriod[0]['lastYear']);
            }
            if ($infoPeriod[0]['lastDay'] != 0) {
                $endDate = $startDate->addDays($infoPeriod[0]['lastDay']);
            }
            $endDate = $endDate->toDateString();
        }
        return response($endDate);
    }
    /**
     * Update and calculate Date End
     */
    public function updateCalculateDateEnd(Request $request){
        $date = $request->input('dateStart');
        $idPeriod = $request->input('idPeriod');
        $infoPeriod = PeriodModel::GetPeriod($idPeriod);
        if ($infoPeriod[0]['lastMonth'] == 0 && $infoPeriod[0]['lastYear'] == 0 && $infoPeriod[0]['lastDay'] == 0) {
            $endDate = StatusConstants::ERROR;
        }else{
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $date.' 00:00:00', 'America/Mexico_City');
            if ($infoPeriod[0]['lastMonth'] != 0) {
                $endDate = $startDate->addMonths($infoPeriod[0]['lastMonth']);
            }
            if($infoPeriod[0]['lastYear'] != 0){
                $endDate = $startDate->addYears($infoPeriod[0]['lastYear']);
            }
            if ($infoPeriod[0]['lastDay'] != 0) {
                $endDate = $startDate->addDays($infoPeriod[0]['lastDay']);
            }
            $endDate = $endDate->toDateString();
        }
        return response($endDate);
    }
    /**
     * Update contract status
     */
    public function updateContractStatus(Request $request){
        $idStatus = $request->input('idStatus');
        $idCorporate = $request->input('idCorporate');
        $idContract = $request->input('idContract');
        $continueSet = true;
        if ($idStatus == StatusConstants::ACTIVE) {
            $activeContract = ContractsModel::getContractActive($idCorporate);
            // validate that there is no other active contract
            //if ( is_array($activeContract) && sizeof($activeContract) > 0 ) {
            //    $continueSet = false;
            //    $status = StatusConstants::WARNING;
            //    $msg = 'No puede haber dos contratos activos de la misma planta';
            //    $data['title'] = 'El contrato "'.$activeContract[0]['contract'].'" esta Activo';
            //}
            if($continueSet){
                $activable = ContractsModel::getIsContractActivable($idContract);
                if ( is_array($activable) && sizeof($activable) < 1 ) {
                    $continueSet = false;
                    $status = StatusConstants::WARNING;
                    $msg = 'El contrato ha expirado, ya no puede activarse.';
                    $data['title'] = '¡Error!';
                }
            }
        }
        if ($continueSet) {
            $updateStatus = ContractsModel::UpdateContractStatus($idContract, $idStatus);
            switch ($updateStatus) {
                case StatusConstants::SUCCESS:
                    DB::commit();
                    $status = StatusConstants::SUCCESS;
                    $msg = 'Estatus de contrato actualizado';
                    break;
                case StatusConstants::WARNING:
                    DB::rollBack();
                    $status = StatusConstants::WARNING;
                    $msg = 'Algo salio mal, intente nuevamente';
                    break;
                case StatusConstants::ERROR:
                    DB::rollBack();
                    $status = StatusConstants::ERROR;
                    $msg = 'No existe el contrato';
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


    /********************** Employs **********************/


    /**
     * Validate there is aplicability
     */
    public function validateAplicability(Request $request){
        $idContract = $request->input('idContract');
        $idCorporate = $request->input('idCorporate');
        $hasState = AddressesModel::GetAddressType($idCorporate, StatusConstants::PHYSICAL);
        if( isset($hasState[0]['id_state']) ){
            $aplicabilityRegister = AplicabilityRegistersModel::GetAplicabilityRegisterByContract($idContract);
            if ( sizeof($aplicabilityRegister) == 0 ) {
                $setAplicabilityRegister = AplicabilityRegistersModel::SetAplicability($idContract, $idCorporate);
                switch ($setAplicabilityRegister['status']) {
                    case StatusConstants::SUCCESS:
                        $status = StatusConstants::SUCCESS;
                        $msg = 'Aplicabilidad Creada';
                        $data['idAplicabilityRegister'] = $setAplicabilityRegister['idAplicabilityRegister'];
                        break;
                    case StatusConstants::ERROR:
                        $status = StatusConstants::ERROR;
                        $msg = 'Error al craer Aplicabilidad';
                        break;
                    default:
                        # code...
                        break;
                }
            } 
            else {
                $status = StatusConstants::SUCCESS;
                $data['msg'] = $aplicabilityRegister[0]['id_aplicability_register'];
                $msg = 'Aplicabilidad Encontrada';
                $data['idAplicabilityRegister'] = $aplicabilityRegister[0]['id_aplicability_register'];
            }
        }
        else {
            $status = StatusConstants::WARNING;
            $msg = 'No se cuenta con el registo de estado en dirección física';
            $data['title'] = 'Sin estado registrado';
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return response($data);
    }
    /**
     * Get matter employ
     */
    public function registerMattersDT(Request $request){
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $idAplicabilityRegister = ( is_null( $request->input('idAplicabilityRegister') ) ) ? 0 : $request->input('idAplicabilityRegister');
        $data = MattersModel::GetMattersEmployDT($page, $rows, $search, $draw, $order, $idAplicabilityRegister);
        return response($data);
    }
    /**
     * Get matter employ
     */
    public function registerAspectsDT(Request $request){
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $idMatter = ( is_null( $request->input('idMatter') ) ) ? 0 : $request->input('idMatter');
        $idContractMatter = ( is_null( $request->input('idContractMatter') ) ) ? 0 : $request->input('idContractMatter');
        $data = AspectsModel::GetAspectsEmployDT($page, $rows, $search, $draw, $order, $idContractMatter, $idMatter);
        return response($data);
    }
    /**
     * Set matter in aplicability
     */
    public function setMatter(Request $request){
        $action = $request->input('action');
        $requestData = $request->all();
        if ($action == 'insert') {
            $setMatter = ContractMattersModel::SetMatter($requestData);
            switch ($setMatter) {
                case StatusConstants::SUCCESS:
                    $status = StatusConstants::SUCCESS;
                    $msg = 'Materia asignada';
                    break;
                case StatusConstants::ERROR:
                    $status = StatusConstants::ERROR;
                    $msg = 'No pudo asignar la materia';
                    break;
                default:
                    # code...
                    break;
            }
        }
        else {
            $deleteMatter = ContractMattersModel::DeleteMatter($requestData);
            switch ($deleteMatter) {
                case StatusConstants::SUCCESS:
                    $status = StatusConstants::SUCCESS;
                    $msg = 'Materia retirada';
                    break;
                case StatusConstants::WARNING:
                    $status = StatusConstants::WARNING;
                    $msg = 'La materia cuenta con aspectos asignados';
                    break;
                case StatusConstants::ERROR:
                    $status = StatusConstants::ERROR;
                    $msg = 'No pudo retirar la materia';
                    break;
                default:
                    # code...
                    break;
            }
        }
        if ($status == StatusConstants::SUCCESS) {
            $statusAplicability = new StatusAplicability();
            $data['updateGloabl'] = $statusAplicability->updateStatusAplicabilityRegisters($requestData['idAplicabilityRegister']);
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return response($data);
    }
    /**
     * Set aspect in aplicability
     */
    public function setAspect(Request $request){
        $action = $request->input('action');
        $requestData = $request->all();
        if ($action == 'insert') {
            $infoContract = ContractsModel::where('id_contract',$request->input('idContract'))->get()->toArray();
            $hasState = AddressesModel::GetAddressType($infoContract[0]['id_corporate'], StatusConstants::PHYSICAL);
            $setAspect = ContractAspectsModel::SetAspect($requestData, $hasState[0]['id_state']);
            switch ($setAspect) {
                case StatusConstants::SUCCESS:
                    $status = StatusConstants::SUCCESS;
                    $msg = 'Aspecto asignado';
                    break;
                case StatusConstants::ERROR:
                    $status = StatusConstants::ERROR;
                    $msg = 'No pudo asignar el aspecto';
                    break;
                default:
                    # code...
                    break;
            }
        }
        else {
            $deleteAspect = ContractAspectsModel::DeleteAspect($requestData);
            switch ($deleteAspect) {
                case StatusConstants::SUCCESS:
                    $status = StatusConstants::SUCCESS;
                    $msg = 'Aspecto retirado';
                    break;
                case StatusConstants::WARNING:
                    $status = StatusConstants::WARNING;
                    $msg = 'El aspecto cuenta evaluaciones';
                    break;
                case StatusConstants::ERROR:
                    $status = StatusConstants::ERROR;
                    $msg = 'No pudo retirar la aspecto';
                    break;
                default:
                    # code...
                    break;
            }
        }
        if ($status == StatusConstants::SUCCESS) {
            $statusAplicability = new StatusAplicability();
            $data['updateMatter'] = $statusAplicability->updateStatusMatter($requestData['idContractMatter']);
            $data['updateGloabl'] = $statusAplicability->updateStatusAplicabilityRegisters($requestData['idAplicabilityRegister']);
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return response($data);
    }
}
