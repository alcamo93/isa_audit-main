<?php

namespace App\Http\Controllers\V2\Audit;

use App\Classes\Applicability\Evaluate;
use App\Classes\Process\HandlerEvaluateQuestion;
use App\Classes\Utilities\MattersSection;
use App\Http\Controllers\Controller;
use App\Models\V2\Audit\Aplicability;
use App\Models\V2\Audit\AplicabilityRegister;
use App\Models\V2\Audit\ContractAspect;
use App\Models\V2\Audit\EvaluateAuditRequirement;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Catalogs\Scope;
use App\Models\V2\Catalogs\Status;
use App\Traits\V2\ResponseApiTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ContractAspectController extends Controller
{
  use ResponseApiTrait;

  /**
   * Get all records
   * 
   * @param int idAuditProcess
   * @param int idAplicabilityRegister
   * 
   * Params used in middleware route
   */
  public function all($idAuditProcess, $idAplicabilityRegister) 
  {
    try {
      $relationships = ['customer', 'corporate', 'scope'];
      $process = ProcessAudit::with($relationships)->find($idAuditProcess);
      $aplicabilityRegister = AplicabilityRegister::with('contract_matters.matter')->findOrFail($idAplicabilityRegister);
      
      $data = ContractAspect::with(['matter','aspect','application_type', 'status'])->included()->customFilter($idAplicabilityRegister)
        ->filter()->customOrder()->getOrPaginate()->toArray();
      
      $data['info']['status'] = Status::where('group', Status::APLICABILITY_GROUP)->get()->toArray();
      $data['info']['audit_process'] = $process->audit_processes;
      $data['info']['customer_name'] = $process->customer->cust_tradename;
      $data['info']['corporate_name'] = $process->corporate->corp_tradename;
      $data['info']['scope'] = $process->id_scope === Scope::CORPORATE ? $process->scope->scope : "{$process->scope->scope}: {$process->specification_scope}";
      $data['info']['evaluate_risk'] = boolval($process->evaluate_risk);
      $data['info']['matters'] = ( new MattersSection('applicability', $idAplicabilityRegister) )->getMatters();

      $filters = request('filters');
      $infoData = null;
      if ( isset($filters['id_contract_matter']) ) {
        $matterSelected = $aplicabilityRegister->contract_matters->firstWhere('id_contract_matter', $filters['id_contract_matter']);
        $progress = $this->calculateProgress($matterSelected->contract_aspects, $aplicabilityRegister->id_status);
        $infoData['matter'] = $matterSelected->matter->matter;
        $infoData['status'] = $matterSelected->status->status;
        $infoData['count_all_aspects'] = $progress['count_all_aspects'];
        $infoData['count_classified_aspects'] = $progress['count_classified_aspects'];
        $infoData['total'] = $progress['total'];
        $data['progress']['matter'] = $infoData;
      }
      
      $allMatters = $aplicabilityRegister->contract_matters->flatMap(fn($item) => $item->contract_aspects);
      $progress = $this->calculateProgress($allMatters, $aplicabilityRegister->id_status);
      $data['progress']['matter'] = $infoData;
      $data['progress']['status'] = $aplicabilityRegister->status;
      $data['progress']['count_all_aspects'] = $progress['count_all_aspects'];
      $data['progress']['count_classified_aspects'] = $progress['count_classified_aspects'];
      $data['progress']['total'] = $progress['total'];
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Complete a specific aspect
   * 
   * @param int idAuditProcess
   * @param int idAplicabilityRegister
   * @param int idContractMatter
   * @param int idContractAspect
   * @return \Illuminate\Http\Response
   * 
   * Params used in middleware route
   */
  public function complete(Request $request, $idAuditProcess, $idAplicabilityRegister, $idContractMatter, $idContractAspect) 
  {
    try {
      $evaluates = EvaluateAuditRequirement::where('id_audit_aspect', $idContractAspect)->get();
      $formIsComplete = $evaluates->filter(fn($item) => $item->applicability->isEmpty())->isEmpty();
      if ( !$formIsComplete ) return $this->errorResponse('Preguntas incompletas, por favor termina de responder cada pregunta');

      DB::beginTransaction();

      $evaluate = new Evaluate($idContractAspect);
      $applicabilityTypeEvaluate = $evaluate->getApplicationTypeAspect();
      if ( !$applicabilityTypeEvaluate['success'] ) {
        return $this->errorResponse($applicabilityTypeEvaluate['message']);
      }
      
      DB::commit();
      return $this->successResponse($applicabilityTypeEvaluate);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  /**
   * Complete a specific aspect
   * 
   * @param int idAuditProcess
   * @param int idAplicabilityRegister
   * @param int idContractMatter
   * @param int idContractAspect
   * @return \Illuminate\Http\Response
   * 
   * Params used in middleware route
   */
  public function sync(Request $request, $idAuditProcess, $idAplicabilityRegister, $idContractMatter, $idContractAspect) 
  {
    try {
      $contractAspect = ContractAspect::with('status')->find($idContractAspect);
      $validateStatus = $contractAspect['status']['key'] != 'CLASSIFIED_APLICABILITY' || $contractAspect['status']['key'] != 'FINISHED_APLICABILITY';
      
      if ( !$validateStatus ) return $this->errorResponse('El aspecto debe estar en status Clasificado o Finalizado');

      DB::beginTransaction();

      $process = ProcessAudit::find($idAuditProcess);
      $handlerEvaluate = new HandlerEvaluateQuestion($process);
      $sync = $handlerEvaluate->setEvaluateQuestion($contractAspect);

      if ( !$sync['success'] ) {
        return $this->errorResponse($sync['message']);
      }

      $message = 'Se han sincronizado las preguntas con catálogos, por favor revisa preguntas que no han sido contestadas';
      DB::commit();
      return $this->successResponse($contractAspect, Response::HTTP_OK, $message);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  private function calculateProgress($groupAspects, $statusApplicabilityRegister)
  {
    $curretStatus = $statusApplicabilityRegister == Aplicability::FINISHED_APLICABILITY 
      ? Aplicability::FINISHED_APLICABILITY
      : Aplicability::CLASSIFIED_APLICABILITY;

    $countAllAspects = $groupAspects->count();
    $countClassifiedAspects = $groupAspects->where('id_status', $curretStatus)->count();

    $infoData['count_all_aspects'] = $countAllAspects;
    $infoData['count_classified_aspects'] = $countClassifiedAspects;
    $infoData['total'] = round($countAllAspects == 0 ? 0 : ($countClassifiedAspects * 100) / $countAllAspects, 2);

    return $infoData;
  }
}
