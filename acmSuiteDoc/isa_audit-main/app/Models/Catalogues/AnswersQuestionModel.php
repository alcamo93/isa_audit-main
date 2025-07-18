<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;

class AnswersQuestionModel extends Model
{
    protected $table = 't_answers_question';
    protected $primaryKey = 'id_answer_question';
    
    public function block() {
        return $this->hasMany('App\Models\Catalogues\AnswerQuestionsDepedencyModel', 'id_answer_question', 'id_answer_question');
    }

    public function question() {
        return $this->belongsTo('App\Models\Catalogues\QuestionsModel', 'id_question', 'id_question');
    }

    /**
     * Get answers to datatables
     */
    public function scopeGetAnswersQuestionDT($query, $page, $rows, $search, $draw, $order, $idQuestion, $filterAnswer, $filterIdAnswerValue){
        $query->where('t_answers_question.id_question', $idQuestion);
        if ($filterAnswer != null) {
            $query->where(function($query) use ($filterAnswer){
                $query->where('t_answers_question.description','LIKE','%'.$filterAnswer.'%');
            });
        }
        if ($filterIdAnswerValue != '') {
            $query->where('t_answers_question.id_answer_value', $filterIdAnswerValue);
        }
        //Order by
        switch ($order[0]['column']) {
            case 0:
                $columnSwitch = 't_answers_question.order';
                break;
            case 1:
                $columnSwitch = 't_answers_question.description';
                break;
            default:
                $columnSwitch = 't_answers_question.id_answer_question';
                break;
        }
        $column = $columnSwitch;   
        $dir = (isset($order[0]['dir']) ? $order[0]['dir'] : 'desc');            
        $query->orderBy($column, $dir);
        
        /*Search*/
        $query->where(function($query) use ($search){
            $query->where('t_answers_question.description','LIKE','%'.$search['value'].'%');
        });
        
        $queryCount = $query->get();
        $result = $query->limit($rows)->offset($page)->get()->toArray();
        $total = $queryCount->count();
        $data['data'] = ( sizeof($result) > 0) ? $result : array();
        $data['recordsTotal'] = $total;
        $data['draw'] = (int) $draw;
        $data['recordsFiltered'] = $total;
        return $data;
    }
    /**
     * Return all answers of question
    */
    public function scopeGetAnswersQuestionByQuestion($query, $idQuestion){
        $query->join('t_answer_values', 't_answer_values.id_answer_value', 't_answers_question.id_answer_value')
            ->where([
                ['t_answers_question.id_question', $idQuestion],
                ['t_answers_question.id_status', StatusConstants::ACTIVE]
            ])
            ->orderBy('t_answers_question.order', 'ASC');
        $data = $query->get()->toArray();
        return $data;
	}
    /**
     * Get data answer
     */
    public function scopeGetAnswersQuestion($query, $idAnswerQuestion){
        $query->where('t_answers_question.id_answer_question', $idAnswerQuestion);
        $data = $query->get()->toArray();
        return $data;
	}
    /**
     * Set/update answer
     */
    public function scopeSetAnswerQuestion($query, $info){
        if ($info['idAnswer'] == null) {
            $model = new AnswersQuestionModel();
            $model->id_question = $info['idQuestion'];
        }
        else {
            try {
                $model = AnswersQuestionModel::findOrFail($info['idAnswer']);
            } catch (\Throwable $th) {
                return StatusConstants::WARNING;
            }
        }
        $model->description = $info['answer'];
        $model->order = $info['order'];
        $model->id_answer_value = $info['idAnswerValue'];
        $model->id_status = StatusConstants::ACTIVE;
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (\Throwable $th) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * delete industry from add catalogs module
     */
    public function scopeDeleteAnswerQuestion($query, $idAnswerQuestion){
        try {
            $model = AnswersQuestionModel::findOrFail($idAnswerQuestion);
            try{
                $model->delete();
                return StatusConstants::SUCCESS;
            }
            catch (\Exception $e) {
                return StatusConstants::ERROR;
            }
        } catch (ModelNotFoundException $th) {
            return StatusConstants::WARNING;
        }
    }
}