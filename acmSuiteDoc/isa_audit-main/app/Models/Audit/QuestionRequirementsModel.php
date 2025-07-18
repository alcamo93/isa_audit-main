<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\Session;

class QuestionRequirementsModel extends Model
{
    protected $table = 'r_question_requirements';
    protected $primaryKey = 'id_question_requirement';

    /**
     * Get requirements by questions 
     */
    public function scopeGetRequirements($query, $idAnswerQuestionArray, $idRequirementTypeArray, $idApplicationType){
        $query->join('t_requirements', 't_requirements.id_requirement', 'r_question_requirements.id_requirement')
            ->whereIn('t_requirements.id_requirement_type', $idRequirementTypeArray)
            ->where('t_requirements.id_application_type', $idApplicationType)
            ->whereIn('r_question_requirements.id_answer_question', $idAnswerQuestionArray)
            ->whereNull('r_question_requirements.id_subrequirement');
        $data = $query->distinct()->pluck('t_requirements.id_requirement')->toArray();
        return $data;
    }

    /**
     * Get requirements by questions 
     */
    public function scopeGetOnlySubrequirements($query, $idAnswerQuestionArray){
        $query->join('t_subrequirements', 't_subrequirements.id_subrequirement', 'r_question_requirements.id_subrequirement')
            ->whereIn('r_question_requirements.id_answer_question', $idAnswerQuestionArray)
            ->whereNotNull('r_question_requirements.id_subrequirement')
            ->select('r_question_requirements.id_question', 'r_question_requirements.id_answer_question',
                'r_question_requirements.id_requirement', 'r_question_requirements.id_subrequirement'
            );
        $data = $query->get()->toArray();
        // $data = $query->distinct()->pluck('t_subrequirements.id_subrequirement')->toArray();
        return $data;
    }

    /**
     * Get subrequirements by questions 
     */
    public function scopeGetSubrequirements($query, $idQuestionArray, $idRequirement, $idApplicationType){
        $query->join('t_subrequirements', 't_subrequirements.id_subrequirement', 'r_question_requirements.id_subrequirement')
            ->select('t_subrequirements.id_subrequirement')
            ->where('t_subrequirements.id_application_type', $idApplicationType)
            ->where('r_question_requirements.id_requirement', $idRequirement)
            ->whereIn('r_question_requirements.id_question', $idQuestionArray);
            // ->whereNotNull('r_question_requirements.id_subrequirement');
        $data = $query->distinct()->get()->toArray();
        return $data;
    }
}