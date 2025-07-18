<?php

namespace App\Http\Controllers\Catalogues;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Catalogues\StatusModel;
use App\Models\Risk\RiskCategoriesModel;
// use App\Models\Risk\RiskConsequencesModel;
// use App\Models\Risk\RiskExhibitionsModel;
// use App\Models\Risk\RiskProbabilitiesModel;
// use App\Models\Risk\RiskSpecificationsModel;
use App\Models\Risk\RiskInterpretationsModel;
use App\Models\Risk\RiskHelpModel;
use App\Models\Risk\RiskAttributesModel;
use App\Classes\StatusConstants;

class RisksController extends Controller
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
    
    /********************* Section risk categories *********************/

    /**
     * Index view
     */
    public function index(){
        $groupStatus = 1; // group status basic
        $arrayData['status'] = StatusModel::getStatusByGroup($groupStatus);
        $arrayData['attributes'] = RiskAttributesModel::GetRiskAttributes();
        return view('catalogs.risks.risks', $arrayData);
    }
    /**
     * Get categories by 
     */
    public function getCategoriesDT(Request $request){
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $fName = $request->input('fName');
        $fIdStatus = $request->input('fIdStatus');
        $data = RiskCategoriesModel::GetCategoriesDT($page, $rows, $search, $draw, $order, $fName, $fIdStatus);
        return response($data);
    }
    /**
     * set risk category 
     */
    public function setCategory(Request $request){
        $dataRequest = $request->all();
        $total = RiskCategoriesModel::count();
        if ($total == 3) {
            $data['status'] = StatusConstants::WARNING;
            $data['msg'] = 'El sistema esta limitado a solo tres categorías';
            return response($data);
        }
        $set = RiskCategoriesModel::SetCategory($dataRequest); 
        switch ($set['status']) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                $msg = 'Categoría registrada';
                $data['info']['idRiskCategory'] = $set['idRiskCategory'];
                $data['info']['riskCategory'] = $set['riskCategory'];
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
        return response($data);
    }
    /**
     * Update risk category
     */
    public function updateCategory(Request $request){
        $dataRequest = $request->all();
        $update = RiskCategoriesModel::UpdateCategory($dataRequest);
        switch ($update) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                $msg = 'Categoría actualizada';
                break;
            case StatusConstants::ERROR:
                $status = StatusConstants::ERROR;
                $msg = 'Algo salio mal, intente nuevamente';
                break;
            case StatusConstants::WARNING:
                $status = StatusConstants::WARNING;
                $msg = 'La categoría no fue encontrada';
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
     * Delete risk category
     */
    public function deleteCategory(Request $request){
        $dataRequest = $request->all();
        $delete = RiskCategoriesModel::DeleteCategory($dataRequest);
        switch ($delete) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                $msg = 'Categoría eliminada';
                break;
            case StatusConstants::ERROR:
                $status = StatusConstants::ERROR;
                $msg = 'Algunos registros dependen de esta Categoría';
                break;
            case StatusConstants::WARNING:
                $status = StatusConstants::WARNING;
                $msg = 'La categoría no fue encontrada';
                break;
            default:
                # code... 
                break;
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return response($data); 
    }

    /********************* Section risk consequences *********************/

    // /**
    //  * Get consequences by 
    //  */
    // public function getConsequencesDT(Request $request){
    //     $page = $request->input('start');
    //     $rows = $request->input('length');
    //     $search = $request->input('search');
    //     $draw = $request->input('draw');
    //     $order = $request->input('order');
    //     $fName = $request->input('fName');
    //     $fIdStatus = $request->input('fIdStatus');
    //     $idRiskCategory = $request->input('idRiskCategory');
    //     $data = RiskConsequencesModel::GetConsequencesDT($page, $rows, $search, $draw, $order, $fName, $fIdStatus, $idRiskCategory);
    //     return response($data);
    // }
    // /**
    //  * set risk Consequence 
    //  */
    // public function setConsequence(Request $request){
    //     $dataRequest = $request->all();
    //     $set = RiskConsequencesModel::SetConsequence($dataRequest);
    //     switch ($set) {
    //         case StatusConstants::SUCCESS:
    //             $status = StatusConstants::SUCCESS;
    //             $msg = 'Consecuencia registrada';
    //             break;
    //         case StatusConstants::ERROR:
    //             $status = StatusConstants::ERROR;
    //             $msg = 'Algo salio mal, intente nuevamente';
    //             break;
    //         default:
    //             # code... 
    //             break;
    //     }
    //     $data['status'] = $status;
    //     $data['msg'] = $msg;
    //     return response($data);
    // }
    // /**
    //  * Update risk consequence
    //  */
    // public function updateConsequence(Request $request){
    //     $dataRequest = $request->all();
    //     $update = RiskConsequencesModel::UpdateConsequence($dataRequest);
    //     switch ($update) {
    //         case StatusConstants::SUCCESS:
    //             $status = StatusConstants::SUCCESS;
    //             $msg = 'Consecuencia actualizada';
    //             break;
    //         case StatusConstants::ERROR:
    //             $status = StatusConstants::ERROR;
    //             $msg = 'Algo salio mal, intente nuevamente';
    //             break;
    //         case StatusConstants::WARNING:
    //             $status = StatusConstants::WARNING;
    //             $msg = 'La Consecuencia no fue encontrada';
    //             break;
    //         default:
    //             # code... 
    //             break;
    //     }
    //     $data['status'] = $status;
    //     $data['msg'] = $msg;
    //     return response($data);
    // }
    // /**
    //  * Delete risk Consequence
    //  */
    // public function deleteConsequence(Request $request){
    //     $dataRequest = $request->all();
    //     $delete = RiskConsequencesModel::DeleteConsequence($dataRequest);
    //     switch ($delete) {
    //         case StatusConstants::SUCCESS:
    //             $status = StatusConstants::SUCCESS;
    //             $msg = 'Consecuencia eliminada';
    //             break;
    //         case StatusConstants::ERROR:
    //             $status = StatusConstants::ERROR;
    //             $msg = 'Algunos registros dependen de esta Consecuencia';
    //             break;
    //         case StatusConstants::WARNING:
    //             $status = StatusConstants::WARNING;
    //             $msg = 'La Consecuencia no fue encontrada';
    //             break;
    //         default:
    //             # code... 
    //             break;
    //     }
    //     $data['status'] = $status;
    //     $data['msg'] = $msg;
    //     return response($data); 
    // }


    // /********************* Section risk exhibition *********************/

    // /**
    //  * Get exhibition by 
    //  */
    // public function getExhibitionsDT(Request $request){
    //     $page = $request->input('start');
    //     $rows = $request->input('length');
    //     $search = $request->input('search');
    //     $draw = $request->input('draw');
    //     $order = $request->input('order');
    //     $fName = $request->input('fName');
    //     $fIdStatus = $request->input('fIdStatus');
    //     $idRiskCategory = $request->input('idRiskCategory');
    //     $data = RiskExhibitionsModel::GetExhibitionsDT($page, $rows, $search, $draw, $order, $fName, $fIdStatus, $idRiskCategory);
    //     return response($data);
    // }
    // /**
    //  * set risk Exhibition 
    //  */
    // public function setExhibition(Request $request){
    //     $dataRequest = $request->all();
    //     $set = RiskExhibitionsModel::SetExhibition($dataRequest);
    //     switch ($set) {
    //         case StatusConstants::SUCCESS:
    //             $status = StatusConstants::SUCCESS;
    //             $msg = 'Exposición registrada';
    //             break;
    //         case StatusConstants::ERROR:
    //             $status = StatusConstants::ERROR;
    //             $msg = 'Algo salio mal, intente nuevamente';
    //             break;
    //         default:
    //             # code... 
    //             break;
    //     }
    //     $data['status'] = $status;
    //     $data['msg'] = $msg;
    //     return response($data);
    // }
    // /**
    //  * Update risk Exhibition
    //  */
    // public function updateExhibition(Request $request){
    //     $dataRequest = $request->all();
    //     $update = RiskExhibitionsModel::UpdateExhibition($dataRequest);
    //     switch ($update) {
    //         case StatusConstants::SUCCESS:
    //             $status = StatusConstants::SUCCESS;
    //             $msg = 'Exposición actualizada';
    //             break;
    //         case StatusConstants::ERROR:
    //             $status = StatusConstants::ERROR;
    //             $msg = 'Algo salio mal, intente nuevamente';
    //             break;
    //         case StatusConstants::WARNING:
    //             $status = StatusConstants::WARNING;
    //             $msg = 'La Exposición no fue encontrada';
    //             break;
    //         default:
    //             # code... 
    //             break;
    //     }
    //     $data['status'] = $status;
    //     $data['msg'] = $msg;
    //     return response($data);
    // }
    // /**
    //  * Delete risk Exhibition
    //  */
    // public function deleteExhibition(Request $request){
    //     $dataRequest = $request->all();
    //     $delete = RiskExhibitionsModel::DeleteExhibition($dataRequest);
    //     switch ($delete) {
    //         case StatusConstants::SUCCESS:
    //             $status = StatusConstants::SUCCESS;
    //             $msg = 'Exposición eliminada';
    //             break;
    //         case StatusConstants::ERROR:
    //             $status = StatusConstants::ERROR;
    //             $msg = 'Algunos registros dependen de esta Exposición';
    //             break;
    //         case StatusConstants::WARNING:
    //             $status = StatusConstants::WARNING;
    //             $msg = 'La Exposición no fue encontrada';
    //             break;
    //         default:
    //             # code... 
    //             break;
    //     }
    //     $data['status'] = $status;
    //     $data['msg'] = $msg;
    //     return response($data); 
    // }


    // /********************* Section risk Probabilities *********************/

    // /**
    //  * Get Probabilities by 
    //  */
    // public function getProbabilitiesDT(Request $request){
    //     $page = $request->input('start');
    //     $rows = $request->input('length');
    //     $search = $request->input('search');
    //     $draw = $request->input('draw');
    //     $order = $request->input('order');
    //     $fName = $request->input('fName');
    //     $fIdStatus = $request->input('fIdStatus');
    //     $idRiskCategory = $request->input('idRiskCategory');
    //     $data = RiskProbabilitiesModel::GetProbabilitiesDT($page, $rows, $search, $draw, $order, $fName, $fIdStatus, $idRiskCategory);
    //     return response($data);
    // }
    // /**
    //  * set risk Probability 
    //  */
    // public function setProbability(Request $request){
    //     $dataRequest = $request->all();
    //     $set = RiskProbabilitiesModel::SetProbability($dataRequest);
    //     switch ($set) {
    //         case StatusConstants::SUCCESS:
    //             $status = StatusConstants::SUCCESS;
    //             $msg = 'Probabilidad registrada';
    //             break;
    //         case StatusConstants::ERROR:
    //             $status = StatusConstants::ERROR;
    //             $msg = 'Algo salio mal, intente nuevamente';
    //             break;
    //         default:
    //             # code... 
    //             break;
    //     }
    //     $data['status'] = $status;
    //     $data['msg'] = $msg;
    //     return response($data);
    // }
    // /**
    //  * Update risk Probability
    //  */
    // public function updateProbability(Request $request){
    //     $dataRequest = $request->all();
    //     $update = RiskProbabilitiesModel::UpdateProbability($dataRequest);
    //     switch ($update) {
    //         case StatusConstants::SUCCESS:
    //             $status = StatusConstants::SUCCESS;
    //             $msg = 'Probabilidad actualizada';
    //             break;
    //         case StatusConstants::ERROR:
    //             $status = StatusConstants::ERROR;
    //             $msg = 'Algo salio mal, intente nuevamente';
    //             break;
    //         case StatusConstants::WARNING:
    //             $status = StatusConstants::WARNING;
    //             $msg = 'La Probabilidad no fue encontrada';
    //             break;
    //         default:
    //             # code... 
    //             break;
    //     }
    //     $data['status'] = $status;
    //     $data['msg'] = $msg;
    //     return response($data);
    // }
    // /**
    //  * Delete risk Probability
    //  */
    // public function deleteProbability(Request $request){
    //     $dataRequest = $request->all();
    //     $delete = RiskProbabilitiesModel::DeleteProbability($dataRequest);
    //     switch ($delete) {
    //         case StatusConstants::SUCCESS:
    //             $status = StatusConstants::SUCCESS;
    //             $msg = 'Probabilidad eliminada';
    //             break;
    //         case StatusConstants::ERROR:
    //             $status = StatusConstants::ERROR;
    //             $msg = 'Algunos registros dependen de esta Probabilidad';
    //             break;
    //         case StatusConstants::WARNING:
    //             $status = StatusConstants::WARNING;
    //             $msg = 'La Probabilidad no fue encontrada';
    //             break;
    //         default:
    //             # code... 
    //             break;
    //     }
    //     $data['status'] = $status;
    //     $data['msg'] = $msg;
    //     return response($data); 
    // }
    

    /********************* Section risk Interpretation *********************/

    // /**
    //  * Get Specifications by 
    //  */
    // public function getSpecificationsDT(Request $request){
    //     $page = $request->input('start');
    //     $rows = $request->input('length');
    //     $search = $request->input('search');
    //     $draw = $request->input('draw');
    //     $order = $request->input('order');
    //     $fName = $request->input('fName');
    //     $fIdStatus = $request->input('fIdStatus');
    //     $idProbability = $request->input('idProbability');
    //     $idExhibition = $request->input('idExhibition');
    //     $idConsequence = $request->input('idConsequence');
    //     $data = RiskSpecificationsModel::GetSpecificationsDT($page, $rows, $search, $draw, $order, $fName, $fIdStatus, $idProbability, $idExhibition, $idConsequence);
    //     return response($data);
    // }
    // /**
    //  * set risk Specification 
    //  */
    // public function setSpecification(Request $request){
    //     $dataRequest = $request->all();
    //     $set = RiskSpecificationsModel::SetSpecification($dataRequest);
    //     switch ($set) {
    //         case StatusConstants::SUCCESS:
    //             $status = StatusConstants::SUCCESS;
    //             $msg = 'Especificación registrada';
    //             break;
    //         case StatusConstants::ERROR:
    //             $status = StatusConstants::ERROR;
    //             $msg = 'Algo salio mal, intente nuevamente';
    //             break;
    //         default:
    //             # code... 
    //             break;
    //     }
    //     $data['status'] = $status;
    //     $data['msg'] = $msg;
    //     return response($data);
    // }
    // /**
    //  * Update risk Specification
    //  */
    // public function updateSpecification(Request $request){
    //     $dataRequest = $request->all();
    //     $update = RiskSpecificationsModel::UpdateSpecification($dataRequest);
    //     switch ($update) {
    //         case StatusConstants::SUCCESS:
    //             $status = StatusConstants::SUCCESS;
    //             $msg = 'Especificación actualizada';
    //             break;
    //         case StatusConstants::ERROR:
    //             $status = StatusConstants::ERROR;
    //             $msg = 'Algo salio mal, intente nuevamente';
    //             break;
    //         case StatusConstants::WARNING:
    //             $status = StatusConstants::WARNING;
    //             $msg = 'La Especificación no fue encontrada';
    //             break;
    //         default:
    //             # code... 
    //             break;
    //     }
    //     $data['status'] = $status;
    //     $data['msg'] = $msg;
    //     return response($data);
    // }
    // /**
    //  * Delete risk Specification
    //  */
    // public function deleteSpecification(Request $request){
    //     $dataRequest = $request->all();
    //     $delete = RiskSpecificationsModel::DeleteSpecification($dataRequest);
    //     switch ($delete) {
    //         case StatusConstants::SUCCESS:
    //             $status = StatusConstants::SUCCESS;
    //             $msg = 'Especificación eliminada';
    //             break;
    //         case StatusConstants::ERROR:
    //             $status = StatusConstants::ERROR;
    //             $msg = 'Algunos registros dependen de esta Especificación';
    //             break;
    //         case StatusConstants::WARNING:
    //             $status = StatusConstants::WARNING;
    //             $msg = 'La Especificación no fue encontrada';
    //             break;
    //         default:
    //             # code... 
    //             break;
    //     }
    //     $data['status'] = $status;
    //     $data['msg'] = $msg;
    //     return response($data); 
    // }
    
    /**
     * Get Interpretations
     */
    public function getInterpretations(Request $request) {
        $idRiskCategory = $request->input('idRiskCategory');
        $data = RiskInterpretationsModel::GetInterpretations($idRiskCategory);
        return response($data);
    }
    /**
     * Get Interpretations
     */
    public function setInterpretations(Request $request) {
        $rangeJson = $request->all();
        $range = json_decode($rangeJson['ranges'], true);
        DB::beginTransaction();
        $set = RiskInterpretationsModel::SetInterpretations($range);
        switch ($set) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                $msg = 'Registro exitoso';
                DB::commit();
                break;
            case StatusConstants::ERROR:
                $status = StatusConstants::ERROR;
                $msg = 'Error en el registros de Interpretaciones';
                DB::rollBack();
                break;
            default:
                # code... 
                break;
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return response($data); 
    }

    /********************* Section risk Help *********************/

    /**
     * Get data for Datatables
     */
    public function getRiskHelpDT(Request $request) {
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $fName = $request->input('fName');
        $filterAttribute = $request->input('filterAttribute');
        $idRiskCategory = $request->input('idRiskCategory');
        $data = RiskHelpModel::GetRiskHelpDT($page, $rows, $search, $draw, $order, $fName, $filterAttribute, $idRiskCategory);
        return response($data);
    }
    /**
     * Get data by id_risk_help
     */
    public function getDataRiskHelp(Request $request){
        $idRiskHelp = $request->input('idRiskHelp');
        $data = RiskHelpModel::GetDataRiskHelp($idRiskHelp);
        return response($data);
    }
    /**
     * Set Help
     */
    public function setRiskHelp(Request $request) {
        $dataRequest = $request->all();
        $set = RiskHelpModel::SetRiskHelp($dataRequest);
        switch ($set) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                $msg = 'Ayuda registrada';
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
        return response($data);
    }
    /**
     * Update Help
     */
    public function updateRiskHelp(Request $request) {
        $requestData = $request->all();
        $update = RiskHelpModel::UpdateRiskHelp($requestData);
        switch ($update) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                $msg = 'Ayuda actualizada';
                break;
            case StatusConstants::ERROR:
                $status = StatusConstants::ERROR;
                $msg = 'Algo salio mal, intente nuevamente';
                break;
            case StatusConstants::WARNING:
                $status = StatusConstants::WARNING;
                $msg = 'La Ayuda no fue encontrada';
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
     * Delete risk
     */
    public function deleteRiskHelp(Request $request) {
        $dataRequest = $request->all();
        $delete = RiskHelpModel::DeleteRiskHelp($dataRequest);
        switch ($delete) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                $msg = 'Ayuda eliminada';
                break;
            case StatusConstants::ERROR:
                $status = StatusConstants::ERROR;
                $msg = 'Algunos registros dependen de esta Ayuda';
                break;
            case StatusConstants::WARNING:
                $status = StatusConstants::WARNING;
                $msg = 'La Ayuda no fue encontrada';
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
     * Get risk help data
     */
    public function getRiskHelp(Request $request) {
        $idRiskAttribute = $request->input('idRiskAttribute');
        $idRiskCategory = $request->input('idRiskCategory');
        $data = RiskHelpModel::GetRiskHelp($idRiskCategory, $idRiskAttribute, 1);
        return response($data);
    }
} 