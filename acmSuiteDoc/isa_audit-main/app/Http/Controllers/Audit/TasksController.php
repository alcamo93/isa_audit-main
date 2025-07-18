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
use App\Classes\Evaluate;
use App\Models\Audit\TasksModel;
use App\Models\Audit\ActionPlansModel;
use App\Models\Audit\ActionExpiredModel;
use App\Models\Audit\TaskUserModel;
use App\Models\Catalogues\StatusModel;
use App\Models\Catalogues\PeriodModel;
use App\Models\Catalogues\ConditionsModel;
use App\Models\Catalogues\RequirementsModel;
use App\Models\Catalogues\SubrequirementsModel;
use App\Notifications\ActionPlanNotification;
use App\Models\Notifications\RemindersModel;
use App\Models\Files\FilesModel;
use App\Classes\Periods;
use Carbon\Carbon;
use App\User;
use App\Models\Audit\TaskExpiredModel;
use App\Traits\HelpersActionPlanTrait;
use App\Traits\HelpersTaskTrait;
use App\Traits\PermissionActionPlanTrait;
use App\Traits\FileTrait;

class TasksController extends Controller
{
    use HelpersActionPlanTrait, HelpersTaskTrait, PermissionActionPlanTrait, FileTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Datatables corporates registers to audit
     */
    public function getTaskDT(Request $request){
        $dataRequest = $request->all();
        $relationships = ['status', 'users.user.person', 'action.users', 'file', 'taskExpired'];
        $tasks = TasksModel::with($relationships)->where('id_action_plan', $dataRequest['idActionPlan']);
        $action = ActionPlansModel::find($dataRequest['idActionPlan']);
        // filter by stage and status
        $isExpired = ( $action->id_status == ActionPlansModel::CLOSED_AP); 
        if ($dataRequest['section'] == 'action' && $isExpired) {
            $filterTasks['stage'] = TasksModel::NORMAL_STAGE;
            $filterTasks['id_status'] = TasksModel::EXPIRED;
            $tasks->where($filterTasks);
        }
        //Order by
        $arrayOrder = [
            0 => 't_tasks.title',
            1 => 't_tasks.task',
            4 => 't_tasks.init_date',
            5 => 't_tasks.close_date'
        ];
        $column = $arrayOrder[$dataRequest['order'][0]['column']];   
        $dir = (isset($dataRequest['order'][0]['dir']) ? $dataRequest['order'][0]['dir'] : 'desc');            
        $tasks->orderBy($column, $dir);
        // Paginate
        $totalRecords = $tasks->count('id_action_plan');
        $paginate = $tasks->skip($dataRequest['start'])->take($dataRequest['length'])->get()->toArray();
        foreach ($paginate as $key => $t) {
            $permissions = $this->getPermissionTask($t['id_task']);
            $paginate[$key]['permission'] = $permissions['inTask'];
            $paginate[$key]['inApproved'] = $permissions['inApproved'];
            $paginate[$key]['inUpload'] = $permissions['inUpload'];
        }
        $data['data'] = $paginate;
        $data['recordsTotal'] = $totalRecords;
        $data['draw'] = intval($dataRequest['draw']);
        $data['recordsFiltered'] = $totalRecords;
        return response($data);
    }
    /**
     * Set task
     */
    public function setTask(Request $request) {
        $requestData = $request->all();
        // Staus by dates
        $requestData['idStatus'] = $this->getStatusTaskByDates($requestData['initDate'], $requestData['endDate']);
        $idActionPlan = $requestData['idActionPlan'];
        try {
            // set task
            DB::beginTransaction();
            $setTask = TasksModel::SetTask($requestData);
            if ($setTask['stage'] == TasksModel::EXPIRED_STAGE) {
                TaskExpiredModel::insert([
                    'init_date' => $requestData['initDate'],
                    'close_date' => $requestData['endDate'],
                    'id_status' => $requestData['idStatus'],
                    'id_task' => $setTask['id_task']
                ]);
            }
            // Set users
            $users = $requestData['users'];
            $usersNotify = [];
            foreach ($users as $u) {
                if ($u['id_task_user'] == null) {
                    $u['id_task'] = ($u['id_task'] == null) ? $setTask['id_task'] : $u['id_task'];
                    $setUser = TaskUserModel::SetUser($u);
                    array_push($usersNotify, $setUser['id_user']);
                }
            }
            // add task in action plan register
            // $addTask = ActionPlansModel::AddTask($idActionPlan);
            // update dates in action plan register
            if ($setTask['stage'] == TasksModel::NORMAL_STAGE) {
                $setDates = $this->setDatesActionByTasks($idActionPlan);
            }
            // update status action plan row
            if ($requestData['idStatus'] != TasksModel::EXPIRED) { // wait until cron change status AP
                $setStatusAP = $this->statusActionByTask($idActionPlan);
            }
            // Notify users assigned
            $this->notifyTaskUser($setTask['id_task'], $usersNotify);
            DB::commit();
            $data['status'] = StatusConstants::SUCCESS;
            $data['msg'] = 'Tarea guardada';
            return response($data);
        } catch (\Throwable $th) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salio mal, intente nuevamente';
            return response($data);
        }
    }
    /**
     * Update task
     */
    public function updateTask(Request $request) {
        $requestData = $request->all();
        // Staus by dates
        $idActionPlan = $requestData['idActionPlan'];
        $task = TasksModel::with(['taskExpired'])->where('id_task', $requestData['idTask'])->first()->toArray();
        try {
            DB::beginTransaction();
            if ($requestData['stage'] == TasksModel::EXPIRED_STAGE) {
                // only update close date in task expired
                $requestData['idStatus'] = $this->getStatusTaskByDates($task['init_date'], $requestData['endDate']);
                $updateTask = TaskExpiredModel::find($task['task_expired']['id_task_expired'])
                    ->update(['close_date' => $requestData['endDate'], 'id_status' => $requestData['idStatus']]);
            }
            else {
                // update task complete
                $requestData['idStatus'] = $this->getStatusTaskByDates($requestData['initDate'], $requestData['endDate']);
                $updateTask = TasksModel::UpdateTask($requestData);
            }
            // Set users
            $users = $requestData['users'];
            $usersNotify = [];
            foreach ($users as $u) {
                if ($u['id_task_user'] == null) {
                    $u['id_task'] = ($u['id_task'] == null) ? $updateTask['id_task'] : $u['id_task'];
                    $setUser = TaskUserModel::SetUser($u);
                    array_push($usersNotify, $setUser['id_user']);
                }
                else {
                    $setUser = TaskUserModel::UpdateUser($u);
                    if ($setUser['id_user_old'] != $setUser['id_user']) {
                        array_push($usersNotify, $setUser['id_user']);
                    }
                }
            }
            // update dates in action plan register
            if ($requestData['idStatus'] != TasksModel::EXPIRED && $task['stage'] == TasksModel::NORMAL_STAGE) {
                $setDates = $this->setDatesActionByTasks($idActionPlan);
            }
            // update status action plan row
            if ($requestData['idStatus'] != TasksModel::EXPIRED || $task['task_expired']['id_status'] != TasksModel::EXPIRED) { 
                // wait until cron change status AP
                $setStatusAP = $this->statusActionByTask($idActionPlan);
            }
            // Notify users assigned
            $this->notifyTaskUser($requestData['idTask'], $usersNotify);
            DB::commit();
            $data['status'] = StatusConstants::SUCCESS;
            $data['msg'] = 'Registro registrado';
            return response($data);
        } catch (\Throwable $th) {
            DB::rollBack();
            $data['status'] = StatusConstants::WARNING;
            $data['msg'] = 'No se puede actualizar el estatus del requerimiento';
            return response($data);
        }
    }
    /**
    * Delete task
    */
    public function deleteTask(Request $request) {
        $idTask = $request->input('idTask');
        $idActionPlan = $request->input('idActionPlan');
        $task = TasksModel::with(['file'])->findOrFail($idTask);
        if ($task->block == TasksModel::BLOCK_ENABLED) {
            $data['status'] = StatusConstants::WARNING;
            $data['msg'] = 'Esta tarea esta vencida, no se puede modificar';
            return response($data);
        }
        try {
            // Delete task
            DB::beginTransaction();
            $task->delete();
            $idFile = is_null($task->file) ? null : $task->file->id_file;
            // Set status AP
            $setDates = $this->setDatesActionByTasks($idActionPlan);
            $countTasks = TasksModel::where('id_action_plan', $idActionPlan)->count('id_action_plan');
            $statusTask = ( $countTasks == 0 ) ? 0 : $task->id_status;
            if ($statusTask != TasksModel::EXPIRED) { // wait until cron change status AP
                $setStatusAP = $this->statusActionByTask($idActionPlan);
            }
            // Delete File
            if ( !is_null($idFile) ) {
                $deleteFile = $this->deleteFile($idFile);
            } else $deleteFile = true;
            if ($deleteFile) {
                DB::commit();
                $data['status'] = StatusConstants::SUCCESS;
                $data['msg'] = 'Tarea eliminada';
                return response($data);
            }
            else {
                DB::rollBack();
                $data['status'] = StatusConstants::ERROR;
                $data['msg'] = 'No se puede borrar la evidencia de la tarea';
                return response($data);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'No se puede borrar la tarea';
            return response($data);
        }
    }
    /**
     * Get task data
     */
    public function getTask(Request $request){
        $idTask = $request->input('idTask');
        $relationships = ['users.user.person', 'file.category', 'actionExpired', 'taskExpired'];
        $data = TasksModel::with($relationships)->where('id_task', $idTask)->first();
        $permissions = $this->getPermissionTask($idTask);
        $data['permission'] = $permissions['inTask'];
        $data['inApproved'] = $permissions['inApproved'];
        $data['inUpload'] = $permissions['inUpload'];
        return response($data);
    }
    /**
     * delete User tasks
     */
    public function removeTaskUser(Request $request){
        $idTaskUser = $request->input('idTaskUser');
        DB::beginTransaction();
        $remove = TaskUserModel::RemoveUser($idTaskUser);
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
    /**
     * Change status task to Aproved 
     */
    public function completeTask(Request $request) {
        $idTask = $request->input('idTask');
        $idActionPlan = $request->input('idActionPlan');
        $status = $request->input('status');
        $task = TasksModel::find($idTask);
        if ($task->block == TasksModel::BLOCK_ENABLED) {
            $data['status'] = StatusConstants::WARNING;
            $data['msg'] = 'Esta tarea esta vencida, no se puede modificar';
            return response($data);
        }
        DB::beginTransaction();
        try {
            $idStatus = ($status == 'true') ? TasksModel::APPROVED : TasksModel::REJECTED;
            $updateStatusTask = TasksModel::find($idTask)->update(['id_status' => $idStatus]);
        } catch (\Throwable $th) {
            DB::rollBack();
            $data['status'] = StatusConstants::WARNING;
            $data['msg'] = 'No se puede actualizar el estatus de la tarea';
            return response($data);
        }
        // verify status global task by requirement
        $setStatusAP = $this->statusActionByTask($idActionPlan);
        if ($setStatusAP != StatusConstants::SUCCESS) {
            DB::rollBack();
            $data['status'] = StatusConstants::WARNING;
            $data['msg'] = 'No se puede actualizar el estatus del requerimiento';
            return response($data);
        }
        DB::commit();
        $data['status'] = StatusConstants::SUCCESS;
        $msg = ($status == 'true') ? 'completada' : 'rechazada';
        $data['msg'] = 'Tarea '.$msg;
        return response($data);
    }
    
    /**
     * Set users to assigned
     */
    public function setUsersAssigned(Request $request){
        $idActionPlan = $request->input('idActionPlan');
        $idUser = $request->input('idUser');
        $idTask = $request->input('idTask');
        DB::beginTransaction();
        $setAsigend = TasksModel::SetAsigned($idTask, $idUser);
        if ($setAsigend == StatusConstants::SUCCESS) {
            $status = StatusConstants::SUCCESS;
            $msg = 'Asignación exitosa';
            DB::commit();
        }
        else {
            $status = StatusConstants::ERROR;
            $msg = 'Algo salio mal, intente de nuevo';
            DB::rollBack();
        }
        // Notification
        if ($status == StatusConstants::SUCCESS) {
            $infoAction = ActionPlansModel::GetActionPlan($idActionPlan);
            $adminUser = User::GetUserInfo(Auth::user()->id_user);
            $infoReq = RequirementsModel::GetRequirement($infoAction[0]['id_requirement']);
            $infoTask = TasksModel::GetTask($idTask);
            $type = 'Requerimiento';
            $infoSub = '';
            if ($infoAction[0]['id_subrequirement'] != null) {
                $infoSub = SubrequirementsModel::GetSubrequirement($infoAction[0]['id_subrequirement']);
                $infoSub = $infoSub[0]['no_subrequirement'];
                $type = 'Subrequerimiento';
            }
            // Data to save
            $notify = User::find($idUser);
            $notifyData = [
                'title' => 'Responsable de tarea en '.$type,
                'body' => 'El usuario <b>'.$adminUser[0]['complete_name'].'</b> te asigno como responsable
                            a la tarea <b>'.$infoTask[0]['title'].'<b> del '.$type.': <b>'.$infoReq[0]['no_requirement'].' '.$infoSub.'</b>',
                'description' => '',
                'link' => 'action'
            ];
            $notify->notify(new ActionPlanNotification($notifyData));
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return response($data);
    }
    /**
     * Get periods for task
     */
    public function getPeriodsForTask(Request $request){
        $idPeriod = $request->input('idPeriod');
        $idActionPlan = $request->input('idActionPlan');
        $edit = $request->input('edit');
        $infoPeriod = PeriodModel::GetPeriod($idPeriod);
        $dataPeriodAllow = $infoPeriod[0];
        $tasks = TasksModel::GetTasks($idActionPlan);
        if ( (sizeof($tasks) > 0) && ($edit == 0) ) {
            $dataPeriodAllow = TasksController::calculateAllowPeriod($tasks, $dataPeriodAllow);
        }
        $allowedPeriods = PeriodModel::GetAllowedPeriods($dataPeriodAllow['lastDay'], $dataPeriodAllow['lastMonth'], $dataPeriodAllow['lastYear']);
        return response($allowedPeriods);
    }
    /**
     * Calculate periods 
     */
    // public function calculateAllowPeriod($tasks, $dataPeriodAllow){
    //     $newTempPeriod = TasksController::totalPeriods($tasks);
    //     // calculate info period
    //     $daysC = ($dataPeriodAllow['lastDay'] - $newTempPeriod['lastDay']);
    //     $monthsC = ($dataPeriodAllow['lastMonth'] - $newTempPeriod['lastMonth']);
    //     $yearsC = ($dataPeriodAllow['lastYear'] - $newTempPeriod['lastYear']);
    //     // Get limit period
    //     $dataPeriodAllow['lastDay'] = ( $daysC > 0 ) ? $daysC : 0;
    //     $dataPeriodAllow['lastMonth'] = ( $monthsC > 0 ) ? $monthsC : 0;
    //     $dataPeriodAllow['lastYear'] = ( $yearsC > 0 ) ? $yearsC : 0;
    //     return $dataPeriodAllow;
    // }
    /**
     * Total periods by tasks
     */
    public static function totalPeriods($tasks){
        $newTempPeriod = [
            'lastDay' => 0,
            'lastMonth' => 0,
            'lastYear' => 0
        ];
        // sum of periods of all tasks
        foreach ($tasks as $t) {
            $tempPeriod = PeriodModel::GetPeriod($t['id_period']);
            $newTempPeriod['lastDay'] += $tempPeriod[0]['lastDay'];
            $newTempPeriod['lastMonth'] += $tempPeriod[0]['lastMonth'];
            $newTempPeriod['lastYear'] += $tempPeriod[0]['lastYear'];
        }
        // days to months
        if ( ($newTempPeriod['lastDay'] != 0) && ($newTempPeriod['lastDay'] >= 30) ) {
            $months = $newTempPeriod['lastDay'] / 30;
            $days = $months - ( (int)($newTempPeriod['lastDay'] / 30) );
            $newTempPeriod['lastMonth'] += (int)$months;
            $newTempPeriod['lastDay'] = (int)(round( ($days * 30), 0, PHP_ROUND_HALF_UP ));
        }
        // months to years
        if ( ($newTempPeriod['lastMonth'] != 0) && ($newTempPeriod['lastMonth'] >= 12) ) {
            $years = $newTempPeriod['lastMonth'] / 12;
            $months = $years - ( (int)($newTempPeriod['lastMonth'] / 12) );
            $newTempPeriod['lastYear'] += (int)$years;
            $newTempPeriod['lastMonth'] = (int)(round( ($months * 12), 0, PHP_ROUND_HALF_UP ));
        }
        return $newTempPeriod;
    }
    /**
     * Get data reminders
     */
    public function getTaskReminders(Request $request){
        $dataRequest = $request->all();
        $idActionPlan = $request->input('idActionPlanTask');
        $infoAction = ActionPlansModel::GetActionPlan($idActionPlan);
        $infoTask = TasksModel::GetTask($dataRequest['idTask']);
        $object = new Periods();
        $dates = $object->createDates($infoAction[0]['currenDate'], $infoTask[0]['id_period']);
        $data['task']['initDate'] = $dates['initDate'];
        $data['task']['closeDate'] = $dates['closeDate'];
        $data['task']['realInitDate'] = $dates['realInitDate'];
        $data['task']['realCloseDate'] = $dates['realCloseDate'];   
        $dates = RemindersModel::GetReminders($dataRequest);
        $datesFormat = [];
        foreach ($dates as $d) {
            $dateTemp = Carbon::createFromFormat('Y-m-d H:i:s', $d['date'], 'America/Mexico_City')->toDateString();
            array_push($datesFormat, $dateTemp);
        }
        $data['dates'] = $datesFormat;
        return response($data);
    }

    public function expiredTest(Request $request) {
        dd('Hola Tester');
    }
}