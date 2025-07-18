<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Audit\Aplicability;
use App\Models\V2\Catalogs\Matter;
use App\Models\V2\Catalogs\Aspect;
use App\Models\V2\Catalogs\QuestionType;
use App\Models\V2\Catalogs\AnswersQuestion;
use App\Models\V2\Catalogs\LegalBasi;
use App\Models\V2\Catalogs\Form;
use App\Models\V2\Catalogs\Status;
use App\Traits\V2\CustomOrderTrait;
use App\Traits\V2\UtilitiesTrait;
use App\Traits\V2\RichTextTrait;

class Question extends Model
{
  use HasFactory, UtilitiesTrait, RichTextTrait, CustomOrderTrait;

  protected $table = 't_questions';
  protected $primaryKey = 'id_question';

  protected $fillable = [
    'question',
    'help_question',
    'order',
    'allow_multiple_answers',
    'id_status',
    'form_id',
    'id_matter',
    'id_aspect',
    'id_question_type',
    'id_state',
    'id_city',
    'id_track',
  ];

  /**
	 * Attributes 
	 */
	protected $appends = [
		'help_question_env'
	];

   /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'form',
    // 'type',
    // 'status'
  ];

  /**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_filter = [
		'question' => ['field' => 'question', 'type' => 'string', 'relation' => null],
		'id_status' => ['field' => 'id_status', 'type' => 'number', 'relation' => null],
    'id_question_type' => ['field' => 'id_question_type', 'type' => 'number', 'relation' => null],  
    'id_application_type' => ['field' => 'id_application_type', 'type' => 'number', 'relation' => null],  
    'id_state' => ['field' => 'id_state', 'type' => 'number', 'relation' => null],  
    'id_city' => ['field' => 'id_city', 'type' => 'number', 'relation' => null],  
	];

  /**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_included = [
		'articles',
    'articles.guideline'
	];

  const ACTIVE = 1;
  const INACTIVE = 2;

  const MULTIPLE_ANSWER = 1;
  const SINGLE_ANSWER = 0;
  
  const FEDERAL = 1;
  const STATE = 2;
  const LOCAL = 4;

  /**
   * get LegalQuoteEnv for enviroment
   */
  public function getHelpQuestionEnvAttribute() 
  {
    return $this->setUrlInRichText($this->help_question);
  }

  public function matter() 
  {
    return $this->belongsTo(Matter::class, 'id_matter', 'id_matter');
  }

  public function aspect() 
  {
    return $this->belongsTo(Aspect::class, 'id_aspect', 'id_aspect');
  }

  public function type() 
  {
    return $this->belongsTo(QuestionType::class, 'id_question_type', 'id_question_type');
  }

  public function answers() 
  {
    return $this->hasMany(AnswersQuestion::class, 'id_question', 'id_question');
  }

  public function legal_basis()
  {
    return $this->belongsToMany(LegalBasi::class, 'r_question_legal_basies', 'id_question', 'id_legal_basis');
  }

  public function form()
  {
    return $this->belongsTo(Form::class);
  }

  public function articles()
  {
    return $this->belongsToMany(LegalBasi::class, 'r_question_legal_basies', 'id_question', 'id_legal_basis');
  }
  
  /**
   * Get all of the aplicability answers for the Question
   */
  public function aplicability_answers()
  {
    return $this->hasMany(Aplicability::class, 'id_question', 'id_question');
  }

  public function status() 
  {
    return $this->belongsTo(Status::class, 'id_status', 'id_status');
  }

  /**
   * Get the state that owns the Question
   */
  public function state()
  {
    return $this->belongsTo(State::class, 'id_state', 'id_state');
  }

  /**
   * Get the city that owns the Question
   */
  public function city()
  {
    return $this->belongsTo(City::class, 'id_city', 'id_city');
  }

  public function scopeWithSource($query)
  {
    return $query->with(['form', 'type', 'status']);
  }

  public function scopeWithIndex($query)
  {
    return $query->with(['type', 'status', 'state', 'city']);
  }

  /**
   * get pool questions for block
   */
  public function scopeGetQuestionForDependency($query, $idQuestion)
  {
    $question = Question::findOrFail($idQuestion);
    $idQuestionType = $question->id_question_type;
    $query->where('form_id', $question->form_id)
      ->where('id_question_type', $idQuestionType)->where('id_matter', $question->id_matter)
      ->where('id_aspect', $question->id_aspect)->where('order', '>', $question->order);
    if ($idQuestionType == QuestionType::FEDERAL) {
      $query->whereNull('id_state')->whereNull('id_city');
    }
    if ($idQuestionType == QuestionType::STATE) {
      $idState = $question->id_state;
      $query->where('id_state', $idState)->whereNull('id_city');
    }
    if ($idQuestionType == QuestionType::LOCAL) {
      $idState = $question->id_state;
      $idCity = $question->id_city;
      $query->where('id_state', $idState)->where('id_city', $idCity);
    }
  }

  /**
   * custom filters or detail filters 
   */
  public function scopeCustomFilters($query, $idForm = null)
  {
    if ( !is_null($idForm) ) {
      $query->where('form_id', $idForm);
    }

    $filters = request('filters');
		if ( empty($filters) ) return;
    
  }
}