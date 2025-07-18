<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Traits\V2\UtilitiesTrait;

class AnswerQuestionDependency extends Pivot
{
  use UtilitiesTrait;

  protected $table = 't_answer_question_dependency';
  protected $primaryKey = 'id_question_dependency';

  protected $fillable = [
    'id_question',
		'id_answer_question'
  ];
}
