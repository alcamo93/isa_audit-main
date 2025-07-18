<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Catalogs\Requirement;
use App\Models\V2\Catalogs\Subrequirement;
use App\Models\V2\Catalogs\Question;
use App\Models\V2\Catalogs\AnswerValue;
use App\Models\V2\Catalogs\Status;
use App\Traits\V2\UtilitiesTrait;

class AnswersQuestion extends Model
{
  use UtilitiesTrait;

  protected $table = 't_answers_question';
  protected $primaryKey = 'id_answer_question';

  protected $fillable = [
    'description',
    'order',
    'id_question',
    'id_answer_value',
    'id_status',
    'id_track',
  ];

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'value',
    // 'status'
  ];
  
  /**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_filter = [
		'description' => ['field' => 'description', 'type' => 'string', 'relation' => null],
		'id_answer_value' => ['field' => 'id_answer_value', 'type' => 'number', 'relation' => null],
	];
  
  /**
   * The requirements that belong to the AnswersQuestion
   */
  public function requirements_assigned()
  {
    
    return $this->belongsToMany(Requirement::class, 'r_question_requirements', 'id_answer_question', 'id_requirement')
      ->whereNull('id_subrequirement')->withPivot(['id_question', 'id_answer_question', 'id_requirement', 'id_subrequirement']);
  }

  /**
   * The subrequirements that belong to the AnswersQuestion
   */
  public function subrequirements_assigned()
  {
    return $this->belongsToMany(Subrequirement::class, 'r_question_requirements', 'id_answer_question', 'id_subrequirement')
      ->withPivot(['id_question', 'id_answer_question', 'id_requirement', 'id_subrequirement']);
  }

  /**
   * The Questions that belong to the AnswersQuestion
   */
  public function dependency()
  {
    return $this->belongsToMany(Question::class, 't_answer_question_dependency', 'id_answer_question', 'id_question');
  }

  /**
   * Get the value that owns the AnswersQuestion
   */
  public function value() 
  {
    return $this->belongsTo(AnswerValue::class, 'id_answer_value', 'id_answer_value');
  }

  /**
   * Get the value that owns the AnswersQuestion
   */
  public function status() 
  {
    return $this->belongsTo(Status::class, 'id_status', 'id_status');
  }

  /**
   * Get the question that owns the AnswersQuestion
   */
  public function question()
  {
    return $this->belongsTo(Question::class, 'id_question', 'id_question');
  }

  /**
   * only dependency list for question
   */
  public function scopeGetDependencyPerQuestion($query, $idQuestion)
  {
    $relationships = ['value', 'status', 'dependency'];
    $query->with($relationships)->where('id_question', $idQuestion);
  }

  /**
   * custom filters or detail filters 
   */
  public function scopeCustomFilters($query, $idQuestion = null)
  {
    if ( !is_null($idQuestion) ) {
      $query->where('id_question', $idQuestion);
    }

    $filters = request('filters');
		if ( empty($filters) ) return;
    
  }

  /**
   * static order
   */
  public function scopeCustomOrder($query)
  {
    $query->orderBy('id_question', 'asc')->orderBy('order', 'asc');
  }

  public function scopeWithIndex($query)
  {
    $query->with(['value','status']);
  }
}