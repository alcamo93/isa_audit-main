<?php

namespace App\Classes;

use App\Classes\StatusConstants;
use App\Models\Catalogues\MattersModel;
use App\Models\Audit\AplicabilityRegistersModel;
use App\Models\Audit\ContractMattersModel;
use App\Models\Audit\ContractAspectsModel;

class StatusAplicability{

    public function updateStatusMatter($idContractMatter){
        $contractMatter = ContractMattersModel::where('id_contract_matter', $idContractMatter)->get()->toArray();
        $matter = MattersModel::GetMatter($contractMatter[0]['id_matter']);
        $data['total'] = ContractAspectsModel::where('id_contract_matter', $idContractMatter)->count();
        $data['notClassfied'] = ContractAspectsModel::CountAspectsByStataus($idContractMatter, StatusConstants::NOT_CLASSIFIED);
        $data['classfied'] = ContractAspectsModel::CountAspectsByStataus($idContractMatter, StatusConstants::CLASSIFIED);
        $data['evaluating'] = ContractAspectsModel::CountAspectsByStataus($idContractMatter, StatusConstants::EVALUATING);
        $data['finished'] = ContractAspectsModel::CountAspectsByStataus($idContractMatter, StatusConstants::FINISHED);
        $percent = 100;

        if ( ($data['total'] == 0) ||  ($data['total'] == $data['notClassfied'] ) ) {
            $data['statusMatter'] = 'Sin clasificar';
            $curentStatus = StatusConstants::NOT_CLASSIFIED;
            $percent = 0;
        }
        elseif (  ( $data['classfied'] > 0 ) && ( ( $data['classfied']+$data['finished'] ) == $data['total'] ) ) {
            $curentStatus = StatusConstants::CLASSIFIED;
            $data['statusMatter'] = 'Clasificado';
        }
        elseif ($data['total'] == $data['finished']) {
            $data['classfied'] = $data['total'];
            $data['statusMatter'] = 'Finalizado';
            $curentStatus = StatusConstants::FINISHED;
        }
        elseif ($data['total'] != $data['finished']) {
            // Update status to contract matter
            if ( ( $data['total'] == $data['classfied'] ) && ( $data['classfied'] > 0 ) ){
                $curentStatus = StatusConstants::CLASSIFIED;
                $data['statusMatter'] = 'Clasificado';
            }
            elseif ( ($data['classfied'] > 0) || ($data['evaluating'] > 0) || ($data['notClassfied'] > 0)){
                $curentStatus = StatusConstants::EVALUATING;
                $data['statusMatter'] = 'Evaluando';
            }
            elseif ($data['classfied'] == 0 && $data['evaluating'] == 0){
                $data['statusMatter'] = 'Sin clasificar';
                $curentStatus = StatusConstants::NOT_CLASSIFIED;
            }
            $percent = ( ($data['classfied'] + $data['finished']) / $data['total'] ) * 100;
        }
        ContractMattersModel::SetStatusMatter($idContractMatter, $curentStatus);
        // Response advence in matter
        $data['percent'] = round($percent, 2);
        $data['matter'] = $matter[0]['matter'];
        $data['idContractMatter'] = $idContractMatter;
        return $data;
    }
    /**
     * Get aplicability Registers Progress
     */
    public function updateStatusAplicabilityRegisters($idAplicabilityRegister){
        $data['total'] = ContractMattersModel::where('id_aplicability_register', $idAplicabilityRegister)->count();
        $data['classfied'] = ContractMattersModel::CountMattersByStataus($idAplicabilityRegister, StatusConstants::CLASSIFIED);
        $data['notClassfied'] = ContractMattersModel::CountMattersByStataus($idAplicabilityRegister, StatusConstants::NOT_CLASSIFIED);
        $data['evaluating'] = ContractMattersModel::CountMattersByStataus($idAplicabilityRegister, StatusConstants::EVALUATING);
        $data['finished'] = ContractMattersModel::CountMattersByStataus($idAplicabilityRegister, StatusConstants::FINISHED);
        // Update status to contract matter
        if (  ( $data['classfied'] > 0 ) && ( ( $data['classfied']+$data['finished'] ) == $data['total'] ) ) {
            $currentStatus = StatusConstants::CLASSIFIED;
            $data['statusMatter'] = 'Clasificado';
        }
        else {
            if ( ($data['total'] == 0) ||  ($data['total'] == $data['notClassfied'] ) ) {
                $data['statusAplicability'] = 'Sin clasificar';
                $currentStatus = StatusConstants::NOT_CLASSIFIED;
            }
            elseif ( $data['total'] == $data['finished'] ){
                $currentStatus = StatusConstants::FINISHED;
                $data['statusAplicability'] = 'Finalizado';
            }
            elseif ( ( $data['classfied'] > 0 ) && ( $data['total'] == $data['classfied'] ) ){
                $currentStatus = StatusConstants::CLASSIFIED;
                $data['statusAplicability'] = 'Clasificado';
            }
            elseif ( ($data['classfied'] > 0) || ($data['evaluating'] > 0) || ($data['notClassfied'] > 0) ){
                $currentStatus = StatusConstants::EVALUATING;
                $data['statusAplicability'] = 'Evaluando';
            }
            elseif ($data['classfied'] == 0 && $data['evaluating'] == 0){
                $data['statusAplicability'] = 'Sin clasificar';
                $currentStatus = StatusConstants::NOT_CLASSIFIED;
            }
        }
        $data['currentStatus'] = $currentStatus;
        AplicabilityRegistersModel::SetStatusAplicability($idAplicabilityRegister, $currentStatus);
        return $data;
    }
}