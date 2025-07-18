<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Catalogs\Question;
use App\Models\V2\Audit\ContractAspect;
use App\Traits\V2\CustomOrderTrait;
use App\Traits\V2\UtilitiesTrait;

class EvaluateApplicabilityQuestion extends Model
{
	use UtilitiesTrait, CustomOrderTrait;

	protected $table = 'evaluate_applicability_question';

	/**
	 * The relationships that should always be loaded.
	 *
	 * @var array
	 */
	protected $with = [
		// 'question',
		// 'applicability',
	];

	/*
  * The attributes that are mass assignable.
  *
  * @var array
  */
	protected $fillable = [
		'complete',
		'id_contract_aspect',
		'id_question',
	];

	const COMPLETE = 1;
	const NO_COMPLETE = 0;

	/**
	 * Get the question that owns the EvaluateApplicabilityQuestion
	 */
	public function question()
	{
		return $this->belongsTo(Question::class, 'id_question', 'id_question');
	}

	/**
	 * Get the contract_aspect that owns the EvaluateApplicabilityQuestion
	 */
	public function contract_aspect()
	{
		return $this->belongsTo(ContractAspect::class, 'id_contract_aspect', 'id_contract_aspect');
	}

	/**
	 * Get the contract_aspect that owns the EvaluateApplicabilityQuestion
	 */
	public function applicability()
	{
		return $this->hasMany(Aplicability::class, 'id_evaluate_question', 'id');
	}

	/**
	 * Get the comment associated with the EvaluateApplicabilityQuestion
	 */
	public function comment()
	{
		return $this->morphOne(Comment::class, 'commentable');
	}
}
