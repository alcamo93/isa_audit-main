<?php

namespace App\Http\Controllers\Catalogues;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Catalogues\StatusModel;
use App\Models\Catalogues\QuestionsModel;
use App\Models\Catalogues\GuidelinesModel;
use App\Models\Catalogues\BasisModel;
use App\Models\Catalogues\AspectsModel;
use App\Models\Catalogues\MattersModel;
use App\Models\Catalogues\StatesModel;
use App\Models\Catalogues\CitiesModel;
use App\Models\Catalogues\CountriesModel;
use App\Models\Catalogues\ApplicationTypesModel;
use App\Models\Catalogues\LegalClassificationModel;
use App\Models\Catalogues\AnswerValuesModel;
use App\Models\Catalogues\EvidencesModel;
use App\Models\Catalogues\PeriodModel;
use App\Models\Catalogues\RequirementsModel;
use App\Models\Catalogues\SubrequirementsModel;
use App\Models\Catalogues\ConditionsModel;
use App\Models\Catalogues\QuestionTypesModel;
use App\Models\Catalogues\RequirementTypesModel;
use App\Models\Catalogues\AnswersQuestionModel;
use App\Models\Catalogues\QuestionRequirementsModel;
use App\Models\Catalogues\QuestionLegalBasiesModel;
use App\Models\Admin\CustomersModel;
use App\Classes\StatusConstants;
use App\Classes\ImageDecodeToFile;
use Illuminate\Support\Facades\Storage;
use App\Traits\FileTrait;
use App\Traits\V2\ParameterTrait;
use App\Traits\V2\OrderByAspect;

class QuestionsController extends Controller
{
    use FileTrait, ParameterTrait, OrderByAspect;
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
     * Main view in questions catalogue module
     */
    public function index($id){
        $arrayData['matters'] = MattersModel::getMatters();
        $arrayData['guidelines'] = GuidelinesModel::getGuidelinesSelection();
        $arrayData['countries'] = CountriesModel::getCountries();
        $arrayData['states'] = StatesModel::getStates(1);
        $arrayData['appTypes'] = ApplicationTypesModel::getApplicationTypes([1]);
        $arrayData['legalC'] = LegalClassificationModel::GetLegalClassifications();
        $arrayData['periods'] = PeriodModel::GetPeriods();
        $arrayData['conditions'] = ConditionsModel::GetConditions();
        $arrayData['questionTypes'] = QuestionTypesModel::getQuestionTypes();
        $arrayData['requirementTypesFV'] = RequirementTypesModel::GetRequirementsTypeGroup(-1);
        $arrayData['customers'] = CustomersModel::getAllCustomers();
        $arrayData['evidences'] = EvidencesModel::GetEvidences();
        $arrayData['answersValues'] = AnswerValuesModel::GetAnswerValues();
        $arrayData['parameters'] = $this->getParametersByFormId($id);
        return view('catalogs.questions.questions_applicability', $arrayData);
    }
    /**
     * Get Questions for Datatable
     */
    public function getQuestionsDT(Request $request){
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $filterName = $request->input('filterName');
        $questionType = $request->input('idQuestionType');
        $idState = $request->input('idState');
        $idCity = $request->input('idCity');
        $form_id = $request->input('form_id');
        
        $data = QuestionsModel::GetQuestionsDT($page, $rows, $search, $draw, $order, $filterName, $questionType, $idState, $idCity, $form_id);
        return response($data);
    }
    /**
     * Set question
     */
    public function setQuestion(Request $request) {
        $requestData = $request->all();
        DB::beginTransaction();
        $response = QuestionsModel::SetQuestion($requestData);
        if ($response['status'] != StatusConstants::SUCCESS) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salio mal, intente nuevamente';
            return response($data);
        }
        // store disk: define in App\Traits\FileTrait
        $storeDisk = 'questionsHelpers';
        $decodeImgObject = new ImageDecodeToFile($storeDisk, $response['idQuestion']);
        $decodeImg = $decodeImgObject->decodeImg64ToLink($requestData['helpQuestion']);
        if (!$decodeImgObject->allCreate) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'No se ha podido crear los archivos, verifica que las imagenes no esten corrompidas o dañadas';
            return response($data);
        }
        try {
            $modelQuestion = QuestionsModel::findOrFail($response['idQuestion'])
                ->update(['help_question' => $decodeImgObject->richText]);
            
        } catch (\Throwable $th) {
            // Delete files with use
            $directory = $response['idQuestion'];
            Storage::disk($decodeImgObject->diskStore)->deleteDirectory($directory);
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salio mal, al guardar la Pregunta';
            return response($data);
        }
        // $this->orderQuestions($response['idQuestion'], $requestData);
        DB::commit();
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Datos registrados';
        return response($data);
    }
    /**
     * Update Question
     */
    public function updateQuestion(Request $request) {
        $requestData = $request->all();
        // store disk: define in App\Traits\FileTrait
        $storeDisk = 'questionsHelpers';
        $decodeImgObject = new ImageDecodeToFile($storeDisk, $requestData['idQuestion']);
        $decodeImg = $decodeImgObject->decodeImg64ToLink($requestData['helpQuestion']);
        if (!$decodeImgObject->allCreate) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'No se ha podido crear los archivos, verifica que las imagenes no esten corrompidas o dañadas';
            return response($data);
        }
        DB::beginTransaction();
        $update = QuestionsModel::UpdateQuestion($requestData, $decodeImgObject->richText);
        if ($update != StatusConstants::SUCCESS) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salio mal, intente nuevamente';
            return response($data);
        }
        // Delete files with use
        $documentsToKeep = $decodeImgObject->linksExistImg;
        $documentsCreated = $decodeImgObject->createdImg;
        $currentFiles = array_merge($documentsToKeep, $documentsCreated);
        $filesInDirectory = Storage::disk($decodeImgObject->diskStore)->allFiles($requestData['idQuestion']);
        if ( sizeof($currentFiles) > 0 ) {
            $filesToDelete = array_diff($filesInDirectory, $currentFiles);
            Storage::disk($decodeImgObject->diskStore)->delete($filesToDelete);
        }
        // $this->orderQuestions($requestData['idQuestion'], $requestData);
        DB::commit();
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Datos actualizado';
        return response($data);
    }
    /**
     * Delete Question
     */
    public function deleteQuestion(Request $request)
    {
        $idQuestion = $request->input('idQuestion');
        $delete = QuestionsModel::deleteQuestion($idQuestion);
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
        // Delete files without use
        if ($delete == StatusConstants::SUCCESS) {
            $storeDisk = $this->getStorageEnviroment('questionsHelpers');
            Storage::disk($storeDisk)->deleteDirectory($idQuestion);
        }
        // response
        $data['status'] = $status;
        $data['msg'] = $msg;
        return $data;
    }
    /**
     * Get question-requirements ids by idQuestion
     */
    public function getQuestionRequirements(Request $request, $idQuestion, $idAnswerQuestion) {
        $requirements = QuestionRequirementsModel::GetRequirementsByIdQuestion($idQuestion, $idAnswerQuestion);
        return response($requirements);
    }
    /**
     * Get question by idQuestion
     */
    public function getQuestion(Request $request, $idQuestion) {
        $data = QuestionsModel::GetQuestion($idQuestion);
        return $data;
    }
    /**
     * Obtain requirements for datatable
     */
    public function getRequirementsRelationDT(Request $request){
        $requestData = $request->all();
        $data = RequirementsModel::GetRequirementsDT($requestData); 
        foreach ($data['data'] as $k => $row) {
            $relation = QuestionRequirementsModel::GetRquierementsRelation($requestData['idQuestion'], $requestData['idAnswerQuestion'], $row['id_requirement']);
            $data['data'][$k]['relation'] = (sizeof($relation) > 0) ? $relation[0]['id_question_requirement'] : null;
        }
        return response($data);
    }
    /**
     * Set or destroy question-requirements relation
     */
    public function updateQuestionRequirements(Request $request) {
        $dataRequest = $request->all();
        DB::beginTransaction();
        $update = QuestionRequirementsModel::UpdateQuestionRequirement($dataRequest);
        if ($update != StatusConstants::SUCCESS) {
            DB::rollBack();
            $data['status'] = StatusConstants::WARNING;
            $data['msg'] = 'Algo salio mal, intente nuevamente';
            return response($data);
        }
        $forSub['status'] = $dataRequest['status'];
        $forSub['idQuestion'] = $dataRequest['idQuestion'];
        $forSub['idAnswerQuestion'] = $dataRequest['idAnswerQuestion'];
        $forSub['idRequirement'] = $dataRequest['idRequirement'];
        if ($dataRequest['hasSubrequirement'] == 1) {
            $subs = SubrequirementsModel::GetSubrequirements($dataRequest['idRequirement']);
            foreach ($subs as $s) {
                $forSub['idSubrequirement'] = $s['id_subrequirement'];
                $updateSub = QuestionRequirementsModel::UpdateQuestionRequirement($forSub);
                if ($updateSub != StatusConstants::SUCCESS) {
                    DB::rollBack();
                    $data['status'] = StatusConstants::WARNING;
                    $data['msg'] = 'Algo salio mal al asignar subrequerimientos, intente nuevamente';
                    return response($data);
                }
            }
        }
        DB::commit();
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Registro actualizado';
        return response($data);
    }
    /**
     * Get Question requirements for Datatable
     */    
    public function getAssignedQuestionRequirementsDT(Request $request) {
        $dataRequest = $request->all();
        $data = QuestionRequirementsModel::GetRequirementsQuestionsDT($dataRequest);
        return response($data);
    }
    /**
     * Get Question subrequirements for Datatable
     */
    public function getAssignedQuestionSubequirementsDT(Request $request) {
        $dataRequest = $request->all();
        $data = QuestionRequirementsModel::GetSubrequirementsQuestionsDT($dataRequest);
        return response($data);
    }
    /**
     * Get question-basis ids by idQuestion
     */
    public function getQuestionBasis(Request $request, $idQuestion) {
        $basis = QuestionLegalBasiesModel::GetBasisByIdQuestion($idQuestion);
        return response($basis);
    }
    /**
     * Get question-basis ids by idQuestion
     */
    public function updateQuestionBasis(Request $request) {
        $idQuestion = $request->input('idQuestion');
        $idBasis = $request->input('idBasis');
        $status = $request->input('status');
        $update = QuestionLegalBasiesModel::UpdateQuestionBasis($idQuestion, $idBasis, $status);
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
        return response($data);
    }
    /**
     * Get Question basis for Datatable
     */
    public function getQuestionBasisDT(Request $request) {
        $dataRequest = $request->all();
        $data = QuestionLegalBasiesModel::GetBasisQuestionsDT($dataRequest);
        return response($data);
    }
    /**
     * Cahge question status
     */
    public function changeStatusQuestion(Request $request)
    {
        $idQuestion = $request->input('idQuestion');
        $status = $request->input('status');
        $update = QuestionsModel::changeStatusQuestion($idQuestion, $status);
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
     * Get info a question
     */
    public function getQuestionInfo(Request $request, $idQuestion){
        $data = QuestionsModel::GetQuestion($idQuestion);
        return response($data);
    }

    /********* Section Answers *********/

    /**
     * get data for datatable
     */
    public function getAnswersQuestionDT(Request $request)
    {
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $filterAnswer = $request->input('filterAnswer');
        $filterIdAnswerValue = $request->input('filterIdAnswerValue');
        $idQuestion = $request->input('idQuestion');
        $data = AnswersQuestionModel::GetAnswersQuestionDT($page, $rows, $search, $draw, $order, $idQuestion, $filterAnswer, $filterIdAnswerValue);
        return response($data);
    }
    /**
     * data answer
     */
    public function getQuestionAnswer(Request $request, $idAnswerQuestion){
        $data = AnswersQuestionModel::GetAnswersQuestion($idAnswerQuestion);
        return response($data);
    }
    /**
     * set or update data answer
     */
    public function setAnswerQuestion(Request $request){
        $dataRequest = $request->all();
        $set = AnswersQuestionModel::SetAnswerQuestion($dataRequest);
        switch ($set) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                $msg = 'Registro actualizado';
                break;
            case StatusConstants::WARNING:
                $status = StatusConstants::WARNING;
                $msg = 'Registro no encontrado';
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
     * Delete answer
     */
    public function deleteAnswerQuestion(Request $request){
        $idAnswerQuestion = $request->input('idAnswerQuestion');
        $delete = AnswersQuestionModel::DeleteAnswerQuestion($idAnswerQuestion);
        if ($delete != StatusConstants::SUCCESS) {
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salio mal, intente nuevamente';
            return response($data);
        }
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Registro borrado';
        return response($data);
    }
    /**
     * Get all data question
     */
    public function getAllDataQuestion(Request $request){
        $idQuestion = $request->input('idQuestion');
        $data = QuestionsModel::GetAllDataQuestion($idQuestion);
        return response($data);
    }
} 