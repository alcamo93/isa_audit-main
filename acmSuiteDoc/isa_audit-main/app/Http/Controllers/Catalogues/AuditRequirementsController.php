<?php

namespace App\Http\Controllers\Catalogues;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Catalogues\StatusModel;
use App\Models\Catalogues\RequirementsModel;
use App\Models\Catalogues\SubrequirementsModel;
use App\Models\Catalogues\GuidelinesModel;
use App\Models\Catalogues\BasisModel;
use App\Models\Catalogues\AspectsModel;
use App\Models\Catalogues\MattersModel;
use App\Models\Catalogues\StatesModel;
use App\Models\Catalogues\CitiesModel;
use App\Models\Catalogues\CountriesModel;
use App\Models\Catalogues\EvidencesModel;
use App\Models\Catalogues\PeriodModel;
use App\Models\Catalogues\ApplicationTypesModel;
use App\Models\Catalogues\LegalClassificationModel;
use App\Models\Catalogues\ConditionsModel;
use App\Models\Catalogues\RequirementTypesModel;
use App\Models\Admin\CustomersModel;
use App\Models\Admin\CorporatesModel;
use App\Models\Audit\ObligationsModel;
use App\Models\Admin\ProfilesModel;
use App\Classes\StatusConstants;
use App\Classes\ProfilesConstants;
use App\Models\Catalogues\RequirementsLegalBasiesModel;
use App\Models\Catalogues\SubrequirementsLegalBasiesModel;
use App\Traits\V2\ParameterTrait;
use App\Traits\V2\OrderByAspect;
use Illuminate\Support\Facades\DB;
use App\Classes\RequirementSpecific;

class AuditRequirementsController extends Controller
{
    use ParameterTrait, OrderByAspect;
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
     * Main view in requirements catalogue module
     */
    public function index($id){
        $data['matters'] = MattersModel::GetMatters();
        $data['guidelines'] = GuidelinesModel::GetGuidelinesSelection(1);
        $data['countries'] = CountriesModel::GetCountries();
        $data['states'] = StatesModel::GetStates(1);
        $data['appTypes'] = ApplicationTypesModel::GetApplicationTypes([1]);
        $data['legalC'] = LegalClassificationModel::GetLegalClassifications();
        $data['evidences'] = EvidencesModel::GetEvidences();
        $data['periods'] = PeriodModel::GetPeriods();
        $data['conditions'] = ConditionsModel::GetConditions();
        $data['requirementTypes'] = RequirementTypesModel::GetRequirementsTypeGroup(0);
        $data['requirementTypesFV'] = RequirementTypesModel::GetRequirementsTypeGroup(-1);
        $data['customers'] = CustomersModel::GetAllCustomers();
        $data['moduleActive'] = 8;
        $data['parameters'] = $this->getParametersByFormId($id);
        return view('catalogs.requirements.audit_requirements', $data);
    }
    /**
     * Main view in requirements module for customers
     */
    public function indexCustomers(){
        $data['matters'] = MattersModel::GetMatters();
        $data['guidelines'] = GuidelinesModel::GetGuidelinesSelection(1);
        $data['states'] = StatesModel::GetStates(1);
        $data['appTypes'] = ApplicationTypesModel::GetApplicationTypes([3]);
        $data['legalC'] = LegalClassificationModel::GetLegalClassifications();
        $data['evidences'] = EvidencesModel::GetEvidences();
        $data['periods'] = PeriodModel::GetPeriods();
        $data['requirementTypesFV'] = RequirementTypesModel::GetRequirementsTypeGroup(-1);
        $data['obligations'] = ObligationsModel::all();
        $groupStatus = 1; // group status basic
        $data['status'] = StatusModel::GetStatusByGroup($groupStatus);
        $data['moduleActive'] = 15;
        switch (Session::get('profile')['id_profile_type']){
            case ProfilesConstants::ADMIN_GLOBAL: 
            case ProfilesConstants::ADMIN_OPERATIVE:
                $data['customers'] = CustomersModel::GetAllCustomers();
                $data['profiles'] = ProfilesModel::GetAllProfiles(1);
                $data['corporates'] = null;
                break;
            case ProfilesConstants::CORPORATE:
                $data['customers'] = CustomersModel::GetCustomer(Session::get('customer')['id_customer']);
                $data['corporates'] = CorporatesModel::GetAllCorporates(Session::get('customer')['id_customer']);
                $data['profiles'] = ProfilesModel::GetAllProfiles(1, Session::get('customer')['id_customer']);
                break;
            case ProfilesConstants::COORDINATOR: 
            case ProfilesConstants::OPERATIVE:
                $data['customers'] = CustomersModel::GetCustomer(Session::get('customer')['id_customer']);
                $data['corporates'] = CorporatesModel::GetCorporate(Session::get('corporate')['id_corporate']);
                $data['profiles'] = ProfilesModel::GetAllProfiles( 1, Session::get('customer')['id_customer'], Session::get('corporate')['id_corporate'] );
                break;
        }
        return view('requirements.requirements_customers', $data);
    }
    /**
     * Obtain requirements for datatable
     */
    public function getRequirementsDT(Request $request){
        $requestData = $request->all();
        $data = RequirementsModel::GetRequirementsDT($requestData);
        return response($data);
    }
    /**
     *  Set requirement
     */
    public function setRequirement(Request $request) {
        $requestData = $request->all();
        DB::beginTransaction();
        $response = RequirementsModel::SetRequirement($requestData);
        if ($response['status'] == StatusConstants::ERROR) {
            DB::rollback();
            $data['status'] = StatusConstants::WARNING;
            $data['msg'] = 'Algo salio mal, intente nuevamente';
            return $data;
        }
        $specific = new RequirementSpecific($response['model']);
        $setInAudit = $specific->setSpecificInProgressAudit();
        if (!$setInAudit) {
            DB::rollback();
            $data['status'] = StatusConstants::WARNING;
            $data['msg'] = 'Algo salio mal, intente nuevamente.';
            return $data;
        }
        // $this->orderRequirement($response['idRequirement'], $requestData);
        DB::commit();
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Datos registrados';
        return $data;
    }
    /**
     * Get data requirement
     */
    public function getRequirementById(Request $request){
        $idRequirement = $request->input('idRequirement');
        $data = RequirementsModel::GetRequirementByIdRequirement($idRequirement);
        return response($data);
    }
    /**
     *  update requirement
     */
    public function updateRequirement(Request $request) {
        $requestData = $request->all();
        DB::beginTransaction();
        $update = RequirementsModel::UpdateRequirement( $requestData );
        switch ($update['status']) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                $msg = 'Registro actualizado';
                break;
            case StatusConstants::ERROR:
                $status = StatusConstants::WARNING;
                $msg = 'Algo salio mal, intente nuevamente';
                break;
            case StatusConstants::WARNING:
                $status = StatusConstants::WARNING;
                $msg = 'Los datos que desea registrar, ya se encuentran en el sistema';
                break;
            default:
                # code... 
                break;
        }
        // $this->orderRequirement($update['idRequirement'], $requestData);
        DB::commit();
        $data['status'] = $status;
        $data['msg'] = $msg;
        return $data;
    }
    /**
     * delete requirement
     */
    public function deleteRequirement(Request $request) {
        $idRequirement = $request->input('idRequirement');
        $delete = RequirementsModel::deleteRequirement($idRequirement);
        switch ($delete) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                $msg = 'Registro eliminado';
                break;
            case StatusConstants::ERROR:
                $status = StatusConstants::WARNING;
                $msg = 'Algo salio mal, intente nuevamente';
                break;
            case StatusConstants::WARNING:
                $status = StatusConstants::WARNING;
                $msg = 'El registro está en uso, no se puede eliminar';
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
     * obtain basis by requirement id
     */
    public function getRequirementBasis(Request $request, $idRequirement){
        $data = RequirementsModel::getRequirementBasis($idRequirement);
        return response($data);
    }
    /**
     *  Basis related to requeriment Datatable
     */
    public function getRequirementBasisDT(Request $request) {
        $dataRequest = $request->all();
        $data = RequirementsLegalBasiesModel::GetBasisByRequirementDT($dataRequest);
        return response($data);
    }
    /**
     *  set/delete requirement-basis relation
     */
    public function updateRequirementBasis(Request $request)
    {
        $id = $request->input('id');
        $idBasis = $request->input('idBasis');
        $status = $request->input('status');
        $update = RequirementsModel::updateRequirementBasis($id, $idBasis, $status);
        switch ($update) {
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
/********************************************************************************************************
 * ****************************************  Subrequirements ********************************************
 ********************************************************************************************************/
    /**
     * Obtain subrequirements for datatable
     */
    public function getSubrequirementsDT(Request $request){
        $requestData = $request->all();
        $data = SubrequirementsModel::GetSubrequirementsDT($requestData);
        return response($data);
    }
    /**
     *  Set subrequirement
     */
    public function setSubrequirement(Request $request) {
        $requestData = $request->all();
        \DB::beginTransaction();
        $infoRequirement = RequirementsModel::find($requestData['idRequirement'])->toArray();
        $response = SubrequirementsModel::SetSubrequirement($requestData, $infoRequirement);
        if ($response['status'] != StatusConstants::SUCCESS) {
            \DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salio mal, intente nuevamente.';
            return response($data);
        }
        // $update = RequirementsModel::UpdateSetHasSubrequirements($idRequirement);
        // if (!$update) {
        //     \DB::rollBack();
        //     $data['status'] = StatusConstants::ERROR;
        //     $data['msg'] = 'Algo salio mal, intente nuevamente';
        //     return response($data);
        // }
        // $this->orderSubrequirement($requestData['idRequirement'], $response['idSubrequirement'], $requestData);
        \DB::commit();
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Datos registrados';
        return response($data);
    }
    /**
     * Get data subrequirement
     */
    public function getSubrequirementById(Request $request){
        $idSubrequirement = $request->input('idSubrequirement');
        $data = SubrequirementsModel::GetSubrequirementById($idSubrequirement);
        return response($data);
    }
    /**
     *  update subrequirement
     */
    public function updateSubrequirement(Request $request) {
        $requestData = $request->all();
        DB::beginTransaction();
        $infoRequirement = RequirementsModel::find($requestData['idRequirement'])->toArray();
        $update = SubrequirementsModel::UpdateSubrequirement($requestData, $infoRequirement); 
        switch ($update['status']) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                $msg = 'Registro actualizado';
                break;
            case StatusConstants::ERROR:
                $status = StatusConstants::WARNING;
                $msg = 'Algo salio mal, intente nuevamente';
                break;
            case StatusConstants::WARNING:
                $status = StatusConstants::WARNING;
                $msg = 'Los datos que desea registrar, ya se encuentran en el sistema';
                break;
            default:
                # code... 
                break;
        }
        // $this->orderSubrequirement($requestData['idRequirement'], $update['idSubrequirement'], $requestData);
        DB::commit();
        $data['status'] = $status;
        $data['msg'] = $msg;
        return $data;
    }
    /**
     * delete requirement
     */
    public function deleteSubrequirement(Request $request) {
        $idSubrequirement = $request->input('idSubrequirement');
        $delete = SubrequirementsModel::DeleteSubrequirement($idSubrequirement);
        switch ($delete) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                $msg = 'Registro eliminado';
                break;
            case StatusConstants::ERROR:
                $status = StatusConstants::WARNING;
                $msg = 'Algo salio mal, intente nuevamente';
                break;
            case StatusConstants::WARNING:
                $status = StatusConstants::WARNING;
                $msg = 'El registro está en uso, no se puede eliminar';
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
     * obtain basis by requirement id
     */
    public function getSubrequirementBasis(Request $request, $idSubrequirement){
        $data = SubrequirementsModel::getSubrequirementBasis($idSubrequirement);
        return response($data);
    }
    /**
     *  set/delete requirement-basis relation
     */
    public function updateSubrequirementBasis(Request $request)
    {
        $id = $request->input('id');
        $idBasis = $request->input('idBasis');
        $status = $request->input('status');
        $update = SubrequirementsModel::updateSubrequirementBasis($id, $idBasis, $status);
        switch ($update) {
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
     *  Basis related to requeriment Datatable
     */
    public function getSubrequirementBasisDT(Request $request) {
        $dataRequest = $request->all();
        $data = SubrequirementsLegalBasiesModel::GetBasisBySubrequirementDT($dataRequest);
        return response($data);
    }
    /**
     *  get subrequirementTypes
     */
    public function getSubrequirementTypes(Request $request) {
        $idRequirementType = $request->input('idRequirementType');
        $applicationType = $request->input('applicationType');
        $infoType = RequirementTypesModel::where('id_requirement_type', $idRequirementType)->get()->toArray();
        $subrequirementTypes = RequirementTypesModel::GetRequirementsTypeGroup($applicationType, [$infoType[0]['identification']]);
        return $subrequirementTypes;
    }
    /**
     * Get all data requirement
     */
    public function getAllDataRequirement(Request $request) {
        $idRequirement = $request->input('idRequirement');
        $data = RequirementsModel::GetAllDataRequirement($idRequirement);
        return response($data);
    }
    /**
     * Get all data subrequirement
     */
    public function getAllDataSubrequirement(Request $request){
        $idSubrequirement = $request->input('idSubrequirement');
        $data = SubrequirementsModel::GetAllDataSubrequirement($idSubrequirement);
        return response($data);
    }
} 