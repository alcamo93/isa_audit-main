<?php

namespace App\Classes;

use App\Classes\StatusConstants;
use App\Models\Catalogues\MattersModel;
use App\Models\Audit\AuditRegistersModel;
use App\Models\Audit\AuditMattersModel;
use App\Models\Audit\AuditAspectsModel;
use App\Models\Audit\AuditModel;

class StatusAudit
{
    public function updateStatusMatter($idAuditMatter)
    {
        $auditMatter = AuditMattersModel::with('matter', 'aspects')->find($idAuditMatter);
        $allAspects = $auditMatter->aspects->where('id_status');
        $data['total'] = $allAspects->count();
        $data['notAudited'] = $allAspects->where('id_status', StatusConstants::NOT_AUDITED)->count();
        $data['audited'] = $allAspects->where('id_status', StatusConstants::AUDITED)->count();
        $data['auditing'] = $allAspects->where('id_status', StatusConstants::AUDITING)->count();
        $data['finished'] = $allAspects->where('id_status', StatusConstants::FINISHED_AUDIT)->count();
        $data['compliance'] = $auditMatter->total;

        if ( ($data['total'] == 0) || ($data['total'] == $data['notAudited'] ) ) {
            $data['statusMatter'] = 'Sin auditar';
            $curentStatus = StatusConstants::NOT_AUDITED;
            $percent = 0;
        }
        elseif (  ( $data['audited'] > 0 ) && ( ( $data['audited']+$data['finished'] ) == $data['total'] ) ) {
            $curentStatus = StatusConstants::AUDITED;
            $data['statusMatter'] = 'Auditado';
        }
        elseif ($data['total'] == $data['finished']) {
            $data['audited'] = $data['total'];
            $data['statusMatter'] = 'Finalizado';
            $curentStatus = StatusConstants::FINISHED_AUDIT;
        }
        elseif ($data['total'] != $data['finished']) {
            // Update status to contract matter
            if ( ( $data['total'] == $data['audited'] ) && ( $data['audited'] > 0 ) ){
                $curentStatus = StatusConstants::AUDITED;
                $data['statusMatter'] = 'Auditado';
            }
            elseif ( ($data['audited'] > 0) || ($data['auditing'] > 0) || ($data['notAudited'] > 0)){
                $curentStatus = StatusConstants::AUDITING;
                $data['statusMatter'] = 'Evaluando';
            }
            elseif ($data['audited'] == 0 && $data['auditing'] == 0){
                $data['statusMatter'] = 'Sin auditar';
                $curentStatus = StatusConstants::NOT_AUDITED;
            }
            $percent = ( ($data['audited'] + $data['finished']) / $data['total'] ) * 100;
        }
        $update = AuditMattersModel::SetStatusMatter($idAuditMatter, $curentStatus);
        // Response advence in matter
        $return['matter'] = $auditMatter->matter->matter;
        $return['idAuditMatter'] = $idAuditMatter;
        $return['status'] = $update;
        $return['msg'] = $update == StatusConstants::SUCCESS ? 'Estatus de materia Actualizado' : 'Error al actualizar Estatus de materia';
        return $return;
    }
    /**
     * Get aplicability Registers Progress
     */
    public function updateStatusAuditRegisters($idAuditRegister)
    {
        $all = AuditMattersModel::where('id_audit_register', $idAuditRegister)->get();
        $data['total'] = $all->count();
        $data['audited'] = $all->where('id_status', StatusConstants::AUDITED)->count();
        $data['notAudited'] = $all->where('id_status', StatusConstants::AUDITED)->count();
        $data['auditing'] = $all->where('id_status', StatusConstants::AUDITING)->count();
        $data['finished'] = $all->where('id_status', StatusConstants::FINISHED_AUDIT)->count();

        // Update status to contract matter
        if (  ( $data['audited'] > 0 ) && ( ( $data['audited']+$data['finished'] ) == $data['total'] ) ) {
            $currentStatus = StatusConstants::AUDITED;
            $data['statusMatter'] = 'Auditado';
        }
        else {
            if ( $data['total'] == $data['finished'] ){
                $currentStatus = StatusConstants::FINISHED_AUDIT;
                $data['statusAudit'] = 'Finalizado';
            }
            elseif ( ( $data['audited'] > 0 ) && ( $data['total'] == $data['audited'] ) ){
                $currentStatus = StatusConstants::AUDITED;
                $data['statusAudit'] = 'Auditado';
            }
            elseif ( ($data['audited'] > 0) || ($data['auditing'] > 0) || ($data['notAudited'] > 0) ){
                $currentStatus = StatusConstants::AUDITING;
                $data['statusAudit'] = 'Auditando';
            }
            elseif ($data['audited'] == 0 && $data['auditing'] == 0){
                $data['statusAudit'] = 'Sin auditar';
                $currentStatus = StatusConstants::NOT_AUDITED;
            }
        }
        $data['currentStatus'] = $currentStatus;
        $update = AuditRegistersModel::SetStatusAudit($idAuditRegister, $currentStatus);
        $data['status'] = $update;
        $data['msg'] = $update == StatusConstants::SUCCESS ? 'Estatus de aspecto Actualizado' : 'Error al actualizar Estatus de aspecto';
        return $data;
    }
    /**
     * Calculate percent in aspect/matter/global audit
     */
    public function calculatePercentAspect($idAuditAspect, $idAuditProcess, $idAspect) 
    {
        $answers = AuditModel::with(['requirement', 'subrequirement'])
            ->where('id_audit_aspect', $idAuditAspect)->get();
        $allParents = $answers->whereNull('subrequirement');

        $noComplies = 0;
        $yesComplies = 1;
        $naComplies = 2;

        $aspect['total'] = $allParents->whereIn('answer', [$noComplies, $yesComplies])->count();
        $aspect['negatives'] = $allParents->whereIn('answer', [$noComplies])->count();
        $aspect['positives'] = $allParents->whereIn('answer', [$yesComplies])->count();

        $percent = $aspect['positives'] != 0 ? ($aspect['positives'] / $aspect['total']) * 100 : 0;
        $aspect['percentAspect'] = round($percent, 2);

        $updateAspect = AuditAspectsModel::UpdatePercent($idAuditAspect, $aspect['percentAspect']);
        if ($updateAspect['status'] != StatusConstants::SUCCESS) {
            $data['status'] = $updateAspect['status'];
            $data['msg'] = 'Error al actualizar porcentaje de Aspecto';
            return $data;
        }
        
        $call = $this->calculatePercentMatter($updateAspect['idAuditMatter'], $idAuditProcess);
        $data['status'] = $call['status'];
        $data['msg'] = $call['msg'];
        return $data;
    }
    /**
     * Calculate percent in matter/global audit
     */
    public function calculatePercentMatter($idAuditMatter, $idAuditProcess)
    {
        $allAspects = AuditAspectsModel::where('id_audit_matter', $idAuditMatter)->get();
        $matter['totalSumAspects'] = $allAspects->sum('total');
        $matter['totalAspects'] = $allAspects->count();
        $percent = $matter['totalSumAspects'] / $matter['totalAspects'];
        $matter['percentMatter'] = round($percent , 2);
        
        $updateMatter = AuditMattersModel::UpdatePercent($idAuditMatter, $matter['percentMatter']);
        if ($updateMatter['status'] != StatusConstants::SUCCESS) {
            $data['status'] = $updateMatter['status'];
            $data['msg'] = 'Error al actualizar porcentaje de Materia';
            return $data;    
        }

        $updateStatusMatter = $this->updateStatusMatter($idAuditMatter);
        if ($updateMatter['status'] != StatusConstants::SUCCESS) {
            $data['status'] = $updateStatusMatter['status'];
            $data['msg'] = 'Error al actualizar porcentaje de Materia';
            return $data;    
        }

        $call = $this->calculatePercentGlobal($updateMatter['idAuditRegister']);
        $data['status'] = $call['status'];
        $data['msg'] = $call['msg'];
        return $data;
    }
    /**
     * Calculate percent in global audit
     */
    public function calculatePercentGlobal($idAuditRegister)
    {
        $allMatters = AuditMattersModel::where('id_audit_register', $idAuditRegister)->get();
        
        $global['totalSumMatter'] = $allMatters->sum('total');
        $global['totalMatter'] = $allMatters->count();
        $percent = $global['totalSumMatter'] / $global['totalMatter'];
        $global['percentGlobal'] = round($percent , 2);

        $updateGlobal = AuditRegistersModel::UpdatePercent($idAuditRegister, $global['percentGlobal']);
        if ($updateGlobal != StatusConstants::SUCCESS) {
            $data['status'] = $updateGlobal['status'];
            $data['msg'] = 'Error al actualizar porcentaje Global';
            return $data;    
        }

        $updateStatusGlobal = $this->updateStatusAuditRegisters($idAuditRegister);
        if ($updateStatusGlobal != StatusConstants::SUCCESS) {
            $data['status'] = $updateStatusGlobal['status'];
            $data['msg'] = 'Error al actualizar estatus Global';
            return $data;    
        }

        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Porcentajes Calculados';
        $data['audit_global'] = $global['percentGlobal'];
        return $data;
    }
    /**
     * Calculate percent as of matters 
     */
    public function calculatePercent($idContract){
        $status = StatusConstants::SUCCESS;
        $msg = 'Porcentajes Calculados';
        $matters = AuditMattersModel::GetAuditMattersByContract($idContract);
        foreach ($matters as $m) {
            $update = $this->calculatePercentMatter($m['id_audit_matter'], $idContract);
            if ($update != StatusConstants::SUCCESS) {
                $status = $update['status'];
                $msg = $update['msg'];
                break;
            }
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return $data;
    }
    /**
     * Calculate all percent
     */
    public function calculateAllPercent($idAuditMatter){
        $status = StatusConstants::SUCCESS;
        $msg = 'Porcentajes Calculados';
        $aspects = AuditAspectsModel::GetAuditedAspectsByMatterStatus($idAuditMatter, [
            StatusConstants::AUDITED, 
            StatusConstants::FINISHED_AUDIT,
            StatusConstants::AUDITING
        ]);
        foreach ($aspects as $a) {
            $update = $this->calculatePercentAspect($a['id_audit_aspect'], $a['id_audit_processes'], $a['id_aspect']);
            if ($update != StatusConstants::SUCCESS) {
                $status = $update['status'];
                $msg = $update['msg'];
            }
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return $data;
    }
}