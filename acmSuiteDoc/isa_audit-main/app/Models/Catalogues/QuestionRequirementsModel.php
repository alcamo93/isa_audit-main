<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;
use App\Classes\StatusConstants;

class QuestionRequirementsModel extends Model
{
    protected $table = 'r_question_requirements';
    protected $primaryKey = 'id_question_requirement';
    /**
     * Set question requirements/subrequirements by question id
     */
    public function scopeUpdateQuestionRequirement($query, $info) {
        if($info['status'] == 'true') {
            $model = new QuestionRequirementsModel();
            $model->id_question = $info['idQuestion'];
            $model->id_answer_question = $info['idAnswerQuestion'];
            $model->id_requirement = $info['idRequirement'];
            $model->id_subrequirement = $info['idSubrequirement'];
            try {
                $model->save();
                return StatusConstants::SUCCESS;
            } catch (\Throwable $th) {
                return StatusConstants::ERROR;
            }
        }   
        else {
            $model = QuestionRequirementsModel::where([
                ['id_question', $info['idQuestion']],
                ['id_answer_question', $info['idAnswerQuestion']],
                ['id_requirement', $info['idRequirement']],
                ['id_subrequirement', $info['idSubrequirement']]
            ]);
            try {
                $model->delete();
                return StatusConstants::SUCCESS;
            } catch (\Throwable $th) {
                return StatusConstants::ERROR;
            }
        }
        
    }
    /**
     * Get Requirements by id question // used in question requirements selection
     */
    public function scopeGetRequirementsByIdQuestion($query, $idQuestion, $idAnswerQuestion) {
        $query->select('id_requirement', 'id_subrequirement', 'id_answer_question')    
        ->where([
            ['id_question', $idQuestion],
            ['id_answer_question', $idAnswerQuestion]
        ]);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     *  Get Requirements by id's list
     */
    public function scopeGetRequirementsQuestionsDT($query, $info) {
        $query->join('t_requirements', 't_requirements.id_requirement', 'r_question_requirements.id_requirement');
        $where = [];
        if ($info['filterRequirementNumber'] != null) array_push($where, ['t_requirements.no_requirement','LIKE','%'.$info['filterRequirementNumber'].'%']);
        if ($info['filterRequirement'] != null) array_push($where, ['t_requirements.requirement','LIKE','%'.$info['filterRequirement'].'%']);
        if ($info['IdEvidence'] != null) array_push($where, ['t_requirements.id_evidence', $info['IdEvidence']]);
        if ($info['IdObtainingPeriod'] != null) array_push($where, ['t_requirements.id_obtaining_period', $info['IdObtainingPeriod']]);
        if ($info['IdUpdatePeriod'] != null) array_push($where, ['t_requirements.id_update_period', $info['IdUpdatePeriod']]);
        $query->where('r_question_requirements.id_question', $info['IdQuestion'])
            ->where('r_question_requirements.id_answer_question', $info['idAnswerQuestion'])
            ->whereNull('r_question_requirements.id_subrequirement')
            ->where($where);
        // Order by
        $arrayOrder = [
            0 => 't_requirements.order',
            1 => 't_requirements.no_requirement',
            2 => 't_requirements.requirement'
        ];
        $column = $arrayOrder[$info['order'][0]['column']];   
        $dir = (isset($info['order'][0]['dir']) ? $info['order'][0]['dir'] : 'desc');            
        $query->orderBy($column, $dir);
        // Paginate
        $totalRecords = $query->count('id_question_requirement');
        $paginate = $query->skip($info['start'])->take($info['length'])->get();
        $data['data'] = $paginate;
        $data['recordsTotal'] = $totalRecords;
        $data['draw'] = intval($info['draw']);
        $data['recordsFiltered'] = $totalRecords;
        return $data;
    }
    /**
     * 
     */
    public function scopeGetSubrequirementsQuestionsDT($query, $info) {
        $query->join('t_subrequirements', 't_subrequirements.id_subrequirement', 'r_question_requirements.id_subrequirement');
        $where = [];
        if ($info['filterSubrequirementNumber'] != null) array_push($where, ['t_subrequirements.no_subrequirement','LIKE','%'.$info['filterSubrequirementNumber'].'%']);
        if ($info['filterSubrequirement'] != null) array_push($where, ['t_subrequirements.subrequirement','LIKE','%'.$info['filterSubrequirement'].'%']);
        if ($info['IdMatter'] != null) array_push($where, ['t_subrequirements.id_matter', $info['IdMatter']]);
        if ($info['IdAspect'] != null) array_push($where, ['t_subrequirements.id_aspect', $info['IdAspect']]);
        if ($info['IdEvidence'] != null) array_push($where, ['t_subrequirements.id_audit_type', $info['IdEvidence']]);
        if ($info['IdObtainingPeriod'] != null) array_push($where, ['t_subrequirements.id_obtaining_period', $info['IdObtainingPeriod']]);
        if ($info['IdUpdatePeriod'] != null) array_push($where, ['t_subrequirements.id_update_period', $info['IdUpdatePeriod']]);
        if ($info['IdRequirementType'] != null)
        {
            if(gettype($info['IdRequirementType']) == 'array')  $query->whereIn('t_subrequirements.id_requirement_type', $info['IdRequirementType']);
            if(gettype($info['IdRequirementType']) == 'string') array_push($where, ['t_subrequirements.id_requirement_type', $info['IdRequirementType']]);  
        }
        $query->where('r_question_requirements.id_question', $info['IdQuestion'])
            ->where('r_question_requirements.id_answer_question', $info['idAnswerQuestion'])
            ->where('t_subrequirements.id_requirement', $info['idRequirement'])
            ->whereNotNull('r_question_requirements.id_subrequirement')
            ->where($where);
        // Order by
        $arrayOrder = [
            0 => 't_subrequirements.order',
            1 => 't_subrequirements.no_subrequirement',
            2 => 't_subrequirements.subrequirement'
        ];
        $column = $arrayOrder[$info['order'][0]['column']];   
        $dir = (isset($info['order'][0]['dir']) ? $info['order'][0]['dir'] : 'desc');            
        $query->orderBy($column, $dir);
        // Paginate
        $totalRecords = $query->count('id_question_requirement');
        $paginate = $query->skip($info['start'])->take($info['length'])->get();
        $data['data'] = $paginate;
        $data['recordsTotal'] = $totalRecords;
        $data['draw'] = intval($info['draw']);
        $data['recordsFiltered'] = $totalRecords;
        return $data;
    }
    /**
     * Get Requirements by id question // used in question requirements selection
     */
    public function scopeGetRquierementsRelation($query, $idQuestion, $idAnswerQuestion, $idRequirement) {
        $query->whereNull('r_question_requirements.id_subrequirement')
            ->where([
                ['r_question_requirements.id_question', $idQuestion],
                ['r_question_requirements.id_requirement', $idRequirement],
                ['r_question_requirements.id_answer_question', $idAnswerQuestion]
            ]);
        $data = $query->get()->toArray();
        return $data;
    }
}