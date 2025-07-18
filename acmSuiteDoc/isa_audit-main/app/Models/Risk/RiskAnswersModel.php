<?php

namespace App\Models\Risk;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException as Exception;
use App\Classes\StatusConstants;

class RiskAnswersModel extends Model
{
    protected $table = 't_risk_answers'; 
    protected $primaryKey = 'id_risk_answer';
    /**
     * set answer risk answer
     */
    public function scopeSetAnswer($query, $info) {
        $where = [
            ['id_audit', $info['idAudit'] ],
            ['id_contract', $info['idContract'] ],
            ['id_risk_attribute', $info['idRiskAttribute'] ],
            ['id_audit_processes', $info['idAuditProcess'] ],
            ['id_requirement', $info['idRequirement'] ],
            ['id_subrequirement', $info['idSubrequirement'] ],
            ['id_risk_category', $info['idRiskCategory'] ],
        ];
        try {
            $model = RiskAnswersModel::where($where)->firstOrFail();
        } catch (ModelNotFoundException $th) {
            $model = new RiskAnswersModel;
            $model->id_audit = $info['idAudit'];
            $model->id_contract = $info['idContract'];
            $model->id_risk_attribute = $info['idRiskAttribute'];
            $model->id_audit_processes = $info['idAuditProcess'];
            $model->id_requirement = $info['idRequirement'];
            $model->id_subrequirement = $info['idSubrequirement'];
            $model->id_risk_category = $info['idRiskCategory'];
        }
        // Save info
        try {
            $model->answer = $info['answer'];
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Get answer
     */
    public function scopeGetAnswer($query, $dataRequest) {
        $where = [
            ['id_contract', $dataRequest['idContract'] ],
            ['id_requirement', $dataRequest['idRequirement'] ],
            ['id_subrequirement', $dataRequest['idSubrequirement'] ],
            ['id_risk_category', $dataRequest['idRiskCategory'] ]
        ];
        switch ($dataRequest['origin']) {
            case 'probabilities':
                array_push($where, ['id_risk_exhibition',  NULL]);
                array_push($where, ['id_risk_consequence', NULL]);
                break;
            case 'exhibitions':
                array_push($where, ['id_risk_probability', NULL]);
                array_push($where, ['id_risk_consequence', NULL]);
                break;
            case 'consequences':
                array_push($where, ['id_risk_probability', NULL]);
                array_push($where, ['id_risk_exhibition', NULL]);
                break;
            default:
                # code...
                break;
        }
        try {
            $model = RiskAnswersModel::where($where)->firstOrFail();
        } catch (ModelNotFoundException $th) {
            return $data['status'] = StatusConstants::WARNING;
        }

        try {
            $data['idRiskProbability'] = $model->id_risk_probability;
            $data['idRiskExhibition'] = $model->id_risk_exhibition;
            $data['idRiskConsequence'] = $model->id_risk_consequence;
            $data['status'] = StatusConstants::SUCCESS;
            return $data;
        } catch (Exception $e) {
            return $data['status'] = StatusConstants::ERROR;
        }
    }
    /**
     * Get answers by id_requirement or id_subrequirement
     */
    public function scopeGetRiskAnswers($query, $idAudit, $withSub = false){
        $query->where('t_risk_answers.id_audit', $idAudit);
        if ($withSub) $query->whereNotNull('t_risk_answers.id_subrequirement');
        else $query->whereNull('t_risk_answers.id_subrequirement');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Delete answers by id_requirement or id_subrequirement
     */
    public function scopeDeleteRiskAnswers($query, $idAuditProcess, $idRequirement, $idSubrequirement){
        try {
            $model = RiskAnswersModel::where([
                ['id_audit_processes', $idAuditProcess],
                ['id_requirement', $idRequirement],
                ['id_subrequirement', $idSubrequirement]
            ]);
        } catch (ModelNotFoundException $nf) {
            return StatusConstants::WARNING;
        }
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Get data attributes 
     */
    public function scopeGetDataByCategoryInAnswer($query, $info, $withSub = false) {
        $query->where([
            ['id_risk_category', $info['idRiskCategory']],
            ['id_audit', $info['idAudit']]
        ]);
        if ($withSub) $query->whereNotNull('t_risk_answers.id_subrequirement');
        else $query->whereNull('t_risk_answers.id_subrequirement');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get categories risk answers
     */
    public function scopeGetCategoriesRiskAnswers($query, $idContract, $idRequirement, $idSubrequirement){
        $query->select('t_risk_answers.id_risk_category')
            ->where([
                ['id_contract', $idContract],
                ['id_requirement', $idRequirement],
                ['id_subrequirement', $idSubrequirement]
            ]);
        $data = $query->distinct()->get()->toArray();
        return $data;
    }
}