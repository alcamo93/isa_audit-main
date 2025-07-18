<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;
use App\Classes\StatusConstants;

class QuestionsModel extends Model
{
    protected $table = 't_questions';
    protected $primaryKey = 'id_question';

    protected $fillable = ['help_question', 'form_id'];

    public function matter() {
        return $this->hasOne('App\Models\Catalogues\MattersModel', 'id_matter', 'id_matter');
    }

    public function aspect() {
        return $this->hasOne('App\Models\Catalogues\AspectsModel', 'id_aspect', 'id_aspect');
    }

    public function type() {
        return $this->hasOne('App\Models\Catalogues\QuestionTypesModel', 'id_question_type', 'id_question_type');
    }

    public function answers() {
        return $this->hasMany('App\Models\Catalogues\AnswersQuestionModel', 'id_question', 'id_question');
    }
    /**
     * Set guideline
     */
    public function scopeSetQuestion($query, $info) {
        $model = new QuestionsModel();
        $model->question = $info['question'];
        $model->order = $info['order'];
        // $model->help_question = $info['helpQuestion']; // commented because extract images
        $model->id_matter = $info['IdMatter']; // remover al quitar columnas
        $model->id_aspect = $info['IdAspect']; // remover al quitar columnas
        $model->id_question_type = $info['IdQuestionType'];
        $model->id_state = $info['IdState'];
        $model->id_city = $info['IdCity'];
        $model->allow_multiple_answers = $info['allowMultipleAnswers'];
        $model->form_id = (int)$info['IdForm'];

        // $all_record = QuestionsModel::where('form_id', $model->form_id)->count();

        // if ($all_record > 0) {
        //     $record = QuestionsModel::where('order', (int)$info['order'])
        //         ->where('form_id', $model->form_id)
        //         ->first();

        //     $model->order = !is_null($record) ? $all_record + 1 : (int)$info['order'];
        // } else {
        //     $model->order = 1;
        // }

        if($model->save()) {
            $data['status'] = StatusConstants::SUCCESS;
            $data['idQuestion'] = $model->id_question;
            return $data;
        } else {
            $data['status'] = StatusConstants::ERROR;
            $data['idQuestion'] = null;
            return $data;
        }
    }
    /**
     * Get guidelines to datatables
     */
    public function scopeGetQuestionsDT($query, $page, $rows, $search, $draw, $order, $filterName, $questionType, $idState, $idCity, $form_id) {
        $where = [];

        if ($form_id != null) array_push($where, ['t_questions.form_id', $form_id]);
        if ($filterName != null) array_push($where, ['t_questions.question','LIKE','%'.$filterName.'%']);
        if ($questionType != null) array_push($where, ['t_questions.id_question_type', $questionType]);
        if ($idState != '0' && $idState != null) array_push($where, ['t_questions.id_state', $idState]);
        if ($idCity != '0' && $idCity != null) array_push($where, ['t_questions.id_city', $idCity]);
        if($where) $query->where($where);

        //Order by
        $query->orderBy('order', 'ASC');

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
     * Update Guideline
     */
    public function scopeUpdateQuestion($query, $info, $helpQuestion) {
        $model = QuestionsModel::find($info['idQuestion']);
        $model->question = $info['question'];
        $model->order = $info['order'];
        $model->help_question = $helpQuestion;
        $model->id_matter = $info['IdMatter'];
        $model->id_aspect = $info['IdAspect'];
        $model->id_question_type = $info['IdQuestionType'];
        $model->id_state = $info['IdState'];
        $model->id_city = $info['IdCity'];
        $model->allow_multiple_answers = $info['allowMultipleAnswers'];
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (\Throwable $th) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * delete guideline
     */
    public function scopeDeleteQuestion($query, $idQuestion)
    {
        $response = StatusConstants::ERROR;
        try{
            $query
            ->where('id_question', $idQuestion)
            ->delete();
            $response = StatusConstants::SUCCESS;
        }
        catch (\Exception $e) {
               if($e->getCode() == '23000') $response =  StatusConstants::WARNING;
               else $response =  StatusConstants::ERROR;
        }
        return $response;
    }
    /**
     * Get questions by aspects
     */
    public function scopeGetQuestionsByAspect($query, $idForm, $idState, $idCity){
        if ($idState != null) $query->where('t_questions.id_state', $idState);
        else $query->whereNull('t_questions.id_state');
        if ($idCity != null) $query->where('t_questions.id_city', $idCity);
        else $query->whereNull('t_questions.id_city');
        $query->where('t_questions.form_id', $idForm)
            ->where('t_questions.id_status', StatusConstants::ACTIVE)
            ->orderBy('order', 'ASC');
        $data = $query->pluck('t_questions.id_question')->toArray();
        return $data;
    }
    /*
    * Get question data
    */
    public function scopeGetQuestion($query, $idquestion){
        $query->where('t_questions.id_question', $idquestion);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Set question basis by question id
     */
    public function scopeChangeStatusQuestion($query, $idQuestion, $status)
    {
        $response = StatusConstants::ERROR;
        if($status == 2) $st = 1;
        if($status == 1) $st = 2;
        $q = $query
            ->where('id_question', $idQuestion)
            ->update([
                'id_status' => $st
            ]);        
        if($q) $response = StatusConstants::SUCCESS;
        return $response;
    }
    /**
     * Get questions by aspects
     */
    public function scopeCountQuestionsByAspect($query, $idAspect, $idQuestionType, $idState, $idCity){
        if ($idState != null) $query->where('t_questions.id_state', $idState);
        else $query->whereNull('t_questions.id_state');
        if ($idCity != null) $query->where('t_questions.id_city', $idCity);
        else $query->whereNull('t_questions.id_city');
        $query->where('t_questions.id_aspect', $idAspect)
            ->where('t_questions.id_question_type', $idQuestionType)
            ->where('t_questions.id_status', StatusConstants::ACTIVE)
            ->orderBy('order', 'ASC');
        $data = $query->get()->count();
        return $data;
    }
    /**
     * Get all data question
     */
    public function scopeGetAllDataQuestion($query, $idQuestion){
        $query->join('c_matters', 'c_matters.id_matter', 't_questions.id_matter')
            ->join('c_aspects', 'c_aspects.id_aspect', 't_questions.id_aspect')
            ->join('c_question_types', 'c_question_types.id_question_type', 't_questions.id_question_type')
            ->leftJoin('c_states', 'c_states.id_state', 't_questions.id_state')
            ->leftJoin('c_cities', 'c_cities.id_city', 't_questions.id_city')
            ->select(
                'c_matters.matter',
                'c_aspects.aspect',
                't_questions.order',
                't_questions.allow_multiple_answers',
                'c_question_types.question_type',
                't_questions.question',
                't_questions.help_question'
            )
            ->where('t_questions.id_question', $idQuestion);
        $data = $query->get()->toArray();
        return $data;
    }

    public function form()
    {
        return $this->belongsTo('App\Models\V2\Catalogs\Form');
    }
}