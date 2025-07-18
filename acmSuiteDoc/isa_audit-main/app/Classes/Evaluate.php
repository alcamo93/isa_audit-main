<?php

namespace App\Classes;

use App\Classes\StatusConstants;
use App\Models\Admin\ProcessesModel;
use App\Models\Audit\AplicabilityModel;
use App\Models\Catalogues\RequirementsModel;
use App\Models\Catalogues\QuestionsModel;
use App\Models\Audit\QuestionRequirementsModel;
use App\Models\V2\Audit\Audit;
use App\Models\Admin\AddressesModel;
use App\Models\Catalogues\ApplicationTypesModel;
use App\Models\Audit\ContractAspectsModel;
use App\Models\Audit\EvaluateRequirementModel;
use App\Models\Catalogues\SubrequirementsModel;

class Evaluate
{   
    public $logClass, $idApplicationType, $idRequirementsArray, $requirements, $onlyView;
    public $idRequirementsLocalesArray, $idRequirementsSpecificArray, $idSubrequirementsArray;
    public $idContractAspect, $idForm, $id_aplicability_register;

    public function __construct($onlyView = true)
    {
        $this->logClass = [];
        $this->idApplicationType = null;
        $this->idRequirementsArray = [];
        $this->requirements = [];
        $this->idRequirementsLocalesArray = [];
        $this->idRequirementsSpecificArray = [];
        $this->idSubrequirementsArray = [];
        $this->idContractAspect = null;
        $this->idForm = null;
        $this->onlyView = $onlyView;
        $this->id_aplicability_register = null;
    }
    /**
     * Change ApplicationType
     */
    public function setApplicationType($idApplicationType){
        $this->idApplicationType = $idApplicationType;
    }
    /**
     * set info forms
     */
    public function setInfoForm($idAuditProcess, $idContractAspect) {
        $this->idContractAspect = intval($idContractAspect);
        $contractAspect = ContractAspectsModel::findOrFail($idContractAspect);
        $this->idForm = $contractAspect->form_id;
    }
    /**
     * Get application type by Aspect
     */
    public function getApplicationTypeAspect($idContractAspect, $idAuditProcess, $idAspect){
        $this->setInfoForm($idAuditProcess, $idContractAspect);
        $requestData['idAuditProcess'] = $idAuditProcess;
        $requestData['idAspect'] = $idAspect;
        // Verify clasification with requirements
        $infoProcess = ProcessesModel::GetProcesses($requestData['idAuditProcess'])[0];
        $infoAddress = AddressesModel::GetAddressType($infoProcess['id_corporate'], StatusConstants::PHYSICAL);
        $this->id_aplicability_register = $infoProcess['id_aplicability_register'];
        $info['idAuditProcess'] = $idAuditProcess;
        $info['idAspect'] = $idAspect;
        $info['idState'] = $infoAddress[0]['id_state'];
        $info['idCity'] =  $infoAddress[0]['id_city'];
        $info['idApplicationType'] = StatusConstants::FEDERAL;
        $applicationTypeInit = ApplicationTypesModel::find(StatusConstants::FEDERAL);
        array_push($this->logClass, 'Se inicio como '.$applicationTypeInit->application_type);
        $this->getRequiremntsByAplicability($info);
        $this->getSpecificRequirements($info);
        $applicationTypeFinal = ApplicationTypesModel::find($this->idApplicationType);
        array_push($this->logClass, 'Termino como: '.$applicationTypeFinal->application_type);
        // set requirements to evaluate
        $setRequirements = $this->setEvaluate($idContractAspect, $this->id_aplicability_register);
        if ($setRequirements =! StatusConstants::SUCCESS) {
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'No se pudo clasificar el aspecto, intenta nuevamente';
            return $data;
        }
        // response
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = $msg = 'El aspecto es: '. $applicationTypeFinal->application_type;
        return $data;
    }

    /********* Get Requiremnts by audit **************/

    /**
     * Get requirements to Audit
     */
    // $RequestData: [idAuditProcess, idContract, idAspect, idState, idCity, idApplicationType]
    public function getRequiremntsByAplicability($requestData, $recursive = false, $idApplicationTypeNew = null){
        $requestData['idApplicationType'] = ($recursive) ? $idApplicationTypeNew : $requestData['idApplicationType'];
        $idApplicationType = $requestData['idApplicationType'];
        $this->idRequirementsArray = [];
        $this->idRequirementsLocalesArray = [];
        $this->idRequirementsSpecificArray = [];
        $this->idSubrequirementsArray = [];
        // Get requirements
        switch ($idApplicationType) {
            case StatusConstants::FEDERAL:
                $federal = $this->onlyFederalIdentification($requestData);
                foreach ($federal as $requerimientFI) {
                    array_push($this->idRequirementsArray, $requerimientFI);
                }
                break;
            case StatusConstants::STATE:
                $state = $this->onlyEstatalIdentification($requestData);
                foreach ($state as $requerimientSI) {
                    array_push($this->idRequirementsArray, $requerimientSI);
                }
                break;
            case StatusConstants::LOCAL:
                $local = $this->onlyLocalIdentification($requestData);
                foreach ($local as $requerimientLI) {
                    array_push($this->idRequirementsArray, $requerimientLI);
                }
                break;
        }
        // Requirements locales Defualt
        if ( ($this->idApplicationType == StatusConstants::FEDERAL || 
            $this->idApplicationType == StatusConstants::STATE) && sizeof($this->idRequirementsLocalesArray) == 0 ) {
            /** */
            array_push($this->logClass, 'Evaluacion de: Requerimientos Locales');
            $localDefault = $this->onlyLocalRequirements($requestData);
            foreach ($localDefault as $id) {
                array_push($this->idRequirementsLocalesArray, $id);
                array_push($this->idRequirementsArray, $id);
            }
            $isLocal = (sizeof($localDefault) > 0) ? 'Si' : 'No';
            array_push($this->logClass, $isLocal.' existen requerimientos Locales por sumar');
        }
        $status = ( sizeof($this->requirements) > 0 ) ? StatusConstants::SUCCESS : StatusConstants::ERROR;
        $msg = ( sizeof($this->requirements) > 0 ) ? 'Iniciando Auditoría' : 'No hay REQUERIMIENTOS para comenzar auditoria';
        $data['status'] = $status;
        $data['msg'] = $msg;
        $data['idAapplicationType'] = $this->idApplicationType;
        $data['requirements'] = $this->requirements;
        return $data;
    }
    /**
     * GET ONLY "Specific Requirements"
     */
    public function getSpecificRequirements($requestData) {
        // Get only "SPECIFIC REQUIREMENTS"
        $process = ProcessesModel::findOrFail($requestData['idAuditProcess']);
        if ( boolval($process->evaluate_especific) ) {
            array_push($this->logClass, 'Evaluación de: Requerimientos especificos');
            $specific = $this->onlySpecificRequirements($process->id_corporate, $requestData['idAspect']);
            foreach ($specific as $requerimientSQ) {
                array_push($this->idRequirementsArray, $requerimientSQ);
            }
            if (sizeof($specific) > 0) {
                $thereSpecific = 'Si';
            }else $thereSpecific = 'No';
            array_push($this->logClass, $thereSpecific.' existen Requerimientos especificos');
            // Classify SPECIFIC
            if ( sizeof($specific) > 0 && $this->idApplicationType == StatusConstants::NO_APPLICABLE ) {
                array_push($this->logClass, 'Es ESPECIFICO, solo existen Requerimientos especificos');
                $this->setApplicationType(StatusConstants::SPECIFIC_APP);   
            }
        }
    }
    /**
     * EVALUATE ONLY "FEDERAL IDENTIFICATION"
     */
    public function onlyFederalIdentification($requestData){
        array_push($this->logClass, 'Inicia evaluación de Clasificación Federal');
        $groupReq = [];
        /** */
        array_push($this->logClass, 'Evaluación de: Identificación federal');
        $identification = $this->getIdentificationRequirements($requestData, StatusConstants::FEDERAL_IDENTIFICATION_Q, 
                [StatusConstants::FEDERAL_IDENTIFICATION_R,  StatusConstants::COMPOSITE_REQUIREMENTS_IDENTIFICATION],
                StatusConstants::FEDERAL, StatusConstants::AFFIRMATIVE);
        foreach ($identification as $id) {
            array_push($groupReq, $id);
        }
        if (sizeof($identification) > 0) {
            $this->setApplicationType(StatusConstants::FEDERAL);
            $identificationLog = true;
        } else $identificationLog = false;
        $isFederal = ($identificationLog) ? 'Es FEDERAL por identificación' : 'No hay Requerimientos Federales por identificación';
        array_push($this->logClass, $isFederal);
        // init Recursive
        if ( sizeof($identification) == 0 ) {
            array_push($this->logClass, 'Pasa a Evaluar Sección Estatal');
            $this->getRequiremntsByAplicability($requestData, true, StatusConstants::STATE);
        }
        return $groupReq;
    }
    /**
     * EVALUATE ONLY "STATE IDENTIFICATION"
     */
    public function onlyEstatalIdentification($requestData){
        array_push($this->logClass, 'Inicia evaluación de Clasificación Estatal');
        $thereQuestion = QuestionsModel::CountQuestionsByAspect($requestData['idAspect'], 
            StatusConstants::STATE_IDENTIFICATION_Q ,$requestData['idState'], null);
        $thereQuesText = ($thereQuestion > 0) ? 'Si' : 'No';
        array_push($this->logClass, $thereQuesText.' existen preguntas de identificación Estatales');
        $groupReq = [];
        if ($thereQuestion > 0) {
            $state = $this->getIdentificationRequirements($requestData, StatusConstants::STATE_IDENTIFICATION_Q, 
                [StatusConstants::STATE_IDENTIFICATION_R, StatusConstants::COMPOSITE_REQUIREMENTS_IDENTIFICATION],
                StatusConstants::STATE, StatusConstants::AFFIRMATIVE);
            foreach ($state as $id) {
                array_push($groupReq, $id);
            }
            if (sizeof($state) > 0) {
                $this->setApplicationType(StatusConstants::STATE);
                $isState = 'Es ESTATAL por identificación';
            } else $isState = 'No hay requerimientos Estatales por identificación';
            array_push($this->logClass, $isState);
        }
        else {
            $state = $this->getNormalRequirements([StatusConstants::STATE_REQUIREMENT, StatusConstants::COMPOSITE_REQUIREMENTS],
                StatusConstants::STATE, $requestData['idState'], null, $requestData['idAspect']);
            foreach ($state as $id) {
                array_push($groupReq, $id);
            }
            if (sizeof($state) > 0) {
                $this->setApplicationType(StatusConstants::STATE);
                $isState = 'Es ESTATAL por requerimientos';
            } else $isState = 'No hay Requerimientos Estatal';
            array_push($this->logClass, $isState);
        }
        // init Recursive
        if ( sizeof($state) == 0 ) {
            array_push($this->logClass, 'Pasa a Evaluar Sección Local');
            $this->getRequiremntsByAplicability($requestData, true, StatusConstants::LOCAL);
        }
        return $groupReq;
    }
    /**
     * EVALUATE ONLY "LOCAL REQUIREMENT" FOR FEDERAL AND ESTATE
     */
    public function onlyLocalRequirements($requestData){
        array_push($this->logClass, 'Evaluación Local: requerimientos Locales por sumar');
        $groupReq = [];
        $thereReqLocal = RequirementsModel::CountRequirementByAspect($requestData['idAspect'], 
            StatusConstants::LOCAL_REQUIREMENT, $requestData['idState'], $requestData['idCity']);
        if ($thereReqLocal > 0) {
            array_push($this->logClass, 'Si existen requerimientos Locales');
            $thereQuestion = QuestionsModel::CountQuestionsByAspect($requestData['idAspect'], 
                StatusConstants::LOCAL_IDENTIFICATION_Q , $requestData['idState'], $requestData['idCity']);
            if ($thereQuestion == 0) {
                array_push($this->logClass, 'No existen preguntas de identificación Locales');
                $localRequirements = $this->getNormalRequirements([StatusConstants::LOCAL_REQUIREMENT, StatusConstants::COMPOSITE_REQUIREMENTS], 
                    StatusConstants::LOCAL, $requestData['idState'], $requestData['idCity'], $requestData['idAspect']);
                array_push($this->logClass, 'Agregando Requerimientos Locales');
                foreach ($localRequirements as $id) {
                    array_push($groupReq, $id);
                }
            }
            else {
                array_push($this->logClass, 'Si existen preguntas de identificación Locales');
                $identificationlocal = $this->getIdentificationRequirements($requestData, StatusConstants::LOCAL_IDENTIFICATION_Q, 
                    [StatusConstants::LOCAL_IDENTIFICATION, StatusConstants::COMPOSITE_REQUIREMENTS_IDENTIFICATION],
                    StatusConstants::LOCAL, StatusConstants::AFFIRMATIVE);
                array_push($this->logClass, 'Agregando Requerimientos de Identificación Locales');
                foreach ($identificationlocal as $id) {
                    array_push($groupReq, $id);
                }
            }
        }
        else {
            array_push($this->logClass, 'No existen requerimientos Locales');
            $identificationlocal = $this->getIdentificationRequirements($requestData, StatusConstants::LOCAL_IDENTIFICATION_Q, 
                [StatusConstants::LOCAL_IDENTIFICATION, StatusConstants::COMPOSITE_REQUIREMENTS_IDENTIFICATION],
                StatusConstants::LOCAL, StatusConstants::AFFIRMATIVE);
            array_push($this->logClass, 'Agregando Requerimientos de Identificación Locales');
            foreach ($identificationlocal as $id) {
                array_push($groupReq, $id);
            }
        }
        return $groupReq;
    }
    /**
     * EVALUATE ONLY "LOCAL IDENTIFICATION"
     */
    public function onlyLocalIdentification($requestData){
        array_push($this->logClass, 'Inicia evaluación de Clasificación Local');
        $thereQuestion = QuestionsModel::CountQuestionsByAspect($requestData['idAspect'], 
            StatusConstants::LOCAL_IDENTIFICATION_Q , $requestData['idState'], $requestData['idCity']);
        $thereQuesText = ($thereQuestion > 0) ? 'Si' : 'No';
        array_push($this->logClass, $thereQuesText.' existen preguntas de indentificación Local');
        $groupReq = [];
        if ($thereQuestion > 0) {
            $local = $this->getIdentificationRequirements($requestData, StatusConstants::LOCAL_IDENTIFICATION_Q, 
                [StatusConstants::LOCAL_IDENTIFICATION, StatusConstants::COMPOSITE_REQUIREMENTS_IDENTIFICATION],
                StatusConstants::LOCAL, StatusConstants::AFFIRMATIVE);
            foreach ($local as $id) {
                array_push($groupReq, $id);
            }
            if (sizeof($local) > 0) {
                $this->setApplicationType(StatusConstants::LOCAL);
                $isLocal = 'Es LOCAL por Requerimientos Locales de Identificación';
            } else $isLocal = 'No existen requerimientos Locales por identificación';
            array_push($this->logClass, $isLocal);
        }
        else {
            $local = $this->getNormalRequirements([StatusConstants::LOCAL_REQUIREMENT, StatusConstants::COMPOSITE_REQUIREMENTS],
                StatusConstants::STATE, $requestData['idState'], $requestData['idCity'], $requestData['idAspect']);
            foreach ($local as $id) {
                array_push($groupReq, $id);
            }
            if (sizeof($local) > 0) {
                $this->setApplicationType(StatusConstants::LOCAL);
                $isLocal = 'Es LOCAL por requerimientos';
            } else $isLocal = 'No existen requerimientos Locales';
            array_push($this->logClass, $isLocal);
        }
        if ( sizeof($local) == 0 ) {
            $this->setApplicationType(StatusConstants::NO_APPLICABLE);
            array_push($this->logClass, 'Es No Aplica');
        }
        return $groupReq;
    }
    /**
     * Evaluate ONLY "SPECIFIC REQUIREMENTS"
     */
    public function onlySpecificRequirements($idCorporate, $idAspect){
        // Get requirements: "id_requirement_type" = 11 and "id_aspect" = ? and "id_corporate" = ?
        $idRequirementsArray = RequirementsModel::GetOnlySpecificRequirements(StatusConstants::SPECIFIC_REQUIREMENT, $idAspect, $idCorporate);
        return $idRequirementsArray;
    }

    /********* Get Requirements by audit **************/

    /**
     * Get requerimients
     * data: [idAuditProcess, idContract, idAspect, idState, idApplicationType, idContractAspect],
     * idQuestionType,[ idRequirementTypeArray], idApplicationType, idAnswerValue
     */
    public function getIdentificationRequirements($data, $idQuestionType, $idRequirementTypeArray, $idApplicationType, $idAnswerValue){
        // Note: only the parent requirements are obtained
        $data['idContractAspect'] = $this->idContractAspect;
        $idAnswerQuestionsArray = AplicabilityModel::GetQuestionsContractAspectAnswers($data, $idQuestionType, $idAnswerValue);
        $idRequirementsArray = QuestionRequirementsModel::GetRequirements($idAnswerQuestionsArray, $idRequirementTypeArray, $idApplicationType);
        $idSubrequirementsArray = QuestionRequirementsModel::GetOnlySubrequirements($idAnswerQuestionsArray);
        foreach ($idSubrequirementsArray as $idSubrequirement) {
            array_push($this->idSubrequirementsArray, $idSubrequirement);
        }
        return $idRequirementsArray;
    }
    /**
     * Get requirements without relation to questions
     */
    public function getNormalRequirements($idRequirementTypeArray, $idApplicationType, $idState, $idCity, $idAspect){
        $idRequirementsArray = RequirementsModel::GetOnlyRequirementByForm($idRequirementTypeArray, $idApplicationType, $idState, $idCity, $this->idForm);
        $idSubrequirementsArray = SubrequirementsModel::GetOnlySubrequirementByForm($idRequirementsArray, $idApplicationType, $idState, $idCity);
        foreach ($idSubrequirementsArray as $idSubrequirement) {
            array_push($this->idSubrequirementsArray, $idSubrequirement);
        }
        return $idRequirementsArray;
    }
    /********* Subrequirements audit **************/

    /**
     * Evalaute subrequiremnts answers to set answer in requirement
     */
    public function evaluateRequirementAnswer($dataRequest){
        $subrequirements = Audit::getRisk()->where('id_audit_aspect', $dataRequest['idAuditAspect'])
            ->where('id_requirement', $dataRequest['idRequirement'])
            ->whereNotNull('id_subrequirement')
            ->get()->toArray();
        // Set total answers
        $data['total'] = sizeof($subrequirements);
        $data['positive'] = 0;
        $data['negative'] = 0;
        $data['noApply'] = 0;
        $data['answer'] = 0;
        // Count answers
        foreach ($subrequirements as $element) {
            if ($element['answer'] == 0) $data['negative']++;
            elseif ($element['answer'] == 1) $data['positive']++;
            elseif ($element['answer'] == 2) $data['noApply']++;   
        }
        // Get answer for requirement
        if ($data['negative'] > 0) $data['answer'] = 0;
        elseif ($data['positive'] > 0) $data['answer'] = 1;
        else $data['answer'] = 2;
        // evaluate if completed risk level
        $data['riskStatus'] = StatusConstants::SUCCESS;
        if ($dataRequest['evaluateRisk'] == 'true') {
            // $data['riskStatus'] = StatusConstants::SUCCESS;
            $countRiskTotal = 9;
            $countNegative = 0;
            $totalAnswers = 0;
            $countRiskCurrent = 0;
            if ($data['answer'] == 0) {
                foreach ($subrequirements as $key => $sub) {
                    if ($sub['answer'] == 0) {
                        $countRiskCurrent += sizeof($sub['risk_answers']);
                        $countNegative ++;
                    }
                }
                $totalAnswers = $countRiskTotal * $countNegative;
                if ($totalAnswers != $countRiskCurrent) {
                    $data['riskStatus'] = StatusConstants::ERROR;
                }
            }
        }
        return $data;
    }

    /**
     * Set requirements evaluate
     */
    public function setEvaluate($idContractAspect, $idAplicabilityRegister){
        if ($this->onlyView) {
            return StatusConstants::SUCCESS;
        }
        try {
            $previuos = EvaluateRequirementModel::where('id_contract_aspect', $idContractAspect)->get();
            if ( $previuos->isNotEmpty() ) {
                $arrayToDelete = $previuos->pluck('id_evaluate_requirement');
                EvaluateRequirementModel::destroy($arrayToDelete);
            }
            foreach ($this->idRequirementsArray as $r) {
                $setReq = EvaluateRequirementModel::SetEvaluate($idContractAspect, null, $r, null, $idAplicabilityRegister);
            }
            $uniqueListSubrequirements = collect($this->idSubrequirementsArray)->unique(fn($item) => $item['id_requirement'].$item['id_subrequirement'] )->values();
            if ( $uniqueListSubrequirements->isNotEmpty() ) {
                foreach ($uniqueListSubrequirements as $element) {
                    $setSub = EvaluateRequirementModel::SetEvaluate($idContractAspect, null, $element['id_requirement'], $element['id_subrequirement'], $idAplicabilityRegister);
                    if (!$setSub) {
                        return StatusConstants::ERROR;
                    }
                }
            }
            return StatusConstants::SUCCESS;
        } catch (\Throwable $th) {
            return StatusConstants::ERROR;
        }
    }
}