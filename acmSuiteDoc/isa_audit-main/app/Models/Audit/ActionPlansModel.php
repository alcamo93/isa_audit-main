<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\Session;

class ActionPlansModel extends Model
{
    use \Awobaz\Compoships\Compoships;

    protected $table = 't_action_plans';
    protected $primaryKey = 'id_action_plan';

    protected $fillable = ['id_status', 'init_date', 'close_date'];

    const UNASSIGNED_AP = 13;
    const PROGRESS_AP = 16;
    const COMPLETED_AP = 17;
    const REVIEW_AP = 18;
    const EXPIRED_AP = 25;
    const CLOSED_AP = 27;
    
    // Values done_tasks and total_tasks
    const NO_MADE = 11;
    const MADE = 12;

    public function requirement() {
        return $this->hasOne('App\Models\Catalogues\RequirementsModel', 'id_requirement', 'id_requirement');
    }

    public function subrequirement() {
        return $this->hasOne('App\Models\Catalogues\SubrequirementsModel', 'id_subrequirement', 'id_subrequirement');
    }

    public function aspect() {
        return $this->hasOne('App\Models\Catalogues\AspectsModel', 'id_aspect', 'id_aspect');
    }

    public function status() {
        return $this->hasOne('App\Models\Catalogues\StatusModel', 'id_status', 'id_status');
    }

    public function users() {
        return $this->hasMany('App\Models\Audit\PlanUserModel', 'id_action_plan', 'id_action_plan');
    }
    
    public function expired() {
        return $this->hasOne('App\Models\Audit\ActionExpiredModel', 'id_action_plan', 'id_action_plan');
    }

    public function risks() {
        // Use Compoships trait packege external to eloquent. Support eager loading
        return $this->hasMany('App\Models\Risk\RiskTotalsModel', 
            ['id_requirement', 'id_subrequirement', 'id_audit_processes'],
            ['id_requirement', 'id_subrequirement', 'id_audit_processes']
        );
    }

    public function tasks() {
        return $this->hasMany('App\Models\Audit\TasksModel', 'id_action_plan', 'id_action_plan');
    }

    public function process() {
        return $this->belongsTo('App\Models\Admin\ProcessesModel', 'id_audit_processes', 'id_audit_processes');
    }

    public function priority() {
        return $this->hasOne('App\Models\Catalogues\PriorityModel', 'id_priority', 'id_priority');
    }

    /**
     * Set action plan
     */
    public function scopeSetActionPlan($query, $actionPlan){
        $model = new ActionPlansModel();
        try {
            $model->insert($actionPlan);
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     *  Get aspects by id contract
     */
    public function scopeGetAspectsByIdAuditProcess($query, $idAuditProcess) {
        $query->join('c_aspects', 'c_aspects.id_aspect','t_action_plans.id_aspect')
            ->join('c_matters', 'c_matters.id_matter', 'c_aspects.id_matter')
            ->select('t_action_plans.id_aspect', 'c_aspects.aspect', 'c_aspects.id_matter')
            ->where('t_action_plans.id_audit_processes', $idAuditProcess);
        $data = $query->distinct()->get()->toArray();
        return $data;
    }

    /**************************** Sub Action Plan ****************************/


    /**
     * Get action plan
     */
    public function scopeGetSubActionPlanDT($query, $page, $rows, $search, $draw, $order, $idActionRegister, $idRequirement){
        $query->join('t_subrequirements', 't_subrequirements.id_subrequirement', 't_action_plans.id_subrequirement')
            ->join('c_conditions', 'c_conditions.id_condition', 't_subrequirements.id_condition')
            ->leftJoin('t_subrequirement_recomendations', 't_subrequirement_recomendations.id_recomendation', 't_action_plans.id_subrecomendation')
            ->join('c_aspects', 'c_aspects.id_aspect', 't_action_plans.id_aspect')
            ->leftJoin('t_users', 't_users.id_user', 't_action_plans.id_user_asigned')
            ->leftJoin('t_people', 't_people.id_person', 't_users.id_person')
            ->join('c_periods', 't_subrequirements.id_obtaining_period', 'c_periods.id_period')
            ->join('c_status', 't_action_plans.id_status', 'c_status.id_status')
            ->select('t_action_plans.id_subrequirement', 't_action_plans.id_requirement','t_subrequirements.no_subrequirement', 't_subrequirements.order',
                't_subrequirements.subrequirement', 'c_conditions.condition', 't_subrequirement_recomendations.recomendation',
                'c_periods.period', 't_action_plans.finding', 't_subrequirements.id_obtaining_period', 't_action_plans.id_user_asigned',
                't_action_plans.complex', 't_action_plans.total_tasks', 't_action_plans.done_tasks', 't_action_plans.id_status', 'c_status.status',
                \DB::raw('CONCAT(t_people.first_name,\' \',t_people.second_name) as complete_name'), 't_action_plans.id_contract',
                \DB::raw('DATE_FORMAT(t_action_plans.close_date, "%d/%m/%Y %H:%i") AS close_date'), 't_action_plans.id_user',
                \DB::raw('DATE_FORMAT(t_action_plans.real_close_date, "%d/%m/%Y %H:%i") AS real_close_date'),
                \DB::raw('DATE_FORMAT(t_action_plans.init_date, "%d/%m/%Y %H:%i") AS init_date'),
                't_action_plans.id_action_register', 't_action_plans.id_action_plan', 't_action_plans.permit', 't_action_plans.export',
                'c_aspects.aspect')
            ->where('t_action_plans.id_action_register', $idActionRegister)
            ->where('t_action_plans.id_requirement', $idRequirement)
            ->whereNotNull('t_action_plans.id_subrequirement')
            ->orderBy('t_subrequirements.order', 'ASC');
        // Order by
        switch ($order[0]['column']) {
            case 1:
                $columnSwitch = 't_subrequirements.no_subrequirement';
                break;
            case 2:
                $columnSwitch = 't_subrequirements.subrequirement';
                break;
            case 3:
                $columnSwitch = 'c_conditions.condition';
                break;
            case 7:
                $columnSwitch = 'c_status.status';
                break;
            case 8:
                $columnSwitch = 't_action_plans.close_date';
                break;
            case 9:
                $columnSwitch = 't_action_plans.real_close_date';
                break;
            default:
                $columnSwitch = 't_subrequirements.order';
                break;
        }
        $column = $columnSwitch;
        $dir = (isset($order[0]['dir']) ? $order[0]['dir'] : 'desc');
        $query->orderBy($column, $dir);

        $queryCount = $query->get();
        $result = $query->limit($rows)->offset($page)->get()->toArray();
        $total = $queryCount->count();
        $data['data'] = ( sizeof($result) > 0) ? $result : array();
        $data['recordsTotal'] = $total;
        $data['draw'] = (int) $draw;
        $data['recordsFiltered'] = $total;
        return $data;
    }


    /**************************** Tasks ****************************/


    /**
     * Handle delete task
     * */
    public function scopeDeleteTask($query, $idActionPlan, $idStatus){
        $task = ActionPlansModel::find($idActionPlan);
        try {
            if ($idStatus == ActionPlansModel::MADE) {
                $task->decrement('total_tasks');
                $task->decrement('done_tasks');
            } else {
                $task->decrement('total_tasks');
            }
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Handle delete task
     * */
    public function scopeAddTask($query, $idActionPlan){
        $task = ActionPlansModel::find($idActionPlan);
        $task->increment('total_tasks');
        return StatusConstants::SUCCESS;
    }
    /**
     * Handle done tasks
     */
    public function scopeDoneTask($query, $idActionPlan, $idStatusTask){
        $task = ActionPlansModel::find($idActionPlan);
        try {
            if ($idStatusTask == StatusConstants::MADE) {
                $task->increment('done_tasks');
            } else {
                $task->decrement('done_tasks');
            }
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Set number task done
     */
    public function scopeSetDoneTaskManual($query, $idActionPlan, $number){
        $task = ActionPlansModel::find($idActionPlan);
        try {
            $task->done_tasks = $number;
            $task->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Get data only register action plan
     */
    public function scopeGetActionPlan($query, $idActionPlan){
        $query->join('t_requirements', 't_requirements.id_requirement', 't_action_plans.id_requirement')
            ->select('t_action_plans.*', 't_requirements.has_subrequirement',
                't_requirements.id_obtaining_period', 't_action_plans.init_date', 't_requirements.no_requirement',
                \DB::raw('DATE_FORMAT(t_action_plans.init_date, "%Y-%m-%d") AS currenDate'),
                \DB::raw('DATE_FORMAT(t_action_plans.close_date, "%Y-%m-%d") AS closeDate'),
                \DB::raw('DATE_FORMAT(t_action_plans.real_close_date, "%Y-%m-%d") AS realCloseDate'))
            ->where('t_action_plans.id_action_plan', $idActionPlan);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * update status
     */
    public function scopeUpdateStatus($query, $idActionPlan, $status){
        $task = ActionPlansModel::find($idActionPlan);
        try {
            $task->id_status = $status;
            $task->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Set asigned
     */
    public function scopeSetAsigned($query, $idActionPlan, $idUser, $complex){
        $model = ActionPlansModel::find($idActionPlan);
        $model->id_user = Session::get('user')['id_user'];
        $model->id_user_asigned = $idUser;
        $model->complex = $complex;
        if ($model->id_status == StatusConstants::NO_RESPONSIBLE) {
            $model->id_status = StatusConstants::NO_REALIZED;
        }
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * reset total task and done task
     */
    public function scopeResetTasks($query, $idActionPlan){
        $task = ActionPlansModel::find($idActionPlan);
        try {
            $task->total_tasks = 0;
            $task->done_tasks = 0;
            $task->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Get subrequirements
     */
    public function scopeGetSubrequirementsAP($query, $idContract, $idRequirement){
        $query->select('id_status')
            ->where('t_action_plans.id_contract', $idContract)
            ->where('t_action_plans.id_requirement', $idRequirement)
            ->where('t_action_plans.id_status', StatusConstants::COMPLETED_AP)
            ->whereNotNull('t_action_plans.id_subrequirement');
        $data = $query->count();
        return $data;
    }
    /**
     * Get subrequirements completed
     */
    public function scopeGetSubrequirementsCompletedAP($query, $idContract, $idRequirement){
        $query->where('t_action_plans.id_contract', $idContract)
            ->where('t_action_plans.id_requirement', $idRequirement)
            ->where('t_action_plans.id_status', StatusConstants::COMPLETED_AP)
            ->whereNotNull('t_action_plans.id_subrequirement');
        $data = $query->count();
        return $data;
    }
    /**
     * Get AP Parent
     */
    public function scopeGetParentAP($query, $idContract, $idRequirement){
        $query->where('t_action_plans.id_contract', $idContract)
            ->where('t_action_plans.id_requirement', $idRequirement)
            ->whereNull('t_action_plans.id_subrequirement');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get subrequirements
     */
    public function scopeGeRequirementsAP($query, $idContract, $idRequirement){
        $query->where('t_action_plans.id_contract', $idContract)
            ->where('t_action_plans.id_requirement', $idRequirement)
            ->whereNull('t_action_plans.id_subrequirement');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Set real close date
     */
    public function scopeSetCloseDates($query, $data){
        $task = ActionPlansModel::find($data['idActionPlan']);
        try {
            if ($data['closeDate'] != null && $data['realCloseDate'] == null) {
                $task->close_date = $data['closeDate'].' 23:59:59';
            }
            if ($data['realCloseDate'] != null) {
                $task->real_close_date = $data['realCloseDate'].' 23:59:59';
            }
            $task->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Set value permit
     */
    public function scopeSetPermit($query, $data){
        $model = ActionPlansModel::find($data['idActionPlan']);
        try {
            $model->permit = $data['permit'];
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }

    /**************************** Notifications mails ****************************/

    /**
     * Get data for notifications
     */
    public function scopeGetDataByUser($query, $idUser, $idActionRegister){
        $query->join('t_requirements', 't_requirements.id_requirement', 't_action_plans.id_requirement')
            ->join('c_conditions', 'c_conditions.id_condition', 't_requirements.id_condition')
            ->leftJoin('t_requirement_recomendations', 't_requirement_recomendations.id_requirement', 't_action_plans.id_requirement')
            ->join('c_aspects', 'c_aspects.id_aspect', 't_action_plans.id_aspect')
            ->leftJoin('t_users', 't_users.id_user', 't_action_plans.id_user_asigned')
            ->leftJoin('t_people', 't_people.id_person', 't_users.id_person')
            ->join('c_periods', 't_requirements.id_obtaining_period', 'c_periods.id_period')
            ->join('c_status', 't_action_plans.id_status', 'c_status.id_status')
            ->select('t_action_plans.id_requirement', 't_requirements.no_requirement', 't_requirements.requirement', 'c_conditions.condition',
                't_requirement_recomendations.recomendation', 't_requirements.has_subrequirement', 'c_periods.period', 't_action_plans.finding',
                't_requirements.id_obtaining_period',  't_action_plans.id_user_asigned', 't_requirements.id_update_period',
                't_action_plans.total_tasks', 't_action_plans.done_tasks', 't_action_plans.id_status', 'c_status.status', 't_action_plans.complex',
                \DB::raw('CONCAT(t_people.first_name,\' \',t_people.second_name) as complete_name'),
                \DB::raw('DATE_FORMAT(t_action_plans.close_date, "%Y-%m-%d") AS close_date'),
                \DB::raw('DATE_FORMAT(t_action_plans.real_close_date, "%Y-%m-%d") AS real_close_date'),
                't_action_plans.id_action_register', 't_action_plans.id_action_plan')
            ->where('t_action_plans.id_user_asigned', $idUser)
            ->where('t_action_plans.id_action_register', $idActionRegister)
            ->whereNull('t_action_plans.id_subrequirement');
        $data = $query->get()->toArray();
        return $data;
    }

    /**************************** Process Dashboard ****************************/

    /**
     *  Get aspects by id contract and id aspect
     */
    public function scopeGetAspectsForDashboard($query, $idAuditProcess, $idAspect) {
        $query->join('t_requirements', 't_requirements.id_requirement', 't_action_plans.id_requirement')
            ->join('c_aspects', 'c_aspects.id_aspect', 't_requirements.id_aspect')
            ->join('c_conditions', 'c_conditions.id_condition', 't_requirements.id_condition')
            ->join('c_status', 't_action_plans.id_status', 'c_status.id_status')
            // ->leftJoin('t_users', 't_users.id_user', 't_action_plans.id_user_asigned')
            // ->leftJoin('t_people', 't_people.id_person', 't_users.id_person')
            ->select('t_action_plans.id_action_plan',
                    't_action_plans.id_requirement',
                    't_requirements.no_requirement',
                    't_requirements.requirement',
                    't_requirements.description',
                    't_requirements.has_subrequirement',
                    't_requirements.id_condition',
                    't_action_plans.id_status',
                    't_requirements.id_aspect',
                    'c_conditions.condition',
                    't_action_plans.total_tasks',
                    't_action_plans.done_tasks',
                    'c_status.status',
                    't_action_plans.id_audit_processes',
                    't_action_plans.finding',
                    // \DB::raw('CONCAT(t_people.first_name,\' \',t_people.second_name) as complete_name'),
                    \DB::raw('DATE_FORMAT(t_action_plans.close_date, "%Y-%m-%d") AS close_date'),
                    \DB::raw('DATE_FORMAT(t_action_plans.real_close_date, "%Y-%m-%d") AS real_close_date')
                )
            ->where('t_action_plans.id_audit_processes', $idAuditProcess)
            ->whereNull('t_action_plans.id_subrequirement');
            if ( !is_null($idAspect) ) {
                $query->where('t_action_plans.id_aspect', $idAspect);
            }
            $query->orderBy('t_requirements.order', 'ASC');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     *  Get total and total by conditions 
     */
    public function scopeGetCount($query, $idAuditProcess, $idCondition) {
        $query->join('t_requirements', 't_requirements.id_requirement', 't_action_plans.id_requirement')
            ->join('c_conditions', 'c_conditions.id_condition', 't_requirements.id_condition')
            ->where('t_action_plans.id_audit_processes', $idAuditProcess);
            if ( !is_null($idCondition) ) {
                $query->where('t_requirements.id_condition', $idCondition);
            }
        $data = $query->count();
        return $data;
    }
    /**
     * Get total action by aspect and codition
     */
    public function scopeGetCountAspectCondition($query, $idAuditProcess, $idCondition, $idAspect) {
        $query->join('t_requirements', 't_requirements.id_requirement', 't_action_plans.id_requirement')
            ->join('c_conditions', 'c_conditions.id_condition', 't_requirements.id_condition')
            ->where('t_action_plans.id_audit_processes', $idAuditProcess)
            ->where('t_requirements.id_condition', $idCondition)
            ->where('t_requirements.id_aspect', $idAspect);
        $data = $query->count();
        return $data;
    }
    /**
     * Count actions for status
     */
    public function scopeGetCountAspect($query, $idAuditProcess, $idStatus) {
        $query->where('t_action_plans.id_audit_processes', $idAuditProcess)
            ->where('t_action_plans.id_status', $idStatus);
        $data = $query->count();
        return $data;
    }

    /**************************** Process Excel ****************************/

    /**
     * Get aspects by id action registers
     */
    public function scopeGetAspectsByIdActionRegister($query, $idActionRegister) {
        $query->select('t_action_plans.id_aspect')
            ->where('t_action_plans.id_action_register', $idActionRegister);
        $data = $query->distinct()->get()->toArray();
        return $data;
    }
    /**
     * Get register by contract
     */
    public function scopeGetRequirementsByIdContract($query, $idContract){
        $query->join('t_requirements', 't_requirements.id_requirement', 't_action_plans.id_requirement')
            ->join('c_conditions', 'c_conditions.id_condition', 't_requirements.id_condition')
            ->leftJoin('t_requirement_recomendations', 't_requirement_recomendations.id_recomendation', 't_action_plans.id_recomendation')
            ->join('c_aspects', 'c_aspects.id_aspect', 't_action_plans.id_aspect')
            ->leftJoin('t_users', 't_users.id_user', 't_action_plans.id_user_asigned')
            ->leftJoin('t_people', 't_people.id_person', 't_users.id_person')
            ->join('c_periods', 't_requirements.id_obtaining_period', 'c_periods.id_period')
            ->join('c_status', 't_action_plans.id_status', 'c_status.id_status')
            ->select('t_action_plans.id_requirement', 't_requirements.no_requirement', 't_requirements.order', 't_requirements.requirement', 'c_conditions.condition',
                't_requirement_recomendations.recomendation', 't_requirements.has_subrequirement', 'c_periods.period', 't_action_plans.finding',
                't_requirements.id_obtaining_period',  't_action_plans.id_user_asigned', 't_requirements.id_update_period', 't_requirements.description',
                't_action_plans.total_tasks', 't_action_plans.done_tasks', 't_action_plans.id_status', 'c_status.status', 't_action_plans.complex',
                \DB::raw('CONCAT(t_people.first_name,\' \',t_people.second_name) as complete_name'),
                \DB::raw('DATE_FORMAT(t_action_plans.close_date, "%Y-%m-%d") AS close_date'), 't_action_plans.id_user',
                \DB::raw('DATE_FORMAT(t_action_plans.real_close_date, "%Y-%m-%d") AS real_close_date'),
                't_action_plans.id_action_register', 't_action_plans.id_action_plan', 't_action_plans.permit', 't_action_plans.export')
            ->whereNull('t_action_plans.id_subrequirement')
            ->where('t_action_plans.id_contract', $idContract)
            ->orderBy('t_requirements.order', 'ASC');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get register by contract
     */
    public function scopeGetSubrequirementsByIdAuditProcess($query, $idAuditProcess, $idRequirement){
        $query->join('t_subrequirements', 't_subrequirements.id_subrequirement', 't_action_plans.id_subrequirement')
            ->join('c_conditions', 'c_conditions.id_condition', 't_subrequirements.id_condition')
            // ->leftJoin('t_subrequirement_recomendations', 't_subrequirement_recomendations.id_recomendation', 't_action_plans.id_subrecomendation')
            ->join('c_aspects', 'c_aspects.id_aspect', 't_action_plans.id_aspect')
            // ->leftJoin('t_users', 't_users.id_user', 't_action_plans.id_user_asigned')
            // ->leftJoin('t_people', 't_people.id_person', 't_users.id_person')
            ->join('c_periods', 't_subrequirements.id_obtaining_period', 'c_periods.id_period')
            ->join('c_status', 't_action_plans.id_status', 'c_status.id_status')
            ->select('t_action_plans.id_subrequirement', 't_action_plans.id_requirement','t_subrequirements.no_subrequirement', 't_subrequirements.order',
                't_subrequirements.subrequirement', 'c_conditions.condition', 't_subrequirements.id_condition',
                'c_periods.period', 't_action_plans.finding', 't_subrequirements.id_obtaining_period',
                't_action_plans.complex', 't_action_plans.total_tasks', 't_action_plans.done_tasks', 't_action_plans.id_status', 'c_status.status',
                't_subrequirements.description', 't_action_plans.id_aspect',
                \DB::raw('DATE_FORMAT(t_action_plans.close_date, "%Y-%m-%d") AS close_date'),
                \DB::raw('DATE_FORMAT(t_action_plans.real_close_date, "%Y-%m-%d") AS real_close_date'),
                't_action_plans.id_action_register', 't_action_plans.id_action_plan', 't_action_plans.permit', 't_action_plans.finding')
            ->whereNotNull('t_action_plans.id_subrequirement')
            ->where('t_action_plans.id_requirement', $idRequirement)
            ->where('t_action_plans.id_audit_processes', $idAuditProcess)
            ->orderBy('t_subrequirements.order', 'ASC');
        $data = $query->get()->toArray();
        return $data;
    }

    /***************** Evaluate ******************/

    /**
     * Count By Status
     */
    public function scopeDistinctSubrequirementsAP($query, $idContract, $idRequirement){
        $query->select('id_status')
            ->where('t_action_plans.id_contract', $idContract)
            ->where('t_action_plans.id_requirement', $idRequirement)
            ->whereNotNull('t_action_plans.id_subrequirement');
        $data = $query->distinct()->get()->toArray();
        return $data;
    }
}
