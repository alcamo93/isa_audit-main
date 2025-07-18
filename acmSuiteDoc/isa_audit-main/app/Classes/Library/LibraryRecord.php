<?php

namespace App\Classes\Library;

use App\Classes\Library\LibraryRecordStatus;
use App\Models\V2\Audit\AplicabilityRegister;
use App\Models\V2\Audit\AuditRegister;
use App\Models\V2\Audit\ObligationRegister;
use App\Models\V2\Audit\EvaluateRequirement;
use App\Models\V2\Audit\File;
use App\Models\V2\Audit\FilesNotification;
use App\Models\V2\Audit\Library;
use App\Models\V2\Audit\Obligation;
use App\Models\V2\Audit\Task;
use App\Traits\V2\FileTrait;
use App\Traits\V2\UtilitiesTrait;
use Carbon\Carbon;
use Illuminate\Http\File as FileLaravel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LibraryRecord
{
  use FileTrait, UtilitiesTrait;
  
  public $aplicabilityRegister = null;
  public $idAplicabilityRegister = null;
  public $isSubrequirement = null;
  public $idRequirement = null;
  public $idSubrequirement = null;
  public $idTask = null;
  public $evaluateModel = null;
  public $models = [];
  public $places = [];

  public function __construct($idAplicabilityRegister, $idRequirement, $idSubrequirement) 
  {
    $this->idAplicabilityRegister = $idAplicabilityRegister;
    $this->aplicabilityRegister = $this->getAplicabilityRegister($idAplicabilityRegister);
    $this->idRequirement = intval($idRequirement);
    $this->idSubrequirement = $idSubrequirement == 'null' || is_null($idSubrequirement) ? null : intval($idSubrequirement);
    $this->places = [];
  }

  private function getAplicabilityRegister()
  {
    return AplicabilityRegister::findOrFail($this->idAplicabilityRegister);
  }

  private function getTypeRequirement($record) 
  {
    $this->isSubrequirement = !is_null($record->subrequirement);
    $this->idRequirement = $record->id_requirement;
    $this->idSubrequirement = $this->isSubrequirement ? $record->id_subrequirement : null;
  }

  private function getClassName($origin)
  {
    $classNames = [
      'obligation' => Obligation::class,
      'task' => Task::class,
    ];
    return $classNames[$origin];
  }

  private function getModelEvaluate($request) 
  {
    $model = EvaluateRequirement::where('aplicability_register_id', $this->idAplicabilityRegister)
      ->where('id_requirement', $this->idRequirement)->where('id_subrequirement', $this->idSubrequirement)->first();
    $evaluate = $model;
    $showInLibrary = $request['show_library'] == 'true';
    if ($showInLibrary) return $evaluate;
    // replicate for new record
    $evaluate = $model->replicate()->fill([ 'show_library' => 0 ]);
    $evaluate->save();
    // search origin no show library
    if ($request['evaluateable_type'] == 'Task') $record = Task::find($request['evaluateable_id']);
    else $record = Obligation::find($request['evaluateable_id']);
    // relation origin model with new evaluate for no show library
    $record->evaluates()->attach($evaluate->id_evaluate_requirement);
    return $evaluate;
  }

  private function getObligations() 
  {
    $timezone = Config('enviroment.time_zone_carbon');
    $today = Carbon::now($timezone)->toDateString();
    /**
     * the first one is obtained because there is only legal compliance
     * by applicability (Obligation Register).
     */
    $filterObligation = function($query) {
      $query->where('id_requirement', $this->idRequirement)
        ->where('id_subrequirement', $this->idSubrequirement);
    };
    $obligationRegister = ObligationRegister::with(['obligations' => $filterObligation])
      ->where('id_aplicability_register', $this->idAplicabilityRegister)
      ->whereDate('init_date', '<=', $today)->whereDate('end_date', '>=', $today)
      ->whereHas('obligations', $filterObligation)->first();
    
    // Build object for places
    if ( !is_null($obligationRegister) ) {
      $recordObligation = $obligationRegister->obligations->first();
      $tmp['section'] = 'Reporte de estatus de Permisos Críticos';
      $tmp['evaluateable_type'] = 'Obligation';
      $tmp['evaluateable_id'] = $recordObligation->id_obligation;
      $tmp['init_date_format'] = $obligationRegister->init_date_format;
      $tmp['end_date_format'] = $obligationRegister->end_date_format;
      // $tmp['current'] = $tmp['evaluateable_type'] == $this->evaluateableType && $tmp['evaluateable_id'] == $this->evaluateableId;
      array_push($this->models, $recordObligation);
      array_push($this->places, $tmp);
    }
    
    /**
     * Get action register for obligations and task main
     */
    $filterActionRecord = function($query) {
      $query->where('id_requirement', $this->idRequirement)
        ->where('id_subrequirement', $this->idSubrequirement);
    };
    $filterAction = function($query) use ($today) {
      $query->whereDate('init_date', '<=', $today)
        ->whereDate('end_date', '>=', $today);
    };
    $filterTaskMain = function($query) {
      $query->where('main_task', 1);
    };
    $actionRegister = ObligationRegister::with([
      'action_plan_register' => $filterAction,
      'action_plan_register.action_plans' => $filterActionRecord,
      'action_plan_register.action_plans.tasks' => $filterTaskMain,
    ])
    ->where('id_aplicability_register', $this->idAplicabilityRegister)
    ->whereHas('action_plan_register', $filterAction)
    ->whereHas('action_plan_register.action_plans', $filterActionRecord)
    ->whereHas('action_plan_register.action_plans.tasks', $filterTaskMain)
    ->first();

    // Build object for places
    if ( !is_null($actionRegister) ) {
      $tmp['section'] = 'Plan de Acción de Permisos Críticos';
      $tmp['evaluateable_type'] = 'Task';
      $recordRegister = $actionRegister->action_plan_register;
      $recordAction = $recordRegister->action_plans->first();
      $recordTask = $recordAction->tasks->first();
      $tmp['evaluateable_id'] = $recordTask->id_task;
      $tmp['init_date_format'] = $recordRegister->init_date_format;
      $tmp['end_date_format'] = $recordRegister->end_date_format;
      // $tmp['current'] = $tmp['evaluateable_type'] == $this->evaluateableType && $tmp['evaluateable_id'] == $this->evaluateableId;
      array_push($this->models, $recordTask);
      array_push($this->places, $tmp);
    }
  }

  private function getTasks() 
  {
    $timezone = Config('enviroment.time_zone_carbon');
    $today = Carbon::now($timezone)->toDateString();
    /**
     * Get action register for audits and task main
     */
    $filterAction = function($query) use ($today) {
      $query->whereDate('init_date', '<=', $today)
        ->whereDate('end_date', '>=', $today);
    };
    $filterActionRecord = function($query) {
      $query->where('id_requirement', $this->idRequirement)
        ->where('id_subrequirement', $this->idSubrequirement);
    };
    $filterTaskMain = function($query) {
      $query->where('main_task', 1);
    };
    $actionRegister = AuditRegister::with([
      'action_plan_register' => $filterAction,
      'action_plan_register.action_plans' => $filterActionRecord,
      'action_plan_register.action_plans.tasks' => $filterTaskMain,
    ])
    ->where('id_aplicability_register', $this->idAplicabilityRegister)
    ->whereHas('action_plan_register', $filterAction)
    ->whereHas('action_plan_register.action_plans', $filterActionRecord)
    ->whereHas('action_plan_register.action_plans.tasks', $filterTaskMain)
    ->get();
    
    // Build object for places
    foreach ($actionRegister as $register) {
      $tmp['section'] = 'Plan de Acción de Auditoría';
      $tmp['evaluateable_type'] = 'Task';
      $recordRegister = $register->action_plan_register;
      $recordAction = $recordRegister->action_plans->first();
      $recordTask = $recordAction->tasks->first();
      $tmp['evaluateable_id'] = $recordTask->id_task;
      $tmp['init_date_format'] = $recordRegister->init_date_format;
      $tmp['end_date_format'] = $recordRegister->end_date_format;
      $tmp['has_auditors'] = sizeof($recordTask->auditors) > 0;
      $hasInitDate = !is_null($recordTask->init_date_format);
      $hasEndDate = !is_null($recordTask->close_date_format);
      $tmp['has_dates'] = $hasInitDate && $hasEndDate;
      // $tmp['current'] = $tmp['evaluateable_type'] == $this->evaluateableType && $tmp['evaluateable_id'] == $this->evaluateableId;
      array_push($this->models, $recordTask);
      array_push($this->places, $tmp);
    }
  }

  public function findRequirement() 
  {
    $this->getObligations();
    $this->getTasks();
    return $this->places;
  }

  private function setTask($data)
  {
    $idTask = $data['id_task'] ?? null;
    $this->idTask = $idTask;
  }

  public function storeFiles($data) {
    // check task 
    $this->setTask($data);
    // get unique model requirement evaluated
    $this->evaluateModel = $this->getModelEvaluate($data);
    if ( is_null($this->evaluateModel) ) {
      $record['success'] = false;
      $record['message'] = 'No existe este requerimiento en la Evaluación de Aplicabilidad';
      return $record;
    } 
    // store data in library
    $library = $this->storeLibrary($data);
    if ( !$library['success'] ) {
      $record['success'] = false;
      $record['message'] = $library['message'];
      return $record;
    }
    // store data in files notifications
    if(!empty($data['notify_days'])){
      $storeNoticicationDays = $this->storeDateNotifications($library['model'], $data);
      if ( !$storeNoticicationDays['success'] ) {
        $record['success'] = false;
        $record['message'] = $storeNoticicationDays['message'];
        return $record;
      }
    }
    // store data files
    $storeFile = $this->storeFilePath($library['model'], $data);
    if ( !$storeFile['success'] ) {
      $record['success'] = false;
      $record['message'] = $storeFile['message'];
      return $record;
    }
    // relations
    $setRelation = $this->setRelations('store', $library['model']);
    if ( !$setRelation['success'] ) {
      $record['success'] = false;
      $record['message'] = $setRelation['message'];
      return $record;
    }
    // response
    $record['success'] = true;
    $record['message'] = 'Registro exitoso';
    if ( isset($setRelation['info']) ) {
      $record['info'] = $setRelation['info'];
    }
    return $record;
  }

  private function storeLibrary($data) 
  {
    try {
      $onlyField = [
        'name','init_date','end_date',
        'days','has_end_date','id_category', 'id_task'
      ];
      // renewal_date
      $timezone = Config('enviroment.time_zone_carbon');
      $loadDate = Carbon::now($timezone)->toDateString();
      // data
      $dataLibrary = collect($data)->only($onlyField)
        ->put('id_user', Auth::id())->put('load_date', $loadDate);
      if ( !boolval($dataLibrary['has_end_date']) ) {
        $dataLibrary['end_date'] = null;
        $dataLibrary['days'] = null;
      }
      $create = $dataLibrary->toArray();
      $library = $this->evaluateModel->library()->create($create);

      
      $record['success'] = true;
      $record['message'] = 'Registro exitoso';
      $record['model'] = $library;
      return $record;
    } catch (\Throwable $th) {
      $record['success'] = false;
      $record['message'] = 'Algo salio mal al iniciar el almacenado de archivos';
      return $record;
    }
  }

  private function storeDateNotifications($library, $data) 
  {
    try {
      $newDataDates = collect($data['notify_days'])->map(fn($item) => $this->getFormatDatetimeSystem($item) );
      $deleteDates = $library->files_notifications->filter( fn($notify) => !$newDataDates->contains($notify->date) )
        ->pluck('id');
      
      $newDataDates->each(function($date) use ($library) {
        $dataDate = ['date' => $date];
        $library->files_notifications()->updateOrCreate($dataDate, $dataDate);
      });
      
      $dates = FilesNotification::whereIn('id', $deleteDates);
      $dates->delete();

      $record['success'] = true;
      $record['message'] = 'Registro exitoso';
      $record['model'] = $library;
      return $record;
    } catch (\Throwable $th) {
      $record['success'] = false;
      $record['message'] = 'Algo salio mal al iniciar el almacenado de los días para notificar';
      return $record;
    }
  }

  private function storeFilePath($library, $data, $renewalNumber = 1)
  {
    try {
      // get directory and disk storage
      $origin = 'library';
      $disk = $this->getStorageEnviroment($origin);
      // build structure for store files and create records 
      $structure = [];
      $loadFiles = $data['file'] ?? [];
      foreach ($loadFiles as $loadFile) {
        $tmp = $this->buildStructureStroreFile($loadFile, $origin, $library, $renewalNumber);
        array_push($structure, $tmp);
      }
      // store files 
      $stores = collect($structure)->map(fn($item) => $item['store']);
      foreach ($stores as $store) {
        $put = Storage::disk($disk)->putFileAs($store['directory'], new FileLaravel($store['file']), $store['file_name']);
        if ( Storage::disk($disk)->missing($put) ) {
          $response['success'] = false;
          $response['message'] = "Algo salio mal al carga el documento {$store['original_name']}, intente nuevamente";
          return $response;
        }
      }
      // create records
      $paths = collect($structure)->map(fn($item) => $item['attributes']);
      $creates = $library->files()->createMany($paths);
      // result
      $response['success'] = true;
      $response['message'] = 'Registro exitoso';
      return $response;
    } catch (\Throwable $th) {
      $response['success'] = false;
      $response['message'] = 'Algo salio mal en cargar los documento, intente nuevamente';
      return $response;
    }
  }

  public function setRelations($method, $library, $inReview = true, $approve = false, $data = null)
  {
    try {
      // check task 
      if ( !is_null($data) ) $this->setTask($data);
      // use unique update
      if ( is_null($this->evaluateModel) ) {
        $this->evaluateModel = $library->evaluate;
      }
      // No has evaluate requirement
      if ( is_null($this->evaluateModel) ) {
        $record['success'] = false;
        $record['message'] = 'No existe este requerimiento en la Evaluación de Aplicabilidad';
        return $record;
      }
      // if no belong to sections, for_review automatic
      if ( !boolval($this->evaluateModel->show_library) ) {
        $libraryStatusNoLibrary = new LibraryRecordStatus($this->evaluateModel, $library, $this->models, $inReview, $approve, $method);
        return $libraryStatusNoLibrary->setRelationsNoLibrary($this->idTask);
      }

      $this->findRequirement();

      $libraryStatus = new LibraryRecordStatus($this->evaluateModel, $library, $this->models, $inReview, $approve, $method);
      return $libraryStatus->setRelations();

    } catch (\Throwable $th) {
      $response['success'] = false;
      $response['message'] = 'Algo salio mal al establecer referencias de archivos';
      return $response;
    }
  }

  private function buildStructureStroreFile($loadFile, $origin, $library, $renewalNumber)
  {
    // dates for to drop in store in 5 years
    $timezone = Config('enviroment.time_zone_carbon');
    $dateStr = strlen($library->load_date) == 10 ? "{$library->load_date} 00:00:00" : $library->load_date;
    $dateToday = Carbon::createFromFormat('Y-m-d H:i:s', $dateStr, $timezone);
    $loadDate = $dateToday->toDateString();
    $dropDate = $dateToday->addYears(5)->toDateString();
    // directory
    $storeVersion = 'storage/v2';
    $req = $this->isSubrequirement ? "{$this->idRequirement}_{$this->idSubrequirement}" : $this->idRequirement;
    $directory = "AR{$this->idAplicabilityRegister}/$req";
    // attributes
    $fileSize = $loadFile->getSize();
    $fileType = $loadFile->getMimeType();
    $extension = Str::of( $loadFile->getClientOriginalExtension() )->lower();
    // filename
    $hash = hash('md5', microtime());
    $fileName = "{$hash}.{$extension}";
    $originalName = $loadFile->getClientOriginalName();
    // structure
    return [
      'attributes' => [
        'original_name' => $originalName,
        'hash_name' => $fileName,
        'store_version' => $storeVersion,
        'store_origin' => $origin,
        'directory' => $directory,
        'extension' => $extension,
        'file_type' => $fileType,
        'file_size' => $fileSize,
        'load_date' => $loadDate,
        'drop_date' => $dropDate,
        'init_date' => $library->init_date,
        'end_date' => $library->end_date,
        'renewal_number' => $renewalNumber,
      ],
      'store' => [
        'original_name' => $originalName,
        'directory' => $directory,
        'file_name' => $fileName,
        'file' => $loadFile,
      ]
    ];
  }

  public function updateFiles($id, $data)
  {
    // check task 
    $this->setTask($data);
    // search data to update
    $infoResponse = [];
    $library = Library::with(['evaluate.obligations', 'evaluate.tasks'])->findOrFail($id);
    $this->evaluateModel = $library->evaluate;
    if ( is_null($this->evaluateModel) ) {
      $record['success'] = false;
      $record['message'] = 'No existe este requerimiento en la Evaluación de Aplicabilidad';
      return $record;
    }
    $dataCollect = collect($data);
    if ( !boolval($dataCollect['has_end_date']) ) {
      $dataCollect['end_date'] = null;
      $dataCollect['days'] = null;
    }
    $updateData = $dataCollect->except(['file', 'remove_files'])->toArray();
    $update = $library->update($updateData);
    // set data about instance obligation
    $setRelation = $this->setRelations('update', $library);
    if ( !$setRelation['success'] ) {
      $record['success'] = false;
      $record['message'] = $setRelation['message'];
      return $record;
    }
    // store data in files notifications
    $storeNoticicationDays = $this->storeDateNotifications($library, $data);
    if ( !$storeNoticicationDays['success'] ) {
      $record['success'] = false;
      $record['message'] = $storeNoticicationDays['message'];
      return $record;
    }
    $storeFile = $this->storeFilePath( $library, $dataCollect->toArray() );
    if ( !$storeFile['success'] ) {
      $record['success'] = false;
      $record['message'] = $storeFile['message'];
      return $record;
    }
    $removeFilesData = $dataCollect->only(['remove_files'])->toArray();
    $removeFiles = isset($removeFilesData['remove_files']) ? explode(',', $removeFilesData['remove_files']) : [];
    $removeFileRecord = $this->removeFiles($removeFiles);
    if ( !$removeFileRecord['success'] ) {
      $record['success'] = false;
      $record['message'] = $removeFileRecord['message'];
      return $record;
    }
    $response['success'] = true;
    $response['message'] = 'Actualizacion exitosa';
    if ( sizeof($infoResponse) > 0 ) {
      $response['info'] = $infoResponse[0];
    }
    return $response;
  }

  private function removeFiles($removeIds)
  {
    try {
      $files = File::whereIn('id', $removeIds)->get();
      foreach ($files as $file) {
        $file->delete();
      }
      foreach ($files as $file) {
        $disk = $this->getStorageEnviroment($file->store_origin);
        $deleteFile = Storage::disk($disk)->delete($file->directory_path);
      }
      $response['success'] = true;
      $response['message'] = 'Registro exitoso';
      return $response;
    } catch (\Throwable $th) {
      $response['success'] = false;
      $response['message'] = 'Error al eliminar Evidencia/documentos';
      return $response;
    }
  }

  public function renewalFiles($id, $data)
  {
    $infoResponse = [];
    $library = Library::with(['evaluate.obligations', 'evaluate.tasks'])->findOrFail($id);
    $this->evaluateModel = $library->evaluate;
    if ( is_null($this->evaluateModel) ) {
      $record['success'] = false;
      $record['message'] = 'No existe este requerimiento en la Evaluación de Aplicabilidad';
      return $record;
    }
    $setRenewal = $this->setRenewal($library, $data);
    if ( !$setRenewal['success'] ) {
      $record['success'] = false;
      $record['message'] = $setRenewal['message'];
      return $record;
    }
    // set data about instance obligation
    $setRelation = $this->setRelations('renewal', $library);
    if ( !$setRelation['success'] ) {
      $record['success'] = false;
      $record['message'] = $setRelation['message'];
      return $record;
    }
    // store files
    $storeFile = $this->storeFilePath( $library, $data, $setRenewal['number'] );
    if ( !$storeFile['success'] ) {
      $record['success'] = false;
      $record['message'] = $storeFile['message'];
      return $record;
    }
    
    $response['success'] = true;
    $response['message'] = 'Renovación exitosa';
    if ( sizeof($infoResponse) > 0 ) {
      $response['info'] = $infoResponse[0];
    }
    return $response;
  }

  private function setRenewal($library, $data)
  {
    try {
      // updates dates and need renewal
      $onlyData = [ 'init_date', 'end_date', 'days' ];
      $updateData = collect($data)->put('need_renewal', 0)->only($onlyData)->toArray();
      $update = $library->update($updateData);
      // update libraries
      $files = $library->files;
      $maxNumberRenewal = $files->pluck('renewal_number')->unique()->max();
      $nextNumberRenewal = $maxNumberRenewal + 1;
      // update files for expired
      $filesCurrent = $files->where('renewal_number', $maxNumberRenewal)->where('is_current', 1);
      foreach ($filesCurrent as $file) {
        $file->update(['is_current' => 0]);
      }
      
      $response['success'] = true;
      $response['message'] = 'Renovación exitosa';
      $response['number'] = $maxNumberRenewal;

      return $response;
    } catch (\Throwable $th) {
      $response['success'] = false;
      $response['message'] = 'Renovación fallida';
    }
  }
}