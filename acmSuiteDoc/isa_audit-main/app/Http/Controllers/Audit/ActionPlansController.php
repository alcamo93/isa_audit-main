<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Classes\StatusConstants;
use App\Classes\ProfilesConstants;
use App\Classes\Evaluate;
use App\Classes\Periods;
use App\Models\Admin\CustomersModel;
use App\Models\Admin\CorporatesModel;
use App\Models\Catalogues\StatusModel;
use App\Models\Catalogues\MattersModel;
use App\Models\Catalogues\AspectsModel;
use App\Models\Catalogues\QuestionsModel;
use App\Models\Audit\AplicabilityRegistersModel;
use App\Models\Audit\ContractAspectsModel;
use App\Models\Admin\ContractsModel;
use App\Models\Audit\AplicabilityModel;
use App\Models\Admin\AddressesModel;
use App\Models\Audit\AuditRegistersModel;
use App\Models\Audit\AuditMattersModel;
use App\Models\Audit\AuditAspectsModel;
use App\Models\Audit\QuestionRequirementsModel;
use App\Models\Audit\ActionRegistersModel;
use App\Models\Catalogues\RequirementsModel;
use App\Models\Catalogues\RequirementsLegalBasiesModel;
use App\Models\Catalogues\RequirementRecomendationsModel;
use App\Models\Catalogues\SubrequirementsModel;
use App\Models\Audit\AuditModel;
use App\Models\Audit\ActionPlansModel;
use App\Models\Audit\ActionExpiredModel;
use App\Models\Audit\TasksModel;
use App\Models\Catalogues\PeriodModel;
use App\Models\Files\FilesModel;
use App\Models\Notifications\RemindersModel;
use App\Notifications\ActionPlanNotification;
use App\Http\Controllers\Notifications\NotificationsController;
use Carbon\Carbon;
use App\User;
use App\Models\Risk\RiskAnswersModel;
use App\Models\Risk\RiskTotalsModel;
use App\Http\Controllers\Audit\AuditController;
use App\Models\Admin\AuditorModel;
use App\Models\Catalogues\ConditionsModel;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Audit\PlanUserModel;
use App\Models\Risk\RiskInterpretationsModel;
use App\Models\Risk\RiskCategoriesModel;
use App\Models\Catalogues\CategoryModel;
use App\Models\Catalogues\PriorityModel;
use App\Models\Audit\TaskExpiredModel;
use App\Traits\HelpersActionPlanTrait;
use App\Traits\PermissionActionPlanTrait;

class ActionPlansController extends Controller
{
    use HelpersActionPlanTrait, PermissionActionPlanTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**************************** Views ****************************/

    
    /**
     * Main view in audit module
     */
    public function index(){
        $profileLvl = Session::get('profile')['id_profile_type'];
        $info = ActionRegistersModel::GetActionRegistersByCorporate(Session::get('profile')['id_corporate']);
        $arrayData = [];
        $arrayData['status'] = StatusModel::getStatusByGroup(4);
        $arrayData['statusAP'] = StatusModel::getStatusByGroup(7);
        $arrayData['priorities'] = PriorityModel::all();
        $arrayData['categories'] = CategoryModel::all(['id_category', 'category']);
        $arrayData['idUser'] = User::getUserInfo(Auth::id())[0]['id_user'];
        $arrayData['periods'] = PeriodModel::GetPeriods();
        $arrayData['today'] = Carbon::now('America/Mexico_City')->toDateString();
        $arrayData['idProfileType'] = $profileLvl;
        $arrayData['statusAPConst'] = json_encode($arrayData['statusAP']);
        switch ($profileLvl) {
            case ProfilesConstants::ADMIN_GLOBAL:
                case ProfilesConstants::ADMIN_OPERATIVE:
                $arrayData['idUser'] = 0;
                $arrayData['customers'] = CustomersModel::getAllCustomers();
                $customer = $arrayData['customers'];
                $arrayData['corporates'] = CorporatesModel::getAllCorporates($customer);
                $actionReg = ActionRegistersModel::all()->toArray();
                $view = ($actionReg == NULL) ? 'errors.Contenido' : 'action_w.init.owner_main';
                break;
            case ProfilesConstants::CORPORATE:
                $arrayData['idUser'] = 0; 
                $customer = Session::get('customer')['id_customer'];
                $arrayData['customer'] = $customer;
                $arrayData['corporates'] = CorporatesModel::getAllCorporates($customer);
                $actionReg = ActionRegistersModel::join('t_corporates', 't_corporates.id_corporate', 't_action_registers.id_corporate')
                    ->join('t_customers', 't_customers.id_customer', 't_corporates.id_customer')
                    ->where('t_customers.id_customer', $arrayData['customer'])->get()->toArray();
                $view = ($actionReg == NULL) ? 'errors.Contenido' : 'action_w.init.customer_admin_main';
                break;
            case ProfilesConstants::COORDINATOR:
            case ProfilesConstants::OPERATIVE:
                $isOperative = $profileLvl == ProfilesConstants::OPERATIVE;
                $arrayData['idUser'] = $isOperative ? Session::get('user')['id_user'] : 0;
                $arrayData['customer'] = Session::get('customer')['id_customer'];
                $arrayData['corporate'] = Session::get('corporate')['id_corporate'];
                $actionReg = ActionRegistersModel::where('t_action_registers.id_corporate', $arrayData['corporate'])->get()->toArray();
                $view = ($actionReg == NULL) ? 'errors.Contenido' : 'action_w.init.corporate_admin_main';
                break;
        }
        return view($view, $arrayData);
    }
    /**
     * get action registers by corporate for data table
     */
    public function getActionRegistersByContractDT(Request $request){
        $dataRequest = $request->all(); 
        $data = ActionRegistersModel::GetActionRegisterByIdCorporateDT($dataRequest);
        return response($data);
    }
    /**
     * Get users to assigned
     */
    public function getActionPlan(Request $request){
        $idActionPlan = $request->input('idActionPlan');
        $infoAction = ActionPlansModel::GetActionPlan($idActionPlan);
        $users = AuditorModel::GetAuditorsForAP($infoAction[0]['id_audit_processes'], [StatusConstants::NO_LEADER]);
        $data['users'] = $users;
        $data['action'] = $infoAction[0];
        $object = new Periods();
        $dates = $object->createDates($infoAction[0]['currenDate'], $infoAction[0]['id_obtaining_period']);
        $data['limits']['initDate'] = $dates['initDate'];
        $data['limits']['closeDate'] = $dates['closeDate'];
        $data['limits']['realCloseDate'] = $dates['realCloseDate'];
        return response($data);
    }
    /**
     * Set users to assigned
     */
    public function setUsersAssigned(Request $request){
        $users = $request->input('users');
        DB::beginTransaction();
        foreach ($users as $u) {
            if ($u['id_plan_user'] == null) {
                $set = PlanUserModel::SetUser($u);
                if ($set != StatusConstants::SUCCESS) {
                    DB::rollBack();
                    $data['status'] = StatusConstants::ERROR;
                    $data['msg'] = 'No se pudo establecer datos de un usuario';
                    return response($data);
                }
            }
            else {
                $update = PlanUserModel::UpdateUser($u);
                if ($update != StatusConstants::SUCCESS) {
                    DB::rollBack();
                    $data['status'] = StatusConstants::ERROR;
                    $data['msg'] = 'No se pudo actualizar datos de un usuario';
                    return response($data);
                }
            }
        }
        DB::commit();
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Registro de resposables exitoso';
        return response($data);
    }
    /**
     * Remove user 
     */
    public function removeUserAssigned(Request $request){
        $idPlanUser = $request->input('idPlanUser');
        DB::beginTransaction();
        $remove = PlanUserModel::RemoveUser($idPlanUser);
        if ($remove != StatusConstants::SUCCESS) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'No se pudo remover al usuario';
            return response($data);
        }
        DB::commit();
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Resposable removido exitosamente';
        return response($data);
    }

    /**************************** Action Plan ****************************/

    /**
     * Get matters for each action register
     */
    public function getMattersByIdActionRegister(Request $request) {
        $idActionRegister = $request->input('idActionRegister');
        $aspects = ActionPlansModel::GetAspectsByIdActionRegister($idActionRegister);
        $aspectsArray = [];
        foreach ($aspects as $idAspects) {
            array_push($aspectsArray, $idAspects);
        }
        $arrayMatters = AspectsModel::GetMattersByAspects($aspectsArray);
        $data = MattersModel::GetMattersByIds($arrayMatters);
        return response($data);
    }
    /**
     * Get aspects by id_ matter
     */
    public function getAspectsByMatter(Request $request){
        $idMatter = $request->input('idMatter');
        $data = AspectsModel::GetAspectsByMatter($idMatter);
        return response($data);
    }
    /**
     * get users by process
     */
    public function getUsersByProcess(Request $request) {
        $idActionRegister = $request->input('idActionRegister');
        $infoAR = ActionRegistersModel::where('id_action_register', $idActionRegister)->first();
        $users = User::with('person')
            ->where('id_corporate', $infoAR->id_corporate)->get();
        return response()->json($users);
    }
    /**
     * 
     */
    public function getCounter(Request $request){
        $idActionRegister = $request->input('idActionRegister');
        $exclud = ActionPlansModel::join('t_requirements', 't_requirements.id_requirement', 't_action_plans.id_requirement')
            ->where([
                ['t_action_plans.id_action_register', $idActionRegister],
                ['t_requirements.has_subrequirement', 1]
            ])
            ->whereNull('t_action_plans.id_subrequirement')
            ->pluck('t_action_plans.id_action_plan')->toArray();
        $data = ActionPlansModel::toBase()
            ->selectRaw('COUNT(CASE WHEN id_status = '.StatusConstants::COMPLETED_AP.' THEN 1 END) AS complete')
            ->selectRaw('COUNT(CASE WHEN id_status = '.StatusConstants::EXPIRED_AP.' THEN 1 END) AS expired')
            ->selectRaw('COUNT(CASE WHEN id_status = '.StatusConstants::PROGRESS_AP.' THEN 1 END) AS progress')
            ->selectRaw('COUNT(CASE WHEN id_status = '.StatusConstants::REVIEW_AP.' THEN 1 END) AS review')
            ->selectRaw('COUNT(CASE WHEN id_status = '.StatusConstants::UNASSIGNED_AP.' THEN 1 END) AS unassigned')
            ->where('id_action_register', $idActionRegister)
            ->whereNotIn('t_action_plans.id_action_plan', $exclud)
            ->first();
        return response()->json($data);
    }
    /**
     * Get action
     */
    public function getActionPlanDT(Request $request){
        $dataRequest = $request->all();
        // risk query
        $risksCallback = function($query) {
            $query->addSelect([
                'interpretation' => RiskInterpretationsModel::select('interpretation')
                ->whereColumn('id_risk_category', 't_risk_totals.id_risk_category')
                ->whereRaw('t_risk_totals.total BETWEEN interpretation_min AND interpretation_max')
                ->take(1)
            ]);
            $query->addSelect([
                'risk_category' => RiskCategoriesModel::select('risk_category')
                ->whereColumn('id_risk_category', 't_risk_totals.id_risk_category')
                ->take(1)
            ]);
        };
        // Get requirement exclud
        $exclud = ActionPlansModel::join('t_requirements', 't_requirements.id_requirement', 't_action_plans.id_requirement')
            ->where([
                ['t_action_plans.id_action_register', $dataRequest['idActionRegister']],
                ['t_requirements.has_subrequirement', 1]
            ])
            ->whereNull('t_action_plans.id_subrequirement')
            ->pluck('t_action_plans.id_action_plan')->toArray();
        // Query
        $relationships = ['aspect', 'requirement', 'subrequirement', 'priority',
            'status', 'users.user.person', 'risks' => $risksCallback, 'expired'];
        $action = ActionPlansModel::with($relationships)
            ->where('t_action_plans.id_action_register', $dataRequest['idActionRegister'])
            ->whereNotIn('t_action_plans.id_action_plan', $exclud);
        // filters
        if ($dataRequest['idStatus'] != 0) $action->where('id_status', $dataRequest['idStatus']);
        if ($dataRequest['initDate'] != null && $dataRequest['endDate'] != null) {
            $action->where(function($initDate) use ($dataRequest) {
                $initDate->where('init_date', '>=', $dataRequest['initDate'].' 00:00:00')
                    ->where('close_date', '<=', $dataRequest['endDate'].' 23:59:59');
            });
        }
        if ($dataRequest['idPriority'] != 0) {
            $action->where('id_priority', $dataRequest['idPriority']);
        }
        // filter relationship
        $action->where(function($filter) use ($dataRequest) {
            if ($dataRequest['idMatter'] != 0) {
                $filter->whereHas('aspect', function($query) use ($dataRequest) {
                    $query->where('id_matter', $dataRequest['idMatter']);
                });
            }
            if ($dataRequest['idAspect'] != 0) {
                $filter->whereHas('aspect', function($query) use ($dataRequest) {
                    $query->where('id_aspect', $dataRequest['idAspect']);
                });
            }
            if ($dataRequest['requirementName'] != null) {
                $filter->whereHas('requirement', function($query) use ($dataRequest) {
                    $query->where('requirement', 'LIKE', '%'.$dataRequest['requirementName'].'%')
                        ->orWhere('no_requirement', 'LIKE', '%'.$dataRequest['requirementName'].'%');
                })
                ->orWhereHas('subrequirement', function($query) use ($dataRequest) {
                    $query->where('subrequirement', 'LIKE', '%'.$dataRequest['requirementName'].'%')
                        ->orWhere('no_subrequirement', 'LIKE', '%'.$dataRequest['requirementName'].'%');
                }); 
            }
            if ($dataRequest['userName'] != null) {
                $filter->whereHas('users.user.person', function($query) use ($dataRequest) {
                    $queryConcat = DB::raw('CONCAT_WS(" ", first_name, second_name, last_name)');
                    $query->where($queryConcat, 'LIKE', '%'.$dataRequest['userName'].'%');
                });
            }
        });
        // Paginate
        $totalRecords = $action->count('id_action_plan');
        $paginate = $action->skip($dataRequest['start'])->take($dataRequest['length'])->get();
        $data['data'] = $paginate;
        $data['recordsTotal'] = $totalRecords;
        $data['draw'] = intval($dataRequest['draw']);
        $data['recordsFiltered'] = $totalRecords;
        return response($data);
    }
    /**
     * Get expired
     */
    public function getActionExpiredDT(Request $request){
        $dataRequest = $request->all();
        // Get requirement exclud
        $exclud = ActionPlansModel::join('t_requirements', 't_requirements.id_requirement', 't_action_plans.id_requirement')
            ->where([
                ['t_action_plans.id_action_register', $dataRequest['idActionRegister']],
                ['t_requirements.has_subrequirement', 1]
            ])
            ->whereNull('t_action_plans.id_subrequirement')
            ->pluck('t_action_plans.id_action_plan')->toArray();
        // Query
        $relationships = ['aspect', 'requirement', 'subrequirement', 'priority', 'status', 'users.user.person', 'expired'];
        $action = ActionPlansModel::with($relationships)
            ->where([
                ['id_action_register', $dataRequest['idActionRegister']],
                ['id_status', StatusConstants::CLOSED_AP]
            ])
            ->whereNotIn('t_action_plans.id_action_plan', $exclud);
        // filters
        if ($dataRequest['idPriority'] != 0) {
            $action->where('id_priority', $dataRequest['idPriority']);
        }
        // filter relationship
        $action->where(function($filter) use ($dataRequest) {
            if ($dataRequest['idStatus'] != 0) {
                $filter->whereHas('expired', function($query) use ($dataRequest) {
                    $query->where('id_status', $dataRequest['idStatus']);
                });
            }
            if ($dataRequest['initDate'] != null && $dataRequest['endDate'] != null) {
                $filter->whereHas('expired', function($query) use ($dataRequest) {
                    $query->where(function($initDate) use ($dataRequest) {
                        $initDate->where('init_date', '>=', $dataRequest['initDate'].' 00:00:00')
                            ->where('close_date', '<=', $dataRequest['endDate'].' 23:59:59');
                    });
                });
            }
            if ($dataRequest['idMatter'] != 0) {
                $filter->whereHas('aspect', function($query) use ($dataRequest) {
                    $query->where('id_matter', $dataRequest['idMatter']);
                });
            }
            if ($dataRequest['idAspect'] != 0) {
                $filter->whereHas('aspect', function($query) use ($dataRequest) {
                    $query->where('id_aspect', $dataRequest['idAspect']);
                });
            }
            if ($dataRequest['requirementName'] != null) {
                $filter->whereHas('requirement', function($query) use ($dataRequest) {
                    $query->where('requirement', 'LIKE', '%'.$dataRequest['requirementName'].'%')
                        ->orWhere('no_requirement', 'LIKE', '%'.$dataRequest['requirementName'].'%');
                })
                ->orWhereHas('subrequirement', function($query) use ($dataRequest) {
                    $query->where('subrequirement', 'LIKE', '%'.$dataRequest['requirementName'].'%')
                        ->orWhere('no_subrequirement', 'LIKE', '%'.$dataRequest['requirementName'].'%');
                }); 
            }
            if ($dataRequest['userName'] != null) {
                $filter->whereHas('users.user.person', function($query) use ($dataRequest) {
                    $queryConcat = DB::raw('CONCAT_WS(" ", first_name, second_name, last_name)');
                    $query->where($queryConcat, 'LIKE', '%'.$dataRequest['userName'].'%');
                });
            }
        });
        // Paginate
        $totalRecords = $action->count('id_action_plan');
        $paginate = $action->skip($dataRequest['start'])->take($dataRequest['length'])->get();
        $data['data'] = $paginate;
        $data['recordsTotal'] = $totalRecords;
        $data['draw'] = intval($dataRequest['draw']);
        $data['recordsFiltered'] = $totalRecords;
        return response($data);
    }
    /**
     * set details expired action plan register
     */
    public function setExpired(Request $request){
        $dataRequest = $request->all();
        DB::beginTransaction();
        $set = ActionExpiredModel::SetExpired($dataRequest);
        if ($set != StatusConstants::SUCCESS) {
            DB::rollBack();
            $msg = ($set == StatusConstants::DUPLICATE) ? 'Este requerimiento ya esta registrado como vencido' : 'Algo salio mal, intente nuevamente';
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = $msg;
            return response($data);
        }
        try {
            $idActionPlan =  $dataRequest['idActionPlan'];
            $realCloseDate = $dataRequest['realCloseDate'];
            $currentAP = ActionPlansModel::with('tasks')->find($idActionPlan);
            $currentAP->update([
                'real_close_date' => $realCloseDate, 
                'id_status' => StatusConstants::CLOSED_AP
            ]);
            $tasks = collect($currentAP->toArray()['tasks']);
            foreach ($tasks as $key => $t) {
                $expiredStatus = $t['id_status'];
                $expiredDate = $t['close_date'];
                if ($t['id_status'] == TasksModel::EXPIRED) {
                    $expiredStatus = TasksModel::NO_STARTED;
                    $expiredDate = null;
                }
                TaskExpiredModel::insert([
                    'init_date' => $t['init_date'],
                    'close_date' => $expiredDate,
                    'id_status' => $expiredStatus,
                    'id_task' => $t['id_task']
                ]);
            }
            $taskToBlock = $tasks->pluck('id_task');
            TasksModel::whereIn('id_task', $taskToBlock)->update(['block' => TasksModel::BLOCK_ENABLED]);
            DB::commit();
            $data['status'] = StatusConstants::SUCCESS;
            $data['msg'] = 'Registro exitoso';
            return response($data);
        } catch (\Throwable $th) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salio mal, intente nuevamente.';
            return response($data);
        }
    }
    /**
     * set details expired action plan register
     */
    public function setAgainExpired(Request $request){
        $dataRequest = $request->all();
        DB::beginTransaction();
        try {
            $requirement = ActionExpiredModel::findOrFail($dataRequest['idActionExpired']);
            $dataRequest['realCloseDateOld'] = $requirement->real_close_date;
            $requirement->update([
                'cause' => $dataRequest['cause'],
                'real_close_date' => $dataRequest['realCloseDate']
            ]);
            // Reset tasks
            $tasks = TasksModel::where('id_action_plan', $requirement->id_action_plan)->pluck('id_task');
            TaskExpiredModel::whereIn('id_task', $tasks)->where('id_status', TasksModel::EXPIRED)
                ->update(['close_date' => null, 'id_status' => TasksModel::NO_STARTED]);
            // set status
            $this->statusActionByTask($requirement->id_action_plan);
            // Notify users
            $this->expiredActionExpiredNotify($requirement->id_action_plan, $dataRequest);
            // set commit
            DB::commit();
            $data['status'] = StatusConstants::SUCCESS;
            $data['msg'] = 'Registro exitoso';
            return response($data);
        } catch (\Throwable $th) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salio mal, intente nuevamente.';
            return response($data);
        }
    }
    /**
     * Get data action plan by id_action_plan
     */
    public function getDataActionPlan(Request $request){
        $idActionPlan = $request->input('idActionPlan');
        $relationships = ['aspect', 'requirement', 'subrequirement', 'status', 'users.user.person','expired.status'];
        $action = ActionPlansModel::with($relationships)
            ->where('id_action_plan', $idActionPlan)->first();
        $action['permission'] = $this->getPermissionReq($idActionPlan);
        return response()->json($action);
    }
    /**
     * Change priority in action plan row
     */
    public function changePriority(Request $request) {
        $idActionPlan = $request->input('idActionPlan');
        $idPriority = $request->input('idPriority');
        try {
            $action = ActionPlansModel::where('id_action_plan', $idActionPlan)
                ->update(['id_priority' => $idPriority]);
        } catch (\Throwable $th) {
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salio mal, intente nuevamente.';
            return response($data);
        }
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Registro exitoso';
        return response($data);
    }
}
