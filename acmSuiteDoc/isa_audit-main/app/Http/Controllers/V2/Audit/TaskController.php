<?php

namespace App\Http\Controllers\V2\Audit;

use App\Classes\ActionPlan\UtilitiesActionPlan;
use App\Classes\Task\UtilitiesTask;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Models\V2\Audit\ActionPlan;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Audit\Task;
use App\Models\V2\Audit\TaskNotification;
use App\Models\V2\Catalogs\Scope;
use App\Traits\V2\FileTrait;
use App\Traits\V2\ResponseApiTrait;
use App\Traits\V2\RequirementTrait;
use App\Traits\V2\UtilitiesTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
  use ResponseApiTrait, FileTrait, UtilitiesTrait, RequirementTrait;

  /**
   * Display a listing of the resource.
   *
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  string  $section
   * @param  int  $idSectionRegister
   * @param  int  $idActionRegister
   * @param  int  $idActionPlan
   * @return \Illuminate\Http\Response
   * 
   * Params used in middleware route
   */
  public function index($idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister, $idActionPlan)
  {
    $relationships = ['customer', 'corporate', 'scope'];
    $process = ProcessAudit::with($relationships)->find($idAuditProcess);
    $relationshipsAP = ['status', 'requirement.matter', 'subrequirement.matter', 'requirement.aspect', 'subrequirement.aspect', 'last_expired'];
    $actionPlan = ActionPlan::with($relationshipsAP)->find($idActionPlan);

    $relationshipTask = [
      'status', 'auditors.person', 'auditors.image',
      'action.requirement.matter', 'action.subrequirement.matter', 
      'action.requirement.aspect', 'action.subrequirement.aspect', 'evaluates.library'
    ];
    $data = Task::with($relationshipTask)->withExists('expired')->included()->filter()->customFilter($idActionPlan)->getOrPaginate()->toArray();
    
    $statusDefault = [ 'color' => 'warning', 'id_status' => 0, 'key' => '', 'status' => "---" ];
    $data['info']['status'] = is_null($actionPlan->status) ? $statusDefault : $actionPlan->status->toArray();
    $data['info']['audit_process'] = $process->audit_processes;
    $data['info']['customer_name'] = $process->customer->cust_tradename;
    $data['info']['corporate_name'] = $process->corporate->corp_tradename;
    $data['info']['scope'] = $process->id_scope === Scope::CORPORATE ? $process->scope->scope : "{$process->scope->scope}: {$process->specification_scope}";
    $data['info']['full_requirement'] = $this->getFieldRequirement($actionPlan, 'full_requirement');
    $data['info']['no_requirement'] = $this->getFieldRequirement($actionPlan, 'no_requirement');
    $data['info']['matter'] = $this->getFieldRequirement($actionPlan, 'matter');
    $data['info']['aspect'] = $this->getFieldRequirement($actionPlan, 'aspect');
    $data['info']['last_expired']['original_date_format'] = $actionPlan->last_expired->original_date_format ?? '---';
    $data['info']['last_expired']['extension_date_format'] = $actionPlan->last_expired->extension_date_format ?? '---';
    $data['info']['last_expired']['extension_date'] = $actionPlan->last_expired->extension_date ?? null;
    $data['info']['last_expired']['cause'] = $actionPlan->last_expired->cause ?? '---';
    
    return $this->successResponse($data);
  }

  /**
   * Store resource
   * 
   * @param \App\Http\Requests\TaskRequest $request
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  string  $section
   * @param  int  $idSectionRegister
   * @param  int  $idActionRegister
   * @param  int  $idActionPlan
   * @return \Illuminate\Http\Response
   * 
   * Params used in middleware route
   */
  public function store(TaskRequest $request, $idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister, $idActionPlan) 
  {
    try {
      $requestTask = $request->all();
      $action = ActionPlan::with(['tasks', 'auditors'])->find($idActionPlan);

      if ( $action->id_status == ActionPlan::EXPIRED_AP ) {
        return $this->errorResponse('No se puede agregar esta tarea ya que el requerimiento está en estatus Vencido, primero agrega una causa al requerimiento para poder agregarla');
      }

      if ( $action->auditors->isEmpty() ) {
        return $this->errorResponse('El Requerimiento debe tener por lo menos un Resposable de Cierre');
      }      

      if ( $requestTask['main_task'] ) {
        $title = is_null($action->subrequirement) ? $action->requirement->no_requirement : "{$action->requirement->no_requirement} - {$action->subrequirement->no_subrequirement}";
        $requestTask['title'] = $title;
      }

      $thereIsTaskMain = $action->tasks->firstWhere('main_task', Task::MAIN_TASK);
      if ( !is_null($thereIsTaskMain) && $requestTask['main_task'] ) {
        return $this->errorResponse('Solo se permite una Tarea de Cierre por Requerimiento');
      }

      $utilitiesActionPlan = new UtilitiesActionPlan();
      $statusPerDates = $utilitiesActionPlan->getStatusTaskByDates($requestTask['init_date'], $requestTask['close_date']);
      
      if ( !$statusPerDates['success'] ) {
        return $this->errorResponse('No se pudo reestablecer las fechas del requerimiento'); 
      }
      // validate dates and status rule
      $requestTask['id_action_plan'] = $idActionPlan;
      $requestTask['id_status'] = $statusPerDates['id_status'];

      if ( $requestTask['id_status'] == Task::EXPIRED_TASK && $requestTask['main_task'] == Task::MAIN_TASK ) {
        return $this->errorResponse('La Tarea de Cierre no puede estar en estatus Vencido');
      }

      // set task
      DB::beginTransaction();
      $task = Task::create($requestTask);

      // set users
      $auditors = [];
      foreach ($requestTask['auditors'] as $item) {
        $auditors[intval($item['id_user'])] = [
          'level' => intval($item['level']),
        ];
      }
      $task->auditors()->attach($auditors);

      // set dates by reminaders
      foreach ($requestTask['notify_dates'] as $date) {
        $task->notifications()->updateOrCreate(['date' => $date], ['date' => $date]);
      }

      // set dates in parent
      $setDates = $utilitiesActionPlan->setDatesActionByTasks($idActionPlan);
      if ( !$setDates['success'] ) {
        DB::rollback();
        return $this->errorResponse('No se pudo reestablecer las fechas del requerimiento'); 
      }

      // update status action plan row
      if ($requestTask['id_status'] != Task::EXPIRED_TASK) { // wait until cron change status AP
        $setStatusAP = $utilitiesActionPlan->statusActionByTask($idActionPlan);
        if ( !$setStatusAP['success'] ) {
          DB::rollback();
          return $this->errorResponse('No se pudo reestablecer el estatus del requerimiento'); 
        }
      }

      ( new UtilitiesTask() )->notifyTaskUser($task->id_task, $requestTask['auditors']);
      DB::commit();
      return $this->successResponse($task);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  string  $section
   * @param  int  $idSectionRegister
   * @param  int  $idActionRegister
   * @param  int  $idActionPlan
   * @param  int  $idTask
   * @return \Illuminate\Http\Response
   * 
   * Params used in middleware route
   */
  public function show($idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister, $idActionPlan, $idTask) 
  {
    try {
      $relationshipTask = ['auditors.person', 'auditors.image', 'notifications', 'expired'];
      $data = Task::with($relationshipTask)->included()->find($idTask);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param \App\Http\Requests\TaskRequest $request
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  string  $section
   * @param  int  $idSectionRegister
   * @param  int  $idActionRegister
   * @param  int  $idActionPlan
   * @param  int  $idTask
   * @return \Illuminate\Http\Response
   * 
   * Params used in middleware route
   */
  public function update(TaskRequest $request, $idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister, $idActionPlan, $idTask) 
  {
    try {
      $requestTask = $request->all();
      $task = Task::with('expired')->find($idTask);

      if ( $task->id_status == Task::EXPIRED_TASK ) {
        return $this->errorResponse('No se puede modificar esta tarea ya que está en estatus Vencido, agrega una causa para poder modificarla');
      }

      $action = ActionPlan::with(['tasks', 'auditors'])->find($idActionPlan);

      if ( $action->id_status == ActionPlan::EXPIRED_AP ) {
        return $this->errorResponse('No se puede modificar esta tarea ya que el requerimiento está en estatus Vencido, primero agrega una causa al requerimiento para poder modificarla');
      }

      if ( $action->auditors->isEmpty() ) {
        return $this->errorResponse('El Requerimiento debe tener por lo menos un Resposable de Cierre');
      }

      if ( $requestTask['main_task'] ) {
        $title = is_null($action->subrequirement) ? $action->requirement->no_requirement : "{$action->requirement->no_requirement} - {$action->subrequirement->no_subrequirement}";
        $requestTask['title'] = $title;
      }

      $thereIsTaskMain = $action->tasks->firstWhere('main_task', Task::MAIN_TASK);
      $sameTask = $thereIsTaskMain->id_task === intval($idTask);
      if ( !$sameTask && !is_null($thereIsTaskMain) && $requestTask['main_task'] ) {
        return $this->errorResponse('Solo se permite una Tarea de Cierre por Requerimiento');
      }

      $utilitiesActionPlan = new UtilitiesActionPlan();
      $statusPerDates = $utilitiesActionPlan->getStatusTaskByDates($requestTask['init_date'], $requestTask['close_date']);
      
      if ( !$statusPerDates['success'] ) {
        return $this->errorResponse('No se pudo reestablecer las fechas del requerimiento'); 
      }
      // validate dates and status rule
      $requestTask['id_action_plan'] = $idActionPlan;
      $requestTask['id_status'] = $statusPerDates['id_status'];

      if ( $requestTask['id_status'] == Task::EXPIRED_TASK && $requestTask['main_task'] == Task::MAIN_TASK ) {
        return $this->errorResponse('La Tarea de Cierre no puede estar en estatus Vencido');
      }

      // set task
      DB::beginTransaction();
      $task->update($requestTask);

      // set users
      $auditors = [];
      foreach ($requestTask['auditors'] as $item) {
        $auditors[intval($item['id_user'])] = [
          'level' => intval($item['level']),
        ];
      }
      $task->auditors()->sync($auditors);

      // set dates by reminaders
      $newDataDates = collect($requestTask['notify_dates'])->map(fn($item) => $this->getFormatDatetimeSystem($item) );
      $deleteDates = $task->notifications->filter( fn($notify) => !$newDataDates->contains($notify->date) )
        ->pluck('id');
      
      $newDataDates->each(function($date) use ($task) {
        $dataDate = ['date' => $date];
        $task->notifications()->updateOrCreate($dataDate, $dataDate);
      });
      
      $dates = TaskNotification::whereIn('id', $deleteDates);
      $dates->delete();

      // set dates in parent
      $setDates = $utilitiesActionPlan->setDatesActionByTasks($idActionPlan);
      if ( !$setDates['success'] ) {
        DB::rollback();
        return $this->errorResponse('No se pudo reestablecer las fechas del requerimiento'); 
      }

      // update status action plan row
      if ($requestTask['id_status'] != Task::EXPIRED_TASK) { // wait until cron change status AP
        $setStatusAP = $utilitiesActionPlan->statusActionByTask($idActionPlan);
        if ( !$setStatusAP['success'] ) {
          DB::rollback();
          return $this->errorResponse('No se pudo reestablecer el estatus del requerimiento'); 
        }
      }

      ( new UtilitiesTask() )->notifyTaskUser($task->id_task, $requestTask['auditors']);
      DB::commit();
      return $this->successResponse($task);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  string  $section
   * @param  int  $idSectionRegister
   * @param  int  $idActionRegister
   * @param  int  $idActionPlan
   * @param  int  $idTask
   * @return \Illuminate\Http\Response
   * 
   * Params used in middleware route
   */
  public function destroy($idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister, $idActionPlan, $idTask) 
  {
    try {
      DB::beginTransaction();
      $task = Task::with(['evaluates'])->find($idTask);
      
      if ( $task->main_task ) {
        DB::rollback();
        return $this->errorResponse('Solo se pueden eliminar las subtareas');
      }

      if ($task->id_status == Task::EXPIRED_TASK) {
        DB::rollback();
        return $this->errorResponse('Esta tarea esta vencida, no se puede modificar');
      }
      // search library
      $libraryIds = $task->evaluates->pluck('library.id');
      $task->delete();

      $utilitiesActionPlan = new UtilitiesActionPlan();

      $setDates = $utilitiesActionPlan->setDatesActionByTasks($idActionPlan);
      if ( !$setDates['success'] ) {
        DB::rollback();
        return $this->errorResponse('No se pudo reestablecer las fechas del requerimiento'); 
      }

      if ($task->id_status != Task::EXPIRED_TASK) { // wait until cron change status AP
        $setStatusAP = $utilitiesActionPlan->statusActionByTask($idActionPlan);
        if ( !$setStatusAP['success'] ) {
          DB::rollback();
          return $this->errorResponse('No se pudo reestablecer el estatus del requerimiento'); 
        }
      }

      // Delete File
      $deleteFile = true;
      if ( sizeof($libraryIds) > 0 ) {
        $remove = $this->removeFiles($libraryIds);
        $deleteFile = $remove['success'];
      }

      if (!$deleteFile) {
        DB::rollback();
        return $this->errorResponse('No se pudo eliminar la evidencia de esta tarea'); 
      }

      DB::commit();
      return $this->successResponse($task);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  /**
   * update the specified resource from storage.
   *
   * @param  \App\Http\Requests\TaskRequest  $request
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  string  $section
   * @param  int  $idSectionRegister
   * @param  int  $idActionRegister
   * @param  int  $idActionPlan
   * @param  int  $idTask
   * @return \Illuminate\Http\Response
   * 
   * Params used in middleware route
   */
  public function expired(TaskRequest $request, $idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister, $idActionPlan, $idTask)
  {
    try {
      $requestData = $request->all();
      // dd($requestData);
      $data = Task::find($idTask);
      $requestData['original_date'] = $data->close_date;

      $action = ActionPlan::withExists('expired')->with('last_expired')->find($idActionPlan);
      
      if ( $action->id_status == ActionPlan::EXPIRED_AP ) {
        return $this->errorResponse('No se puede extender esta tarea ya que el requerimiento está en estatus Vencido, agrega primero una causa al requerimiento para poder extender está tarea');
      }
      
      if ( $action->expired_exists ) {
        $extensionDateTask = Carbon::parse($requestData['extension_date']);
        $extensionDateActionPlan = Carbon::parse($action['last_expired']['extension_date']);
        $validateDates = $extensionDateTask->lessThanOrEqualTo($extensionDateActionPlan);
        if ( !$validateDates ) {
          $extensionDateAP = $action['last_expired']['extension_date_format'];
          return $this->errorResponse("La fecha de resolución debe ser menor o igual a la fecha de extensión del requerimiento {$extensionDateAP}");
        }
      }
      
      DB::beginTransaction();

      if ($data->id_status != Task::EXPIRED_TASK) {
        return $this->errorResponse('Está tarea debe estar como Vencido para extender su fecha');
      }
      $utilitiesActionPlan = new UtilitiesActionPlan();
      $statusPerDates = $utilitiesActionPlan->getStatusTaskByDates($data['init_date'], $requestData['extension_date']);
      
      if ( !$statusPerDates['success'] ) {
        DB::rollback();
        return $this->errorResponse('No se pudo reestablecer las fechas del requerimiento'); 
      }
      // set extension date
      $data->update([
        'close_date' => $requestData['extension_date'],
        'id_status' => $statusPerDates['id_status'],
      ]);
      // Create record in Exprired 
      $data->expired()->create($requestData);

      // set dates in parent
      $setDates = $utilitiesActionPlan->setDatesActionByTasks($idActionPlan);
      if ( !$setDates['success'] ) {
        DB::rollback();
        return $this->errorResponse('No se pudo reestablecer las fechas del requerimiento'); 
      }

      // update status action plan row
      if ($statusPerDates['id_status'] != Task::EXPIRED_TASK) { // wait until cron change status AP
        $setStatusAP = $utilitiesActionPlan->statusActionByTask($idActionPlan);
        if ( !$setStatusAP['success'] ) {
          DB::rollback();
          return $this->errorResponse('No se pudo reestablecer el estatus del requerimiento'); 
        }
      }
      DB::commit();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }
}