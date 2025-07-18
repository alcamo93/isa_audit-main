<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\Session;

class ActionRegistersModel extends Model
{
    protected $table = 't_action_registers';
    protected $primaryKey = 'id_action_register';

    public function process(){
        return $this->belongsTo('App\Models\Admin\ProcessesModel', 'id_audit_processes', 'id_audit_processes');
    }

    public function plans() {
        return $this->hasMany('App\Models\Audit\ActionPlansModel', 'id_action_register', 'id_action_register');
    }
    /**
     *  Get action registers by
     */
    public function scopeGetActionRegisterByIdCorporateDT($query, $info){
        $relationship = ['process', 'process.audit', 'process.customer', 'process.corporate', 'process.scope'];
        $action = ActionRegistersModel::with($relationship);
        // filters
        $action->whereHas('process', function($filter) use ($info) {
            if ($info['idCustomer'] != 0) {
                $filter->where(function($process) use ($info) {
                   $process->where('id_customer', $info['idCustomer']); 
                });
           }
           if ($info['idCorporate'] != 0) {
                $filter->where(function($process) use ($info) {
                   $process->where('id_corporate', $info['idCorporate']);
                }); 
           }
           if ($info['auditProcess'] != '') {
                $filter->where(function($process) use ($info) {
                   $process->where('t_audit_processes.audit_processes', 'like', '%'.$info['auditProcess'].'%');
                });
           }
        });
        // For only user profile
        if ($info['idUser'] != 0) {
            $relationship = array_merge($relationship, ['plans.users', 'plans.tasks.users']);
            $action->whereHas('plans.users', function($planUser) use ($info) {
                $planUser->where('id_user', $info['idUser']);
            });
            $action->orWhereHas('plans.tasks.users', function($taskUser) use ($info) {
                $taskUser->where('id_user', $info['idUser']);
            });
        }
        // Paginate
        $totalRecords = $action->count('id_action_register');
        $paginate = $action->skip($info['start'])->take($info['length'])->get();
        $data['data'] = $paginate;
        $data['recordsTotal'] = $totalRecords;
        $data['draw'] = intval($info['draw']);
        $data['recordsFiltered'] = $totalRecords;
        return $data;
    }

    public function scopeGetActionRegisterByIdCorporate($query, $idCorporate)
    {
        $query
        ->select('t_action_registers.id_action_register')
        ->where([
            ['t_action_registers.id_corporate', $idCorporate],
            ['t_action_registers.id_status', 1],
        ]);
        $data =  $query->get()->toArray();
        return $data;
    }

    /**
     * Get active contracts
     * (by default it looks for contracts near to finish in at least 6 next months,
     * if the customer is set it will look for the customer contracts)
     */
    public function scopeGetActionRegister($query, $idCustomer = null) {
        $today = now()->format('Y-m-d'). ' 23:59:59';
        $where = [
            ['t_action_registers.id_status', 1],
            ['t_contracts.end_date', '>', $today],
        ];

        if($idCustomer) array_push($where, ['t_corporates.id_customer', $idCustomer]);
        else
        {
            $dpmonths = now()->addMonth(6)->format('Y-m-d'). ' 23:59:59';
            array_push($where, ['t_contracts.end_date', '<', $dpmonths]);
        }

        $query
        ->join('t_corporates', 't_corporates.id_corporate', 't_action_registers.id_corporate')
        ->join('t_contracts', 't_contracts.id_contract', 't_action_registers.id_contract')
        ->join('t_customers', 't_corporates.id_customer', 't_customers.id_customer')
        ->where($where);

        $query->orderBy('contract', 'ASC');
        $result = $query->get()->toArray();
        $data = ( sizeof($result) > 0) ? $result : array();
        return $data;
    }

    /**
     * Get active contracts
     * (by default it looks for contracts near to finish in at least 6 next months,
     * if the customer is set it will look for the customer contracts)
     */
    public function scopeGetActionRegisterByID($query, $idActionRegister)
    {
        $today = now()->format('Y-m-d'). ' 23:59:59';
        $where = [
            ['t_action_registers.id_status', 1],
            ['t_contracts.end_date', '>', $today],
            ['t_action_registers.id_action_register', $idActionRegister]
        ];

        $query
        ->join('t_corporates', 't_corporates.id_corporate', 't_action_registers.id_corporate')
        ->join('t_contracts', 't_contracts.id_contract', 't_action_registers.id_contract')
        ->join('t_customers', 't_corporates.id_customer', 't_customers.id_customer')
        ->where($where);

        $query->orderBy('contract', 'ASC');
        $result = $query->get()->toArray();
        $data = ( sizeof($result) > 0) ? $result : array();
        return $data;
    }

    /**
     *
     */
    public function scopeGetActionPlansByAR($query, $idActionRegister, $idCorporate = null)
    {
        $where = [
            ['id_action_register', $idActionRegister]
        ];

        if($idCorporate) array_push($where, ['id_corporate', $idCorporate]);

        $query = \DB::table('t_action_plans')
            ->join('c_aspects', 'c_aspects.id_aspect', 't_action_plans.id_aspect')
            ->join('c_matters', 'c_matters.id_matter', 'c_aspects.id_matter')
            ->join('t_requirements', 't_requirements.id_requirement', 't_action_plans.id_requirement')
            ->join('t_users', 't_users.id_user', 't_action_plans.id_user')
            ->join('t_people', 't_people.id_person', 't_users.id_person')
            ->join('c_status', 'c_status.id_status', 't_action_plans.id_status')
            ->select(
                't_action_plans.id_action_plan',
                't_action_plans.init_date',
                't_action_plans.close_date',
                't_action_plans.real_close_date',
                't_action_plans.finding',
                't_action_plans.total_tasks',
                't_action_plans.done_tasks',
                't_action_plans.id_contract',
                't_action_plans.id_aspect',
                't_action_plans.id_requirement',
                't_action_plans.id_subrequirement',
                't_action_plans.id_user',
                't_action_plans.id_recomendation',
                't_action_plans.id_action_register',
                't_action_plans.id_user_asigned',
                't_action_plans.created_at',
                't_action_plans.updated_at',
                'c_matters.id_matter',
                'c_matters.matter',
                'c_aspects.aspect',
                't_requirements.requirement',
                't_requirements.no_requirement',
                't_requirements.id_condition',
                'c_status.status as name_status',
                \DB::raw('CONCAT(t_people.first_name,\' \',t_people.second_name) as name_asigned')
            )
            ->where($where)
            ->whereNull('t_action_plans.id_subrequirement');
        $result = $query->get()->toArray();
        $data = ( sizeof($result) > 0) ? $result : array();

        return $data;
    }
    /**
     *
     */
    public function scopeGetTask($query, $idActionPlan = null)
    {
        $query = \DB::table('t_tasks');
        if($idActionPlan)  $query->where('id_action_plan', $idActionPlan);
        $result = $query->get()->toArray();
        $data = ( sizeof($result) > 0) ? $result : array();
        return $data;
    }
    /**
     * Get Action register by contract
     */
    // public function scopeGetActionRegisterByContract($query, $idContract){
    //     $query->where('t_action_registers.id_contract', $idContract);
    //     $data = $query->get()->toArray();
    //     return $data;
    // }

    public function scopeSetActionRegister($query, $idAuditProcess, $idCorporate){
        $model = new ActionRegistersModel();
        $model->id_corporate = $idCorporate;
        $model->id_audit_processes = $idAuditProcess;
        $model->id_status = StatusConstants::ACTIVE;
        try {
            $model->save();
            $data['status'] = StatusConstants::SUCCESS;
            $data['idActionRegister'] = $model->id_action_register;
            return $data;
        } catch (Exception $e) {
            return $data['status'] = StatusConstants::ERROR;
        }
    }
    /**
     * Get Action register by contracts
     */
    public function scopeGetActionRegistersByGroup($query, $idsContracts){
        $query->select('t_action_registers.id_action_register')
            ->whereIn('t_action_registers.id_contract', $idsContracts);
        $data = $query->distinct()->get()->toArray();
        return $data;
    }
    /**
     * Verfify there are process with action registers
     */
    public function scopeGetActionRegistersByProcess($query, $idUser){
        $query->join('t_audit_processes', 't_audit_processes.id_audit_processes', 't_action_registers.id_audit_processes');
        $query->join('t_auditor', 't_audit_processes.id_audit_processes', 't_auditor.id_audit_processes')
        ->where('t_auditor.id_user', $idUser);
        $data = $query->distinct()->get()->toArray();
        return $data;
    }

    public function scopeGetActionRegistersByCorporate($query, $idCorporate){
        $query->select('t_action_registers.id_action_register')
            ->where('t_action_registers.id_corporate', $idCorporate);
        $data = $query->distinct()->get()->toArray();
        return $data;
    }
    
    
    public function scopeGetActionRegisterContract($query, $idCorporate){
            $query->select('t_action_registers.id_contract')
            ->where('t_action_registers.id_corporate', $idCorporate);
        $data = $query->distinct()->get()->toArray();
        return $data;
        }

}
