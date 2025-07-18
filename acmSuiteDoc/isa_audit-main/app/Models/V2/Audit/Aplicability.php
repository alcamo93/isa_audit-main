<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Catalogs\AnswersQuestion;
use App\Models\V2\Catalogs\Question;
use App\Traits\V2\CustomOrderTrait;
use App\Traits\V2\UtilitiesTrait;

class Aplicability extends Model
{
  use UtilitiesTrait, CustomOrderTrait;
  
  protected $table = 't_aplicability';
  protected $primaryKey = 'id_aplicability';

  /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'id_answer_value',
    'id_contract_aspect',
    'id_aspect',
    'id_question',
    'id_answer_question',
    'id_user',
    'id_evaluate_question',
	];

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'answer_question'
  ];
  
  /**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_included = [
    //
  ];

  /**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_filter = [
    // 
  ];

  const NOT_CLASSIFIED_APLICABILITY =	3;
  const CLASSIFIED_APLICABILITY =	4;
  const EVALUATING_APLICABILITY =	5;
  const FINISHED_APLICABILITY =	6;

  /**
   * Get the answer that owns the Aplicability
   */
  public function answer_question()
  {
    return $this->belongsTo(AnswersQuestion::class, 'id_answer_question', 'id_answer_question');
  }

  /**
   * Get the question that owns the Aplicability
   */
  public function question()
  {
    return $this->belongsTo(Question::class, 'id_question', 'id_question');
  }
}