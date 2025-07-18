<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;

class QuestionTypesModel extends Model
{
       protected $table = 'c_question_types';
       protected $primaryKey = 'id_question_type';

       /**
        * Return all questions type
        */
       public function scopeGetQuestionTypes($query){
              $query->orderBy('question_type', 'DESC');
              $data = $query->get()->toArray();
              return $data;
       }
}