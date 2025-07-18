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
use App\Classes\ProfilesConstants;
use App\Classes\StatusAplicability;
use App\Models\Admin\CorporatesModel;
use App\Models\Admin\ContractsModel;
use App\Models\Admin\ContractDetailsModel;
use App\Models\Admin\ContractsExtensionModel;
use App\Models\Catalogues\StatusModel;
use App\Models\Admin\LicensesModel;
use App\Models\Admin\CustomersModel;
use App\Models\Catalogues\PeriodModel;
use App\Models\Catalogues\MattersModel;
use App\Models\Catalogues\AspectsModel;
use App\Models\Catalogues\ScopeModel;
use App\Models\Audit\AplicabilityRegistersModel;
use App\Models\Audit\AuditRegistersModel;
use App\Models\Audit\ContractMattersModel;
use App\Models\Audit\ContractAspectsModel;
use App\Models\Admin\AddressesModel;
use App\Models\Admin\ProcessesModel;
use App\Models\Admin\AuditorModel;
use App\User;
use Carbon\Carbon;
use App\Models\Catalogues\RequirementsModel;

class ProcessesController extends Controller
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
    public function index() {
        // generic data
        $arrayData['scopes'] = ScopeModel::GetScopes();
        $arrayData['matters'] = MattersModel::GetMatters();
        foreach ($arrayData['matters'] as $key => $m) {
            $arrayData['matters'][$key]['aspects'] = AspectsModel::GetAspectsByMatter($m['id_matter']);
        }
        // data by view
        $arrayData['idProfileType'] = Session::get('profile')['id_profile_type'];
        switch ($arrayData['idProfileType']) {
            case ProfilesConstants::ADMIN_GLOBAL:
                case ProfilesConstants::ADMIN_OPERATIVE:
                $arrayData['idUser'] = null;
                $arrayCustomers = ContractsModel::GetArrayCustomerActiveContract();
                $arrayData['customers'] = CustomersModel::GetCustomerActiveContract($arrayCustomers);
                $arrayData['corporates'] = [];
                $arrayData['idCorporate'] = null;
                $arrayData['auditors'] = [];
                $view = 'processes.main.view_owner';
                break;
            case ProfilesConstants::CORPORATE:
                $arrayData['idUser'] = null;
                $arrayData['idCustomer'] = Session::get('customer')['id_customer'];
                $arrayData['customerTrademark'] = Session::get('customer')['cust_trademark'];
                $arrayData['corporates'] = CorporatesModel::GetCorporateActiveContract($arrayData['idCustomer']);
                $arrayData['idCorporate'] = null;
                $arrayData['auditors'] = [];
                $view = 'processes.main.view_corporate';
                break;
            case ProfilesConstants::COORDINATOR:
                $arrayData['idUser'] = null;
                $arrayData['idCustomer'] = Session::get('customer')['id_customer'];
                $arrayData['idCorporate'] = Session::get('corporate')['id_corporate'];
                $arrayData['customerTrademark'] = Session::get('customer')['cust_trademark'];
                $arrayData['corporateTrademark'] = Session::get('corporate')['corp_trademark'];
                $arrayData['corporates'] = CorporatesModel::GetCorporateActiveContract($arrayData['idCustomer']);
                $arrayData['auditors'] = User::GetUserProcesss(Session::get('corporate')['id_corporate']);
                $view = 'processes.main.view_coordinator';
                break;
            case ProfilesConstants::OPERATIVE:
                $arrayData['idUser'] = Session::get('user')['id_user'];
                $arrayData['idCustomer'] = Session::get('customer')['id_customer'];
                $arrayData['idCorporate'] = Session::get('corporate')['id_corporate'];
                $arrayData['customerTrademark'] = Session::get('customer')['cust_trademark'];
                $arrayData['corporateTrademark'] = Session::get('corporate')['corp_trademark'];
                $arrayData['corporates'] = CorporatesModel::GetCorporateActiveContract($arrayData['idCustomer']);
                $arrayData['auditors'] = User::GetUserProcesss(Session::get('corporate')['id_corporate']);
                $view = 'processes.main.view_coordinator';
                break;
        }
        return view($view, $arrayData);
    }
    /**
     * Get contracts to datatables
     */
    public function getProcessesDT(Request $request){
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $fIdCustomer = $request->input('filterIdCustomer');
        $fIdCorporate = $request->input('filterIdCorporate');
        $filterIdUser = $request->input('filterIdUser');
        $fProcess = $request->input('fProcess');
        $data = ProcessesModel::ProcessesRegistersDT($page, $rows, $search, $draw, $order, $fIdCustomer, $fIdCorporate, $fProcess, $filterIdUser);
        return response($data);
    }
    /**
     * Get process info for edit
     */
    public function getProcesses(Request $request, $idProcess){
        $data['process'] = ProcessesModel::GetProcesses($idProcess); 
        $data['leader'] = AuditorModel::GetAuditorsByProcess($idProcess, StatusConstants::LEADER);
        $data['auditors'] = AuditorModel::GetAuditorsByProcess($idProcess, StatusConstants::NO_LEADER);
        $data['allAuditors'] = AuditorModel::GetAuditorsForAP($idProcess, [StatusConstants::LEADER, StatusConstants::NO_LEADER]);
        $data['aspects'] = ContractAspectsModel::GetContractAspectsByProcess($idProcess);
        return response($data);
    }
    /**
     * Set info contract 
     */
    public function setProcesses(Request $request){
        $requestData = $request->all();
        // dd($requestData);
        $duplicate = ProcessesModel::where('audit_processes', $requestData['processes'])
            ->where('id_corporate', $requestData['idCorporate'])->first();
        if ($duplicate != null) {
            $data['status'] = StatusConstants::WARNING;
            $data['msg'] = 'EL nombre de la auditoría ya existe, cambie el nombre';
            return response($data);
        }
        DB::beginTransaction();
        // Set data base
        $setProcesses = ProcessesModel::SetProcesses($requestData);
        if ($setProcesses['status'] == StatusConstants::ERROR) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salió mal, intente nuevamente';
            return response($data);
        }
        // set data auditors
        $setLeader = AuditorModel::SetLeader($requestData, $setProcesses['idProcesses']);
        if ($setLeader['status'] == StatusConstants::SUCCESS) {
            $auditors = ( isset($requestData['auditors']) ) ? $requestData['auditors'] : [];
            foreach($auditors as $a) {
                $setAuditor = AuditorModel::SetAuditor($a, $setProcesses['idProcesses']);
                if ($setLeader['status'] == StatusConstants::ERROR) {
                    DB::rollBack();
                    $data['status'] = StatusConstants::ERROR;
                    $data['msg'] = 'Error en resgitrar a un Auditor, intente nuevamente';
                    return response($data);
                }
            }
        }
        else{
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Error en resgitrar al Lider, intente nuevamente';
            return response($data);
        }
        // Validate address
        $hasState = AddressesModel::GetAddressType($requestData['idCorporate'], StatusConstants::PHYSICAL); 
        if (sizeof($hasState) == 0){
            DB::rollBack();
            $data['status'] = StatusConstants::WARNING;
            $data['msg'] = 'La planta no cuenta con una dirección Física, comuníquese con el administrador';
            return response($data);
        }
        // Set aplicability register
        $infoContract = ContractsModel::GetContractByCorporate($requestData['idCorporate']);
        $setAplicability = AplicabilityRegistersModel::SetAplicability($infoContract[0]['id_contract'], $requestData['idCorporate'], $setProcesses['idProcesses']);
        if ($setAplicability['status'] == StatusConstants::ERROR) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Error en resgitrar Aplicabilidad, intente nuevamente';
            return response($data);
        }
        // Build array matters and aspects
        $aspectsIdsArray = $requestData['aspects'];
        $aspects = [];
        foreach ($aspectsIdsArray as $i => $a) {
            $temp = AspectsModel::GetAspect($a);
            array_push($aspects, $temp[0]);
        }
        $matters = AspectsModel::GetMattersByAspects($aspectsIdsArray);
        foreach ($matters as $j => $ma) {
            $tmp = [];
            foreach ($aspects as $i => $as) {
                if ($ma['id_matter'] == $as['id_matter'] ) {
                    array_push($tmp, $as['id_aspect']);
                }
            }
            $matters[$j]['aspects'] = $tmp;
        }
        // Set Matters and aspects in aplicability
        $idAplicabilityRegister = $setAplicability['idAplicabilityRegister'];
        foreach($matters as $m){
            $setMatter = ContractMattersModel::SetMatter($infoContract[0]['id_contract'], $m['id_matter'], $setProcesses['idProcesses'], $idAplicabilityRegister);
            if ($setMatter['status'] == StatusConstants::ERROR) {
                DB::rollBack();
                $data['status'] = StatusConstants::ERROR;
                $data['msg'] = 'Error al resgitrar Materias, intente nuevamente';
                return response($data);
            }
            foreach ($m['aspects'] as $idAspect) {
                $setAspect = ContractAspectsModel::SetAspect($setMatter['id_contract_matter'], $infoContract[0]['id_contract'], $m['id_matter'], $idAspect, $setProcesses['idProcesses'], $hasState[0]['id_state']);
                if ($setAspect['status'] == StatusConstants::ERROR) {
                    DB::rollBack();
                    $data['status'] = StatusConstants::ERROR;
                    $data['msg'] = 'Error al resgitrar Aspectos, intente nuevamente';
                    return response($data);
                }
            }
        }
        DB::commit();
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Registro Exitoso';
        return response($data);
    }

    /**
     * Update info contract 
     */
    public function updateProcesses(Request $request){
        $requestData = $request->all();
        $duplicate = ProcessesModel::where('audit_processes', $requestData['process'])
            ->where('id_corporate', $requestData['idCorporate'])->first();
        if ( $duplicate != null && ($duplicate->id_audit_processes != $requestData['idProcess']) ) {
            $data['status'] = StatusConstants::WARNING;
            $data['msg'] = 'EL nombre de la auditoría ya existe, cambie el nombre';
            return response($data);
        }
        DB::beginTransaction();
        $updateProcess = ProcessesModel::UpdateProcess($requestData);
        if ($updateProcess['status'] != StatusConstants::SUCCESS) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salió mal, intente nuevamente';
            return response($data);
        }
        $updateLeader = AuditorModel::UpdateLeader($requestData['idLeader'], $requestData['idProcess']);
        if ($updateLeader['status'] != StatusConstants::SUCCESS) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'No se actualizo el Auditor Lider, intente nuevamente';
            return response($data);
        }
        // delete auditors
        $deleteAuditors = AuditorModel::DeleteAuditors($requestData['idProcess']);
        $auditors = ( isset($requestData['auditors']) ) ? $requestData['auditors'] : [];
        foreach($auditors as $a) {
            $setAuditor = AuditorModel::SetAuditor($a, $requestData['idProcess']);
            if ($setAuditor['status'] == StatusConstants::ERROR) {
                DB::rollBack();
                $data['status'] = StatusConstants::ERROR;
                $data['msg'] = 'Error en resgitrar a un Auditor, intente nuevamente';
                return response($data);
            }
        }
        // delete matters
        $deleteMatters = ContractMattersModel::DeleteMattersByProcess($requestData['idProcess']);
        if ($updateLeader['status'] != StatusConstants::SUCCESS) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'No se ha podido reordenar las Materias y aspectos, intente nuevamente';
            return response($data);
        }
        // Build array matters and aspects
        $aspectsIdsArray = $requestData['aspects'];
        $aspects = [];
        foreach ($aspectsIdsArray as $i => $a) {
            $temp = AspectsModel::GetAspect($a);
            array_push($aspects, $temp[0]);
        }
        $matters = AspectsModel::GetMattersByAspects($aspectsIdsArray);
        foreach ($matters as $j => $ma) {
            $tmp = [];
            foreach ($aspects as $i => $as) {
                if ($ma['id_matter'] == $as['id_matter'] ) {
                    array_push($tmp, $as['id_aspect']);
                }
            }
            $matters[$j]['aspects'] = $tmp;
        }
        $infoAR = AplicabilityRegistersModel::GetAplicabilityRegisterProcess($requestData['idProcess']);
        $hasState = AddressesModel::GetAddressType($requestData['idCorporate'], StatusConstants::PHYSICAL); 
        foreach($matters as $m){
            $setMatter = ContractMattersModel::SetMatter($infoAR[0]['id_contract'], $m['id_matter'], $requestData['idProcess'], $infoAR[0]['id_aplicability_register']);
            if ($setMatter['status'] == StatusConstants::ERROR) {
                DB::rollBack();
                $data['status'] = StatusConstants::ERROR;
                $data['msg'] = 'Error al resgitrar Materias, intente nuevamente';
                return response($data);
            }
            foreach ($m['aspects'] as $idAspect) {
                $setAspect = ContractAspectsModel::SetAspect($setMatter['id_contract_matter'], $infoAR[0]['id_contract'], $m['id_matter'], $idAspect, $requestData['idProcess'], $hasState[0]['id_state']);
                if ($setAspect['status'] == StatusConstants::ERROR) {
                    DB::rollBack();
                    $data['status'] = StatusConstants::ERROR;
                    $data['msg'] = 'Error al resgitrar Aspectos, intente nuevamente';
                    return response($data);
                }
            }
        }
        DB::commit();
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Actualizacion Exitosa';
        return response($data);
    }
    /**
     * Delete processes
     */
    public function deleteProcesses(Request $request){
        $idProcesses = $request->input('idProcesses');
        DB::beginTransaction();
        $processes = ProcessesModel::DeleteProcesses($idProcesses);
        if ($processes == StatusConstants::SUCCESS) {
            DB::commit();
            $data['status'] = StatusConstants::SUCCESS;
            $data['msg'] = 'Error al resgitrar Materias, intente nuevamente';
        }else{
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Error al resgitrar Materias, intente nuevamente';
        }
        return $data;
    }
    /**
     * Get users by corporate
     */
    public function getUserProcesss(Request $request){
        $idCorporate = $request->input('idCorporate');
        $data = User::GetUserProcesss($idCorporate);
        return response($data);
    }
    /**
     * Get process 
     */
    public function getCorporatesToAudit(Request $request, $idCorporate) {
        $data = ProcessesModel::where('id_corporate', $idCorporate)->get();
        return response($data);
    }
    /**
     * Validate if there are requirements by aspect
     */
    public function getValidateSpecificReqProcesss(Request $request, $idAuditProcess, $idAspect) {
        $process = ProcessesModel::findOrFail($idAuditProcess);
        $hasEvaluate = boolval($process->evaluate_especific);
        $hasRequirements = false;
        if ( $hasEvaluate ) {
            $requirements = RequirementsModel::where('id_customer', $process->id_customer)
                ->where('id_corporate', $process->id_corporate)
                ->where('id_aspect', $idAspect)->get();
            $hasRequirements = $requirements->isNotEmpty();
        }
        $data['data']['has_evaluate'] = $hasEvaluate;
        $data['data']['has_requirements'] = $hasRequirements;
        return response($data);
    }
}
