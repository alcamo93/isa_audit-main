<?php

namespace App\Models\V2\Catalogs;

use App\Models\V2\Catalogs\Question;
use App\Models\V2\Catalogs\Requirement;
use App\Models\V2\Catalogs\Subrequirement;
use App\Models\V2\Catalogs\Guideline;
use App\Traits\V2\UtilitiesTrait;
use App\Traits\V2\RichTextTrait;
use App\Traits\V2\RelationLegals;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalBasi extends Model
{
  use HasFactory, UtilitiesTrait, RichTextTrait, RelationLegals;

  protected $table = 't_legal_basises';
  protected $primaryKey = 'id_legal_basis';

  /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
    'legal_basis',
    'legal_quote',
    'publish',
    'order',
    'id_guideline',
    'id_application_type',
  ];

  /**
	 * The relationships that should always be loaded.
	 *
	 * @var array
	 */
	protected $with = [
    //'guideline'
	];

  /**
	 * Attributes 
	 */
	protected $appends = [
		'legal_quote_env'
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
		'id_legal_basis' => ['field' => 'id_legal_basis', 'type' => 'number', 'relation' => null],
		'legal_basis' => ['field' => 'legal_basis', 'type' => 'string', 'relation' => null],
		'legal_quote' => ['field' => 'legal_quote', 'type' => 'string', 'relation' => null],
    'id_guideline' => ['field' => 'id_guideline', 'type' => 'number', 'relation' => null],
    'id_legal_classification' => ['field' => 'id_legal_c', 'type' => 'number', 'relation' => 'guideline'],
	];

  /**
   * get LegalQuoteEnv for enviroment
   */
  public function getLegalQuoteEnvAttribute() 
  {
    return $this->setUrlInRichText($this->legal_quote);
  }
  /**
   * The questions that belong to the LegalBasi
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function questions()
  {
    return $this->belongsToMany(Question::class, 'r_question_legal_basies', 'id_legal_basis', 'id_question');
  }

  /**
   * The requirements that belong to the LegalBasi
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function requirements()
  {
    return $this->belongsToMany(Requirement::class, 'r_requirements_legal_basies', 'id_legal_basis', 'id_requirement');
  }

  /**
   * The subrequirements that belong to the LegalBasi
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function subrequirements()
  {
    return $this->belongsToMany(Subrequirement::class, 'r_subrequirements_legal_basies', 'id_legal_basis', 'id_subrequirement');
  }

  /**
   * Get the guideline that owns the LegalBasi
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function guideline()
  {
    return $this->belongsTo(Guideline::class, 'id_guideline', 'id_guideline');
  }
  
  /**
   * custom filters or detail filters 
   */
  public function scopeCustomFilters($query, $idGuideline = null)
  {
    if ( !is_null($idGuideline) ) {
      $query->where('id_guideline', $idGuideline);
    }

    $filters = request('filters');
		if ( empty($filters) ) return;
    
  }

  /**
   * static order
   */
  public function scopeCustomOrder($query)
  {
    $query->orderBy('id_guideline', 'asc')->orderBy('order', 'asc');
  }

  /**
   * get legal basis for relations with a requirement
   */
  public function scopeGetRelationForRequirement($query, $idRequirement)
  {
    $filterGuidelines = $this->getGuidelineByRequirement($idRequirement);
    $guidelineIds = $filterGuidelines->pluck('id_guideline');
    $filterByRequirement = fn($subquery) => $subquery->wherePivot('id_requirement', $idRequirement);
    $query->with(['requirements' => $filterByRequirement])->whereIn('id_guideline', $guidelineIds);
    
    $filters = request('filters');
		if ( empty($filters) ) return;
    
    if ( isset($filters['has_relation']) && boolval($filters['has_relation']) ) {
      $query->whereHas('requirements', function($subquery) use ($idRequirement) {
        $subquery->where('t_requirements.id_requirement', $idRequirement);
      });
    }
    if ( isset($filters['has_relation']) && !boolval($filters['has_relation']) ) {
      $query->whereDoesntHave('requirements', function($subquery) use ($idRequirement) {
        $subquery->where('t_requirements.id_requirement', $idRequirement);
      });
    }
  }

  /**
   * get legal basis for relations with a subrequirement
   */
  public function scopeGetRelationForSubrequirement($query, $idSubrequirement)
  {
    $filterGuidelines = $this->getGuidelineBySubrequirement($idSubrequirement);
    $guidelineIds = $filterGuidelines->pluck('id_guideline');
    $filterBySubrequirement = fn($subquery) => $subquery->wherePivot('id_subrequirement', $idSubrequirement);
    $query->with(['subrequirements' => $filterBySubrequirement])->whereIn('id_guideline', $guidelineIds);
    
    $filters = request('filters');
		if ( empty($filters) ) return;
    
    if ( isset($filters['has_relation']) && boolval($filters['has_relation']) ) {
      $query->whereHas('subrequirements', function($subquery) use ($idSubrequirement) {
        $subquery->where('t_subrequirements.id_subrequirement', $idSubrequirement);
      });
    }
    if ( isset($filters['has_relation']) && !boolval($filters['has_relation']) ) {
      $query->whereDoesntHave('subrequirements', function($subquery) use ($idSubrequirement) {
        $subquery->where('t_subrequirements.id_subrequirement', $idSubrequirement);
      });
    }
  }

  /**
   * get legal basis for relations with a question
   */
  public function scopeGetRelationForQuestion($query, $idQuestion)
  {
    $filterGuidelines = $this->getGuidelineByQuestion($idQuestion);
    $guidelineIds = $filterGuidelines->pluck('id_guideline');
    $filterByQuestion = fn($subquery) => $subquery->wherePivot('id_question', $idQuestion);
    $query->with(['questions' => $filterByQuestion])->whereIn('id_guideline', $guidelineIds);
    
    $filters = request('filters');
		if ( empty($filters) ) return;
    
    if ( isset($filters['has_relation']) && boolval($filters['has_relation']) ) {
      $query->whereHas('questions', function($subquery) use ($idQuestion) {
        $subquery->where('t_questions.id_question', $idQuestion);
      });
    }
    if ( isset($filters['has_relation']) && !boolval($filters['has_relation']) ) {
      $query->whereDoesntHave('questions', function($subquery) use ($idQuestion) {
        $subquery->where('t_questions.id_question', $idQuestion);
      });
    }
  }

  public function scopeWithIndex($query)
	{
		return $query->with(['guideline.application_type', 'guideline.legal_classification', 'guideline.guidelines', 'guideline.aspects']);
	}
}