<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\V2\Audit\ObligationRegister;
use App\Models\V2\Audit\AuditorObligation;
use App\Models\V2\Audit\EvaluateRequirement;
use App\Models\V2\Audit\RiskTotal;
use App\Models\V2\Audit\RiskAnswer;
use App\Models\V2\Catalogs\RiskInterpretation;
use App\Models\V2\Catalogs\Requirement;
use App\Models\V2\Catalogs\Subrequirement;
use App\Models\V2\Catalogs\Status;
use App\Models\V2\Catalogs\Priority;
use App\Traits\V2\CustomOrderTrait;
use App\Traits\V2\UtilitiesTrait;
use App\Traits\V2\PermissionFileTrait;

class Obligation extends Model
{
	use UtilitiesTrait, PermissionFileTrait, CustomOrderTrait;

	protected $table = 't_obligations';
	protected $primaryKey = 'id_obligation';

	const NO_STARTED_OBLIGATION = 20;
	const FOR_EXPIRED_OBLIGATION = 21;
	const EXPIRED_OBLIGATION = 22;
	const APPROVED_OBLIGATION = 23;
	const NO_DATES_OBLIGATION = 24;
	const NO_EVIDENCE_OBLIGATION = 28;

	protected $fillable = [
		'init_date',
		'end_date',
		'obligation_register_id',
		'id_requirement',
		'id_subrequirement',
		'id_status',
	];

	/**
	 * The relationships that should always be loaded.
	 *
	 * @var array
	 */
	protected $with = [
		// 'auditor',
		// 'requirement',
		// 'subrequirement',
		// 'status',
		// 'evaluates',
	];

	/**
	 * Attributes 
	 */
	protected $appends = [
		'init_date_format',
		'end_date_format',
		'permissions'
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

	/**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_filter = [
		'obligation_register_id' => ['field' => 'obligation_register_id', 'type' => 'number', 'relation' => null],
		'id_matter' => ['field' => 'matter_id', 'type' => 'number', 'relation' => 'requirement.form'],
		'id_aspect' => ['field' => 'aspect_id', 'type' => 'number', 'relation' => 'requirement.form'],
		'id_status' => ['field' => 'id_status', 'type' => 'number', 'relation' => null],
		'group_status' => ['field' => 'id_status', 'type' => 'array_in', 'relation' => null],
	];

	/*
	* Get init_date format
	*/
	public function getInitDateFormatAttribute()
	{
		return $this->getFormatDate($this->init_date);
	}

	/*
	* Get end_date format
	*/
	public function getEndDateFormatAttribute()
	{
		return $this->getFormatDate($this->end_date);
	}

	/**
	 * get permission for user loggin per requirement
	 */
	public function getPermissionsAttribute()
	{
		return $this->getPermissionRequirement($this, 'obligation');
	}

	/**
	 * Get the obligation_register that owns the Obligation
	 */
	public function obligation_register()
	{
		return $this->belongsTo(ObligationRegister::class);
	}

	/**
	 * Get the auditor that owns the Obligation
	 */
	public function auditor()
	{
		return $this->hasOne(AuditorObligation::class, 'id_obligation', 'id_obligation');
	}

	/**
	 * Get the requirement that owns the Obligation
	 */
	public function requirement()
	{
		return $this->belongsTo(Requirement::class, 'id_requirement', 'id_requirement');
	}

	/**
	 * Get the subrequirement that owns the Obligation
	 */
	public function subrequirement()
	{
		return $this->belongsTo(Subrequirement::class, 'id_subrequirement', 'id_subrequirement');
	}

	/**
	 * Get the status associated with the Obligation
	 */
	public function status()
	{
		return $this->belongsTo(Status::class, 'id_status', 'id_status');
	}

	/**
	 * Get the priority that owns the Obligation
	 */
	public function priority()
	{
		return $this->belongsTo(Priority::class, 'id_priority', 'id_priority');
	}

	/**
	 * Get all of the EvaluateRequirement for the Obligation
	 */
	public function evaluates()
	{
		return $this->morphToMany(EvaluateRequirement::class, 'evaluateable', 'evaluateables', 'evaluateable_id', 'evaluate_requirement_id');
	}

	/**
	 * Get all of the risk_totals for the Obligation
	 */
	public function risk_totals()
	{
		return $this->morphMany(RiskTotal::class, 'registerable');
	}

	/**
	 * Get all of the risk_answers for the Obligation
	 */
	public function risk_answers()
	{
		return $this->morphMany(RiskAnswer::class, 'registerable');
	}

	public function scopeWithIndex($query) 
	{
		$relationships = [
			'auditor.user.person',
			'auditor.user.image',
			'requirement',
			'requirement.matter',
			'requirement.aspect',
			'subrequirement.matter',
			'subrequirement.aspect',
			'status',
			'evaluates.library'
		];
		return $query->with($relationships);
	}

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
	public function scopeCustomFilter($query, $idObligationRegister)
	{
		$query->where('obligation_register_id', $idObligationRegister);

		$filters = request('filters');
		if (empty($filters)) return;

		if (isset($filters['dates'])) {
			$dates = explode(',', $filters['dates']);
			$query->whereDate('init_date', '>=', $dates[0])
				->whereDate('end_date', '<=', $dates[1]);
		}

		if (isset($filters['name'])) {
			$query->whereHas('auditor.user.person', function ($subquery) use ($filters) {
				$queryConcat = DB::raw('CONCAT_WS(" ", first_name, second_name, last_name)');
				$subquery->where($queryConcat, 'LIKE', "%{$filters['name']}%");
			});
		}

		if (isset($filters['no_requirement'])) {
			$string = "%{$filters['no_requirement']}%";

			$query->where(function ($q) use ($string) {
				$q->whereHas('requirement', function ($subquery) use ($string) {
					$subquery->where('no_requirement', 'LIKE', $string)
						->orWhere('requirement', 'LIKE', $string);
				})
				->orWhereHas('subrequirement', function ($subquery) use ($string) {
					$subquery->where('no_subrequirement', 'LIKE', $string)
						->orWhere('subrequirement', 'LIKE', $string);
				});
			});
		}
	}

	public function scopeGetRecordsDashboardObligation($query, $idObligationRegister)
	{
		$filters = request('filters');
		
		if ( empty($filters) && !isset($filters['id_aspect']) ) return $query->whereNull('id_obligation');

		$filterAspect = fn($subquery) => $subquery->where('id_aspect', $filters['id_aspect']);
		$query->with(['status', 'requirement' => $filterAspect])
			->where('obligation_register_id', $idObligationRegister)
			->whereHas('requirement', $filterAspect)->where(function($subquery) {
				$subquery->where('id_status', $this::NO_EVIDENCE_OBLIGATION)->orWhere('id_status', $this::EXPIRED_OBLIGATION);
			});
	}
}
