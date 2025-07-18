<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Catalogs\Matter;
use App\Models\V2\Catalogs\Aspect;
use App\Models\V2\Catalogs\Condition;
use App\Models\V2\Catalogs\LegalBasi;
use App\Models\V2\Catalogs\SubrequirementRecomendation;
use App\Models\V2\Catalogs\ApplicationType;
use App\Models\V2\Catalogs\Evidence;
use App\Models\V2\Catalogs\RequirementType;
use App\Models\V2\Catalogs\State;
use App\Models\V2\Catalogs\City;
use App\Models\V2\Catalogs\Periodicity;
use App\Models\V2\Catalogs\AnswersQuestion;
use App\Traits\V2\CustomOrderTrait;
use App\Traits\V2\UtilitiesTrait;

class Subrequirement extends Model
{
  use HasFactory, UtilitiesTrait, CustomOrderTrait;

  protected $table = 't_subrequirements';
  protected $primaryKey = 'id_subrequirement';

  protected $fillable = [
    'no_subrequirement',
    'subrequirement',
    'description',
    'help_subrequirement',
    'acceptance',
    'document',
    'order',
    'id_requirement',
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
    // 'matter',
    // 'aspect',
    // 'condition',
    // 'evidence',
    // 'application_type',
    // 'periodicity',
  ];

  /**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_filter = [
    'id_requirement' => ['field' => 'id_requirement', 'type' => 'number', 'relation' => null],  
    'no_subrequirement' => ['field' => 'no_subrequirement', 'type' => 'string', 'relation' => null],
    'subrequirement' => ['field' => 'subrequirement', 'type' => 'string', 'relation' => null],  
    'description' => ['field' => 'description', 'type' => 'string', 'relation' => null],  
    'id_requirement_type' => ['field' => 'id_requirement_type', 'type' => 'number', 'relation' => null],  
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
    'articles.guideline',
	];

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

  public function legal_basis()
  {
    return $this->belongsToMany(LegalBasi::class, 'r_subrequirements_legal_basies', 'id_subrequirement', 'id_legal_basis');
  }

  public function recomendations()
  {
    return $this->hasMany(SubrequirementRecomendation::class, 'id_subrequirement', 'id_subrequirement');
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
    return $this->belongsToMany(LegalBasi::class, 'r_subrequirements_legal_basies', 'id_subrequirement', 'id_legal_basis');
  }

  public function periodicity() 
  {
    return $this->belongsTo(Periodicity::class, 'id_periodicity');
  }

  /**
   * The subrequirements that belong to the AnswersQuestion
   */
  public function answers_question()
  {
    return $this->belongsToMany(AnswersQuestion::class, 'r_question_requirements', 'id_subrequirement', 'id_answer_question')
      ->whereNotNull('id_subrequirement')->withPivot(['id_question', 'id_answer_question', 'id_requirement', 'id_subrequirement']);
  }

  public function scopeWithIndex($query)
  {
    return $query->with(['requirement_type', 'application_type','periodicity', 'matter', 'aspect']);
  }
  /**
   * custom filters or detail filters 
   */
  public function scopeCustomFilters($query, $idRequirement = null)
  {
    if ( !is_null($idRequirement) ) {
      $relationships = ['application_type', 'requirement_type', 'state', 'city'];
      $query->with($relationships)->where('id_requirement', $idRequirement);
    }

    $filters = request('filters');
		if ( empty($filters) ) return;
    
  }

  /**
   * get Subrequirements for requirement features 
   */
  public function scopeGetRelationForAnswerQuestion($query, $idAnswerQuestion, $idRequirement, $manualFilter = null)
  {
    $filterByAnswerQuestion = fn($subquery) => $subquery->wherePivot('id_answer_question', $idAnswerQuestion);
    $query->with(['answers_question' => $filterByAnswerQuestion])->where('id_requirement', $idRequirement);
    // filters
    $filters = !is_null($manualFilter) ? $manualFilter : request('filters');
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