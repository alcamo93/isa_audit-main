<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\V2\Catalogs\RiskInterpretation;
use App\Models\V2\Catalogs\Requirement;
use App\Models\V2\Catalogs\Subrequirement;
use App\Models\V2\Audit\AuditAspect;
use App\Models\V2\Audit\Audit;
use App\Traits\V2\CustomOrderTrait;
use App\Traits\V2\UtilitiesTrait;

class EvaluateAuditRequirement extends Model
{
	use UtilitiesTrait, CustomOrderTrait;

	protected $table = 'evaluate_audit_requirement';

	/**
	 * The relationships that should always be loaded.
	 *
	 * @var array
	 */
	protected $with = [
		// 'requirement',
		// 'subrequirement',
		// 'audit',
	];

	/*
  * The attributes that are mass assignable.
  *
  * @var array
  */
	protected $fillable = [
		'complete',
		'id_audit_aspect',
		'id_requirement',
		'id_subrequirement',
		'id_audit'
	];

	const COMPLETE = 1;
	const NO_COMPLETE = 0;

	/**
	 * Get the requirement that owns the EvaluateAuditRequirement
	 */
	public function requirement()
	{
		return $this->belongsTo(Requirement::class, 'id_requirement', 'id_requirement');
	}

	/**
	 * Get the subrequirement that owns the EvaluateAuditRequirement
	 */
	public function subrequirement()
	{
		return $this->belongsTo(Subrequirement::class, 'id_subrequirement', 'id_subrequirement');
	}

	/**
	 * Get the audit_aspect that owns the EvaluateAuditRequirement
	 */
	public function audit_aspect()
	{
		return $this->belongsTo(AuditAspect::class, 'id_audit_aspect', 'id_audit_aspect');
	}

	/**
	 * Get the audit_aspect that owns the EvaluateAuditRequirement
	 */
	public function audit()
	{
		return $this->belongsTo(Audit::class, 'id_audit', 'id_audit');
	}

	/**
	 * Get risk relationships
	 */
	public function scopeGetRisk($query)
	{
		$risksCallback = function ($subquery) {
			$subquery->addSelect([
				'interpretation' => RiskInterpretation::select('interpretation')
					->whereColumn('id_risk_category', 't_risk_totals.id_risk_category')
					->whereRaw('t_risk_totals.total BETWEEN interpretation_min AND interpretation_max')
					->take(1)
			]);
		};

		$relationships = [
			'audit.risk_totals' => $risksCallback, 'audit.risk_totals.category', 'audit.risk_answers.category'
		];
		$query->with($relationships);
	}

	/**
	 * Set custom filters
	 */
	public function scopeAuditRegisterFilterEvaluate($query, $onlyRequirements, $idAuditAspect)
	{
		$query->where('id_audit_aspect', $idAuditAspect);

		if ($onlyRequirements) {
			$query->whereNull('id_subrequirement');
		} else {
			$query->whereNotNull('id_subrequirement');
		}
	}

	/**
	 * customer filters 
	 */
	public function scopeCustomFilterAuditEvaluate($query, $isRequirement)
	{
		$filters = request('filters');
		if (empty($filters)) return;

		if (isset($filters['several_no_requirement'])) {
			$words = Str::of($filters['several_no_requirement'])->explode(',')
				->filter(fn($item) => Str::of($item)->trim()->isNotEmpty())
				->map(fn($item) => Str::of($item)->trim());

			$query->whereHas('requirement', function ($subquery) use ($words) {
				$subquery->where(function ($queryItem) use ($words) {
					foreach ($words as $word) {
						$queryItem->orWhere('no_requirement', 'like', "%{$word}%");
					}
				});
			});
		}

		if (isset($filters['requirement'])) {
			$query->whereHas('requirement', function ($subquery) use ($filters) {
				$subquery->where('requirement', 'like', "%{$filters['requirement']}%");
			});
		}

		if (isset($filters['subrequirement'])) {
			$query->whereHas('subrequirement', function ($subquery) use ($filters) {
				$subquery->where('subrequirement', 'like', "%{$filters['subrequirement']}%");
			});
		}

		$typeRequirement = $isRequirement ? 'requirement' : 'subrequirement';

		if (isset($filters['document'])) {
			$query->whereHas($typeRequirement, function ($subquery) use ($filters) {
				$subquery->where('document', 'like', "%{$filters['document']}%");
			});
		}

		if (isset($filters['id_evidence'])) {
			$query->whereHas($typeRequirement, function ($subquery) use ($filters) {
				$subquery->where('id_evidence', $filters['id_evidence']);
			});
		}

		if (isset($filters['id_condition'])) {
			$query->whereHas($typeRequirement, function ($subquery) use ($filters) {
				$subquery->where('id_condition', $filters['id_condition']);
			});
		}

		if (isset($filters['answer'])) {
			$query->whereHas('audit', function ($subquery) use ($filters) {
				$subquery->where('answer', $filters['answer']);
			});
		}

		if (isset($filters['complete'])) {
			$query->where('complete', $filters['complete']);
		}
	}

	public function scopeAddParents($query, $idAuditAspect, $addExtras)
	{
		$query->orWhere(function ($query) use ($idAuditAspect, $addExtras) {
			foreach ($addExtras as $item) {
				$query->orWhere('id_requirement', $item)->where('id_audit_aspect', $idAuditAspect)
					->whereNull('id_subrequirement');
			}
		});
	}
}
