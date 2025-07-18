<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Classes\StatusConstants;
use App\Classes\StatusAudit;
use App\Classes\Evaluate;
use App\Classes\Periods;
use App\Exports\AuditReportExcelOld;
use App\Exports\AuditProgressReportExcel;
use App\Exports\AuditDocumentsReportExcel;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Admin\CustomersModel;
use App\Models\Admin\CorporatesModel;
use App\Models\Catalogues\StatusModel;
use App\Models\Catalogues\MattersModel;
use App\Models\Catalogues\AspectsModel;
use App\Models\Catalogues\QuestionsModel;
use App\Models\Audit\AplicabilityRegistersModel;
use App\Models\Audit\ContractAspectsModel;
use App\Models\Admin\ContractsModel;
use App\Models\Audit\AplicabilityModel;
use App\Models\Admin\AddressesModel;
use App\Models\Audit\AuditRegistersModel;
use App\Models\Audit\AuditMattersModel;
use App\Models\Audit\AuditAspectsModel;
use App\Models\Audit\QuestionRequirementsModel;
use App\Models\Catalogues\RequirementsModel;
use App\Models\Catalogues\RequirementsLegalBasiesModel;
use App\Models\Catalogues\SubrequirementsLegalBasiesModel;
use App\Models\Catalogues\RequirementRecomendationsModel;
use App\Models\Catalogues\SubrequirementRecomendationsModel;
use App\Models\Catalogues\SubrequirementsModel;
use App\Models\Audit\AuditModel;
use App\Models\V2\Audit\Audit;
use App\Models\Audit\ActionRegistersModel;
use App\Models\Audit\ActionPlansModel;
use App\Models\Audit\ObligationsModel;
use App\Models\Audit\EvaluateRequirementModel;
use App\Models\V2\Audit\EvaluateAuditRequirement;
use Carbon\Carbon;
use App\User;
use App\Models\Risk\RiskCategoriesModel;
use App\Models\Risk\RiskHelpModel;
// use App\Models\Risk\RiskConsequencesModel;
// use App\Models\Risk\RiskProbabilitiesModel;
// use App\Models\Risk\RiskExhibitionsModel;
// use App\Models\Risk\RiskSpecificationsModel;
use App\Models\Risk\RiskAttributesModel;
use App\Models\Risk\RiskAnswersModel;
use App\Models\Risk\RiskTotalsModel;
use App\Models\Risk\RiskInterpretationsModel;
use App\Models\Admin\ProcessesModel;
use App\Models\Admin\AuditorModel;
use App\Traits\AuditTrait;
use App\Traits\FileTrait;

use App\Models\V2\Audit\AuditRegister;

class AuditController extends Controller
{
    use AuditTrait, FileTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /************************ Owner View ************************/

    /**
     * Main view in audit module
     */
    public function index(Request $request, $idAuditRegister){
        $groupStatus = 3; // group status audit
        $idDecode = $idAuditRegister;
        $infoAR = AuditRegistersModel::GetAuditRegister($idDecode);
        $address = AddressesModel::GetAddressType($infoAR[0]['id_corporate'], StatusConstants::PHYSICAL)[0];
        $textScope = ($infoAR[0]['id_scope'] == 2) ? $infoAR[0]['scope'].': '.$infoAR[0]['specification_scope'] : $infoAR[0]['scope'];
        $arrayData['customer'] = $infoAR[0]['cust_trademark'];
        $arrayData['corporate'] = $infoAR[0]['corp_tradename'];
        $arrayData['idCorporate'] = $infoAR[0]['id_corporate'];
        $arrayData['status'] = StatusModel::getStatusByGroup($groupStatus);
        $arrayData['idProfileType'] = Session::get('profile')['id_profile_type'];
        $arrayData['idAuditRegister'] = $idDecode;
        $arrayData['idContractAR'] = $infoAR[0]['id_contract'];
        $arrayData['idAuditProcesses'] = $infoAR[0]['id_audit_processes'];
        $arrayData['auditProcesses'] = $infoAR[0]['audit_processes'];
        $arrayData['evaluateRisk'] = $infoAR[0]['evaluate_risk'];
        $arrayData['scopeAuditProcesses'] = $textScope;
        $arrayData['idState'] = $address['id_state'];
        $arrayData['idCity'] = $address['id_city'];
        $totalRiskAttributes = RiskAttributesModel::count();
        $totalRiskCategories = RiskCategoriesModel::count();
        $arrayData['totalRisk'] = $totalRiskCategories * $totalRiskAttributes;
        $view = 'audit.customerView.main';
        return view($view, $arrayData);
    }

    /**
     * Datatables corporates registers to audit
     */
    public function auditRegistersDT(Request $request){
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $fIdStatus = $request->input('fIdStatus');
        $fIdCustomer = $request->input('fIdCustomer');
        $fIdCorporate = $request->input('fIdCorporate');
        $data = AuditRegistersModel::AuditRegistersDT($page, $rows, $search, $draw, $order,
                $fIdStatus, $fIdCustomer, $fIdCorporate);
        return response($data);
    }

    /************************ Info View ************************/

    /**
     * Get Matters in contracts
     */
    public function auditRegistersMatters(Request $request, $idAuditRegister){
        $contractAspect = AuditMattersModel::GetContractMatters($idAuditRegister);
        return response($contractAspect);
    }
    /**
     * Get Matters in contracts
     */
    public function auditRegistersMatterProgress(Request $request, $idAuditRegister){
        $relationsihps = ['status', 'matters.matter', 'matters.status', 'matters.aspects', 'matters.aspects.status', 'matters.aspects.aspect'];
        $data = AuditRegistersModel::with($relationsihps)->findOrFail($idAuditRegister);
        $statusAudit = new StatusAudit();
        $data['audit'] = $statusAudit->updateStatusAuditRegisters($idAuditRegister);
        return response($data);
    }
    /**
     *Get aspects by matter in contract
     */
    public function auditRegistersAspectsDT(Request $request){
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $idAuditRegister = $request->input('idAuditRegister');
        $idAuditMatter = $request->input('idAuditMatter');
        $filterIdStatus = $request->input('filterIdStatus');
        $contractAspects = AuditAspectsModel::GetAuditAspectsDT($page, $rows, $search, $draw, $order, $idAuditRegister, $idAuditMatter, $filterIdStatus);
        return response($contractAspects);
    }
    /**
     * Set data in action plan
     */
    public function setInAction(Request $request)
    {
        // $idAuditRegister = $request->input('idAuditRegister');
        // $idAuditProcess = $request->input('idAuditProcess');
        // $setActionPlan = AuditController::setInActionProccess($idAuditRegister, $idAuditProcess);
        // return $setActionPlan;
        try {
            $idAuditRegister = $request->input('idAuditRegister');
            $auditRegister = AuditRegister::findOrFail($idAuditRegister);
            DB::beginTransaction();
            $auditRegister->audit_matters->each(function($matter) {
                $matter->update(['id_status' => StatusConstants::FINISHED_AUDIT]);
                $matter->audit_aspects->each(function($aspect) {
                    $aspect->update(['id_status' => StatusConstants::FINISHED_AUDIT]);
                });
            });
            $auditRegister->update(['id_status' => StatusConstants::FINISHED_AUDIT]);
            DB::commit();
            $data['status'] = StatusConstants::SUCCESS;
            $data['msg'] = 'Ahora puedes activar el Plan de Acción correspondiente a esta Auditoría';
            $data['title'] = 'Auditoria finalizada';
            return $data;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }
    /**
     * Process finish action plan in manual and automatic audit
     */
    public static function setInActionProccess($idAuditRegister, $idAuditProcess){
        $initAction = [];
        DB::beginTransaction();
        // Update status to finished Audit Register
        $updateStatusAudit = AuditRegistersModel::SetStatusAudit($idAuditRegister, StatusConstants::FINISHED_AUDIT);
        if ($updateStatusAudit != StatusConstants::SUCCESS) {
            DB::rollBack();
            $data['status'] = $updateStatusAudit;
            $data['msg'] = 'Error al finalizar Auditoria';
            return response($data);
        }
        // Update status to finishied Matters
        $updateStatusMatters = AuditMattersModel::SetFinishedMatters($idAuditRegister);
        if ($updateStatusAudit != StatusConstants::SUCCESS) {
            DB::rollBack();
            $data['status'] = $updateStatusAudit;
            $data['msg'] = 'Error al finalizar Materias';
            return response($data);
        }
        // Get audited aspects
        $idAspectsArray = AuditAspectsModel::where([
            ['id_audit_processes', $idAuditProcess],
            ['id_status', StatusConstants::AUDITED]
        ])->pluck('id_aspect')->toArray();
        // Update status to finishied Aspects
        $updateStatusAspects = AuditAspectsModel::SetFinishedAspects($idAuditProcess);
        if ($updateStatusAspects != StatusConstants::SUCCESS) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Error al finalizar Aspectos';
            return response($data);
        }
        // Init action plan
        $initAction = AuditController::initAction($idAuditProcess, $idAspectsArray);
        if ($initAction['status'] != StatusConstants::SUCCESS) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = $initAction['msg'];
            return response($data);
        }
        DB::commit();
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Ahora esta disponible el proceso de Plan de Acción';
        $data['title'] = 'Auditoria finalizada';
        return $data;
    }
    /**
     * Init process in action plan
     */
    public static function initAction($idAuditProcess, $idAspectsArray){
        $processInfo = ProcessesModel::GetProcesses($idAuditProcess);
        $setActionRegister = ActionRegistersModel::SetActionRegister($idAuditProcess, $processInfo[0]['id_corporate']);
        if ($setActionRegister['status'] != StatusConstants::SUCCESS) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Error en alta de Plan de Acción';
            return;
        }
        // Get obligation requirements by aspects with answer 
        // $answerPositive = 1;
        // $obligationRequirements = AuditModel::GetAuditedRequirements($idAuditProcess, $idAspectsArray, $answerPositive);
        // if ( sizeof($obligationRequirements) > 0 ) {
        //     $setCreateObligations = AuditController::setActionPlanObligations($idAuditProcess, $setActionRegister['idActionRegister'], $obligationRequirements);
        //     if ($setCreateObligations != StatusConstants::SUCCESS) {
        //         DB::rollBack();
        //         $data['status'] = StatusConstants::ERROR;
        //         $data['msg'] = 'Error en alta de Obligaciones del Plan de Acción';
        //         return $data;
        //     }
        // }
        // Get requirements by aspects with answer 0
        $answerNegative = 0;
        $requirements = AuditModel::GetAuditedRequirements($idAuditProcess, $idAspectsArray, $answerNegative);
        $setActionPlan = AuditController::setActionPlan($setActionRegister['idActionRegister'], $requirements, $idAuditProcess);
        if ($setActionPlan != StatusConstants::SUCCESS) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $msg = 'Error en registro de Plan de Acción';
            return $data;
        }
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = '';
        return $data;
    }
    // creates the action plan
    public static function setActionPlan($idActionRegister, $requirements, $idAuditProcess) {
        // Build a structure
        $ids = [];
        $actionPlan = array();
        foreach ($requirements as $index => $element) {
            array_push($ids, $element['id_audit']);
            $actionPlan[$index]['id_audit_processes'] = $idAuditProcess;
            $actionPlan[$index]['id_action_register'] = $idActionRegister;
            $actionPlan[$index]['id_aspect'] = $element['id_aspect'];
            $actionPlan[$index]['id_requirement'] = $element['id_requirement'];
            $actionPlan[$index]['id_subrequirement'] = $element['id_subrequirement'];
            $actionPlan[$index]['finding'] = $element['finding'];
            $actionPlan[$index]['id_status'] = StatusConstants::UNASSIGNED_AP;
            $isParent = ($element['has_subrequirement'] == 1 && $element['id_subrequirement'] == null) ? true : false;
            if ($isParent) {
                $answerNegative = 0;
                $countSub = AuditModel::whereNotNull('id_subrequirement')
                    ->where([
                        ['id_audit_processes', $idAuditProcess],
                        ['id_requirement', $element['id_requirement']],
                        ['answer', $answerNegative]
                    ])->count();
                $actionPlan[$index]['total_tasks'] = $countSub;
            } else $actionPlan[$index]['total_tasks'] = 1;
        }
        return ActionPlansModel::SetActionPlan($actionPlan);
    }
    /**
     * create the obligations
     */
    public static function setActionPlanObligations($idAuditProcess, $idActionRegister, $requirements) {
        // Build a structure
        $today = Carbon::now('America/Mexico_City')->toDateString();
        $obligations = [];
        foreach ($requirements as $index => $element) {
            $requirement = RequirementsModel::getRequirement($element['id_requirement']);
            if($requirement[0]['has_subrequirement'] != 1) {
                $obligations[$index]['title'] = $requirement[0]['no_requirement'].' '.$requirement[0]['requirement'];
                $obligations[$index]['obligation'] = ($requirement[0]['description'] == null) ? '' : $requirement[0]['description'];
                // $obligations[$index]['init_date'] = $today.' 00:00:00';
                $obligations[$index]['id_period'] = $requirement[0]['id_update_period'];
                $obligations[$index]['id_condition'] = $requirement[0]['id_condition'];
                $obligations[$index]['id_action_register'] = $idActionRegister;
                $obligations[$index]['id_audit_processes'] = $idAuditProcess;
                $obligations[$index]['id_obligation_type'] = StatusConstants::OBLIGATION;
            }
            else {
                if($element['id_subrequirement'] != null) {
                    $subrequirement = SubrequirementsModel::getSubrequirement($element['id_subrequirement']);
                    if ($subrequirement[0]['id_update_period'] != null) {
                        $obligations[$index]['title'] = $requirement[0]['no_requirement'].' - '.$subrequirement[0]['no_subrequirement'].' '.$subrequirement[0]['subrequirement'];
                        $obligations[$index]['obligation'] = ($subrequirement[0]['description'] == null) ? '' : $subrequirement[0]['description'];
                        // $obligations[$index]['init_date'] = $today.' 00:00:00';
                        $obligations[$index]['id_period'] = $subrequirement[0]['id_update_period'];
                        $obligations[$index]['id_condition'] = $subrequirement[0]['id_condition'];
                        $obligations[$index]['id_action_register'] = $idActionRegister;
                        $obligations[$index]['id_audit_processes'] = $idAuditProcess;
                        $obligations[$index]['id_obligation_type'] = StatusConstants::OBLIGATION;
                    }
                }
            }
       }
       try {
            $setObliations = ObligationsModel::insert($obligations);
            return StatusConstants::SUCCESS;
       } catch (\Throwable $th) {
            return StatusConstants::ERROR;
       }
    }

    /************************ Quiz View ************************/

    /**
     * Get aplicability answers and questions
     */
    public function auditQuizAspects(Request $request){
        $requestData = $request->all();
        $relationsihps = ['requirement', 'requirement.condition', 'requirement.application_type'];
        $data['requirements'] = EvaluateAuditRequirement::with($relationsihps)
            ->where('id_audit_aspect', $requestData['idAuditAspect'])
            ->whereNull('id_subrequirement')
            ->get()->toArray();
            // ->pluck('id_requirement')->toArray(); 
        // dd($idRequirementsArray);
        // $data['requirements'] = RequirementsModel::GetGroupRequirement($idRequirementsArray);
        $data['risk'] = RiskCategoriesModel::GetRiskCategories(StatusConstants::ACTIVE);
        $attributes = RiskAttributesModel::GetRiskAttributes();
        foreach ($data['risk'] as $k => $c) {
            $data['risk'][$k]['attributes'] = $attributes;
            foreach ($data['risk'][$k]['attributes'] as $i => $a) {
                $data['risk'][$k]['attributes'][$i]['valuations'] = RiskHelpModel::GetRiskHelp($c['id_risk_category'], $a['id_risk_attribute'], StatusConstants::ACTIVE);
            }
        }
        // response
        $status = ( sizeof($data['requirements']) > 0 ) ? StatusConstants::SUCCESS : StatusConstants::ERROR;
        $msg = ( sizeof($data['requirements']) > 0 ) ? 'Iniciando Auditoría' : 'No hay REQUERIMIENTOS para comenzar auditoria';
        $data['status'] = $status;
        $data['msg'] = $msg;
        return response($data);

    }
    /**
     * Get Info Requirement
     */
    public function getRequirement(Request $request, $idRequirement){
        $data = RequirementsModel::GetRequirement($idRequirement);
        return response($data);
    }
    /**
     * Get legal basies
     */
    public function getRequirementBasies(Request $request, $idRequirement){
        $data = RequirementsLegalBasiesModel::GetBasiesByRequirement($idRequirement);
        foreach ($data as $index => $row) {
            $data[$index]['legal_quote'] = $this->setUrlInRichText($row['legal_quote']);
        }
        return response($data);
    }
    /**
     * Get requiremnt recomendations
     */
    public function getRequirementRecomendations(Request $request, $idRequirement){
        $data = RequirementRecomendationsModel::GetRecomendationsByIdRequirement($idRequirement);
        return response($data);
    }
    /**
     * Set Recomendation
     */
    public function setAuditRecomendation(Request $request){
        $requestData = $request->all();
        DB::beginTransaction();
        $SetRecomendation = AuditModel::SetRecomendation($requestData);
        switch ($SetRecomendation) {
            case StatusConstants::SUCCESS:
                DB::commit();
                $status = StatusConstants::SUCCESS;
                $msg = 'Recomendación seleccionada y guardada';
                break;
            case StatusConstants::WARNING:
                DB::rollBack();
                $status = StatusConstants::WARNING;
                $msg = 'Primero debes seleccionar una respuesta';
                break;
            case StatusConstants::ERROR:
                DB::rollBack();
                $status = StatusConstants::ERROR;
                $msg = 'Error al guardar la recomendación intentelo nuevamente';
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
     * Set finding
     */
    public function setFinding(Request $request){
        $requestData = $request->all();
        DB::beginTransaction();
        $setFinding = AuditModel::SetFinding($requestData);
        switch ($setFinding) {
            case StatusConstants::SUCCESS:
                DB::commit();
                $status = StatusConstants::SUCCESS;
                $msg = 'Hallazgo registrado';
                break;
            case StatusConstants::WARNING:
                DB::rollBack();
                $status = StatusConstants::WARNING;
                $msg = 'Primero debes seleccionar una respuesta';
                break;
            case StatusConstants::ERROR:
                DB::rollBack();
                $status = StatusConstants::ERROR;
                $msg = 'Error al registrar hallazgo intentelo nuevamente';
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
     * Set answer evaluate 
     */
    public function setAnswerEvaluate(Request $request){
        $idAuditAspect = $request->input('idAuditAspect');
        $idRequirement = $request->input('idRequirement');
        $idSubrequirement = $request->input('idSubrequirement');
        $complete = $request->input('complete');
        try {
            $toEvaluate = EvaluateAuditRequirement::where([
                ['id_audit_aspect', $idAuditAspect],
                ['id_requirement', $idRequirement],
                ['id_subrequirement', $idSubrequirement],
            ])
            ->update(['complete' => $complete]);
            if ( !is_null($idSubrequirement) ) { 
                EvaluateAuditRequirement::where([
                    ['id_audit_aspect', $idAuditAspect],
                    ['id_requirement', $idRequirement],
                    ['id_subrequirement', null],
                ])
                // ->get()->toArray();
                ->update(['complete' => $complete]);
            }
            $data['status'] = StatusConstants::SUCCESS;
            $data['msg'] = 'Verificación de respuesta exitosa';
            return response($data);
        } catch (\Throwable $th) {
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Error al registrar respuesta intentelo nuevamente';
            return response($data);
        }
    }
    /**
     * Set answer audit
     */
    public function setAnswers(Request $request){
        $requestData = $request->all();
        // Set answer in audit
        $statusAnswer = AuditController::statusAuditByAnswer($requestData);
        return response($statusAnswer);
    }
    /**
     * Set status by answer in manual and automatic audit
     */
    public static function statusAuditByAnswer($requestData){
        DB::beginTransaction();
        if ( $requestData['recursiveResponse'] == 'true' ) {
            $subrequirements = EvaluateAuditRequirement::where([
                ['id_audit_aspect', $requestData['idAuditAspect']],
                ['id_requirement', $requestData['idRequirement']]
            ])
            ->whereNotNull('id_subrequirement')
            ->get()->pluck('id_subrequirement');
            
            foreach ($subrequirements as $key => $id) {
                $requestData['idSubrequirement'] = $id;
                $setAudit = AuditModel::SetAudit($requestData);
                EvaluateAuditRequirement::where([
                    ['id_audit_aspect', $requestData['idAuditAspect']],
                    ['id_requirement', $requestData['idRequirement']],
                    ['id_subrequirement', $requestData['idSubrequirement']],
                ])->update(['complete' => 1]);
                if ($setAudit['status'] != StatusConstants::SUCCESS) {
                    DB::rollBack();
                    $data['status'] = StatusConstants::ERROR;
                    $data['msg'] = 'Error al registrar respuesta intentelo nuevamente';
                    return $data;
                }
            }
        }
        else {
            $setAudit = AuditModel::SetAudit($requestData);
        }
        
        if ($setAudit['status'] != StatusConstants::SUCCESS) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Error al registrar respuesta intentelo nuevamente';
            return $data;
        }
        // Update status contract aspect to evaluating
        $infoAspect = AuditAspectsModel::find($requestData['idAuditAspect']);
        if ($infoAspect->id_status == StatusConstants::NOT_AUDITED) {
            $updateStatus = AuditAspectsModel::UpdateStatusAspectQuiz($requestData['idAuditAspect'], StatusConstants::AUDITING);
            if ($updateStatus['status'] != StatusConstants::SUCCESS) {
                DB::rollBack();
                $data['status'] = $updateStatus['status'];
                $data['msg'] = 'Error al actualizar aspecto intentelo nuevamente';
                return $data;
            }
        }
        // handle risk answer
        if ($setAudit['previousAnswer']) {
            if ($requestData['recursiveResponse'] == 'true') {
                foreach ($subrequirements as $key => $id) {
                    $requestData['id_subrequirement'] = $id;
                    $delete = RiskAnswersModel::DeleteRiskAnswers($requestData['idAuditProcess'], $requestData['idRequirement'], $requestData['idSubrequirement']);
                    if ($delete != StatusConstants::SUCCESS) {
                        DB::rollBack();
                        $data['status'] = $delete;
                        $data['msg'] = 'Error al eliminar respuestas de nivel de riesgo';
                        return $data;
                    }
                }
            }
            else {
                $delete = RiskAnswersModel::DeleteRiskAnswers($requestData['idAuditProcess'], $requestData['idRequirement'], $requestData['idSubrequirement']);
                if ($delete != StatusConstants::SUCCESS) {
                    DB::rollBack();
                    $data['status'] = $delete;
                    $data['msg'] = 'Error al eliminar respuestas de nivel de riesgo';
                    return $data;
                }
            }
            
            if ($requestData['recursiveResponse'] == 'true') {
                foreach ($subrequirements as $key => $id) {
                    $requestData['id_subrequirement'] = $id;
                    $deleteTotal = RiskTotalsModel::DeleteRiskTotal($requestData['idAuditProcess'], $requestData['idRequirement'], $requestData['idSubrequirement']);
                    if ($deleteTotal != StatusConstants::SUCCESS) {
                        DB::rollBack();
                        $data['status'] = $deleteTotal;
                        $data['msg'] = 'Error al eliminar interpretación de nivel de riesgo';
                        return $data;
                    }
                }
            }
            else {
                $deleteTotal = RiskTotalsModel::DeleteRiskTotal($requestData['idAuditProcess'], $requestData['idRequirement'], $requestData['idSubrequirement']);
                if ($deleteTotal != StatusConstants::SUCCESS) {
                    DB::rollBack();
                    $data['status'] = $deleteTotal;
                    $data['msg'] = 'Error al eliminar interpretación de nivel de riesgo';
                    return $data;
                }
            }
        }

        $statusAudit = new StatusAudit();
        $percentages = $statusAudit->calculatePercentAspect($requestData['idAuditAspect'], $requestData['idAuditProcess'], $requestData['idAspect']);

        DB::commit();
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Respuesta Guardada';
        // $data['evaluate'] = $evaluate;
        $data['idAudit'] = $setAudit['idAudit'];
        return $data;
    }
    /**
     * Get answers in audit
     */
    public function getAspectAnswers(Request $request){
        $dataRequest = $request->all();
        $data = Audit::with('requirement')->getRisk()->where('id_audit_aspect', $dataRequest['idAuditAspect'])
            ->whereNull('id_subrequirement')
            ->get()->toArray();
        return response($data);
    }
    /**
     * Get answer in audit
     */
    public function getOnlyAnswer(Request $request){
        $dataRequest = $request->all();
        $data['auditAnswer'] = AuditModel::GetOnlyAnswer($dataRequest);
        $data['recomendations'] = [];
        if (sizeof($data['auditAnswer']) > 0 && $dataRequest['idSubrequirement'] == null) {
            $data['recomendations'] = RequirementRecomendationsModel::GetRecomendationsByIdRequirement($data['auditAnswer'][0]['id_requirement']);
        }
        if (sizeof($data['auditAnswer']) > 0 && $dataRequest['idSubrequirement'] != null) {
            $data['recomendations'] = SubrequirementRecomendationsModel::GetRecomendationsByIdSubrequirement($data['auditAnswer'][0]['id_subrequirement']);
        }
        
        return response($data);
    }
    /**
     * Classify aspect
     */
    public function setClassifyAspect(Request $request)
    {
        $idAuditAspect = $request->input('idAuditAspect');
        $idAuditProcess = $request->input('idAuditProcess');
        $idAspect = $request->input('idAspect');
        // Verify is completed
        $allComplete = EvaluateAuditRequirement::where([
                ['id_audit_aspect', $idAuditAspect],
                ['complete', 0]
            ])->pluck('id_requirement');
        if ( sizeof($allComplete) > 0 ) {
            $data['status'] = StatusConstants::ERROR;
            $data['title'] = 'Aún no es posible Terminar la Auditoría de este aspecto';
            $data['msg'] = 'Clic sobre los Requerimientos marcados en color rojo y completa lo que se pide';
            $data['uncomplete'] = $allComplete;
            return response($data);
        }
        // Classify aspect
        DB::beginTransaction();
        $finalize = AuditAspectsModel::UpdateStatusAspectQuiz($idAuditAspect, StatusConstants::AUDITED);
        if ($finalize['status'] != StatusConstants::SUCCESS) {
            DB::rollback();
            $data['status'] = StatusConstants::ERROR;
            $data['title'] = 'No se puede finalizar el aspecto';
            $data['msg'] = 'Algo salió mal, intentelo nuevamente';
            return response($data);
        }
        $statusAudit = new StatusAudit();
        $percentages = $statusAudit->calculatePercentAspect($idAuditAspect, $idAuditProcess, $idAspect);
        if ($percentages['status'] != StatusConstants::SUCCESS) {
            DB::rollback();
            $data['status'] = StatusConstants::ERROR;
            $data['title'] = 'No se puede finalizar el aspecto';
            $data['msg'] = 'No se puede actualizar el estatus del formulario';
            return response($data);
        }
        DB::commit();
        $aspect = AspectsModel::GetAspect($finalize['id_aspect']);
        $data['status'] = StatusConstants::SUCCESS;
        $data['title'] = 'Finalizado';
        $data['msg'] = 'Auditoria de: '.$aspect[0]['aspect'];
        return response($data);
    }
    /**
     * Delete Audit Aspect 
     */
    public function deleteAuditAspect(Request $request){
        $idAuditMatter = $request->input('idAuditMatter');
        $idAuditAspect = $request->input('idAuditAspect');
        DB::beginTransaction();
        $countAspects = AuditAspectsModel::where('id_audit_matter', $idAuditMatter)->count();
        if ($countAspects == 1) {
            $deleteMatter = AuditMattersModel::DeleteAuditMatter($idAuditMatter);
            switch ($deleteMatter) {
                case StatusConstants::SUCCESS:
                    DB::commit();
                    $status = StatusConstants::SUCCESS;
                    $msg = 'Materia y Aspecto eliminado';
                    break;
                case StatusConstants::ERROR:
                    DB::rollBack();
                    $status = StatusConstants::ERROR;
                    $msg = 'Error al eliminar Materia y aspecto';
                    break;
                case StatusConstants::WARNING:
                    DB::rollBack();
                    $status = StatusConstants::WARNING;
                    $msg = 'No se encontro el registro de Materia';
                    break;
                default:
                    # code...
                    break;
            }
            $data['status'] = $status;
            $data['msg'] = $msg;
            return response($data);
        }
        $delete = AuditAspectsModel::DeleteAuditAspect($idAuditAspect);
        switch ($delete) {
            case StatusConstants::SUCCESS:
                DB::commit();
                $status = StatusConstants::SUCCESS;
                $msg = 'Aspecto eliminado';
                break;
            case StatusConstants::ERROR:
                DB::rollBack();
                $status = StatusConstants::ERROR;
                $msg = 'Error al eliminar aspecto';
                break;
            case StatusConstants::WARNING:
                DB::rollBack();
                $status = StatusConstants::WARNING;
                $msg = 'No se encontro el registro';
                break;
            default:
                # code...
                break;
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return response($data);
    }

    /************************ Sub Quiz View ************************/

    /**
     * Get Subrequirements
     */
    public function getSubrequirements(Request $request){
        $idAuditProcess = $request->input('idAuditProcess');
        $idApplicationType = $request->input('idApplicationType');
        $idAuditAspect = $request->input('idAuditAspect');
        $idRequirement = $request->input('idRequirement');
        $idSubrequirementsArray = EvaluateAuditRequirement::where([
            ['id_audit_aspect', $idAuditAspect],
            ['id_requirement', $idRequirement]
        ])->whereNotNull('id_subrequirement')->pluck('id_subrequirement')->toArray();
        $data['subReq'] = SubrequirementsModel::GetSubrequirementsAudit($idSubrequirementsArray);
        $data['risk'] = RiskCategoriesModel::GetRiskCategories(StatusConstants::ACTIVE);
        $attributes = RiskAttributesModel::GetRiskAttributes();
        foreach ($data['risk'] as $k => $c) {
            $data['risk'][$k]['attributes'] = $attributes;
            foreach ($data['risk'][$k]['attributes'] as $i => $a) {
                $data['risk'][$k]['attributes'][$i]['valuations'] = RiskHelpModel::GetRiskHelp($c['id_risk_category'], $a['id_risk_attribute'], StatusConstants::ACTIVE);
            }
        }
        return response($data);
    }
    /**
     * Get subrequirement for manual and automatic audit
     */
    public static function processGetSubrequirement($idRequirement, $idAuditProcess, $idApplicationType){
        // Get data requirement
        $infoRequirement = RequirementsModel::GetRequirement($idRequirement);
        // Get question positives in aspect
        $questionAplicability = AplicabilityModel::GetQuestionInAspect($idAuditProcess, $infoRequirement[0]['id_aspect']);
        $idQuestionArray = [];
        foreach ($questionAplicability as $question) {
            array_push($idQuestionArray, $question['id_question']);
        }
        // Get relation questions with subrequeriments
        $subDistinct = QuestionRequirementsModel::GetSubrequirements($idQuestionArray, $idRequirement, $idApplicationType);
        $idSubequirementArray = [];
        foreach ($subDistinct as $subrequirement) {
            array_push($idSubequirementArray, $subrequirement['id_subrequirement']);
        }
        // Get state subrequirements if application type is STATE
        if ($idApplicationType == StatusConstants::STATE) {
            $subState = SubrequirementsModel::GetSubrequirementsByType($idRequirement, 10);
            foreach ($subState as $sub) {
                array_push($idSubequirementArray, $sub['id_subrequirement']);
            }
        }
        // Get subrequiremnts for audit
        $data = SubrequirementsModel::GetSubrequirementsAudit($idSubequirementArray);
        return $data;
    }
    /**
     * Get Info Requirement
     */
    public function getSubrequirement(Request $request, $idSubrequirement){
        $data = SubrequirementsModel::GetSubrequirement($idSubrequirement);
        return response($data);
    }
    /**
     * Get legal basies on subrequiremnts
     */
    public function getSubrequirementBasies(Request $request, $idSubrequirement){
        $data = SubrequirementsLegalBasiesModel::GetBasiesBySubrequirement($idSubrequirement);
        foreach ($data as $index => $row) {
            $data[$index]['legal_quote'] = $this->setUrlInRichText($row['legal_quote']);
        }
        return response($data);
    }
    /**
     * Get subrequiremnt recomendations
     */
    public function getSubrequirementRecomendations(Request $request, $idSubrequirement){
        $data = SubrequirementRecomendationsModel::GetRecomendationsByIdSubrequirement($idSubrequirement);
        return response($data);
    }
    /**
     * Get subrequirements answers
     */
    public function getSubrequirementAnswers(Request $request){
        $requestData = $request->all();
        $data = Audit::getRisk()->where('id_audit_aspect', $requestData['idAuditAspect'])
        ->where('id_requirement', $requestData['idRequirement'])
        ->whereNotNull('id_subrequirement')
        ->get()->toArray();
        // $data = AuditModel::GetSubrequirementAnswers($requestData);
        // foreach ($data as $i => $e) {
        //     $data[$i]['risks'] = RiskAnswersModel::GetRiskAnswers($e['id_audit'], true);
        //     $data[$i]['riskTotal'] = RiskTotalsModel::GetData($e['id_audit_processes'], $e['id_requirement'], $e['id_subrequirement']);
        //     foreach ($data[$i]['riskTotal'] as $k => $v) {
        //         $data[$i]['riskTotal'][$k]['interpretation'] = AuditController::textRiskLevel($v['id_risk_category'], $v['total']);
        //     }
        // }
        return response($data);
    }
    /**
     * Evaluate subrequirement answer
     */
    public function evaluateSubrequirementsAnswers(Request $request){
        $requestData = $request->all();
        $evaluate = new Evaluate();
        $requirementAnswer = $evaluate->evaluateRequirementAnswer($requestData);
        $data['riskStatus'] = $requirementAnswer['riskStatus'];
        if ($requirementAnswer['riskStatus'] == StatusConstants::ERROR && $requestData['evaluateRisk'] == 'true') {
            $data['statusAnswer']['title'] = 'Nivel de Riesgo incompleto';
            $data['statusAnswer']['msg'] = 'Completa todas las evaluaciones de los botones en gris';
        }
        else {
            $setDataAnswer['idRequirement'] = $requestData['idRequirement'];
            $setDataAnswer['idContract'] = $requestData['idContract'];
            $setDataAnswer['idAuditProcess'] = $requestData['idAuditProcess'];
            $setDataAnswer['idAspect'] = $requestData['idAspect'];
            $setDataAnswer['idAuditAspect'] = $requestData['idAuditAspect'];
            $setDataAnswer['idSubrequirement'] = null;
            $setDataAnswer['answer'] = $requirementAnswer['answer'];
            $setDataAnswer['recursiveResponse'] = false;
            // Set answer in audit
            $statusAnswer = AuditController::statusAuditByAnswer($setDataAnswer);
            $data['statusAnswer'] = $statusAnswer;
            $data['answer'] = $requirementAnswer['answer'];
            $data['idRequirement'] = $requestData['idRequirement'];
        }
        return response($data);
    }
    /**
     * Get report
     */
    public function getReport($idAuditRegister)
    {
        $data = AuditRegister::findOrFail($idAuditRegister)->toArray();
        $evalRisk = ProcessesModel::find($data['id_audit_processes'])->toArray();
        $corporate = CorporatesModel::GetCorporate($data['id_corporate'])[0];
        $address = AddressesModel::GetAddressType($data['id_corporate'], 1)[0];
        $mtts = AuditMattersModel::GetContractMatters($idAuditRegister);
        $today = Carbon::now('America/Mexico_City')->format('d/m/Y');
        $todayName = Carbon::now('America/Mexico_City')->format('d-m-Y');
        $data['allowRisk'] = $evalRisk['evaluate_risk'];
        /*** Dashboard ***/
        $data['dashboard']['corp_tradename'] = $corporate['corp_tradename'];
        $data['dashboard']['corp_trademark'] = $corporate['corp_trademark'];
        $data['dashboard']['rfc'] = $corporate['rfc'];
        $data['dashboard']['status'] = $corporate['status'];
        $data['dashboard']['industry'] = $corporate['industry'];
        $data['dashboard']['street'] = $address['street'];
        $data['dashboard']['suburb'] = $address['suburb'];
        $data['dashboard']['city'] = $address['city'];
        $data['dashboard']['state'] = $address['state'];
        $data['dashboard']['country'] = $address['country'];
        $data['dashboard']['date'] = $today;
        $data['dashboard']['audit_global'] = AuditRegistersModel::GetAuditRegisterPercent($idAuditRegister)['total'];
        $data['dashboard']['count_global'] = ActionPlansModel::where('id_audit_processes', $data['id_audit_processes'])->count();
        $idUsers = AuditModel::select('id_user')->where('id_audit_processes', $data['id_audit_processes'])->distinct()->get()->toArray();
        $users = [];
        foreach ($idUsers as $u) {
            $tempU = User::GetUserInfo($u['id_user']);
            array_push($users, $tempU[0]['complete_name']);
        }
        $data['dashboard']['users'] = $users; 
        $matters = [];
        foreach ($mtts as $i => $m) {
            $temp = [];
            $temp['id_matter'] = $m['id_matter'];
            $temp['matter'] = $m['matter'];
            $temp['total'] = $m['total'];
            $temp['color'] = $m['color'];
            $temp['img'] = $m['image'];
            $aspects = AuditAspectsModel::GetAuditedAspectsByMatter($m['id_audit_matter'], StatusConstants::FINISHED_AUDIT);
            $aspectArray = [];
            foreach ($aspects as $j => $a) {
                array_push($aspectArray, $a['id_aspect']);
            }
            $temp['count'] = ActionPlansModel::where('id_audit_processes', $data['id_audit_processes'])->whereIn('id_aspect', $aspectArray)->count();
            array_push($matters, $temp);
        }
        $data['dashboard']['matters'] = $matters;
        /*** Audit ***/
        $auditArray = [];
        foreach ($mtts as $i => $m) {
            $temp = [];
            $temp['matter'] = $m['matter'];
            $temp['color'] = $m['color'];
            $temp['total'] = $m['total'];
            $temp['aspects'] = AuditAspectsModel::GetAuditedAspectsByMatter($m['id_audit_matter'], StatusConstants::FINISHED_AUDIT);
            array_push($auditArray, $temp);
        }
        $data['audit'] = $auditArray;
        /*** Action Plan ***/
        $actionArray = [];
        $asp = ActionPlansModel::GetAspectsByIdAuditProcess($data['id_audit_processes']);
        foreach ($mtts as $i => $m) { 
            $tempG = [];
            $tempG['matter'] = $m['matter'];
            $tempG['color'] = $m['color'];
            $aspectsData = [];
            $countTemp = 0;
            $report = [];
            foreach ($asp as $k => $a) {
                if ( $m['id_matter'] == $a['id_matter'] ) {
                    $a['count'] = ActionPlansModel::where('id_audit_processes', $data['id_audit_processes'])->where('id_aspect', $a['id_aspect'])->count();
                    $countTemp = $countTemp + $a['count'];
                    $req = ActionPlansModel::GetAspectsForDashboard($data['id_audit_processes'], $a['id_aspect']);
                    $a['action'] = [];
                    foreach ($req as $i => $r) {
                        $temp = [];
                        $temp['num'] = $r['no_requirement'];
                        $temp['description'] = $r['description'];
                        $temp['finding'] = $r['finding'];
                        $temp['risk'] = RiskTotalsModel::GetData($data['id_audit_processes'], $r['id_requirement'], null);
                        foreach ($temp['risk'] as $x => $y) {
                            $temp['risk'][$x]['interpretation'] = AuditController::textRiskLevel($y['id_risk_category'], $y['total']);
                        }
                        $detailReq['type'] = 'req';
                        $detailReq['id'] = $r['id_requirement'];
                        $detailReq['name'] = 'Requerimiento '.$r['no_requirement'];
                        $encodeReq = base64_encode(json_encode($detailReq));
                        $temp['legal'] = asset('details/basis').'/'.$encodeReq;
                        $basiesR = RequirementsLegalBasiesModel::GetBasiesByRequirement($r['id_requirement']);
                        $quoteR = '';
                        foreach ($basiesR as $h => $br) {
                            $quoteR .= 'Basado de '.$br['guideline'].': '.$br['legal_basis'].'<br>';
                        }
                        $temp['legal_name'] = $quoteR;
                        array_push($a['action'], $temp);
                        if ($r['has_subrequirement'] == 1) {
                            $sub = ActionPlansModel::GetSubrequirementsByIdAuditProcess($data['id_audit_processes'], $r['id_requirement']);
                            foreach ($sub as $j => $s) {
                                $tmp = [];
                                $tmp['num'] = $r['no_requirement'].' Subrequerimiento '.$s['no_subrequirement'];
                                $tmp['description'] = $s['description'];
                                $tmp['finding'] = $s['finding'];
                                $tmp['risk'] = RiskTotalsModel::GetData($data['id_audit_processes'], $s['id_requirement'], $s['id_subrequirement']);
                                foreach ($tmp['risk'] as $w => $z) {
                                    $tmp['risk'][$w]['interpretation'] = AuditController::textRiskLevel($z['id_risk_category'], $z['total']);
                                }
                                $detailSub['type'] = 'sub';
                                $detailSub['id'] = $r['id_requirement'];
                                $detailSub['name'] = 'Requerimiento '.$r['no_requirement'].' Subrequerimiento '.$s['no_subrequirement'];
                                $encodeSub = base64_encode(json_encode($detailSub));
                                $tmp['legal'] = asset('details/basis').'/'.$encodeSub;
                                $basiesS = SubrequirementsLegalBasiesModel::GetBasiesBySubrequirement($s['id_subrequirement']);
                                $quoteS = '';
                                foreach ($basiesS as $k => $bs) {
                                    $quoteS .= 'Basado de '.$bs['guideline'].': '.$bs['legal_basis'].'<br>';
                                }
                                $tmp['legal_name'] = $quoteS;
                                array_push($a['action'], $tmp);
                            }
                        }
                    }
                    array_push($aspectsData, $a);
                   
                }
            }
            $tempG['aspects'] = $aspectsData;
            $tempG['total'] = $countTemp;
            array_push($actionArray, $tempG);
        }
        $data['action'] = $actionArray;
        return Excel::download(new AuditReportExcelOld($data), 'Reporte de auditoría - '.$corporate['corp_trademark'].' - '.$todayName.'.xlsx');
    }

    /**
     * Get report progress
     */
    public function getReportAuditProgress($idAuditRegister){
        // info audit process by id audit register
        $auditRegister = AuditRegistersModel::GetAuditRegister($idAuditRegister);
        $auditors = AuditRegistersModel::getAuditosByIdAuditRegister($auditRegister[0]['id_audit_register']);
        $contractAspect = AuditAspectsModel::GetAspectsNameByAuditProcesses($auditRegister[0]['id_audit_processes'])->toArray();
        $aspects = AuditAspectsModel::GetAspectsByAuditProcesses($auditRegister[0]['id_audit_processes']);
        $auditAnswers = AuditModel::where('id_audit_processes', $auditRegister[0]['id_audit_processes'])->get()->toArray();
        // corporate
        $address = AddressesModel::GetAddressType($auditRegister[0]['id_corporate'], StatusConstants::PHYSICAL)[0];
        // consult date
        $today = Carbon::now('America/Mexico_City')->format('d/m/Y');
        $todayName = Carbon::now('America/Mexico_City')->format('d-m-Y');
        // Header document
        $data['audit_processes'] = $auditRegister[0]['audit_processes'];
        $data['corp_tradename'] = $auditRegister[0]['corp_tradename'];
        $data['corp_trademark'] = $auditRegister[0]['corp_trademark'];
        $data['rfc'] = $auditRegister[0]['rfc'];
        $data['status'] = $auditRegister[0]['status'];
        $data['industry'] = $auditRegister[0]['industry'];
        $data['scope'] = $auditRegister[0]['scope'];
        $data['street'] = $address['street'];
        $data['suburb'] = $address['suburb'];
        $data['city'] = $address['city'];
        $data['state'] = $address['state'];
        $data['country'] = $address['country'];
        $data['date'] = $today;
        $data['auditors'] = implode(',', array_map(function($entry) {
            return $entry['responsible'];
        }, $auditors));
        $data['aspects_evaluated'] = implode(',', array_map(function($entry) {
            return $entry['aspect'];
        }, $contractAspect));
        // Get Requirements
        $evaluate = new Evaluate();
        $requeriments = [];
        foreach ($aspects as $asp){
            $arrayReq = EvaluateAuditRequirement::where('id_audit_aspect', $asp['id_audit_aspect'])
            ->whereNull('id_subrequirement')->pluck('id_requirement')->toArray();
            $getReq = RequirementsModel::GetGroupRequirement($arrayReq);
            foreach ($getReq as $reqKey => $r) {
                $arraySub = EvaluateAuditRequirement::where([
                    ['id_audit_aspect', $asp['id_audit_aspect']],
                    ['id_requirement', $r['id_requirement']]
                ])->whereNotNull('id_subrequirement')->pluck('id_subrequirement')->toArray();
                if ( sizeof($arraySub) != 0 ) {
                    $getReq[$reqKey]['subrequirements'] = SubrequirementsModel::GetGroupSubrequirement($arraySub);
                } else $getReq[$reqKey]['subrequirements'] = [];
            }
            foreach ($getReq as $req){
                array_push($requeriments, [
                    'id_aspect' => $req['id_aspect'],
                    'aspect' => $req['aspect'],
                    'id_requirement' => $req['id_requirement'],
                    'id_subrequirement' => null,
                    'no_requirement' => $req['no_requirement'],
                    'requirement' => $req['requirement'],
                    'description' => $req['description'],
                    'finding' => 'Pendiente',
                    'has_subrequirement' => $req['has_subrequirement'],
                    'response' => ''
                ]);
                foreach ($req['subrequirements'] as $subKey => $s) {
                    array_push($requeriments, [
                        'id_aspect' => $req['id_aspect'],
                        'aspect' => $req['aspect'],
                        'id_requirement' => $req['id_requirement'],
                        'id_subrequirement' => $s['id_subrequirement'],
                        'no_requirement' => $req['no_requirement'].' '.$s['no_subrequirement'],
                        'requirement' => $req['requirement'],
                        'description' => $req['description'],
                        'finding' => 'Pendiente',
                        'has_subrequirement' => 0,
                        'response' => ''
                    ]);
                }
            }
        }
        // Totals count responses
        $cotunRequerimentMeets = 0;
        $cotunRequerimentFails = 0;
        $cotunRequerimentNotApply = 0;
        $answersArray = [];
        foreach ($requeriments as $key => $requeriment) {
            if ($requeriment['no_requirement'] == 'AMB-IA-04 ' || $requeriment['no_requirement'] == 'AMB-IA-04') {
                array_push($answersArray, $requeriment);
            }
            $requeriments[$key]['response'] = 'Pendiente';
            foreach ($auditAnswers as $rowAnswers) {
                if($requeriment['id_requirement'] == $rowAnswers['id_requirement'] && $requeriment['id_subrequirement'] == $rowAnswers['id_subrequirement']){
                    switch ($rowAnswers['answer']){
                        case 0:
                            $cotunRequerimentFails++;
                            $requeriments[$key]['response'] = 'No cumple';
                            //$requeriments[$key]['finding'] = ( is_null($rowAnswers['finding'])) ? 'Aún sin especificar' : $rowAnswers['finding'];
                            if($rowAnswers['finding'] == null && $requeriment['has_subrequirement'] == 0){
                                $requeriments[$key]['finding'] = 'Aún sin especificar';
                            }else if($rowAnswers['finding'] == null && $requeriment['has_subrequirement'] ==1){
                                $requeriments[$key]['finding'] = 'Revise los comentarios en los subrequerimientos';
                            }else{
                                $requeriments[$key]['finding'] = $rowAnswers['finding'];
                            }
                            break;
                        case 1:
                            $cotunRequerimentMeets++;
                            $requeriments[$key]['response'] = 'Cumple';
                            $requeriments[$key]['finding'] = 'N/A';
                            break;
                        case 2:
                            $cotunRequerimentNotApply++;
                            $requeriments[$key]['response'] = 'N/A';
                            $requeriments[$key]['finding'] = 'N/A';
                            break;
                    }
                }
            }
        }
        $data['requeriments'] = $requeriments;
        $data['totals'] = [
            'total_audited_requirements' => $cotunRequerimentMeets + $cotunRequerimentFails + $cotunRequerimentNotApply,
            'total_requeriment_meets' => $cotunRequerimentMeets,
            'total_requeriment_fails' => $cotunRequerimentFails,
            'total_not_apply' => $cotunRequerimentNotApply,
            'total_unaudited_requirements' => count($requeriments) - ($cotunRequerimentMeets + $cotunRequerimentFails + $cotunRequerimentNotApply),
            'total_requerements' => count($requeriments)
        ];

        return Excel::download(new AuditProgressReportExcel($data),
            'Reporte de auditoría - '.$auditRegister[0]['corp_trademark'].' - '.$todayName.'.xlsx');
    }

    /**
     * Get report progress
     */
    public function getReportAuditDocument($idAuditRegister){
        // info audit process by id audit register
        $auditRegister = AuditRegistersModel::GetAuditRegister($idAuditRegister);
        $leaderAuditor = AuditorModel::GetAuditorsForAP($auditRegister[0]['id_audit_processes'], [StatusConstants::LEADER]);
        $auditors = AuditorModel::GetAuditorsForAP($auditRegister[0]['id_audit_processes'], [StatusConstants::NO_LEADER]);
        $contractAspect = AuditAspectsModel::GetAspectsNameByAuditProcesses($auditRegister[0]['id_audit_processes'])->toArray();
        $aspects = AuditAspectsModel::GetAspectsByAuditProcesses($auditRegister[0]['id_audit_processes']);
        $auditAnswers = AuditModel::where('id_audit_processes', $auditRegister[0]['id_audit_processes'])->get()->toArray();
        // corporate
        $address = AddressesModel::GetAddressType($auditRegister[0]['id_corporate'], StatusConstants::PHYSICAL)[0];
        // Header document
        $data['corpTradename'] = $auditRegister[0]['corp_tradename'];
        $data['corpTrademark'] = $auditRegister[0]['corp_trademark'];
        $data['address'] = $address['street'].', '.$address['suburb'].', '.$address['city'].', '.$address['state'];
        $data['date'] = Carbon::now('America/Mexico_City')->format('d/m/Y');
        $data['auditProcesses'] = $auditRegister[0]['audit_processes'];
        $data['scope'] = $auditRegister[0]['scope'];
        $data['leaderAuditor'] = $leaderAuditor[0]['complete_name'];
        $data['industry'] = $auditRegister[0]['industry'];
        $data['auditors'] = implode(',', array_map(function($entry) {
            return $entry['complete_name'];
        }, $auditors));
        $data['aspects'] = implode(',', array_map(function($entry) {
            return $entry['aspect'];
        }, $contractAspect));
        // Get Requirements
        $requirements = [];
        $callbackUnique = function ($item) {
            return $item['document'].$item['id_application_type'];
        };
        foreach ($aspects as $asp){
            $arrayReq = EvaluateAuditRequirement::where('id_audit_aspect', $asp['id_audit_aspect'])
                ->whereNull('id_subrequirement')->pluck('id_requirement')->toArray();
            $uniqueDocReq = RequirementsModel::whereIn('id_requirement', $arrayReq)->whereNotNull('document')->get();
            $arrayIdsReqFilter = $uniqueDocReq->unique($callbackUnique)->pluck('id_requirement');
            $arrayIdsSubFilter = [];
            foreach ($arrayIdsReqFilter as $req) {
                if ( isset($arraySub[$req])) {
                    $arraySub = EvaluateAuditRequirement::where([
                        ['id_audit_aspect', $asp['id_audit_aspect']],
                        ['id_requirement', $req]
                    ])->whereNotNull('id_subrequirement')->pluck('id_subrequirement')->toArray();
                    $uniqueDocSub = SubrequirementsModel::whereIn('id_subrequirement', $arraySub)
                        ->whereNotNull('document')->get();
                    if ( sizeof($uniqueDocSub) > 0) {
                        $tempIdsSubFilter = $uniqueDocSub->unique($callbackUnique)->pluck('id_subrequirement');
                        $arrayIdsSubFilter[$req] = $tempIdsSubFilter;
                    }
                }
            }
            // structure row per aspect
            $requirementsTemp = [];
            $reqData = RequirementsModel::GetGroupRequirement($arrayIdsReqFilter);
            foreach ($reqData as $key => $req) {
                array_push($requirementsTemp, [
                    'id_matter' => $req['id_matter'],
                    'matter' => $req['matter'],
                    'id_aspect' => $req['id_aspect'],
                    'aspect' => $req['aspect'],
                    'applicationType' => $req['application_type'],
                    'document' => $req['document'],
                    'no_requirement' => $req['no_requirement'],
                ]);
                if ( isset($arrayIdsSubFilter[$req['id_requirement']]) ) {
                    $subData = SubrequirementsModel::GetGroupSubrequirement($arrayIdsSubFilter[$req['id_requirement']]);
                    foreach ($subData as $sub) {
                        array_push($requirementsTemp, [
                            'id_matter' => $req['id_matter'],
                            'matter' => $req['matter'],
                            'id_aspect' => $req['id_aspect'],
                            'aspect' => $req['aspect'],
                            'applicationType' => $sub['application_type'],
                            'document' => $sub['document'],
                            'no_requirement' => "{$req['no_requirement']} - {$sub['no_subrequirement']}",
                        ]);
                    }
                }
                
            }
            $uniquePerAspect = collect($requirementsTemp)->unique(function ($item) {
                return $item['document'].$item['applicationType'];
            })->sortBy('matter')->sortBy('aspect')->toArray();
            $requirements = array_merge($requirements, $uniquePerAspect);
        }
        $data['requirements'] = $requirements;
        $today = Carbon::now('America/Mexico_City')->format('d-m-Y');
        $documentName = 'Reporte de documentos - '.$data['corpTrademark'].' - '.$data['auditProcesses'].' - '.$today.'.xlsx';
        $documentNameValid = str_replace('/', '-', $documentName);
        $documentNameValid = str_replace('\'', '-', $documentNameValid);
        return Excel::download(new AuditDocumentsReportExcel($data), $documentNameValid);
    }

    /********** Risk **********/

    public function getDataRisk(Request $request){
        $dataRequest = $request->all();

        if ($dataRequest['origin'] == 'probabilities') {
            $data['items'] = RiskProbabilitiesModel::GetRiskProbabilities($dataRequest['idRiskCategory'], 1);
            if ( sizeof($data['items']) > 0 ) {
                foreach ($data['items'] as $j => $p) {
                    $data['items'][$j]['specifications'] = RiskSpecificationsModel::GetSpecifications($p['id_risk_probability'], NULL, NULL, StatusConstants::ACTIVE);
                }
            }
            $data['subtitle'] = 'Probabilidades';
        }
        if ($dataRequest['origin'] == 'exhibitions') {
            $data['items'] = RiskExhibitionsModel::GetRiskExhibitions($dataRequest['idRiskCategory'], 1);
            if ( sizeof($data['items']) > 0 ) {
                foreach ($data['items'] as $k => $e) {
                    $data['items'][$k]['specifications'] = RiskSpecificationsModel::GetSpecifications(NULL, $e['id_risk_exhibition'], NULL, StatusConstants::ACTIVE);
                }
            }
            $data['subtitle'] = 'Exposiciones';
        }
        if ($dataRequest['origin'] == 'consequences') {
            $data['items'] = RiskConsequencesModel::GetRiskConsequences($dataRequest['idRiskCategory'], 1);
            if ( sizeof($data['items']) > 0 ) {
                foreach ($data['items'] as $l => $c) {
                    $data['items'][$l]['specifications'] = RiskSpecificationsModel::GetSpecifications(NULL, NULL, $c['id_risk_consequence'], StatusConstants::ACTIVE);
                }
            }
            $data['subtitle'] = 'Consecuencias';
        }

        $data['title'] = $dataRequest['riskCategory'];
        $data['origin'] = $dataRequest['origin'];
        $data['answer'] = RiskAnswersModel::GetAnswer($dataRequest);

        return response($data);
    }

    /**
     * Set answer risk level by register in audit
     */
    public function setAnswerRisk(Request $request) {
        $requestData = $request->all();
        DB::beginTransaction();
        $set = RiskAnswersModel::SetAnswer($requestData);
        if ($set != StatusConstants::SUCCESS) {
            DB::rollBack();
            $data['status'] = $set;
            $data['msg'] = 'Error al registrar respuesta de nivel de riesgo';
            return $data;
        }
        $data['riskTotal'] = null;
        $withSub = !is_null($requestData['idSubrequirement']);
        $answerCategory = RiskAnswersModel::GetDataByCategoryInAnswer($requestData, $withSub);
        $countAttributes = RiskAttributesModel::count();
        if (sizeof($answerCategory) == $countAttributes) {
            $total = 1;
            foreach ($answerCategory as $v) {
                $total *= $v['answer'];
            }
            $setTotal = RiskTotalsModel::SetTotal($total, $requestData);
            if ($setTotal['status'] != StatusConstants::SUCCESS) {
                DB::rollBack();
                $data['status'] = $setTotal['status'];
                $data['msg'] = 'Error al calcular el nivel de riesgo';
                return $data;
            }
            $interpretation = AuditController::textRiskLevel($requestData['idRiskCategory'], $setTotal['riskTotal']);
            $data['riskTotal'] = $interpretation;
        }
        DB::commit();
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Respuesta Guardada';
        $data['totalAnswers'] = RiskAnswersModel::where('id_audit', $requestData['idAudit'])->count();
        return response($data);
    }
    /** 
     * Set textual value 
    */
    public static function textRiskLevel($idRiskCategory, $riskTotal){
        $value = RiskInterpretationsModel::GetInterpretationByRange($idRiskCategory, $riskTotal);
        if ( sizeof($value) > 0 ) {
            $data = $value[0]['interpretation'];
        }
        else {
            $max = RiskInterpretationsModel::where('id_risk_category', $idRiskCategory)->max('interpretation_min');
            $value = RiskInterpretationsModel::GetInterpretationByMinValue($idRiskCategory, $max);
            $data = $value[0]['interpretation'];
        }
        return $data;
    }
}
