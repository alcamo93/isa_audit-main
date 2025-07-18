<?php

namespace App\Models\V2\Catalogs;

use App\Traits\V2\UtilitiesTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
	use HasFactory, UtilitiesTrait;

	/*
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'version',
		'is_current',
		'matter_id',
		'aspect_id', 
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
	];

	/**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_included = [
		'matter',
		'aspect',
	];

	/**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_filter = [
		'name' => ['field' => 'name', 'type' => 'string', 'relation' => null],
		'id_matter' => ['field' => 'matter_id', 'type' => 'number', 'relation' => null],
		'id_aspect' => ['field' => 'aspect_id', 'type' => 'number', 'relation' => null],
		'is_current' => ['field' => 'is_current', 'type' => 'number', 'relation' => null],
	];

	/* Relations one to many (inverse) */
	public function matter()
	{
		return $this->belongsTo('App\Models\V2\Catalogs\Matter', 'matter_id', 'id_matter');
	}

	public function aspect()
	{
		return $this->belongsTo('App\Models\V2\Catalogs\Aspect', 'aspect_id', 'id_aspect');
	}

	/* Relations one to many */
	public function questions()
	{
		return $this->hasMany('App\Models\V2\Catalogs\Question');
	}

	/* Relations one to many */
	public function requirements()
	{
		return $this->hasMany('App\Models\V2\Catalogs\Requirement');
	}

	public function getLastVersion($idAspect)
	{
		$forms = Form::where('aspect_id', $idAspect)->get();
		$maxVersion = $forms->pluck('version')->max();
		$lastVersionForm = is_null($maxVersion) ? 1 : $maxVersion + 1;
		return $lastVersionForm;
	}

	public function scopeCustomOrder($query)
	{
		$query->orderBy('matter_id')->orderBy('aspect_id')->orderBy('name', 'asc')->orderBy('version');
	}

	public function scopeWithIndex($query)
	{
		return $query->with(['matter','aspect']);
	}

	/**
   * Verify ownership level audit of the section used in paths 
   */
  public function scopeVerifyOwnershipFormQuestion($query, $idForm, $idQuestion = null, $idAnswerQuestion = null)
  {
    $query->where('id', $idForm);

    if ( !is_null($idQuestion) ) {
      $filterQuestion = fn($subquery) => $subquery->where('id_question', $idQuestion);
      $query->with(['questions' => $filterQuestion])
        ->whereHas('questions', $filterQuestion);
    }
    
    if ( !is_null($idAnswerQuestion) ) {
      $filterAnswers = fn($subquery) => $subquery->where('id_answer_question', $idAnswerQuestion);
      $query->with(['questions.answers' => $filterAnswers])
        ->whereHas('questions.answers', $filterAnswers);
    }

    return $query->exists();
  }

	/**
   * Verify ownership level audit of the section used in paths 
   */
  public function scopeVerifyOwnershipFormRequirement($query, $idForm, $idRequirement = null, $idSubrequirement = null)
  {
    $query->where('id', $idForm);

    if ( !is_null($idRequirement) ) {
      $filterQuestion = fn($subquery) => $subquery->where('id_requirement', $idRequirement);
      $query->with(['requirements' => $filterQuestion])
        ->whereHas('requirements', $filterQuestion);
    }
    
    if ( !is_null($idSubrequirement) ) {
      $filterAnswers = fn($subquery) => $subquery->where('id_subrequirement', $idSubrequirement);
      $query->with(['requirements.subrequirements' => $filterAnswers])
        ->whereHas('requirements.subrequirements', $filterAnswers);
    }

    return $query->exists();
  }

	public function scopeVerifyOwnershipFormRequirementRecomendation($query, $idForm, $idRequirement, $idRecomendation)
	{
    $query->where('id', $idForm);

    if ( !is_null($idRequirement) ) {
      $filterQuestion = fn($subquery) => $subquery->where('id_requirement', $idRequirement);
      $query->with(['requirements' => $filterQuestion])
        ->whereHas('requirements', $filterQuestion);
    }
		
		if ( !is_null($idRecomendation) ) {
			$filterRecomendation = fn($subquery) => $subquery->where('id_recomendation', $idRecomendation);
			$query->with(['requirements.recomendations' => $filterRecomendation])
				->whereHas('requirements.recomendations', $filterRecomendation);
		}

    return $query->exists();
  }

	public function scopeVerifyOwnershipFormRequirementSubrecomendation($query, $idForm, $idRequirement, $idSubrequirement, $idRecomendation)
	{
    $query->where('id', $idForm);

    if ( !is_null($idRequirement) ) {
      $filterQuestion = fn($subquery) => $subquery->where('id_requirement', $idRequirement);
      $query->with(['requirements' => $filterQuestion])
        ->whereHas('requirements', $filterQuestion);
    }
		
    if ( !is_null($idSubrequirement) ) {
			$filterAnswers = fn($subquery) => $subquery->where('id_subrequirement', $idSubrequirement);
      $query->with(['requirements.subrequirements' => $filterAnswers])
			->whereHas('requirements.subrequirements', $filterAnswers);
    }
		
		if ( !is_null($idRecomendation) ) {
			$filterRecomendation = fn($subquery) => $subquery->where('id_recomendation', $idRecomendation);
			$query->with(['requirements.subrequirements.recomendations' => $filterRecomendation])
				->whereHas('requirements.subrequirements.recomendations', $filterRecomendation);
		}

    return $query->exists();
  }
}
