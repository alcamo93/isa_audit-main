<?php

namespace App\Models\V2\Catalogs;

use App\Models\V2\Catalogs\ApplicationType;
use App\Models\V2\Catalogs\City;
use App\Models\V2\Catalogs\LegalClassification;
use App\Models\V2\Catalogs\LegalBasi;
use App\Models\V2\Catalogs\State;
use App\Traits\V2\UtilitiesTrait;
use App\Traits\V2\RelationLegals;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guideline extends Model
{
	use HasFactory, UtilitiesTrait, RelationLegals;

  protected $table = 't_guidelines';
  protected $primaryKey = 'id_guideline';

	/*
	* The attributes that are mass assignable.
	*
	* @var array
	*/
	protected $fillable = [
		'guideline',
		'initials_guideline',
		'last_date',
		'id_application_type',
		'id_legal_c',
		'id_state',
		'id_city',
		'objective'  
	];

	/**
	 * The relationships that should always be loaded.
	 *
	 * @var array
	 */
	protected $with = [
		// 'application_type',
		// 'legal_classification',
		// 'guidelines',
		// 'aspects'
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
	 * Attributes 
	 */
	protected $appends = [
	  'last_date_format_text'
	];

	/**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_filter = [
		'guideline' => ['field' => 'guideline', 'type' => 'string', 'relation' => null],
		'initials_guideline' => ['field' => 'initials_guideline', 'type' => 'string', 'relation' => null],
		'id_application_type' => ['field' => 'id_application_type', 'type' => 'number', 'relation' => null],
		'id_legal_classification' => ['field' => 'id_legal_c', 'type' => 'number', 'relation' => null],
		'id_state' => ['field' => 'id_state', 'type' => 'number', 'relation' => null],
		'id_city' => ['field' => 'id_city', 'type' => 'number', 'relation' => null],
	];

	/*
	 * Get start date publication format
	 */
	public function getLastDateFormatTextAttribute()
	{
	  return $this->getFormatDateTextShort($this->last_date);
	}

	/**
	 * Get the application_type that owns the Guideline
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function application_type()
	{
		return $this->belongsTo(ApplicationType::class, 'id_application_type', 'id_application_type');
	}

	/**
	 * Get the legal_classification that owns the Guideline
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function legal_classification()
	{
		return $this->belongsTo(LegalClassification::class, 'id_legal_c', 'id_legal_c');
	}

	/**
   * Get the state that owns the Address
   */
  public function state()
  {
    return $this->belongsTo(State::class, 'id_state', 'id_state');
  }

  /**
   * Get the city that owns the Address
   */
  public function city()
  {
    return $this->belongsTo(City::class, 'id_city', 'id_city');
  }

	/**
	 * Get all of the articles for the Guideline
	 */
	public function articles()
	{
		return $this->hasMany(LegalBasi::class, 'id_guideline', 'id_guideline');
	}

  /**
   * Get all of the articles for the Guideline
   */
  public function topics()
  {
	return $this->belongsToMany(Topic::class, 'guideline_topic', 'guideline_id', 'topic_id');
  }

  /**
   * Get all of the articles for the Guideline
   */
  public function aspects()
  {
	return $this->belongsToMany(Aspect::class, 'aspect_guideline', 'guideline_id', 'aspect_id');
  }

  /**
   * Get all of the articles for the Guideline
   */
  public function guidelines()
  {
	return $this->belongsToMany(Guideline::class, 'guideline_ext_guideline', 'guideline_id', 'ext_guideline_id')
	  ->orderBy('last_date', 'desc');
  }

	/**
   * get legal basis for relations with a requirement
   */
  public function scopeGetRelationsForRequirement($query, $idRequirement)
  {
    $filterGuidelines = $this->getGuidelineByRequirement($idRequirement);
    $guidelineIds = $filterGuidelines->pluck('id_guideline');
    $query->whereIn('id_guideline', $guidelineIds);
  }

	/**
   * get legal basis for relations with a subrequirement
   */
	public function scopeGetRelationsForSubrequirement($query, $idSubrequirement)
  {
    $filterGuidelines = $this->getGuidelineBySubrequirement($idSubrequirement);
    $guidelineIds = $filterGuidelines->pluck('id_guideline');
    $query->whereIn('id_guideline', $guidelineIds);
  }

	/**
   * get legal basis for relations with a requirement
   */
  public function scopeGetRelationsForQuestion($query, $idQuestion)
  {
    $filterGuidelines = $this->getGuidelineByQuestion($idQuestion);
    $guidelineIds = $filterGuidelines->pluck('id_guideline');
    $query->whereIn('id_guideline', $guidelineIds);
  }

	public function scopeVerifyOwnershipArticleGuideline($query, $idGuideline, $idLegalBasi)
  {
    $query->where('id_guideline', $idGuideline);
    
    if ( !is_null($idLegalBasi) ) {
      $filter = fn($subquery) => $subquery->where('id_legal_basis', $idLegalBasi);
      $query->with(['articles' => $filter])
        ->whereHas('articles', $filter);
    }

    return $query->exists();
  }

	public function scopeWithIndex($query)
	{
		return $query->with(['application_type','legal_classification','guidelines','aspects']);
	}
}
