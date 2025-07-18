<?php

namespace App\Models\V2\Catalogs;

use App\Models\V2\Catalogs\Requirement;
use App\Models\V2\Catalogs\Subrequirement;
use App\Traits\V2\UtilitiesTrait;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AnswerQuestionRequirement extends Pivot
{
  use UtilitiesTrait;

  protected $table = 'r_question_requirements';
  protected $primaryKey = 'id_question_requirement';

  protected $fillable = [
    'id_question',
		'id_answer_question',
		'id_requirement',
		'id_subrequirement'
  ];

  /**
   * Get the requirement that owns the AnswerQuestionRequirement
   */
  public function requirement()
  {
    return $this->belongsTo(Requirement::class, 'id_requirement', 'id_requirement');
  }

  /**
   * Get the subrequirement that owns the AnswerQuestionRequirement
   */
  public function subrequirement()
  {
    return $this->belongsTo(Subrequirement::class, 'id_requirement', 'id_requirement');
  }
}
