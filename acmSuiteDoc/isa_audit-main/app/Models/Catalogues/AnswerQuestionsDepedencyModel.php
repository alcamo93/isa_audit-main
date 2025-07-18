<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;

class AnswerQuestionsDepedencyModel extends Model
{
    protected $table = 't_answer_question_dependency';
    protected $primaryKey = 'id_question_dependency';
    
    protected $fillable = ['id_question', 'id_answer_question'];

    public function question() {
        return $this->belongsTo('App\Models\Catalogues\QuestionsModel', 'id_question', 'id_question');
    }
}