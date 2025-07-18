<?php

namespace App\Http\Controllers\Audit;

use App\Exports\AplicabilityReportExcel;
use App\Http\Controllers\Controller;
use App\Models\Audit\AuditModel;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Classes\StatusConstants;
use App\Classes\StatusAplicability;
use App\Classes\StatusAudit;
use App\Classes\Evaluate;
use App\Models\Admin\CustomersModel;
use App\Models\Admin\CorporatesModel;
use App\Models\Catalogues\StatusModel;
use App\Models\Catalogues\MattersModel;
use App\Models\Catalogues\AspectsModel;
use App\Models\Catalogues\QuestionsModel;
use App\Models\Catalogues\AnswersQuestionModel;
use App\Models\Catalogues\QuestionLegalBasiesModel;
use App\Models\Admin\ProcessesModel;
use App\Models\Audit\AplicabilityRegistersModel;
use App\Models\Audit\AplicabilityModel;
use App\Models\Audit\AplicabilityAnswersModel;
use App\Models\Audit\ContractMattersModel;
use App\Models\Audit\ContractAspectsModel;
use App\Models\Admin\ContractsModel;
use App\Models\Admin\AddressesModel;
use App\Models\Audit\AuditRegistersModel;
use App\Models\Audit\AuditMattersModel;
use App\Models\Audit\AuditAspectsModel;
use App\Models\Audit\EvaluateRequirementModel;
use App\Http\Controllers\Audit\AuditController;
use App\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Catalogues\AnswerQuestionsDepedencyModel;
use App\Traits\HelpersQuestionTrait;
use App\Classes\CreateAuditObligation;
use App\Traits\V2\RichTextTrait;
use App\Models\V2\Audit\AplicabilityRegister;
use App\Models\V2\Catalogs\Question;

class AplicabilityController extends Controller
{
    use HelpersQuestionTrait, RichTextTrait;
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
     * Main view in aplicability module
     */
    public function index(Request $request, $idAuditProcess, $idAplicabilityRegister){
        $groupStatus = 2; // group status basic
        $idDecode = $idAplicabilityRegister;
        $infoAR = AplicabilityRegistersModel::GetAplicabilityRegister($idDecode);
        $textScope = ($infoAR[0]['id_scope'] == 2) ? $infoAR[0]['scope'].': '.$infoAR[0]['specification_scope'] : $infoAR[0]['scope'];
        $arrayData['customer'] = $infoAR[0]['cust_trademark'];
        $arrayData['corporate'] = $infoAR[0]['corp_tradename'];
        $arrayData['status'] = StatusModel::getStatusByGroup($groupStatus);
        $arrayData['idProfileType'] = Session::get('profile')['id_profile_type'];
        $arrayData['idAplicabilityRegister'] = $idDecode;
        $arrayData['idContractAR'] = $infoAR[0]['id_contract'];
        $arrayData['idAuditProcesses'] = $infoAR[0]['id_audit_processes'];
        $arrayData['auditProcesses'] = $infoAR[0]['audit_processes'];
        $arrayData['scopeAuditProcesses'] = $textScope;
        $view = 'aplicability.customerView.main';
        return view($view, $arrayData);
    }

    /**
     * Datatables corporates registers to aplicability
     */
    public function aplicabilityRegistersDT(Request $request){
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $fIdStatus = $request->input('fIdStatus');
        $fIdCustomer = $request->input('fIdCustomer');
        $fIdCorporate = $request->input('fIdCorporate');
        $data = AplicabilityRegistersModel::AplicabilityRegistersDT($page, $rows, $search, $draw, $order,
                $fIdStatus, $fIdCustomer, $fIdCorporate);
        return response($data);
    }
    /**
     * Get Matters in contracts
     */
    public function aplicabilityRegistersMatters(Request $request, $idAplicabilityRegister){
        $contractAspect = ContractMattersModel::GetContractMatters($idAplicabilityRegister);
        return response($contractAspect);
    }
    /**
     * Get Matters in contracts
     */
    public function aplicabilityRegistersMatterProgress(Request $request, $idAplicabilityRegister){
        $getData = ContractMattersModel::GetContractMatters($idAplicabilityRegister);
        $statusAplicability = new StatusAplicability();
        $data['matters'] = [];
        foreach ($getData as $cm) {
            array_push($data['matters'], $statusAplicability->updateStatusMatter($cm['id_contract_matter']));
        }
        $data['aplicability'] = $statusAplicability->updateStatusAplicabilityRegisters($idAplicabilityRegister);
        return response($data);
    }
    /**
     *Get aspects by matter in contract
     */
    public function aplicabilityRegistersAspectsDT(Request $request){
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $idContractMatter = $request->input('idContractMatter');
        $filterIdStatus = $request->input('filterIdStatus');
        $idAuditProcess = $request->input('idAuditProcess');
        $contractAspects = ContractAspectsModel::GetContractAspectsDT($page, $rows, $search, $draw, $order, $idContractMatter, $filterIdStatus, $idAuditProcess);
        return response($contractAspects);
    }
    /**
     * Delete aspects
     */
    public function deleteContractAspect(Request $request){
        $idContractMatter = $request->input('idContractMatter');
        $idContractAspect = $request->input('idContractAspect');
        $countAspects = ContractAspectsModel::where('id_contract_matter', $idContractMatter)->count();
        if ($countAspects == 1) {
            $deleteMatter = ContractMattersModel::DeleteContractMatter($idContractMatter);
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
        $delete = ContractAspectsModel::DeleteContractAspect($idContractAspect);
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
    /**
     * Get questions by aspects
     */
    public function getQuestionsByAspect(Request $request){
        $requestData = $request->all();        
        $infoProcess = ProcessesModel::GetProcesses($requestData['idAuditProcess'])[0];
        $infoAddress = AddressesModel::GetAddressType($infoProcess['id_corporate'], StatusConstants::PHYSICAL);
        if ( !isset($infoAddress[0]['id_state']) && !isset($infoAddress[0]['id_city']) ) {
            $data['status'] = StatusConstants::ERROR;
            return response($data);
        }
        $questions = [];
        $dependency = [];
        $allowedAll = [];
        $blockedAll = [];
        if ($requestData['idStatus'] == StatusConstants::NOT_CLASSIFIED || $requestData['idStatus'] == StatusConstants::EVALUATING) {
            $QF = QuestionsModel::GetQuestionsByAspect($requestData['idForm'], null, null);
            $QS = QuestionsModel::GetQuestionsByAspect($requestData['idForm'], $infoAddress[0]['id_state'], null);
            $QL = QuestionsModel::GetQuestionsByAspect($requestData['idForm'], $infoAddress[0]['id_state'], $infoAddress[0]['id_city']);
            $mix = array_merge($QF, $QS, $QL);
            $answers = AplicabilityModel::where('id_audit_processes', $requestData['idAuditProcess'])
                ->where('id_contract_aspect', $request['idContractAspect'])->get();
            $questions = QuestionsModel::whereIn('id_question', $mix)->orderBy('id_question_type', 'ASC')->orderBy('order', 'ASC')->get()->toArray();
            foreach ($questions as $key => $q) {
                $questions[$key]['answers_question'] = AnswersQuestionModel::GetAnswersQuestionByQuestion($q['id_question']);
                $foundAnswer = $answers->where('id_question', $q['id_question'])->first();
                $questions[$key]['id_answer_value'] = $foundAnswer['id_answer_value'] ?? null;
                $questions[$key]['comments']= $foundAnswer['comments'] ?? null;
                $questions[$key]['id_aplicability']= $foundAnswer['id_aplicability'] ?? null;
                foreach ($questions[$key]['answers_question'] as $k => $d) {
                    $allowed = AnswerQuestionsDepedencyModel::where('id_answer_question', $d)->get()->pluck('id_question')->toArray();
                    $blocked = collect( $questions )->where('order', '>', $q['order'])
                        ->whereNotIn('id_question', $allowed)->pluck('id_question')->toArray();
                    array_push($allowedAll, $allowed);
                    array_push($blockedAll, $blocked);
                    array_push($dependency, [
                        'id_answer_question' => $q['id_question'],
                        'id_answer_question' => $d['id_answer_question'],
                        'allowed' => $allowed,
                        'blocked' => $blocked,
                    ]);
                }
            }
        }
        else {
            $questions = AplicabilityModel::GetAspectAnswers($requestData);
            foreach ($questions as $key => $q) {
                $questions[$key]['answers_question'] = AnswersQuestionModel::GetAnswersQuestionByQuestion($q['id_question']);
                foreach ($questions[$key]['answers_question'] as $k => $d) {
                    $allowed = AnswerQuestionsDepedencyModel::where('id_answer_question', $d)->get()->pluck('id_question')->toArray();
                    $blocked = collect( $questions )->where('order', '>', $q['order'])
                        ->whereNotIn('id_question', $allowed)->pluck('id_question')->toArray();
                    array_push($allowedAll, $allowed);
                    array_push($blockedAll, $blocked);
                    array_push($dependency, [
                        'id_answer_question' => $q['id_question'],
                        'id_answer_question' => $d['id_answer_question'],
                        'allowed' => $allowed,
                        'blocked' => $blocked
                    ]);
                }
            }
        }
        // quit question without dependency
        $arrayAllow = collect($allowedAll)->collapse()->unique();
        $arrayBlock = collect($blockedAll)->collapse()->unique();
        $freeDependency = $arrayBlock->diff( $arrayAllow->toArray() )->values()->toArray();
        foreach ($dependency as $key => $d) {
            $dependency[$key]['blocked'] = collect($d['blocked'])->diff( $freeDependency )->values()->toArray();
        }
        $data['status'] = StatusConstants::SUCCESS;
        $data['questions'] = $questions;
        $data['dependency'] = $dependency;
        $data['freeDependency'] = $freeDependency;
        return response($data);
    }
    /**
     * Set anwer
     */
    public function setAnswers(Request $request){
        $requestData = $request->all();
        $requestData['dependencyInForm'] = $requestData['dependencyInForm'] ?? [];
        $requestData['questionInForm'] = $requestData['questionInForm'] ?? [];
        $requestData['dependencyFreeInForm'] = $requestData['dependencyFreeInForm'] ?? [];
        DB::beginTransaction();
        // verify allow multiple answer
        $questionInfo = QuestionsModel::with('answers.block')->find($requestData['idQuestion']);
        $allowOnlyAnswer = $questionInfo->allow_multiple_answers == StatusConstants::NOT_ALLOW_MULTIPLE_ANSWERS;
        if ( $allowOnlyAnswer ) {
            $previusDelete = AplicabilityModel::DeletePreviusAnswers($requestData);
            if ($previusDelete != StatusConstants::SUCCESS) {
                DB::rollBack(); 
                $data['status'] = StatusConstants::ERROR;
                $data['msg'] = 'Error al eliminar respuesta previa';
                return $data;
            }
        }
        // Set Answer
        $setAplicability = AplicabilityModel::SetAplicability($requestData);
        if ($setAplicability['status'] != StatusConstants::SUCCESS) {
            DB::rollBack(); 
            $action = ($requestData['setAnswer'] == 'true') ? 'registrar' : 'retirar';
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Error al '.$action.' respuesta intentelo nuevamente';
            return $data;
        }
        $currentPosition = $questionInfo->order;
        $questionType = $questionInfo->id_question_type;
        $previousQuestion = $this->previousQuestionDependency($requestData['idQuestion'], $currentPosition, $questionType, $requestData['idContractAspect'], $requestData['dependencyInForm'], $requestData['dependencyFreeInForm'], $requestData['questionInForm']);
        $setDependency = $this->setDependencyByAnswer($currentPosition, $requestData, $previousQuestion);
        if ( !$setDependency ) {
            DB::rollBack(); 
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Error al registrar dependecias de respuesta intentelo nuevamente';
            return $data;
        }
        $previousQuestionAgain = $this->previousQuestionDependency($requestData['idQuestion'], $currentPosition, $questionType, $requestData['idContractAspect'], $requestData['dependencyInForm'], $requestData['dependencyFreeInForm'], $requestData['questionInForm']);
        $setDependencyAgain = $this->setDependencyByAnswer($currentPosition, $requestData, $previousQuestionAgain);
        if ( !$setDependencyAgain ) {
            DB::rollBack(); 
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Error al volver a comprobar dependecias de respuesta intentelo nuevamente';
            return $data;
        }
        // Verify status contract aspect
        $idContractAspect = $requestData['idContractAspect'];
        $infoAspect = ContractAspectsModel::find($idContractAspect);
        // Update status contract aspect to evaluating
        if ($infoAspect->id_status == StatusConstants::NOT_CLASSIFIED) {
            $updateStatus = ContractAspectsModel::SetEvaluatingAspectQuiz($idContractAspect);
            if ($updateStatus != StatusConstants::SUCCESS) {
                DB::rollBack();
                $msgError = 'Error al registrar respuesta, intentelo nuevamente';
                $msgWarning = 'Sin registro de Materia a evaluar';
                $data['status'] = $updateStatus;
                $data['msg'] = ($updateStatus == StatusConstants::ERROR) ? $msgError : $msgWarning;
                return $data;
            }
        }
        // Update application type if status is classified
        elseif ($infoAspect->id_status == StatusConstants::CLASSIFIED) {
            // Get application type in aspect
            $evaluate = new Evaluate(false);
            $typeApp = $evaluate->getApplicationTypeAspect($idContractAspect, $requestData['idAuditProcess'], $requestData['idAspect']);
            // Set answer and aspect update
            $updateAnswerInClassified = ContractAspectsModel::UpdateApplicationType($idContractAspect, $evaluate->idApplicationType);
            if ($updateAnswerInClassified != StatusConstants::SUCCESS) {
                DB::rollBack();
                $data['status'] = StatusConstants::ERROR;
                $data['msg'] = 'Error al actualizar respuesta y aspecto intentelo nuevamente';
            }
        }
        // verify exist answers in question
        $existsAnswers = AplicabilityModel::where([
            ['id_audit_processes', $requestData['idAuditProcess']],
            ['id_question', $requestData['idQuestion']],
            ['id_contract_aspect', $requestData['idContractAspect']]
        ])->exists('id_aplicability');
        DB::commit();
        $action = ($requestData['setAnswer'] == 'true') ? 'Guardada' : 'Retirada';
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Respuesta '.$action;
        $data['wasAnswered'] = $existsAnswers;
        $data['idAplicability'] = $setAplicability['idAplicability'];
        return response($data);
    }
    /**
     * set multi answer if positive
     */
    public function setMultiAnswers(Request $request){
        $requestData = $request->all();
        $setAplicability = AplicabilityAnswersModel::SetAnswer($requestData);
        if ($setAplicability != StatusConstants::SUCCESS) {
            $action = ($requestData['setAnswer'] == 'true') ? 'Guardar' : 'Retirar';
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Error al '.$action.' la especificacion de respuesta, intentelo nuevamente';
            return response($data);
        }
        $action = ($requestData['setAnswer'] == 'true') ? 'Guardada' : 'Retirada';
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Respuesta '.$action;
        return response($data);
    }
    /**
     * Set classify aspect quiz
     */
    public function setClassifyAspectQuiz(Request $request){
        $idAuditProcess = $request->input('idAuditProcess');
        $idContractAspect = $request->input('idContractAspect');
        $idAspect = $request->input('idAspect');
        // Get application type in aspect
        DB::beginTransaction();
        $evaluate = new Evaluate(false);
        $typeApp = $evaluate->getApplicationTypeAspect($idContractAspect, $idAuditProcess, $idAspect);
        if ($typeApp['status'] =! StatusConstants::SUCCESS) {
            $data['status'] = StatusConstants::WARNING;
            $data['msg'] = 'No se pudo definir el tipo de aplicación';
            $data['title'] = 'SIN IDENTIFICACIÓN FEDERAL';
            DB::rollBack();
            return response($data);
        }
        // Classify aspect
        $finalize = ContractAspectsModel::SetClassifyAspectQuiz($idContractAspect, $evaluate->idApplicationType);
        if ($finalize['status'] != StatusConstants::SUCCESS) {
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salio mal clasificar el aspecto';
            DB::rollBack();
            return response($data);
        }
        $aspect = AspectsModel::GetAspect($idAspect);
        $data['status'] = StatusConstants::SUCCESS;
        $data['title'] = 'Finalizado';
        $data['msg'] = 'Cuestionario de: '.$aspect[0]['aspect'];
        $data['idApplicationType'] = $evaluate->idApplicationType;
        $data['log'] = $evaluate->logClass;
        $data['req'] = $evaluate->idRequirementsArray;
        $data['loc'] = $evaluate->idRequirementsLocalesArray;
        $data['sub'] = $evaluate->idSubrequirementsArray;
        DB::commit();
        return response($data);
    }
    /**
     * Get answers in aplicability
     */
    public function getAspectAnswers(Request $request){
        $dataRequest = $request->all();
        // $data = AplicabilityModel::GetAspectAnswers($dataRequest);
        $data = AplicabilityModel::with(['question', 'answer.block'])->where([
            ['t_aplicability.id_contract_aspect', $dataRequest['idContractAspect']],
            ['t_aplicability.id_audit_processes', $dataRequest['idAuditProcess']]
        ])->get();
        // foreach ($data as $k => $v) {
        //     $data[$k]['answers_questions'] = AplicabilityAnswersModel::GetAnswers($v['id_aplicability']);
        // }
        return response($data);
    }
    /**
     * Get legals basis by id_questions
     */
    public function getLegalsQuestions(Request $request, $idQuestion){
        $data = QuestionLegalBasiesModel::GetBasisByQuestions($idQuestion);
        foreach ($data as $index => $row) {
            $data[$index]['legal_quote'] = $this->setUrlInRichText($row['legal_quote']);
        }
        return response($data);
    }
    /**
     * Set register in audit table
     */
    public function setInAudit(Request $request) 
    {
        DB::beginTransaction();
        $audit = new CreateAuditObligation($request->input('idAplicabilityRegister'));
        $finish = $audit->setFinalizeApplicability();
        if ( !$finish ) {
            DB::rollback();
            $data['status'] = StatusConstants::ERROR;
            $data['title'] = 'Error al finalizar Aplicabilidad';
            return response($data);
        }
        DB::commit();
        $data['status'] = StatusConstants::SUCCESS;
        $data['title'] = 'Aplicabilidad finalizada';
        return response($data);
    }
    /**
     * Set register comments in aplicability table
     */
    public function setComments(Request $request) 
    {
        $idAplicability = $request->input('idAplicability');
        $comments = $request->input('comments');
        $aplicability = AplicabilityModel::findOrFail($idAplicability);
        $aplicability->update(['comments', $comments]);
        
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Comentarios guardados correctamente';
        return response($data);
    }
    
    private function in_array_field($field, $value, $haystack, $strict = false) {
        if ($strict) {
            foreach ($haystack as $key => $item){
                if ($item[$field] === $value)
                    return ['isExist' => true, 'index' => $key];
            }
        }
        else {
            foreach ($haystack as $key => $item){
                if ($item[$field] == $value)
                    return ['isExist' => true, 'index' => $key];
            }
        }
        return false;
    }
}
