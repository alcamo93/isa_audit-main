<?php

namespace App\Http\Controllers\V2\Files;

use App\Classes\Utilities\DataSection;
use App\Classes\Library\LibraryRecord;
use App\Http\Controllers\Controller;
use App\Http\Requests\LibraryRequest;
use App\Models\V2\Audit\EvaluateRequirement;
use App\Models\V2\Audit\Library;
use App\Models\V2\Audit\Task;
use App\Models\V2\Admin\User;
use App\Notifications\TaskChangeStatus;
use App\Traits\V2\ResponseApiTrait;
use App\Traits\V2\FileTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LibraryController extends Controller
{
  use ResponseApiTrait, FileTrait;

  /**
   * Redirect to view.
   */
  public function view() 
  {
    return view('v2.library.main');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  { 
    $data = EvaluateRequirement::included()->filter()->excludeParents()
      ->forLibrary()->filterLibrary()->customOrder()->getOrPaginate();
    return $this->successResponse($data);
  }
  
  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\LibraryRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(LibraryRequest $request) 
  { 
    try {
      DB::beginTransaction();
      $requestData = $request->all();
      $records = new LibraryRecord($requestData['id_aplicability_register'], $requestData['id_requirement'], $requestData['id_subrequirement'] ?? null);
      $store = $records->storeFiles($requestData);
      
      if ( !$store['success'] ) {
        DB::rollback();
        return $this->errorResponse($store['message']);
      }
      DB::commit();
      $dataResponse = [
        'id_aplicability_register' => $requestData['id_aplicability_register'],
        'id_requirement' => $requestData['id_requirement'], 
        'id_subrequirement' => $requestData['id_subrequirement']
      ];
      if ( isset($store['info']) ) {
        return $this->successResponse($dataResponse, Response::HTTP_OK, $store['message'], $store['info']);
      }
      return $this->successResponse($dataResponse, Response::HTTP_OK, $store['message']);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id) 
  {
    try {
      $data = Library::included()->withIndex()->findOrFail($id);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  LibraryRequest  $request
   * @param  int  $id     
   * @return \Illuminate\Http\Response
   */
  public function update(LibraryRequest $request, $id) 
  {
    try {
      DB::beginTransaction();
      $requestData = $request->all();
      $library = Library::with(['evaluate'])->findOrFail($id);
      $idAplicabilityRegister = $library->evaluate->aplicability_register_id; 
      $idRequirement = $library->evaluate->id_requirement; 
      $idSubrequirement = $library->evaluate->id_subrequirement;
      $records = new LibraryRecord($idAplicabilityRegister, $idRequirement, $idSubrequirement);
      $update = $records->updateFiles($id, $requestData);
      if ( !$update['success'] ) {
        DB::rollback();
        return $this->errorResponse($update['message']);
      }
      DB::commit();
      if ( isset($update['info']) ) {
        return $this->successResponse($library, Response::HTTP_OK, $update['message'], $update['info']);
      }
      return $this->successResponse($library, Response::HTTP_OK, $update['message']);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id) 
  {
    try {
      DB::beginTransaction();
      
      DB::commit();
      return $this->successResponse([]);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

    /**
   * Remove the specified resource from storage.
   *
   * @param  Request  $request
   * @param  int  $id     
   * @return \Illuminate\Http\Response
   */
  public function renewal(Request $request, $id) 
  {
    try {
      DB::beginTransaction();
      $requestData = $request->all();
      $library = Library::with(['evaluate'])->findOrFail($id);
      $idAplicabilityRegister = $library->evaluate->aplicability_register_id; 
      $idRequirement = $library->evaluate->id_requirement; 
      $idSubrequirement = $library->evaluate->id_subrequirement;
      $records = new LibraryRecord($idAplicabilityRegister, $idRequirement, $idSubrequirement);
      $update = $records->renewalFiles($id, $requestData);
      if ( !$update['success'] ) {
        DB::rollback();
        return $this->errorResponse($update['message']);
      }
      DB::commit();
      if ( isset($update['info']) ) {
        return $this->successResponse($library, Response::HTTP_OK, $update['message'], $update['info']);
      }
      return $this->successResponse($library, Response::HTTP_OK, $update['message']);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  /**
   * Change status task to Aproved 
   */
  public function approve(LibraryRequest $request, $id) 
  { 
    try {
      DB::beginTransaction();
      
      $relationships = [
        'evaluate' => fn($query) => $query->without(['library']),
        'evaluate.tasks' => fn($query) => $query->without(['evaluates', 'auditors']),
        'evaluate.tasks.action.action_register' => fn($query) => $query->without(['action_plans']),
        'evaluate.tasks.action.action_register.process' => fn($query) => $query->without(['aplicability_register', 'auditors']),
        'evaluate.tasks.action.action_register.process.customer' => fn($query) => $query->without(['images']),
        'evaluate.tasks.action.action_register.process.corporate' => fn($query) => $query->without(['addresses', 'industry', 'status', 'image']),
      ];

      $library = Library::with($relationships)->without(['files', 'files_notifications', 'auditor'])->findOrFail($id);
      
      $approve = $request->input('approve');
      $idAplicabilityRegister = $library->evaluate->aplicability_register_id; 
      $idRequirement = $library->evaluate->id_requirement; 
      $idSubrequirement = $library->evaluate->id_subrequirement;

      $records = new LibraryRecord($idAplicabilityRegister, $idRequirement, $idSubrequirement);
      $change = $records->setRelations('approve', $library, false, $approve, $request->all());

      if ( !$change['success'] ) {
        DB::rollback();
        return $this->errorResponse($change['message']);
      }
      
      $notyfy = $this->notifyAction($approve, $request->input('id_task'), $library);
      if ( !$notyfy['success'] ) {
        DB::rollback();
        return $this->errorResponse($notyfy['message']);
      }
      
      $msg = $approve ? 'Aprovado' : 'Rechazado';
      $alert = 'Evidencia/Documento '.$msg;
      DB::commit();

      return $this->successResponse($library, Response::HTTP_OK, $alert);
    } catch (\Throwable $th) {
      DB::rollBack();
      return $this->errorResponse('No se puede aprobar la Evidencia/Documento');
    }   
  }

  /**
   * 
   */
  private function notifyAction($approve, $idTask, $library) 
  {
    try {
      Log::channel('task_approve')->info("***************{$idTask}***************");
      $task = Task::without(['evaluates', 'auditors'])->findOrFail($idTask);
    
      $todayDate = Carbon::now(Config('enviroment.time_zone_carbon'));
      $closeDate = Carbon::parse($task->close_date);
      
      $item['approve'] = $approve ? 'aprobada' : 'rechazada';
      $item['corp_tradename'] = $library->evaluate->aplicability_register->process->corporate->corp_tradename;
      $item['audit_processes'] = $library->evaluate->aplicability_register->process->audit_processes;
      $item['init_date_format'] = $library->evaluate->aplicability_register->process->date_format;
      $item['end_date_format'] = $library->evaluate->aplicability_register->process->end_date_format;
      $item['origin'] = $library->evaluate->tasks->first()->action->action_register->origin;
      $item['no_requirement'] = $library->evaluate->requirement->no_requirement;
      $item['requirement'] = $library->evaluate->requirement->requirement;
      $item['title'] = $task->title;
      $item['task'] = $task->task;
      $item['type_task'] = $task->type_task;
      $item['close_date'] = $task->close_date_format;
      $item['status'] = $task->status->status;
      $item['days'] = $closeDate->diffInDays($todayDate);
      $item['path'] = (new DataSection('task', $idTask))->getSectionPath();

      $userIds = $task->auditors->pluck('id_user');
      $users = User::whereIn('id_user', $userIds)->get();
      
      $users->each(function ($user) use ($item) {
        $item['full_name'] = $user->person->full_name;
        $user->notify( new TaskChangeStatus($item) );
        Log::channel('task_approve')->info($item);
      });

      $info['success'] = true;
      $info['message'] = 'Notificaciones enviadas';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al enviar notificación';
      return $info;
    }
  }
}