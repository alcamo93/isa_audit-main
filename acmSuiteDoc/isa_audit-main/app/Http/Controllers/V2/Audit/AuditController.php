<?php

namespace App\Http\Controllers\V2\Audit;

use App\Classes\Audit\EvaluateAuditAnswer;
use App\Classes\Audit\VerifyAuditAnswer;
use App\Classes\Files\StoreImage;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuditEvaluateRequest;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Audit\AuditRegister;
use App\Models\V2\Audit\AuditAspect;
use App\Models\V2\Audit\Audit;
use App\Models\V2\Audit\EvaluateAuditRequirement;
use App\Traits\V2\ResponseApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AuditController extends Controller
{
  use ResponseApiTrait;

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function source()
  {
    try {
      $data = Audit::included()->filter()->auditRegisterFilterDashboard()->getRisk()->getOrPaginate();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Display a listing of the resource.
   * @param int $idAuditProcess
   * @param int $idAplicabilityRegister
   * @param int $idAuditRegister
   * @param int $idAuditMatter
   * @param int $idAuditAspect
   * @return \Illuminate\Http\Response
   * 
   * Params used in Middleware route
   */
  public function index($idAuditProcess, $idAplicabilityRegister, $idAuditRegister, $idAuditMatter, $idAuditAspect)
  {
    $validate = $this->verifyRequest('index', $idAuditRegister);
    if (!$validate['success']) {
      return $this->errorResponse($validate['message']);
    }

    $relationships = [
      'requirement.application_type', 
      'requirement.evidence', 
      'requirement.condition', 
      'subrequirement.application_type', 
      'subrequirement.evidence',
      'subrequirement.condition',
    ];
    $subrequirements = EvaluateAuditRequirement::with($relationships)->included()->auditRegisterFilterEvaluate(false, $idAuditAspect)
      ->getRisk()->customFilterAuditEvaluate(false)->customOrder()->get();
    
    $addExtras = $subrequirements->pluck('id_requirement');
    $requirements = EvaluateAuditRequirement::with($relationships)->included()->auditRegisterFilterEvaluate(true, $idAuditAspect)
      ->getRisk()->customFilterAuditEvaluate(true)->addParents($idAuditAspect, $addExtras)->customOrder()->getOrPaginate();

    foreach ($requirements->items() as $item) {
      $item->childs = [];
      if ( boolval($item->requirement->has_subrequirement) ) {
        $item->childs = $subrequirements->where('id_requirement', $item->id_requirement)->values()->map(function($child, $index) {
          $child->recursive = $index == 0;
          return $child;
        })->values();
      }
    } 
    
    $data = $requirements->toArray();
    $process = ProcessAudit::with(['customer', 'corporate'])->findOrFail($idAuditProcess);
    $auditAspect = AuditAspect::with(['status'])->findOrFail($idAuditAspect);

    $data['info']['audit_process'] = $process->audit_processes;
    $data['info']['customer_name'] = $process->customer->cust_tradename;
    $data['info']['corporate_name'] = $process->corporate->corp_tradename;
    $data['info']['evaluate_risk'] = boolval($process->evaluate_risk);

    $data['progress']['aspect_total_progress'] = $auditAspect->total;
    $data['progress']['aspect_status'] = $auditAspect->status;
    
    return $this->successResponse($data);
  }

  /**
	 * Store/update finding in audit.
	 *
	 * @param App\Http\Requests\AuditEvaluateRequest $request
   * @param int $idAuditProcess
   * @param int $idAplicabilityRegister
   * @param int $idAuditRegister
   * @param int $idAuditMatter
   * @param int $idAuditAspect
   * @param int $idAuditEvaluate
	 * @return \Illuminate\Http\Response
   * 
   * Params used in Middleware route
	 */
  public function finding(AuditEvaluateRequest $request, $idAuditProcess, $idAplicabilityRegister, $idAuditRegister, $idAuditMatter, $idAuditAspect, $idAuditEvaluate)
  {
    try {
      $validate = $this->verifyRequest('finding', $idAuditRegister, $idAuditEvaluate, $request->all());
      if (!$validate['success']) {
        return $this->errorResponse($validate['message']);
      }
      DB::beginTransaction();
      $record = EvaluateAuditRequirement::included()->findOrFail($idAuditEvaluate);
      $audit = $record->audit;
      if ( is_null($audit) ) {
        DB::rollback();
        return $this->errorResponse('No se cuenta con respuesta, por favor primero elige una respuesta');
      }
      $audit->update($validate['request']);
      $auditProcess = ProcessAudit::without('aplicability_register')->findOrFail($idAuditProcess);
      $evaluateRisk = !is_null($auditProcess) ? boolval($auditProcess->evaluate_risk) : false;
      $verify = new VerifyAuditAnswer($idAuditEvaluate, $evaluateRisk);
      $complete = $verify->verifyAnswer();
      if (!$complete['success']) {
        DB::rollback();
        return $this->errorResponse($complete['message']);        
      }
      DB::commit();
      return $this->successResponse($record);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  /**
	 * Store/update answer in audit.
	 *
	 * @param App\Http\Requests\AuditEvaluateRequest $request
   * @param int $idAuditProcess
   * @param int $idAplicabilityRegister
   * @param int $idAuditRegister
   * @param int $idAuditMatter
   * @param int $idAuditAspect
   * @param int $idAuditEvaluate
	 * @return \Illuminate\Http\Response
   * 
   * Params used in Middleware route
	 */
  public function answer(AuditEvaluateRequest $request, $idAuditProcess, $idAplicabilityRegister, $idAuditRegister, $idAuditMatter, $idAuditAspect, $idAuditEvaluate)
  {
    try {
      $validate = $this->verifyRequest('answer', $idAuditRegister, $idAuditEvaluate, $request->all());
      if (!$validate['success']) {
        return $this->errorResponse($validate['message']);        
      }
      DB::beginTransaction();
      $isRecursive = $validate['request']['recursive'] ?? false;
      $answer = new EvaluateAuditAnswer($idAuditRegister, $idAuditEvaluate, $validate['request']['answer'], $isRecursive);
      $setAnswer = $answer->setAnswer();
      if (!$setAnswer['success']) {
        return $this->errorResponse($setAnswer['message']);        
      }
      DB::commit();
      return $this->successResponse($validate['request']);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  /**
   * Store/update images
   *
   * @param App\Http\Requests\AuditEvaluateRequest $request
   * @param int $idAuditProcess
   * @param int $idAplicabilityRegister
   * @param int $idAuditRegister
   * @param int $idAuditMatter
   * @param int $idAuditAspect
   * @param int $idAuditEvaluate
   * @return \Illuminate\Http\Response
   * 
   * Params used in Middleware route
   */
  public function images(AuditEvaluateRequest $request, $idAuditProcess, $idAplicabilityRegister, $idAuditRegister, $idAuditMatter, $idAuditAspect, $idAuditEvaluate) 
  {
    try {
      $validate = $this->verifyRequest('images', $idAuditRegister, $idAuditEvaluate);
      if (!$validate['success']) {
        return $this->errorResponse($validate['message']);        
      }
      $auditEvaluate = EvaluateAuditRequirement::findOrFail($idAuditEvaluate);
      $files = $request->file('file');
      $imageableId = $auditEvaluate->audit->id_audit;
      $origin = 'audit';

      foreach ($files as $file) {
        $store = new StoreImage($file, $imageableId, $origin);
        $image = $store->storeImage();
        if ( !$image['success'] ) {
          return $this->errorResponse($image['message']);
        }
      }
      return $this->successResponse($image);
    } catch (\Throwable $th) {  
      throw $th;
    }
  }

  /**
   * Display the specified resource.
   * @param int $idAuditProcess
   * @param int $idAplicabilityRegister
   * @param int $idAuditRegister
   * @param int $idAuditMatter
   * @param int $idAuditAspect
   * @param int $idAuditEvaluate
   * @return \Illuminate\Http\Response
   * 
   * Params used in Middleware route
   */
  public function show($idAuditProcess, $idAplicabilityRegister, $idAuditRegister, $idAuditMatter, $idAuditAspect, $idAuditEvaluate) 
  {
    try {
      $validate = $this->verifyRequest('show', $idAuditRegister, $idAuditEvaluate);
      if (!$validate['success']) {
        return $this->errorResponse($validate['message']);        
      }
      $data = EvaluateAuditRequirement::with(['audit.images', 'requirement', 'subrequirement'])->findOrFail($idAuditEvaluate);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Verify data
   */
  private function verifyRequest($method, $idAuditRegister, $idAuditEvaluate = null, $data = null)
  {
    try {
      if ($method == 'index') {
        $response['success'] = true;
        return $response;
      }

      if ($method == 'show') {
        $hasAnswer = $this->hasAnswer($idAuditEvaluate);
        return $hasAnswer;
      }

      $auditRegister = AuditRegister::findOrFail($idAuditRegister);
      if ($auditRegister->id_status == Audit::FINISHED_AUDIT_AUDIT) {
        $statusName = $auditRegister->status->status;
        $response['success'] = false;
        $response['message'] = "El aspecto esta en estatus {$statusName}, no es posible modificar el registro";
        return $response;
      }

      if ($method == 'finding') {
        $hasAnswer = $this->hasAnswer($idAuditEvaluate);
        if ( !$hasAnswer ) return $hasAnswer;
        $request = Arr::only($data, ['finding']);
        $response['success'] = true;
        $response['request'] = $request;
        return $response;
      }

      if ($method == 'answer') {
        $hasAnswer = $this->hasAnswer($idAuditEvaluate);
        if ( !$hasAnswer ) return $hasAnswer;
        $request = Arr::only($data, ['answer', 'recursive']);
        if ( $request['answer'] != Audit::NO_APPLY && $request['recursive'] ) {
          $request['recursive'] = false;
        }
        $response['success'] = true;
        $response['request'] = $request;
        return $response;
      }

      if ($method == 'images') {
        $hasAnswer = $this->hasAnswer($idAuditEvaluate);
        if ( !$hasAnswer ) return $hasAnswer;
        $isNegative = $hasAnswer['audit_evaluate']['audit']['answer'] == Audit::NEGATIVE;
        if ( !$isNegative ) {
          $response['success'] = false;
          $response['message'] = 'Solo se puede agregar evidencias cuando la respueta es No Cumple';
          return $response;
        }
        $response['success'] = true;
        $response['request'] = $isNegative;
        return $response;
      }
      
      $response['success'] = true;
      $response['message'] = 'Registro verificado correctamente';
      return $response;
    } catch (\Throwable $th) {
      $response['success'] = false;
      $response['message'] = 'Algo salio mal.';
      return $response;
    }
  }

  private function hasAnswer($idAuditEvaluate)
  {
    $auditEvaluate = EvaluateAuditRequirement::find($idAuditEvaluate);
    if ( is_null($auditEvaluate->audit) ) {
      $response['success'] = false;
      $response['message'] = 'No se cuenta con respuesta, por favor primero elige una respuesta';
      return $response;
    }
    $response['success'] = true;
    $response['message'] = 'Ya cuenta con una respuesta';
    $response['audit_evaluate'] = $auditEvaluate;
    return $response;
  }
}