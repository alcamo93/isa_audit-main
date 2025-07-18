<?php

namespace App\Http\Controllers\Catalogues;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Models\Catalogues\RequirementRecomendationsModel;
use App\Models\Catalogues\SubrequirementRecomendationsModel;
use App\Classes\StatusConstants;

class RecomendationsController extends Controller
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
     * Get requirement recomendation by id
     */
    public function getRequirementRecomendation(Request $request) {
        $idRequirement = $request->input('id'); 
        $data = RequirementRecomendationsModel::GetRecomendation($idRequirement);
        return $data;
    }
    /**
     * Get recomendations by id requirement
     */
    public function getRecomendationsByIdRequirement(Request $request)
    {
        $idRequirement = $request->input('id');
        $data = RequirementRecomendationsModel::getRecomendationsByIdRequirement($idRequirement);
        return $data;
    }
    /**
     * Set requirement recomendation
     */
    public function setRecomendation(Request $request) {
        $idRequirement = $request->input('id');
        $recommendation = $request->input('recommendation');
        $set = RequirementRecomendationsModel::setRecomendation($recommendation, $idRequirement);
        switch ($set) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                $msg = 'Datos registrados';
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
     * Update recomendation
     */
    public function updateRecomendation(Request $request) {
        $idRecomendation = $request->input('id');
        $recommendation = $request->input('recommendation');
        $update = RequirementRecomendationsModel::UpdateRecomendation($recommendation, $idRecomendation);
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
     * Delete recomendation
     */
    public function deleteRecomendation(Request $request) {
        $idRecomendation = $request->input('idRecomendation');
        $delete = RequirementRecomendationsModel::deleteRecomendation($idRecomendation);
        switch ($delete) {
               case StatusConstants::SUCCESS:
                      $status = StatusConstants::SUCCESS;
                      $msg = 'Registro eliminado';
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

    /************************************************************************
     *  Sub requirements ****************************************************
     ***********************************************************************/

    /**
     * Get requirement recomendation by id
     */
    public function getSubrequirementRecomendation(Request $request) {
        $idSubrequirement = $request->input('id'); 
        $data = SubrequirementRecomendationsModel::GetRecomendation($idSubrequirement);
        return $data;
    }
    /**
     * Get recomendations by id requirement
     */
    public function getRecomendationsByIdSubrequirement(Request $request)
    {
        $idSubrequirement = $request->input('id');
        $data = SubrequirementRecomendationsModel::getRecomendationsByIdSubrequirement($idSubrequirement);
        return $data;
    }
    /**
     * Set subrequirement recomendation
     */
    public function setSubReqRecomendation(Request $request) {
        $idSubrequirement = $request->input('id');
        $recommendation = $request->input('recommendation');
        $set = SubrequirementRecomendationsModel::setRecomendation($recommendation, $idSubrequirement);
        switch ($set) {
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
     * 
     */
    public function deleteSubReqRecomendation(Request $request) {
        $idRecomendation = $request->input('idRecomendation');
        $delete = SubrequirementRecomendationsModel::deleteRecomendation($idRecomendation);
        switch ($delete) {
               case StatusConstants::SUCCESS:
                      $status = StatusConstants::SUCCESS;
                      $msg = 'Registro eliminado';
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
     * Update recomendation
     */
    public function updateSubrecomendation(Request $request) {
        $idSubrecomendation = $request->input('id');
        $recommendation = $request->input('recommendation');
        $update = SubrequirementRecomendationsModel::UpdateRecomendation($recommendation, $idSubrecomendation);
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
} 