<?php

namespace App\Classes\Library;

use App\Classes\ActionPlan\CreateEvaluateObligationAP;
use App\Classes\Library\LibraryAction;
use App\Models\V2\Audit\ActionPlan;
use App\Models\V2\Audit\Library;
use App\Models\V2\Audit\Task;
use App\Models\V2\Audit\Obligation;
use App\Traits\V2\HelpersActionPlanTrait;
use App\Traits\V2\ObligationTrait;
use Illuminate\Support\Facades\Auth;

class LibraryRecordStatus
{
  use ObligationTrait, HelpersActionPlanTrait;

  public $requirement = null;
  public $library = null;
  public $records = null;
  public $inReview = false;
  public $approve = false;
  public $method = null;

  public $infoResponse = [];
  public $tasksModel = null;
  public $obligationsModel = null;

  public function __construct($requirement, $library, $records, $inReview = true, $approve = false, $method) 
  {
    $this->requirement = $requirement;
    $this->library = $library;
    $this->records = collect($records);
    $this->inReview = $inReview;
    $this->approve = $approve;
    $this->method = $method;
    $this->getCollections();
  }

  private function getCollections()
  {
    $this->tasksModel = $this->records->whereInstanceOf(Task::class)->values();
    $this->obligationsModel = $this->records->whereInstanceOf(Obligation::class)->values();

    ( new LibraryAction($this->library->id) )->setAction($this->method, [
      'approve' => $this->approve, 'in_review' => $this->inReview,
      'task_ids' => $this->tasksModel->pluck('id_task'),
      'obligation_ids' => $this->obligationsModel->pluck('id_obligation')
    ]);
  }

  public function setRelations()
  {
    try {
      // exec only $this->approve is true and method is different to approve
      foreach ($this->tasksModel as $task) {
        $create = $this->processForTask($task, $this->library, $this->inReview, $this->approve);
        if ( isset($create['info']) ) array_push($this->infoResponse, $create['info']);
        $task->evaluates()->sync($this->requirement->id_evaluate_requirement);
      }
      
      /**
       * if the method is de approve and aprrove is rejected
       * only to update status all task to rejected 
       */
      if ($this->method == 'approve' && !$this->approve) {
        $response['success'] = true;
        $response['message'] = 'Registro exitoso.';
        return $response;
      }
      
      foreach ($this->obligationsModel as $obligation) {
        $create = $this->processForObligation($obligation, $this->library, $this->approve);
        if ( isset($create['info']) ) array_push($this->infoResponse, $create['info']);
        $obligation->evaluates()->sync($this->requirement->id_evaluate_requirement);
      }
      
      $response['success'] = true;
      $response['message'] = 'Registro exitoso';
      if ( sizeof($this->infoResponse) > 0 ) {
        $response['info'] = $this->infoResponse[0];
      }
      return $response;
    } catch (\Throwable $th) {
      $response['success'] = false;
      $response['message'] = 'Algo salio mal al establecer referencias de archivos';
      return $response;
    }
  }

  private function processForObligation($model, $library, $approve)
  {
    // evaluate obligation only if is approve and exist in action plan or if no exist in action plan
    $isInAction = $this->searchInActionPlan($model);
    if ($isInAction && $this->method != 'approve') {
      return;
    }
    // set status by dates
    $hasStatus = $this->setStatusObligation($model, $library);
    if ( !$hasStatus['success'] ) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al establecer estatus en Permisos críticos';
      return $info;
    }
    // set dates in evaluateModel requirements
    $updateDates = $model->update([
      'init_date' => $library->init_date, 
      'end_date' => $library->end_date
    ]);
    if ( !$updateDates ) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al establecer fechas en Permisos críticos';
      return $info;
    }
    // set user
    $hasAuditor = !is_null($model->auditor);
    if ($hasAuditor) {
      $model->auditor()->update(['days' => $library->days]);
    } else {
      $model->auditor()->create([
        'level' => 1,
        'days' => $library->days,
        'id_user' => Auth::id(),
      ]);
    }
    // create in action plan validating status
    $forActionPlan = new CreateEvaluateObligationAP($model);
    $createAP = $forActionPlan->createRecordAP();
    return $createAP;
  }

  private function processForTask($model, $library, $inReview, $approve)
  {
    if ($inReview) {
      $model->update(['id_status' => Task::REVIEW_TASK]);
      $library->update(['for_review' => Library::UNDER_REVIEW]);
    } else {  
      $idStatus = $approve ? Task::APPROVED_TASK : Task::REJECTED_TASK;
      $model->update(['id_status' => $idStatus]);
      $library->update(['for_review' => Library::NO_UNDER_REVIEW]);
      // approve all tasks if main task is approve
      if ( $idStatus == Task::APPROVED_TASK && boolval($model->main_task) ) {
        $allTasks = Task::where('id_action_plan', $model->id_action_plan);
        $allTasks->update(['id_status' => Task::APPROVED_TASK]);
      }
      // verify status global task by requirement
      $setStatusAP = $this->statusActionByTask($model->id_action_plan);
    }
  }

  public function searchInActionPlan($model)
  {
    $actionsId = $this->tasksModel->pluck('id_action_plan')->unique()->first();
    $action = ActionPlan::where('id_action_plan', $actionsId)
      ->where('id_requirement', $model->id_requirement)
      ->where('id_subrequirement', $model->id_subrequirement)
      ->first();
    return !is_null($action);
  }

  public function setRelationsNoLibrary($idTask)
  {
    try {
      $tasks = $this->requirement->tasks()->get();
      $task = $tasks->firstWhere('id_task', $idTask);
      // first elvaluate if is approve
      if ( !$this->inReview ) {
        $idStatus = $this->approve ? Task::APPROVED_TASK : Task::REJECTED_TASK;
        $task->update(['id_status' => $idStatus]);
        $this->requirement->library()->update(['for_review' => 0]);
      } else {
        // review library and task
        $task->update(['id_status' => Task::REVIEW_TASK]);
        $this->requirement->library()->update(['for_review' => 1]);
      }
      // verify status global task by requirement
      $idActionPlan = $task->id_action_plan;
      $setStatusAP = $this->statusActionByTask($idActionPlan);

      if ( !$setStatusAP ) {
        $response['success'] = false;
        $response['message'] = 'Algo salio mal al establecer estatus del requerimiento';
        return $response;
      }

      $response['success'] = true;
      $response['message'] = 'Registro exitoso';
      return $response;
    } catch (\Throwable $th) {
      $response['success'] = false;
      $response['message'] = 'Algo salio mal al establecer referencias de archivos para mis archivos no visibles';
      return $response;
    }
  }
}