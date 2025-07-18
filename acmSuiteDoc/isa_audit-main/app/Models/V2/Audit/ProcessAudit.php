<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Models\V2\Admin\Customer;
use App\Models\V2\Admin\Corporate;
use App\Models\V2\Admin\User;
use App\Models\V2\Audit\Auditor;
use App\Models\V2\Audit\AplicabilityRegister;
use App\Models\V2\Catalogs\Scope;
use App\Models\V2\Catalogs\EvaluationType;
use App\Models\V2\Catalogs\ProfileType;
use Carbon\Carbon;
use App\Traits\V2\UtilitiesTrait;

class ProcessAudit extends Model
{
	use UtilitiesTrait;
	
	protected $table = 't_audit_processes';
	protected $primaryKey = 'id_audit_processes';

	/**
	 * The relationships that should always be loaded.
	 *
	 * @var array
	 */
	protected $with = [
		// 'customer',
		// 'corporate',
		// 'aplicability_register',
		// 'evaluation_type',
		// 'scope',
		// 'auditors'
	];

	/*
	* The attributes that are mass assignable.
	*
	* @var array
	*/
	protected $fillable = [
		'audit_processes',
		'specification_scope',
		'evaluate_risk',
		'evaluate_especific',
		'id_corporate',
		'id_customer',
		'id_scope',
		'evaluation_type_id',
		'date',
		'end_date',
		'per_year',
		'use_kpi'
	];

	/**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_included = [
		'customer',
		'corporate',
		'corporate.users',
		'scope',
		'evaluation_type',
		'auditors',
		'forms'
	];

	/**
	 * load the scopes that allow use in api request in this model with prefix "scopeWith".
	 * Exmaple: scopeWith + scopeMethod
	 *
	 * @var array
	 */
	protected $allow_scope = [
		'source',
		'owner',
		'sections',
		'auditors',
		'library',
		'users',
	];

	/**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_filter = [
		'audit_processes' => ['field' => 'audit_processes', 'type' => 'string', 'relation' => null],
		'id_customer' => ['field' => 'id_customer', 'type' => 'number', 'relation' => null],
		'id_corporate' => ['field' => 'id_corporate', 'type' => 'number', 'relation' => null],
		'id_scope' => ['field' => 'id_scope', 'type' => 'number', 'relation' => null],
		'date' => ['field' => 'date', 'type' => 'date_range', 'relation' => null],
		'evaluation_type_id' => ['field' => 'evaluation_type_id', 'type' => 'number', 'relation' => null],
	];

	/**
	 * Attributes 
	 */
	protected $appends = [
		'process_name',
		'date_format',
		'end_date_format',
		'dates_format',
		'is_in_current_year',
	];

	const USE_KPI = 1;
	const NO_USE_KPI = 0;

	const YES_EVALUATE_SPECIFIC = 1;
	const NO_EVALUATE_SPECIFIC = 0;

	/**
   * Get ProcessName
   */
  public function getProcessNameAttribute()
  {
		$lower = Str::lower($this->audit_processes);
		$explode = Str::of( $lower )->explode(' ');
		$standard = $explode->map(fn($word) => Str::of($word)->ucfirst())->join(' ');
    return $standard;
  }

	/*
	* Get date format
	*/
	public function getDateFormatAttribute()
	{
		return $this->getFormatDate($this->date);
	}

	/*
	* Get end_date format
	*/
	public function getEndDateFormatAttribute()
	{
		return $this->getFormatDate($this->end_date);
	}

	/*
	* Get end_date format
	*/
	public function getDatesFormatAttribute()
	{
		$init = $this->getFormatDate($this->date);
		$end = $this->getFormatDate($this->end_date);
		return "{$init} - {$end}";
	}

	/*
	* Get end_date format
	*/
	public function getIsInCurrentYearAttribute()
	{
		return $this->isInCurrentYear($this->date, $this->end_date);
	}

	/**
	 * Get the customer that owns the ProcessAudit
	 */
	public function customer() 
	{
		return $this->belongsTo(Customer::class, 'id_customer', 'id_customer');
	}

	/**
	 * Get the customer that owns the ProcessAudit
	 */
	public function corporate() 
	{
		return $this->belongsTo(Corporate::class, 'id_corporate', 'id_corporate');
	}

	/**
	 * Get the customer associated to ProcessAudit
	 */
	public function scope() 
	{
		return $this->belongsTo(Scope::class, 'id_scope', 'id_scope');
	}

	/**
	 * Get the evaluation that owns the ProcessAudit
	 */
	public function evaluation_type()
	{
		return $this->belongsTo(EvaluationType::class);
	}

	/**
	 * Get the auditors associated to ProcessAudit
	 */
	public function auditors()
	{
		return $this->belongsToMany(User::class, 't_auditor', 'id_audit_processes', 'id_user')
			->withPivot('leader');
	}

	/**
	 * Get the forms associated to ProcessAudit
	 */
	public function forms() 
	{
		return $this->hasMany(Auditor::class, 'id_audit_processes', 'id_audit_processes');
	}

	/**
	 * Get the aplicability associated to ProcessAudit
	 */
	public function aplicability_register() 
	{
		return $this->hasOne(AplicabilityRegister::class, 'id_audit_processes', 'id_audit_processes');
	}

	/**
	 * Get the renewal associated with the ProcessAudit
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function renewal()
	{
		return $this->hasOne(EvaluationRenewal::class, 'id_audit_processes', 'id_audit_processes');
	}

	public function scopeWithSource($query)
	{
		return $query->with(['evaluation_type','scope']);
	}
	
	public function scopeWithOwner($query)
	{
		return $query->with(['customer', 'corporate']);
	}

	public function scopeWithAuditors($query) 
	{
		return $query->with(['auditors.person', 'auditors.image']);
	}

	public function scopeWithUsers($query)
	{
		return $query->with(['corporate.users.person', 'corporate.users.image']);
	}
	
	public function scopeWithSections($query)
	{
		$relationships = [
			'aplicability_register.status',
			'aplicability_register.contract_matters.matter',
			'aplicability_register.contract_matters.contract_aspects.aspect',
			'aplicability_register.obligation_register',
			'aplicability_register.obligation_register.action_plan_register',
			'aplicability_register.audit_register.status',
			'aplicability_register.audit_register.action_plan_register',
		];
		return $query->with($relationships);
	}

	public function scopeWithLibrary($query)
	{
		$relationships = [
			'aplicability_register.contract_matters.matter',
			'aplicability_register.contract_matters.contract_aspects.aspect',
		];
		return $query->with($relationships)->where('id_scope', Scope::CORPORATE);
	}

	public function scopeWithIndex($query)
	{
		$relationships = ['customer','corporate','evaluation_type','scope','auditors.person'];
		return $query->with($relationships)->withSections();
	}

	/**
   * 
   */
  public function scopeCustomFilters($query)
  {
    $filters = request('filters');
		if ( empty($filters) ) return;
    
		if ( isset($filters['corporate_name']) ) {
      $query->with('corporate')->whereHas('corporate', function($subquery) use ($filters) {
				$subquery->where('corp_tradename', 'LIKE', "%{$filters['corporate_name']}%")
        ->orWhere('corp_trademark', 'LIKE', "%{$filters['corporate_name']}%");
			});
		}
  }

	/**
	 * 
	 */
	public function scopeGetPerLevel($query)
	{
		if ( !Auth::check() ) return;

		$level = Session::get('user')['profile_level'];
		
		// get all records
		if ($level == ProfileType::ADMIN_GLOBAL || $level == ProfileType::ADMIN_OPERATIVE) return;

		// get all record for customer
		if ($level == ProfileType::CORPORATE) {
			$idCustomer = Session::get('user')['id_customer'];
			$query->where('id_customer', $idCustomer);
			return;
		}

		// get all record for customer
		if ($level == ProfileType::COORDINATOR || $level == ProfileType::OPERATIVE) {
			$idCustomer = Session::get('user')['id_customer'];
			$idCorporate = Session::get('user')['id_corporate'];
			$query->where('id_customer', $idCustomer)->where('id_corporate', $idCorporate);
			return;
		}
	}

	/**
	 * 
	 */
	public function scopeNoLoadRelationships($query)
	{
		$relationships = [
			'aplicability_register.obligation_register' => fn($query) => $query->without('obligations'),
			'aplicability_register.obligation_register.action_plan_register' => fn($query) => $query->without('action_plans'),
			'aplicability_register.audit_register.action_plan_register' => fn($query) => $query->without('action_plans'),
			'aplicability_register.contract_matters.contract_aspects' => fn($query) => $query->without('aplicability_answers'),
		];
		$query->with($relationships);
	}

	/**
	 * filter by options
	 */
	public function scopeSpecialFilter($query)
	{
		$query->orderBy('id_audit_processes', 'DESC');

		$filters = request('filters');
		if ( empty($filters) ) return;
    
		if ( isset($filters['custom_filter']) && $filters['custom_filter'] == 'DASHBOARD' ) {
			$timezone = Config('enviroment.time_zone_carbon');
			$currentYear = Carbon::now($timezone)->format('Y');
			$filterByYear = fn($subquery) => $subquery->whereYear('date', '<=', $currentYear)->whereYear('end_date', '>=', $currentYear);
			$filterBySections = fn($subquery) => $subquery->whereHas('obligation_register')->orWhereHas('audit_register');
			
			$query->whereHas('aplicability_register', $filterBySections )->where($filterByYear)
				->where('evaluation_type_id', EvaluationType::EVALUATE_BOTH)->where('id_scope', Scope::CORPORATE)->where('use_kpi', ProcessAudit::USE_KPI)->get();
		}

		if ( isset($filters['custom_filter']) && $filters['custom_filter'] == 'USE_KPI' ) {
			$query->where('use_kpi', ProcessAudit::USE_KPI);
		}

		if ( isset($filters['custom_filter']) && $filters['custom_filter'] == 'NO_USE_KPI' ) {
			$query->where('use_kpi', ProcessAudit::NO_USE_KPI);
		}

		if ( isset($filters['custom_filter']) && $filters['custom_filter'] == 'IN_YEAR' ) {
			$timezone = Config('enviroment.time_zone_carbon');
			$currentYear = Carbon::now($timezone)->format('Y');
			$query->whereYear('date', '<=', $currentYear)->whereYear('end_date', '>=', $currentYear);
		}
	}

	/**
	 * get process by corporates in current year with some section evaluated or actived to graph
	 * 
	 * @param  array $arrayIdCorporates
	 */
	public function scopeGetProcessKpiWithSomeSectionActive($query, $arrayIdCorporates)
	{
		$timezone = Config('enviroment.time_zone_carbon');
    $currentYear = Carbon::now($timezone)->format('Y');
    $filterByYear = fn($query) => $query->whereYear('date', '<=', $currentYear)->whereYear('end_date', '>=', $currentYear);

    $filterBySections = fn($subquery) => $subquery->whereHas('obligation_register')->orWhereHas('audit_register');

    $relationships = [ 
			'customer.images' => fn($query) => $query->where('usage', 'dashboard'), 
			'corporate', 
			'aplicability_register' 
		];

		$query->with($relationships)->whereHas('aplicability_register', $filterBySections )
			->where($filterByYear)->whereIn('id_corporate', $arrayIdCorporates)
			->where('evaluation_type_id', EvaluationType::EVALUATE_BOTH)
			->where('id_scope', Scope::CORPORATE)->where('use_kpi', ProcessAudit::USE_KPI);
	}

	/**
	 * get process by customer in current year with section to evaluate
	 * 
   * @param  string $type (obligation, audit, compliance)
   * @param  string|int $currentYear
   * @param  int $idCustomer
	 */
	public function scopeGetProcessKpiWithSpecificSectionToEvaluate($query, $type, $currentYear, $idCustomer)
	{
		$filterByYear = fn($query) => $query->whereYear('init_date', '<=', $currentYear)->whereYear('end_date', '>=', $currentYear);
    $filterHistoricalsByYear = fn($query) => $query->whereYear('date', '<=', $currentYear)->whereYear('date', '>=', $currentYear);

    $relationships = [
      'aplicability_register.contract_matters.contract_aspects',
    ];

    if ($type == 'audit' || $type == 'compliance') {
      array_merge($relationships, [
        'aplicability_register.audit_register' => $filterByYear,
        'aplicability_register.audit_register.historicals' => $filterHistoricalsByYear,
      ]);
    }

    if ($type == 'obligation') {
      array_merge($relationships, [
        'aplicability_register.obligation_register' => $filterByYear,
        'aplicability_register.obligation_register.historicals' => $filterHistoricalsByYear,
      ]);
    }

		$query->with($relationships)->where('id_customer', $idCustomer)
			->where('id_scope', Scope::CORPORATE)
      ->where('evaluation_type_id', EvaluationType::EVALUATE_BOTH)
      ->where('use_kpi', ProcessAudit::USE_KPI); 

    if ($type == 'audit' || $type == 'compliance') {
      $query->whereHas('aplicability_register.audit_register', $filterByYear);
    }
    if ($type == 'obligation') {
      $query->whereHas('aplicability_register.obligation_register', $filterByYear);
    }

	}
}