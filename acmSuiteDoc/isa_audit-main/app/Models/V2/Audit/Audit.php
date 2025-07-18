<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Audit\RiskTotal;
use App\Models\V2\Audit\RiskAnswer;
use App\Models\V2\Audit\AuditAspect;
use App\Models\V2\Audit\EvaluateAuditRequirement;
use App\Models\V2\Catalogs\RiskInterpretation;
use App\Models\V2\Catalogs\Requirement;
use App\Models\V2\Catalogs\Subrequirement;
use App\Models\V2\Catalogs\Aspect;
use App\Models\V2\Admin\Image;
use App\Traits\V2\CustomOrderTrait;
use App\Traits\V2\UtilitiesTrait;

class Audit extends Model
{
	use UtilitiesTrait, CustomOrderTrait;

	protected $table = 't_audit';
	protected $primaryKey = 'id_audit';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'answer',
		'finding',
		'id_audit_processes',
		'id_aspect',
		'id_audit_aspect',
		'id_requirement',
		'id_subrequirement',
		'id_user',
		'id_recomendation',
		'id_subrecomendation',
	];

	/**
	 * The relationships that should always be loaded.
	 *
	 * @var array
	 */
	protected $with = [
		// 'requirement',
		// 'subrequirement',
		// 'aspect',
	];

	/**
	 * Attributes 
	 */
	protected $appends = [
		'key_answer',
	];

	/**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_included = [
		'risk_totals',
		'risk_answers',
	];

	const NOT_AUDITED_AUDIT = 7;
	const AUDITING_AUDIT = 8;
	const AUDITED_AUDIT = 9;
	const FINISHED_AUDIT_AUDIT = 10;

	const NEGATIVE = 0;
	const AFFIRMATIVE = 1;
	const NO_APPLY = 2;

	/**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_filter = [
		'id_aspect' => ['field' => 'id_aspect', 'type' => 'number', 'relation' => null],
		'no_requirement' => ['field' => 'no_requirement', 'type' => 'string', 'relation' => 'requirement'],
		'requirement' => ['field' => 'requirement', 'type' => 'string', 'relation' => 'requirement'],
		'answer' => ['field' => 'answer', 'type' => 'number', 'relation' => null],
	];

	/**
	 * get key answer 
	 */
	public function getKeyAnswerAttribute()
	{
		$keys = [
			'0' => ['key' => 'NEGATIVE', 'label' => 'No Cumple'],
			'1' => ['key' => 'AFFIRMATIVE', 'label' => 'Cumple'],
			'2' => ['key' => 'NO_APPLY', 'label' => 'No Aplica']
		];
		return $keys[$this->answer];
	}

	/**
	 * Get the requirement that owns the Audit
	 */
	public function requirement()
	{
		return $this->belongsTo(Requirement::class, 'id_requirement', 'id_requirement');
	}

	/**
	 * Get the subrequirement that owns the Audit
	 */
	public function subrequirement()
	{
		return $this->belongsTo(Subrequirement::class, 'id_subrequirement', 'id_subrequirement');
	}

	/**
	 * Get all of the risk_totals for the Audit
	 */
	public function risk_totals()
	{
		return $this->morphMany(RiskTotal::class, 'registerable');
	}

	/**
	 * Get all of the risk_answers for the Audit
	 */
	public function risk_answers()
	{
		return $this->morphMany(RiskAnswer::class, 'registerable');
	}

	/**
	 * Get the aspect that owns the Audit
	 */
	public function aspect()
	{
		return $this->belongsTo(Aspect::class, 'id_aspect', 'id_aspect');
	}

	/**
	 * Get the audit_aspect that owns the Audit
	 */
	public function audit_aspect()
	{
		return $this->belongsTo(AuditAspect::class, 'id_audit_aspect', 'id_audit_aspect');
	}

	/**
	 * Get the evaluate_requirement that owns the Audit
	 */
	public function evaluate_requirement()
	{
		return $this->belongsTo(EvaluateAuditRequirement::class, 'id_audit', 'id_audit');
	}

	/**
	 * Get the user's image.
	 */
	public function images()
	{
		return $this->morphMany(Image::class, 'imageable');
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

		$relationships = ['risk_totals' => $risksCallback, 'risk_totals.category', 'risk_answers.category'];
		$query->with($relationships);
	}

	/**
	 * Set custom filters
	 */
	public function scopeAuditRegisterFilterDashboard($query)
	{
		$filters = request('filters');
		if (empty($filters)) return;

		if (isset($filters['id_audit_register'])) {
			$register = AuditRegister::findOrFail($filters['id_audit_register']);
			$auditAspects = $register->audit_matters->pluck('audit_aspects')
				->collapse()->pluck('id_audit_aspect')->toArray();
			$query->whereIn('id_audit_aspect', $auditAspects);
		}
	}

	public function scopeGetRecordsDashboardAuditOrCompliance($query, $section, $idAuditRegister)
	{
		$filters = request('filters');
		
		if ( empty($filters) && !isset($filters['id_aspect']) ) return $query->whereNull('id_audit');

		$register = AuditRegister::findOrFail($idAuditRegister);
		$auditAspect = $register->audit_matters->pluck('audit_aspects')->collapse()->firstWhere('id_aspect', $filters['id_aspect']);
		
		if ( is_null($auditAspect) ) return $query->whereNull('id_audit');

		$query->where('id_audit_aspect', $auditAspect->id_audit_aspect)->where('answer', $this::NEGATIVE);
		
		if ( $section == 'compliance') {
			$actionPlanRegister = $register->action_plan_register;
			if ( is_null($actionPlanRegister) ) return;
			$records = ActionPlan::where('id_action_register', $actionPlanRegister->id_action_register)->where('id_status', ActionPlan::COMPLETED_AP)->get();
			$skipRequirements = $records->whereNull('id_subrequirement')->pluck('id_requirement')->values()->toArray();
			$skipSubrequirements = $records->whereNotNull('id_subrequirement')->pluck('id_subrequirement')->values()->toArray();
			$query->whereNotIn('id_requirement', $skipRequirements)->whereNotIn('id_subrequirement', $skipSubrequirements);
		}
	}

	public function scopeNoLoadRelationships($query)
	{
		$relationships = [
			'requirement' => fn($query) => $query->without('subrequirements'),
		];
		$query->with($relationships);
	}

}
