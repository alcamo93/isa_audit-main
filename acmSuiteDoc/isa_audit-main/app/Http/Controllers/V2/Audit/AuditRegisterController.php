<?php

namespace App\Http\Controllers\V2\Audit;

use App\Classes\Process\CreateAuditObligation;
use App\Classes\Audit\UtilitiesAudit;
use App\Exports\AuditDocumentsReportExcel;
use App\Exports\AuditProgressReportExcel;
use App\Exports\AuditReportExcel;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuditRegisterRequest;
use App\Models\V2\Audit\Audit;
use App\Models\V2\Admin\Address;
use App\Models\V2\Audit\AuditRegister;
use App\Models\V2\Audit\EvaluateAuditRequirement;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Catalogs\Status;
use App\Traits\V2\ResponseApiTrait;
use App\Traits\V2\RequirementTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;

class AuditRegisterController extends Controller
{
  use ResponseApiTrait, RequirementTrait;

  /**
   * Store the specified resource in storage.
   *
   * @param  App\Http\Requests\AuditRegisterRequest  $request
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @return \Illuminate\Http\Response
   * 
   * Params used in Middleware route
   */
  public function store(AuditRegisterRequest $request, $idAuditProcess, $idAplicabilityRegister)
  {
    try {
      DB::beginTransaction();
      $initDate = $request->input('init_date');
      $endDate = $request->input('end_date');
      $audit = new CreateAuditObligation($idAplicabilityRegister, $initDate, $endDate);
      $initAudit = $audit->initAudit();
      if (!$initAudit['status']) {
        DB::rollback();
        return $this->errorResponse($initAudit['message']);
      }
      DB::commit();
      return $this->successResponse($request, Response::HTTP_CREATED, 'Auditoría Nueva Creada');
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
   * @param  int  $idAuditRegister
   * @return \Illuminate\Http\Response
   * 
   * Params used in Middleware route
   */
  public function show($idAuditProcess, $idAplicabilityRegister, $idAuditRegister) 
  {
    try { 
      $data = AuditRegister::included()->with(['audit_matters.matter'])->findOrFail($idAuditRegister)->toArray();
      $data['process_audit'] = ProcessAudit::with(['customer', 'corporate', 'scope'])->find($idAuditProcess);
      $data['status'] = Status::where('group', Status::AUDIT_GROUP)->get()->toArray();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Complete the specified resource.
   *
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  int  $idAuditRegister
   * @return \Illuminate\Http\Response
   * 
   * Params used in Middleware route
   */
  public function complete(Request $request, $idAuditProcess, $idAplicabilityRegister, $idAuditRegister)
  {
    try {
      $record = AuditRegister::with(['audit_matters.audit_aspects'])->included()->findOrFail($idAuditRegister);
      $mattersCompleted = $record->audit_matters->pluck('id_status')->every(fn($item) => $item == Audit::AUDITED_AUDIT);
      
      if (!$mattersCompleted) {
        $info['title'] = 'No es posible finalizar la Auditoría';
        $info['message'] = 'Asegurate que todas las materias tengan el estatus Auditado para poder finalizar';
        return $this->successResponse([], Response::HTTP_OK, '', $info);
      }
      DB::beginTransaction();
      foreach ($record->audit_matters as $matter) {
        $matter->update(['id_status' => Audit::FINISHED_AUDIT_AUDIT]);
        foreach ($matter->audit_aspects as $aspect) {
          $aspect->update(['id_status' => Audit::FINISHED_AUDIT_AUDIT]);
        }
      }
      $record->update(['id_status' => Audit::FINISHED_AUDIT_AUDIT]);
      DB::commit();
      return $this->successResponse($record);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  /**
   * Get report
   * 
   * Params used in Middleware route
   */
  public function reportAudit($idAuditProcess, $idAplicabilityRegister, $idAuditRegister)
  {
    try {
      ini_set('max_execution_time', 18000);
      ini_set('memory_limit', '-1');
      $relationships = ['status', 'audit_matters.audit_aspects'];
      $auditRegister = AuditRegister::with($relationships)->findOrFail($idAuditRegister);
      $relationshipsProcess = ['corporate.addresses', 'corporate.industry', 'auditors.person', 'scope'];
      $process = ProcessAudit::with($relationshipsProcess)->find($idAuditProcess);
      $corporate = $process->corporate;
      $data['dashboard']['corp_tradename'] = $corporate->corp_tradename_format;
      $data['dashboard']['corp_trademark'] = $corporate->corp_trademark_format;
      $data['dashboard']['rfc'] = $corporate->rfc;
      $data['dashboard']['status'] = $auditRegister->status->status;
      $data['dashboard']['industry'] = $corporate->industry->industry;
      $data['dashboard']['users'] = $process->auditors->pluck('person.full_name')->join(', ');
      $data['dashboard']['scope'] = $process->scope->scope;
      $address = $corporate->addresses->firstWhere('type', Address::PHYSICAL);
      $data['dashboard']['street'] = $address['street'];
      $data['dashboard']['suburb'] = $address['suburb'];
      $data['dashboard']['city'] = $address['city']['city'];
      $data['dashboard']['state'] = $address['state']['state'];
      $data['dashboard']['country'] = $address['country']['country'];
      $data['dashboard']['date'] = $auditRegister->init_date_format;
      $data['dashboard']['evaluate_risk'] = $process->evaluate_risk;
      // all audits
      $auditAspectsIds = $auditRegister->audit_matters->flatMap(fn($item) => $item->audit_aspects)->pluck('id_audit_aspect')->toArray();
      $relationshipsRecords = ['images', 'requirement.articles.guideline', 'requirement.evidence', 'subrequirement.articles.guideline', 'subrequirement.evidence'];
      $audits = Audit::with($relationshipsRecords)->getRisk()->customOrder()->whereIn('id_audit_aspect', $auditAspectsIds)->get();
      $auditsParentNegative = $audits->whereNull('id_subrequeriment')->where('key_answer.key', 'NEGATIVE')->values();
      $auditsAllNegative = $audits->where('key_answer.key', 'NEGATIVE')->values();
      // report 
      $data['dashboard']['audit_global'] = $auditRegister->total;
      $data['dashboard']['count_global'] = $auditsAllNegative->count();
      $data['dashboard']['matters'] = $auditRegister->audit_matters->map(function($matter) use ($audits, $auditsParentNegative) {
        $aspectsIds = $matter->audit_aspects->pluck('id_aspect')->toArray();
        $matterTmp = $matter->matter->toArray();
        $matterTmp['total'] = $matter->total;
        $matterTmp['count'] = $auditsParentNegative->whereIn('id_aspect', $aspectsIds)->values()->count();
        $matterTmp['aspects'] = $matter->audit_aspects->map(function($aspect) use ($audits, $auditsParentNegative) {
          $utilitiesAudit = new UtilitiesAudit();
          $mains = $audits->whereNull('id_subrequirement')->where('id_aspect', $aspect->id_aspect)->map( fn($record) => $utilitiesAudit->defineRecord($record) )->values();
          $childs = $audits->whereNotNull('id_subrequirement')->where('id_aspect', $aspect->id_aspect)->map( fn($record) => $utilitiesAudit->defineRecord($record) )->values();
          $answers = $mains->map(function($main) use ($childs) {
            $main['childs'] = $childs->where('id_requirement', $main['id_requirement'])->sortBy('order')->values();
            $main['child_findings'] = $childs->where('id_requirement', $main['id_requirement'])->where('key_answer', 'NEGATIVE')->sortBy('order')->values();
            return $main;
          })->sortBy('order')->values();
          $aspectTmp = $aspect->aspect->toArray();
          $aspectTmp['total'] = $aspect->total;
          $aspectTmp['count'] = $auditsParentNegative->where('id_aspect', $aspect->id_aspect)->values()->count();
          $aspectTmp['audits'] = $answers->toArray();
          $aspectTmp['findings'] = $answers->where('key_answer', 'NEGATIVE')->values()->toArray();
          return $aspectTmp;
        })->toArray();
        return $matterTmp;
      })->sortBy('order')->values()->toArray();
      $documentName = "Reporte de auditoría - ({$auditRegister->init_date}) {$corporate->corp_trademark_format}.xlsx";
      return Excel::download(new AuditReportExcel($data), $documentName);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Get report
   * 
   * Params used in Middleware route
   */
  public function reportDocument($idAuditProcess, $idAplicabilityRegister, $idAuditRegister)
  {
    try {
      ini_set('max_execution_time', 18000);
      ini_set('memory_limit', '-1');
      $relationships = ['status', 'audit_matters.audit_aspects'];
      $auditRegister = AuditRegister::with($relationships)->findOrFail($idAuditRegister);
      $relationshipsProcess = ['corporate.addresses', 'corporate.industry', 'auditors.person', 'scope'];
      $process = ProcessAudit::with($relationshipsProcess)->find($idAuditProcess);
      $corporate = $process->corporate;
      $data['corp_tradename'] = $corporate->corp_tradename_format;
      $data['corp_trademark'] = $corporate->corp_trademark_format;
      $address = $corporate->addresses->firstWhere('type', Address::PHYSICAL);
      $data['full_address'] = $address['full_address'];
      $data['date'] = $auditRegister->init_date_format;
      $data['audit_processes'] = $process->audit_processes;
      $data['scope'] = $process->scope->scope;
      $audiors = $process->auditors;
      $leaderAuditor = $audiors->where('pivot.leader', 1)->first();
      $normalAuditor = $audiors->where('pivot.leader', 0)->values();
      $data['leader_auditor'] = $leaderAuditor->person->full_name;
      $data['auditors'] = $normalAuditor->pluck('person.full_name')->join(', ');
      $data['industry'] = $corporate->industry->industry;
      $aspects = $auditRegister->audit_matters->flatMap(fn($item) => $item->audit_aspects)->values();
      $data['aspects'] = $aspects->pluck('aspect.aspect')->join(', ');

      $auditAspectsIds = $auditRegister->audit_matters->flatMap(fn($item) => $item->audit_aspects)->pluck('id_audit_aspect')->values()->toArray();
      $relationships = ['requirement.application_type', 'subrequirement.application_type'];
      $evaluateRequirements = EvaluateAuditRequirement::with($relationships)->whereIn('id_audit_aspect', $auditAspectsIds)->customOrder()->get();
      
      $data['requirements'] = $evaluateRequirements->map(function($item, $index) {
        $row['index'] = ++$index;
        $row['aspect'] = $this->getFieldRequirement($item, 'aspect');
        $row['application_type'] = $this->getFieldRequirement($item, 'application_type');
        $row['no_requirement'] = $this->getFieldRequirement($item, 'no_requirement');
        $row['document'] = $this->getFieldRequirement($item, 'document');
        return $row;
      });
      
      $documentName = "Documentos Requeridos Auditoría ({$auditRegister->init_date}) - {$corporate->corp_trademark_format}.xlsx";
      return Excel::download(new AuditDocumentsReportExcel($data), $documentName);
    } catch (\Throwable $th) {
      return $th;
    }
  }

  /**
   * Get report
   * 
   * Params used in Middleware route
   */
  public function reportProgress($idAuditProcess, $idAplicabilityRegister, $idAuditRegister)
  {
    try {
      ini_set('max_execution_time', 18000);
      ini_set('memory_limit', '-1');
      // data report
      $relationships = ['status', 'audit_matters.audit_aspects'];
      $auditRegister = AuditRegister::with($relationships)->findOrFail($idAuditRegister);
      $relationshipsProcess = ['corporate.addresses', 'corporate.industry', 'auditors.person', 'scope'];
      $process = ProcessAudit::with($relationshipsProcess)->find($idAuditProcess);
      $corporate = $process->corporate;
      $data['audit_processes'] = $process->audit_processes;
      $data['corp_tradename'] = $corporate->corp_tradename_format;
      $data['corp_trademark'] = $corporate->corp_trademark_format;
      $data['rfc'] = $corporate->rfc;
      $data['status'] = $auditRegister->status->status;
      $data['industry'] = $corporate->industry->industry;
      $data['scope'] = $process->scope->scope;
      $address = $corporate->addresses->firstWhere('type', Address::PHYSICAL);
      $data['street'] = $address['street'];
      $data['suburb'] = $address['suburb'];
      $data['city'] = $address['city']['city'];
      $data['state'] = $address['state']['state'];
      $data['country'] = $address['country']['country'];
      $data['date'] = $auditRegister->init_date_format;
      $data['evaluate_risk'] = $process->evaluate_risk;
      $data['auditors'] = $process->auditors->pluck('person.full_name')->join(', ');
      $aspects = $auditRegister->audit_matters->pluck('audit_aspects')->collapse()->values();
      $data['aspects'] = $aspects->pluck('aspect.aspect')->join(', ');
      $auditAspectsIds = $auditRegister->audit_matters->flatMap(fn($item) => $item->audit_aspects)->pluck('id_audit_aspect')->values()->toArray();
      $evaluateRequirements = EvaluateAuditRequirement::whereIn('id_audit_aspect', $auditAspectsIds)->customOrder()->get();
      $data['requirements'] = $evaluateRequirements->map(function($item, $index) {
        $hasResponse = !is_null($item->audit);
        $row['index'] = ++$index;
        $row['aspect'] = $this->getFieldRequirement($item, 'aspect');
        $row['no_requirement'] = $this->getFieldRequirement($item, 'no_requirement');
        $row['requirement'] = $this->getFieldRequirement($item, 'requirement');
        $row['finding'] = (new UtilitiesAudit())->defineFinding($item, true);
        $row['answer'] = $hasResponse ? $item->audit->key_answer['label'] : 'Pendiente';
        return $row;
      })->toArray();
      $data['total']['total_requirements_audited'] = $evaluateRequirements->whereNotNull('audit')->count();
      $data['total']['total_affirmative'] = $evaluateRequirements->whereNotNull('audit')->where('audit.answer', Audit::AFFIRMATIVE)->count();
      $data['total']['total_negative'] = $evaluateRequirements->whereNotNull('audit')->where('audit.answer', Audit::NEGATIVE)->count();
      $data['total']['total_not_apply'] = $evaluateRequirements->whereNotNull('audit')->where('audit.answer', Audit::NO_APPLY)->count();
      $data['total']['total_requirement_no_audit'] = $evaluateRequirements->whereNull('audit')->count();
      $data['total']['total_requerements'] = $evaluateRequirements->count();
      $documentName = "Progreso de Auditoría ({$auditRegister->init_date}) - {$corporate->corp_trademark_format}.xlsx";
      return Excel::download(new AuditProgressReportExcel($data), $documentName);
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}
