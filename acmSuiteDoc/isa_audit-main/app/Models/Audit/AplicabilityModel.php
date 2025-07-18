<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\Session;

class AplicabilityModel extends Model
{
    protected $table = 't_aplicability';
    protected $primaryKey = 'id_aplicability';

    protected $fillable = [
        'comments',
    ];

    public function question(){
        return $this->belongsTo('App\Models\Catalogues\QuestionsModel', 'id_question', 'id_question');
    }

    public function answer(){
        return $this->belongsTo('App\Models\Catalogues\AnswersQuestionModel', 'id_answer_question', 'id_answer_question');
    }

    public function scopeSetAplicability($query, $info){
        try {
            $model = AplicabilityModel::where([
                ['id_question', $info['idQuestion']],
                ['id_answer_question', $info['idAnswerQuestion']],
                ['id_contract_aspect', $info['idContractAspect']],
            ])->firstOrFail();
        } catch (ModelNotFoundException $th) {
            $model = new AplicabilityModel;
            $model->id_audit_processes = $info['idAuditProcess'];
            $model->id_question = $info['idQuestion'];
            $model->id_answer_question = $info['idAnswerQuestion'];
            $model->id_aspect = $info['idAspect'];
            $model->id_contract_aspect = $info['idContractAspect'];  
        } 
        $model->id_user = $idUser = Session::get('user')['id_user'];
        $model->id_answer_value = $info['idAnswerValue'];
        // Save data
        try {
            if ($info['setAnswer'] == 'true') $model->save();
            else $model->delete();
            $data['status'] = StatusConstants::SUCCESS;
            $data['idAplicability'] = $model->id_aplicability;
        } catch (Exception $e) {
            $data['status'] = StatusConstants::ERROR;
        }
        return $data;
    }
    /**
     * Get answer by contract aspect
     */
    public function scopeGetAspectAnswers($query, $info){
        $query->join('t_questions', 't_questions.id_question', 't_aplicability.id_question')
            ->where([
                ['t_questions.form_id', $info['idForm']],
                ['t_aplicability.id_audit_processes', $info['idAuditProcess']]
            ])
            ->orderBy('t_questions.order', 'ASC')
            ->orderBy('t_questions.id_question_type', 'ASC');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get answer by contract aspect
     */ 
    public function scopeGetQuestionsAspectAnswers($query, $info, $idQuestionType, $idAnswerValue){
        $query->join('t_questions', 't_questions.id_question', 't_aplicability.id_question')
            ->where('t_aplicability.id_audit_processes', $info['idAuditProcess'])
            ->where('t_aplicability.id_aspect', $info['idAspect'])
            ->where('t_aplicability.id_answer_value', $idAnswerValue)
            ->where('t_questions.id_question_type', $idQuestionType);
        $data = $query->pluck('t_aplicability.id_answer_question')->toArray();
        return $data;
    }
    /**
     * Get answer by contract aspect
     */ 
    public function scopeGetQuestionsContractAspectAnswers($query, $info, $idQuestionType, $idAnswerValue){
        $query->join('t_questions', 't_questions.id_question', 't_aplicability.id_question')
            ->where('t_aplicability.id_audit_processes', $info['idAuditProcess'])
            ->where('t_aplicability.id_contract_aspect', $info['idContractAspect'])
            ->where('t_aplicability.id_answer_value', $idAnswerValue)
            ->where('t_questions.id_question_type', $idQuestionType);
        $data = $query->pluck('t_aplicability.id_answer_question')->toArray();
        return $data;
    }
    /**
     * Count by question type
     */
    public function scopeCountQuestionTypeAspect($query, $info, $questionType, $answer){
        $query->join('t_questions', 't_questions.id_question', 't_aplicability.id_question')
            ->where([
                ['t_aplicability.id_audit_processes', $info['idAuditProcess']], 
                ['t_aplicability.id_aspect', $info['idAspect']],
                ['t_questions.id_question_type', $questionType],
                ['t_aplicability.id_answer_value', $answer]
            ]);
        $data = $query->get()->count();
        return $data; 
    }
    /**
     * 
     */
    public function scopeGetAspectAnswersDistinct($query, $data, $questionType){
        $query->join('t_questions', 't_questions.id_question', 't_aplicability.id_question')
            ->select('t_aplicability.id_answer_value')
            ->where([
                ['t_aplicability.id_audit_processes', $data['idAuditProcess']], 
                ['t_aplicability.id_aspect', $data['idAspect']],
                ['t_questions.id_question_type', $questionType]
            ])->distinct()->get()->toArray();
        return $data;
    }

    /**
     * Get question in audit
     */
    public function scopeGetQuestionInAspect($query, $idAuditProcess, $idAspect){
        $query->join('t_questions', 't_questions.id_question', 't_aplicability.id_question')
            ->where([
                ['t_aplicability.id_audit_processes', $idAuditProcess], 
                ['t_aplicability.id_aspect', $idAspect],
                ['t_aplicability.id_answer_value', StatusConstants::AFFIRMATIVE] 
            ]);
        $data = $query->get()->toArray();
        return $data;
    }

    public function scopeDeletePreviusAnswers($query, $info){
        $model = AplicabilityModel::where([
            ['id_audit_processes', $info['idAuditProcess']],
            ['id_question', $info['idQuestion']],
            ['id_contract_aspect', $info['idContractAspect']]
        ]);
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (\Throwable $th) {
            return StatusConstants::ERROR;
        }
    }

    /**
     * 
     */
    public function scopeGetAnswerByQuestion($query, $idAuditProcess, $idQuestion){
        $query->select('t_answers_question.description', 't_aplicability.id_aplicability')
            ->join('t_answers_question', 't_answers_question.id_answer_question', 't_aplicability.id_answer_question')
            ->where([
                ['t_aplicability.id_audit_processes', $idAuditProcess],
                ['t_aplicability.id_question', $idQuestion]
            ])
            ->orderBy('t_answers_question.order', 'ASC');
        $data = $query->get()->toArray();
        return $data;
    }
}