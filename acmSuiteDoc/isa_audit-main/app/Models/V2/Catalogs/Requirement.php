<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Catalogs\AnswersQuestion;
use App\Models\V2\Catalogs\ApplicationType;
use App\Models\V2\Catalogs\Form;
use App\Models\V2\Catalogs\Question;
use App\Models\V2\Catalogs\Matter;
use App\Models\V2\Catalogs\Aspect;
use App\Models\V2\Catalogs\Condition;
use App\Models\V2\Catalogs\Subrequirement;
use App\Models\V2\Catalogs\LegalBasi;
use App\Models\V2\Catalogs\RequirementRecomendation;
use App\Models\V2\Catalogs\Evidence;
use App\Models\V2\Catalogs\RequirementType;
use App\Models\V2\Catalogs\State;
use App\Models\V2\Catalogs\City;
use App\Models\V2\Catalogs\Periodicity;
use App\Traits\V2\CustomOrderTrait;
use App\Traits\V2\UtilitiesTrait;
use App\Traits\V2\RelationRequirements;

class Requirement extends Model
{
  use HasFactory, UtilitiesTrait, RelationRequirements, CustomOrderTrait;

  protected $table = 't_requirements';
  protected $primaryKey = 'id_requirement';

  protected $fillable = [
    'no_requirement',
    'requirement',
    'description',
    'help_requirement',
    'acceptance',
    'document',
    'has_subrequirement',
    'order',
    'form_id',
    'id_matter',
    'id_aspect',
    'id_evidence',
    'id_periodicity',
    'id_condition',
    'id_requirement_type',
    'id_application_type',
    'id_state',
    'id_city',
    'id_track',
  ];

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'form',
    // 'matter',
    // 'aspect',
    // 'condition',
    // 'evidence',
    // 'requirement_type',
    // 'application_type',
    // 'state',
    // 'city',
    // 'periodicity'
  ];

  /**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_filter = [
		'form_id' => ['field' => 'form_id', 'type' => 'number', 'relation' => null],
		'id_matter' => ['field' => 'id_matter', 'type' => 'number', 'relation' => null],
		'id_aspect' => ['field' => 'id_aspect', 'type' => 'number', 'relation' => null],
    'no_requirement' => ['field' => 'no_requirement', 'type' => 'string', 'relation' => null],
    'requirement' => ['field' => 'requirement', 'type' => 'string', 'relation' => null],  
    'description' => ['field' => 'description', 'type' => 'string', 'relation' => null],  
    'id_requirement_type' => ['field' => 'id_requirement_type', 'type' => 'number', 'relation' => null],  
    'id_application_type' => ['field' => 'id_application_type', 'type' => 'number', 'relation' => null],  
    'id_state' => ['field' => 'id_state', 'type' => 'number', 'relation' => null],  
    'id_city' => ['field' => 'id_city', 'type' => 'number', 'relation' => null],  
    'id_evidence' => ['field' => 'id_evidence', 'type' => 'number', 'relation' => null],  
    'id_condition' => ['field' => 'id_condition', 'type' => 'number', 'relation' => null],  
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

  /**
   * Get the form that owns the Requirement
   */
  public function form()
  {
    return $this->belongsTo(Form::class);
  }
  
  public function matter() 
  {
    return $this->belongsTo(Matter::class, 'id_matter', 'id_matter');
  }

  public function aspect() 
  {
    return $this->belongsTo(Aspect::class, 'id_aspect', 'id_aspect');
  }

  public function condition() 
  {
    return $this->belongsTo(Condition::class, 'id_condition', 'id_condition');
  }

  public function subrequirements()
  {
    return $this->hasMany(Subrequirement::class, 'id_requirement', 'id_requirement');
  }

  public function legal_basis()
  {
    return $this->belongsToMany(LegalBasi::class, 'r_requirements_legal_basies', 'id_requirement', 'id_legal_basis');
  }

  public function recomendations()
  {
    return $this->hasMany(RequirementRecomendation::class, 'id_requirement', 'id_requirement');
  }

  public function application_type()
  {
    return $this->belongsTo(ApplicationType::class, 'id_application_type', 'id_application_type');
  }

  public function evidence() 
  {
    return $this->belongsTo(Evidence::class, 'id_evidence', 'id_evidence');
  }

  public function requirement_type() 
  {
    return $this->belongsTo(RequirementType::class, 'id_requirement_type', 'id_requirement_type');
  }

  public function state() 
  {
    return $this->belongsTo(State::class, 'id_state', 'id_state');
  }

  public function city() 
  {
    return $this->belongsTo(City::class, 'id_city', 'id_city');
  }

  public function articles()
  {
    return $this->belongsToMany(LegalBasi::class, 'r_requirements_legal_basies', 'id_requirement', 'id_legal_basis');
  }

  public function periodicity() 
  {
    return $this->belongsTo(Periodicity::class, 'id_periodicity');
  }
  
  /**
   * The requirements that belong to the AnswersQuestion
   */
  public function answers_question()
  {
    return $this->belongsToMany(AnswersQuestion::class, 'r_question_requirements', 'id_requirement', 'id_answer_question')
      ->whereNull('id_subrequirement')->withPivot(['id_question', 'id_answer_question', 'id_requirement']);
  }

  public function scopeWithIndex($query)
  {
    $relationships = ['requirement_type', 'application_type', 'state', 'city', 'form', 'matter', 'aspect'];
    return $query->with($relationships);
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

  /**
   * get Requirements for question features 
   */
  public function scopeGetRelationForAnswerQuestion($query, $idQuestion, $idAnswerQuestion)
  {
    $filterByAnswerQuestion = fn($subquery) => $subquery->wherePivot('id_answer_question', $idAnswerQuestion);
    $query->with(['answers_question' => $filterByAnswerQuestion]);
    $question = Question::findOrFail($idQuestion);
    $idMatter = $question->id_matter;
    $idAspect = $question->id_aspect;
    // filters by aspects and only requirements catalogs
    $query->where('id_matter', $idMatter)->where('id_aspect', $idAspect)
      ->whereNull('id_customer')->whereNull('id_corporate');
    // search about question type
    $idQuestionType = $question->id_question_type;
    $isFederal = $idQuestionType == QuestionType::FEDERAL;
    $isState = $idQuestionType == QuestionType::STATE;
    $isLocal = $idQuestionType == QuestionType::LOCAL;
    // filter by requirements types
    $filterRequirementTypes = $this->getRequirementTypeByAnswerQuestion($idQuestion);
    // filter by locations about type
    if ($isFederal) {
      $query->whereIn('id_requirement_type', $filterRequirementTypes)
        ->where('id_application_type', ApplicationType::FEDERAL)
        ->whereNull('id_state')->whereNull('id_city')->get();
    }
    if ($isState) {
      $idState = $question->id_state;
      $query->whereIn('id_requirement_type', $filterRequirementTypes)
        ->where('id_application_type', ApplicationType::STATE)
        ->where('id_state', $idState)->whereNull('id_city')->get();
    }
    if ($isLocal) {
      $idState = $question->id_state;
      $idCity = $question->id_city;
      $query->whereIn('id_requirement_type', $filterRequirementTypes)
        ->where('id_application_type', ApplicationType::LOCAL)
        ->where('id_state', $idState)->where('id_city', $idCity)->get();
    }
    // filters
    $filters = request('filters');
		if ( empty($filters) ) return;
    
    if ( isset($filters['has_relation']) && boolval($filters['has_relation']) ) {
      $query->whereHas('answers_question', function($subquery) use ($idAnswerQuestion) {
        $subquery->where('t_answers_question.id_answer_question', $idAnswerQuestion);
      });
    }
    if ( isset($filters['has_relation']) && !boolval($filters['has_relation']) ) {
      $query->whereDoesntHave('answers_question', function($subquery) use ($idAnswerQuestion) {
        $subquery->where('t_answers_question.id_answer_question', $idAnswerQuestion);
      });
    }
  }
}