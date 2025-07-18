<?php

namespace App\Traits;

use App\Models\Admin\ProcessesModel;
use App\Models\Audit\AuditModel;
use App\Models\Risk\RiskCategoriesModel;
use App\Models\Risk\RiskHelpModel;
use App\Models\Risk\RiskAttributesModel;
use App\Models\Risk\RiskAnswersModel;
use App\Models\Audit\EvaluateRequirementModel;
use App\Classes\StatusConstants;

trait AuditTrait {
    /**
     * Evaluate complete checklist
     */
    public function evaluate($idAudit) {
        $check = $this->checklist($idAudit);
        if ($check['type'] == 'child') {
            $check['parent'] = $this->checkParent($check['info']);
        }
        return $check;
    }
    /**
     * classify by answer type
     */
    public function typeRecord($auditRecord){
        $data['isSubrequirement'] = is_null($auditRecord->subrequirement) ? false : true;
        $data['hasSubrequirements'] = ($auditRecord->requirement->has_subrequirement == 1) ? true : false;
        $data['isParent'] = !$data['isSubrequirement'] && $data['hasSubrequirements'];
        $data['idAuditAspect'] = $auditRecord->id_audit_aspect;
        $data['idRequirement'] = $auditRecord->id_requirement;
        $data['idSubrequirement'] = $auditRecord->id_subrequirement;
        $data['answer'] = is_null($auditRecord) ? null : $auditRecord->answer;
        // select type
        if ($data['isParent'] && $data['hasSubrequirements']) {
            $data['type'] = 'parent';
            return $data;
        }
        if ($data['isSubrequirement'] && !$data['isParent']) {
            $data['type'] = 'child';
            return $data;
        }
        if (!$data['isParent'] ) {
            $data['type'] = 'normal';
            return $data;
        }
    }
    /**
     * Checklist by answer 
     */
    public function checklist($idAudit, $recursive = false) {
        $relationship = ['process', 'requirement', 'subrequirement'];
        $answer = AuditModel::with($relationship)->findOrFail($idAudit);
        $type = $this->typeRecord($answer);
        // checklist
        $globalTotal = 0;
        $check['idAudit'] = $idAudit;
        $check['answer'] = $type['answer'];
        $check['type'] = $type['type'];
        $check['requiredFinding'] = true;
        $check['finding'] = true;
        $check['requiredRisk'] = true;
        $check['risk'] = true;
        // evaluate
        $answeValue = $answer->answer;
        $evluateRisk = ($answer->process->evaluate_risk == ProcessesModel::EVALUATION_RISK) ? true : false;
        $type['isSubrequirement'] = is_null($answer->id_subrequirement);
        // Rules for negative answer
        if ($answeValue == AuditModel::NEGATIVE_ANSWER) {
            $check['requiredFinding'] = $this->requiredFindingOptions($type['type']);
            $check['finding'] = !is_null($answer->finding) ? true : false;
            $globalTotal += ($check['requiredFinding'] && $check['finding']) ? 0 : 1;
            $check['requiredRisk'] = $this->requiredRiskOptions($type['type']);
            if ($evluateRisk && $check['requiredRisk']) {
                $check['risk'] = $this->riskComplete($idAudit, !$type['isSubrequirement']);
                $globalTotal += ($check['requiredRisk'] && $check['risk']) ? 0 : 1;
            } else $check['risk'] = false;
        }
        $check['isCompleted'] = ($globalTotal > 0) ? false : true;
        $check['info'] = $type;
        return $check;
    }
    /**
     * Evaluate all childs for parent evaluate
     */
    public function checkParent($type) {
        if ($type['type'] == 'child') {
            $subrequirements = EvaluateRequirementModel::where([
                    ['id_requirement', $type['idRequirement']],
                    ['id_audit_aspect', $type['idAuditAspect']]
                ])
                ->whereNotNull('id_subrequirement')
                ->get()->pluck('id_subrequirement');
            $auditAnswer = AuditModel::where('id_audit_aspect', $type['idAuditAspect'])
                ->whereIn('id_subrequirement', $subrequirements)
                ->get()->pluck('id_audit');
            $storageTmp = []; 
            foreach ($auditAnswer as $key => $a) {
                $tmp = $this->checklist($a);
                array_push($storageTmp, $tmp['isCompleted']);
            }
            $thereAreFalse = !in_array(false, $storageTmp);
            $data['childsComplete'] = $thereAreFalse;
            $answer = AuditModel::where([
                ['id_audit_aspect', $type['idAuditAspect']],
                ['id_requirement', $type['idRequirement']],
            ])->whereNull('id_subrequirement')->first();
            $data['finished'] = is_null($answer) ? false : true;
            $data['answer'] = is_null($answer) ? null : $answer->answer;
            return $data;
        }
    }
    /**
     * Options by finding options
     */
    public function requiredFindingOptions($type) {
        $options = [
            'parent' => false,
            'child' => true,
            'normal' => true
        ];
        return $options[$type];
    }
    /**
     * Options by risk options
     */
    public function requiredRiskOptions($type) {
        $options = [
            'parent' => false,
            'child' => true,
            'normal' => true
        ];
        return $options[$type];
    }
    /**
     * Evaluate if risk is completed
     */
    public function riskComplete($idAudit, $isParent) {
        // total fields risk to evaluate 
        $riskCategories = RiskCategoriesModel::where('id_status', StatusConstants::ACTIVE)->count();
        $riskAttributes = RiskAttributesModel::count();
        $answerTotalRisk = $riskCategories * $riskAttributes;
        // total answers risk
        $responseTotalRisk = RiskAnswersModel::where('t_risk_answers.id_audit', $idAudit);
        if ($isParent) $responseTotalRisk->whereNotNull('t_risk_answers.id_subrequirement');
        else $responseTotalRisk->whereNull('t_risk_answers.id_subrequirement');
        $responseTotalRisk = $responseTotalRisk->count();
        // risk is complete
        return ($responseTotalRisk == $answerTotalRisk) ? true : false;
    }
    
    public function setCompleted($check) {
        EvaluateRequirementModel::where([
            ['id_audit_aspect', $check['info']['idAuditAspect']],
            ['id_requirement', $check['info']['idRequirement']],
            ['id_subrequirement', $check['info']['idSubrequirement']],
        ])
        ->update(['complete' => $check['isCompleted']]);
        if ($check['type'] == 'child') {
            EvaluateRequirementModel::where([
                ['id_audit_aspect', $check['info']['idAuditAspect']],
                ['id_requirement', $check['info']['idRequirement']]
            ])
            ->whereNull('id_subrequirement')
            ->update(['complete' => $check['parent']['finished']]);
        }
    }
}