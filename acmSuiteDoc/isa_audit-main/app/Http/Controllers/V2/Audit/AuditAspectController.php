<?php

namespace App\Http\Controllers\V2\Audit;

use App\Classes\Audit\EvaluateAuditAnswerPercentage;
use App\Classes\Utilities\MattersSection;
use App\Http\Controllers\Controller;
use App\Models\V2\Audit\Audit;
use App\Models\V2\Audit\AuditAspect;
use App\Models\V2\Audit\AuditRegister;
use App\Models\V2\Audit\EvaluateAuditRequirement;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Catalogs\Scope;
use App\Models\V2\Catalogs\Status;
use App\Traits\V2\ResponseApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditAspectController extends Controller
{
  use ResponseApiTrait;

  /**
   * Get all records
   */
  public function all($idAuditProcess, $idAplicabilityRegister, $idAuditRegister) 
  {
    try {
      $relationships = ['customer', 'corporate', 'scope'];
      $process = ProcessAudit::with($relationships)->find($idAuditProcess);
      $relationshipsAudit = ['status', 'audit_matters.matter','audit_matters.audit_aspects.aspect'];
      $auditRegister = AuditRegister::with($relationshipsAudit)->findOrFail($idAuditRegister);
      $relationshipsAspect = ['matter','aspect','status','application_type'];
      $data = AuditAspect::with($relationshipsAspect)->included()->customFilter($idAuditRegister)
        ->filter()->customOrder()->getOrPaginate()->toArray();
      
      $data['info']['status'] = Status::where('group', Status::AUDIT_GROUP)->get()->toArray();
      $data['info']['audit_process'] = $process->audit_processes;
      $data['info']['customer_name'] = $process->customer->cust_tradename;
      $data['info']['corporate_name'] = $process->corporate->corp_tradename;
      $data['info']['scope'] = $process->id_scope === Scope::CORPORATE ? $process->scope->scope : "{$process->scope->scope}: {$process->specification_scope}";
      $data['info']['evaluate_risk'] = boolval($process->evaluate_risk);
      $data['info']['matters'] = ( new MattersSection('audit', $idAuditRegister) )->getMatters();

      $filters = request('filters');
      $infoData = null;
      if ( isset($filters['id_audit_matter']) ) {
        $matterSelected = $auditRegister->audit_matters->firstWhere('id_audit_matter', $filters['id_audit_matter']);
        $progress = $this->calculateProgress($matterSelected->audit_aspects, $auditRegister->id_status);
        $infoData['matter'] = $matterSelected->matter->matter;
        $infoData['status'] = $matterSelected->status->status;
        $infoData['count_all_aspects'] = $progress['count_all_aspects'];
        $infoData['count_audited_aspects'] = $progress['count_audited_aspects'];
        $infoData['total'] = $auditRegister['total'];
        $data['progress']['matter'] = $infoData;
      }
      
      $allMatters = $auditRegister->audit_matters->flatMap(fn($item) => $item->audit_aspects);
      $progress = $this->calculateProgress($allMatters, $auditRegister->id_status);
      $data['progress']['matter'] = $infoData;
      $data['progress']['status'] = $auditRegister->status;
      $data['progress']['count_all_aspects'] = $progress['count_all_aspects'];
      $data['progress']['count_audited_aspects'] = $progress['count_audited_aspects'];
      $data['progress']['total'] = $auditRegister['total'];
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Display a listing of the resource.
   *
   * @param int idAuditProcess
   * @param int idAplicabilityRegister
   * @param int idAuditRegister
   * @param int idAuditMatter
   * @return \Illuminate\Http\Response
   * 
   * Params used in middleware route
   */
  public function index($idAuditProcess, $idAplicabilityRegister, $idAuditRegister, $idAuditMatter)
  {
    try {
      $data = AuditAspect::included()->customFilter($idAuditRegister, $idAuditMatter)
        ->filter()->customOrder()->getOrPaginate();
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
   * @param int idAuditRegister
   * @param int idAuditMatter
   * @param int idAuditAspect
   * @return \Illuminate\Http\Response
   * 
   * Params used in middleware route
   */
  public function complete(Request $request, $idAuditProcess, $idAplicabilityRegister, $idAuditRegister, $idAuditMatter, $idAuditAspect) 
  {
    try {
      $evaluates = EvaluateAuditRequirement::where('id_audit_aspect', $idAuditAspect)->get();
      $isAllComplete = $evaluates->where('complete', 0)->count() == 0;
      if (!$isAllComplete) {
        $info['title'] = 'Requerimientos incompleto';
        $info['message'] = 'Usa el filtro "Requerimientos evaluados" y/o revisa los registros con iconos en color rojo y sus respuestas';
        return $this->successResponse([], 200, '', $info);
      }
      DB::beginTransaction();
      // finish aspect
      $auditAspect = AuditAspect::findOrFail($idAuditAspect);
      $auditAspect->update(['id_status' => Audit::AUDITED_AUDIT]);
      // calculate percentage and estatus
      $calculate = new EvaluateAuditAnswerPercentage();
      $update = $calculate->calculateAspectLevel($idAuditAspect);
      if (!$update['success']) {
        return $this->errorResponse('Algo salio mal al finzalizar aspecto');
      }
      DB::commit();
      return $this->successResponse($auditAspect);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  private function calculateProgress($groupAspects, $statusAuditRegister)
  {
    $curretStatus = $statusAuditRegister == Audit::FINISHED_AUDIT_AUDIT 
      ? Audit::FINISHED_AUDIT_AUDIT
      : Audit::AUDITED_AUDIT;

    $countAllAspects = $groupAspects->count();
    $countAuditedAspects = $groupAspects->where('id_status', $curretStatus)->count();

    $infoData['count_all_aspects'] = $countAllAspects;
    $infoData['count_audited_aspects'] = $countAuditedAspects;
    $infoData['total'] = round($countAllAspects == 0 ? 0 : ($countAuditedAspects * 100) / $countAllAspects, 2);

    return $infoData;
  }
}
