<?php

namespace App\Models\V2\Audit;

use App\Models\V2\Audit\ActionExpired;
use App\Models\V2\Audit\ActionPlanRegister;
use App\Models\V2\Audit\Task;
use App\Models\V2\Audit\RiskAnswer;
use App\Models\V2\Audit\RiskTotal;
use App\Models\V2\Admin\User;
use App\Models\V2\Catalogs\Aspect;
use App\Models\V2\Catalogs\Priority;
use App\Models\V2\Catalogs\Requirement;
use App\Models\V2\Catalogs\RiskInterpretation;
use App\Models\V2\Catalogs\Status;
use App\Models\V2\Catalogs\Subrequirement;
use App\Traits\V2\ActionPlanTrait;
use App\Traits\V2\CustomOrderTrait;
use App\Traits\V2\PermissionFileTrait;
use App\Traits\V2\UtilitiesTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ActionPlan extends Model
{
  use UtilitiesTrait, ActionPlanTrait, PermissionFileTrait, CustomOrderTrait;
  
  protected $table = 't_action_plans';
  protected $primaryKey = 'id_action_plan';

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'requirement',
    // 'subrequirement',
    // 'status',
    // 'priority',
    // 'auditors',
    // 'expired',
  ];

  /*
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
    'init_date',
    'close_date',
    'real_close_date',
    'finding',
    'total_tasks',
    'done_tasks',
    'complex',
    'permit',
    'id_audit_processes',
    'id_aspect',
    'id_requirement',
    'id_subrequirement',
    'id_action_register',
    'id_status',
    'id_priority',
  ];

  /**
	 * Attributes 
	 */
	protected $appends = [
		'init_date_format',
		'close_date_format',
    'real_close_date_format',
    'permissions',
	];

  /**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_included = [
    'aspect.matter',
  ];

  /**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_filter = [
    'id_action_register' => ['field' => 'id_action_register', 'type' => 'number', 'relation' => null],
    'id_matter' => ['field' => 'matter_id', 'type' => 'number', 'relation' => 'requirement.form'],
		'id_aspect' => ['field' => 'aspect_id', 'type' => 'number', 'relation' => 'requirement.form'],
    'id_condition' => ['field' => 'id_condition', 'type' => 'string', 'relation' => 'requirement'],
		'id_priority' => ['field' => 'id_priority', 'type' => 'number', 'relation' => null],
  ];

  const UNASSIGNED_AP = 13;
  const PROGRESS_AP = 16;
  const COMPLETED_AP = 17;
  const REVIEW_AP = 18;
  const EXPIRED_AP = 25;
  const CLOSED_AP = 27;
  
  // Values done_tasks and total_tasks
  const NO_MADE = 11;
  const MADE = 12;

  /*
	* Get init_date format
	*/
	public function getInitDateFormatAttribute()
	{
    return $this->getFormatDate($this->init_date);
	}

  /*
	* Get close_date format
	*/
	public function getCloseDateFormatAttribute()
	{
    return $this->getFormatDate($this->close_date);
	}

  /*
	* Get real_close_date format
	*/
	public function getRealCloseDateFormatAttribute()
	{
    return $this->getFormatDate($this->real_close_date);
	}
  
  /**
   * get permission for user loggin per requirement
   */
  public function getPermissionsAttribute() 
  {
    return $this->getPermissionRequirement($this, 'action');
  }

  /**
   * Get the ActionRegister that owns the ActionPlan
   */
  public function action_register()
  {
      return $this->belongsTo(ActionPlanRegister::class, 'id_action_register', 'id_action_register');
  }
  /**
	 * Get the requirement that owns the ActionPlan
	 */
	public function requirement()
	{
		return $this->belongsTo(Requirement::class, 'id_requirement', 'id_requirement');
	}

	/**
	 * Get the subrequirement that owns the ActionPlan
	 */
	public function subrequirement()
	{
		return $this->belongsTo(Subrequirement::class, 'id_subrequirement', 'id_subrequirement');
	}

  /**
   * Get the status associated with the ActionPlan
   */
  public function status()
  {
    return $this->belongsTo(Status::class, 'id_status', 'id_status');
  }

	/**
	 * Get the priority that owns the ActionPlan
	 */
	public function priority() 
	{
		return $this->belongsTo(Priority::class, 'id_priority', 'id_priority');
	}

  /**
   * Get the aspect that owns the ActionPlan
   */
  public function aspect()
  {
    return $this->belongsTo(Aspect::class, 'id_aspect', 'id_aspect');
  }

   /**
   * The auditors that belong to the ActionPlan
   */
  public function auditors() 
  {
    return $this->belongsToMany(User::class, 't_plan_user', 'id_action_plan', 'id_user')
			->withPivot('level', 'days');
  }

  /**
   * The risk_answers that belong to the ActionPlan
   */
  public function risk_answers() 
  {
    return $this->belongsToMany(RiskAnswer::class, 'action_plan_risk_answer', 'id_action_plan', 'id_risk_answer');
  }

  /**
   * The risk_totals that belong to the ActionPlan
   */
  public function risk_totals() 
  {
    return $this->belongsToMany(RiskTotal::class, 'action_plan_risk_total', 'id_action_plan', 'id_risk_total');
  }

  /**
   * Get all of the tasks for the ActionPlan
   */
  public function tasks() 
  {
    return $this->hasMany(Task::class, 'id_action_plan', 'id_action_plan');
  }

  public function expired() 
  {
    return $this->morphMany(ExpiredCause::class, 'expiredable');
  }

  /**
   * Get the last audit associated to ProcessAudit
   */
  public function last_expired() 
  {
    return $this->morphOne(ExpiredCause::class, 'expiredable')->latestOfMany('id');
  }


  public function scopeWithIndex($query)
  {
    return $query->withExists('expired')->with(['requirement','subrequirement','status','priority','auditors.person','auditors.image']);
  }

  public function scopeWithShow($query)
  {
    return $query->with(['requirement','subrequirement', 'expired']);
  }

  public function scopeWithExpiredIndex($query)
  {
    return $query->with(['requirement','subrequirement','expired.status','priority','auditors.person','auditors.image','expired']);
  }

  /**
   * validate if task main is complete dates 
   * in filed main_task_is_complete = 1 Ok main_task_is_complete = 0
   */
  public function scopeValidateMainTask($query) 
  {
    $query->addSelect([
      'main_task_id' => Task::select('id_task')
      ->whereColumn('t_action_plans.id_action_plan', 't_tasks.id_action_plan')
      ->where('main_task', 1)
      ->take(1)
    ]);

    $query->addSelect([
      'main_task_is_complete' => Task::selectRaw('IF(init_date IS NOT NULL AND close_date IS NOT NULL, 1, 0)')
      ->whereColumn('t_action_plans.id_action_plan', 't_tasks.id_action_plan')
      ->where('main_task', 1)
      ->take(1)
    ]);
  }

  public function scopeGetRisk($query)
	{
		$risksCallback = function($subquery) {
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
   * Filter to get only the usable requirements 
   * in the main Action Plan table 
   * or in the Expired Action Plan table.
   * method excludParents in ActionPlanTrait
   */
  public function scopeOnlyUsables($query, $idActionRegister) 
  {
    $this->excludParents($query, $idActionRegister);
  }

  public function scopeOnlyUsablesWithExpired($query, $idActionRegister)
  {
    $query->with('expired')->where('id_status', $this::CLOSED_AP);
    $this->excludParents($query, $idActionRegister);
  }

  public function scopeCustomFilters($query)
  {
    $filters = request('filters');
		if ( empty($filters) ) return;

    if ( isset($filters['name']) ) {
			$query->whereHas('auditors.person', function($subquery) use ($filters) {
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
  /**
	 * Set custom filters main requirements in action plan
	 */
	public function scopeCustomFilterMain($query) 
  {
		$filters = request('filters');
		if ( empty($filters) ) return;

    if ( isset($filters['id_status']) ) {
			$query->where('id_status', $filters['id_status']);
		}

		if ( isset($filters['dates']) ) {
			$dates = explode(',', $filters['dates']);
			$query->whereDate('init_date', '>=', $dates[0])
				->whereDate('close_date', '<=', $dates[1]);
		}
	}

  /**
	 * Set custom filters expired requirements in action plan
	 */
	public function scopeCustomFilterExpired($query) 
  {
		$filters = request('filters');
		if ( empty($filters) ) return;

    if ( isset($filters['id_status']) ) {
			$query->whereHas('expired', function($subquery) use ($filters) {
        $subquery->where('id_status', $filters['id_status']);
      });
		}

		if ( isset($filters['dates']) ) {
			$dates = explode(',', $filters['dates']);
      $query->whereHas('expired', function($subquery) use ($dates) {
        $subquery->whereDate('real_close_date', '>=', $dates[0])
          ->whereDate('real_close_date', '<=', $dates[1]);
      });
		}
	}

  /**
   * set filter for data dashboard 
   */
  public function scopeGetRecordsDashboardActionPlan($query, $idActionRegister)
  {
    $filters = request('filters');
		
		if ( empty($filters) ) return $query->whereNull('id_action_plan');
    // filter per status and matters
    if (isset($filters['id_matter']) && isset($filters['id_status'])) {
      $filter = fn($subquery) => $subquery->where('id_matter', $filters['id_matter']);
	    $query->with(['requirement' => $filter])->whereHas('requirement', $filter)
        ->where('id_status', $filters['id_status']);
    }
		// filter per condition and aspectos
    if (isset($filters['id_condition']) && isset($filters['aspects'])) {
      $filter = fn($subquery) => $subquery->where('id_matter', $filters['id_matter'])->whereIn('id_aspect', $filters['aspects']);
	    $query->with(['requirement' => $filter])->whereHas('requirement', $filter);
    }

    if (isset($filters['id_condition']) && isset($filters['id_aspect'])) {
      $filter = fn($subquery) => $subquery->where('id_aspect', $filters['id_aspect']);
	    $query->with(['requirement' => $filter])->whereHas('requirement', $filter);
    }

    if ( isset($filters['id_risk_category']) && isset($filters['interpretation']) ) {
      $interpretation = RiskInterpretation::where('id_risk_category', $filters['id_risk_category'])
        ->where('interpretation', $filters['interpretation'])->first();

      $risksCallback = function($subquery) use ($interpretation, $filters) {
        $subquery->where('id_risk_category', $filters['id_risk_category'])
          ->whereBetween('total', [$interpretation->interpretation_min, $interpretation->interpretation_max]);
      };
	    $query->whereHas('risk_totals', $risksCallback);
    }

    $query->with('status')->where('id_action_register', $idActionRegister);
  }
}