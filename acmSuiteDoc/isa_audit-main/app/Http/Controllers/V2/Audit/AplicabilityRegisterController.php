<?php

namespace App\Http\Controllers\V2\Audit;

use App\Classes\Library\LibraryRecord;
use App\Http\Controllers\Controller;
use App\Models\V2\Admin\Address;
use App\Models\V2\Audit\AplicabilityRegister;
use App\Models\V2\Audit\EvaluateRequirement;
use App\Exports\Applicability\AplicabilityResultReportExcel;
use App\Exports\Applicability\ApplicabilityReportExcel;
use App\Models\V2\Audit\Aplicability;
use App\Models\V2\Audit\EvaluateApplicabilityQuestion;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Catalogs\Status;
use App\Traits\V2\RequirementTrait;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

use App\Traits\V2\ResponseApiTrait;
 
class AplicabilityRegisterController extends Controller
{
  use ResponseApiTrait, RequirementTrait;

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($idAuditProcess, $idAplicabilityRegister) 
  { 
    try { 
      $option = request('option');
      if ( !is_null($option) && $option == 'share' ) {
        $filters = request('filters');
        $records = new LibraryRecord($idAplicabilityRegister, $filters['id_requirement'], $filters['id_subrequirement'] ?? null);
        $records = $records->findRequirement();
        return $this->successResponse($records);
      }
      $data = AplicabilityRegister::with(['contract_matters.matter'])->included()->findOrFail($idAplicabilityRegister)->toArray();
      $data['process_audit'] = ProcessAudit::with(['customer', 'corporate', 'scope'])->find($idAuditProcess);
      $data['status'] = Status::where('group', Status::APLICABILITY_GROUP)->get()->toArray();
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
   * @return \Illuminate\Http\Response
   * 
   * Params used in Middleware route
   */
  public function complete(Request $request, $idAuditProcess, $idAplicabilityRegister)
  {
    try {
      $record = AplicabilityRegister::with(['contract_matters.contract_aspects'])->included()->findOrFail($idAplicabilityRegister);
      $mattersCompleted = $record->contract_matters->pluck('id_status')->every(fn($item) => $item == Aplicability::CLASSIFIED_APLICABILITY);
      
      if (!$mattersCompleted) {
        $info['title'] = 'No es posible finalizar la Aplicabilidad';
        $info['message'] = 'Asegurate que todas las materias tengan el estatus Clasificado para poder finalizar';
        return $this->successResponse([], Response::HTTP_OK, '', $info);
      }
      DB::beginTransaction();
      foreach ($record->contract_matters as $matter) {
        $matter->update(['id_status' => Aplicability::FINISHED_APLICABILITY]);
        foreach ($matter->contract_aspects as $aspect) {
          $aspect->update(['id_status' => Aplicability::FINISHED_APLICABILITY]);
        }
      }
      $record->update(['id_status' => Aplicability::FINISHED_APLICABILITY]);
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
  public function reportAnswers($idAuditProcess, $idAplicabilityRegister)
  {
    try {
      ini_set('max_execution_time', 18000);
      ini_set('memory_limit', '-1');
      $timezone = Config('enviroment.time_zone_carbon');
      $today = Carbon::now($timezone)->format('d-m-Y');
      $relationships = ['status', 'process.corporate.addresses', 'process.corporate.industry', 'process.auditors.person', 'process.scope'];
      $aplicabilityRegister = AplicabilityRegister::with($relationships)->findOrFail($idAplicabilityRegister);
      // data process
      $process = $aplicabilityRegister->process;
      $corporate = $process->corporate;
      $data['headers']['corp_tradename'] = $corporate->corp_tradename_format;
      $data['headers']['corp_trademark'] = $corporate->corp_trademark_format;
      $data['headers']['rfc'] = $corporate->rfc;
      $data['headers']['status'] = $aplicabilityRegister->status->status;
      $data['headers']['industry'] = $corporate->industry->industry;
      $data['headers']['users'] = $process->auditors->where('pivot.leader', 1)->pluck('person.full_name')->join(', ');
      $data['headers']['scope'] = $process->scope->scope;
      $address = $corporate->addresses->firstWhere('type', Address::PHYSICAL);
      $data['headers']['street'] = $address['street'];
      $data['headers']['suburb'] = $address['suburb'];
      $data['headers']['city'] = $address['city']['city'];
      $data['headers']['state'] = $address['state']['state'];
      $data['headers']['country'] = $address['country']['country'];
      $data['headers']['date'] = $today;
      $data['headers']['evaluate_risk'] = $process->evaluate_risk;
      // search all aspects
      $contractAspect = $aplicabilityRegister->contract_matters->flatMap(fn($item) => $item->contract_aspects);
      $data['headers']['aspects_evaluated'] = $contractAspect->pluck('aspect.aspect')->join(', ');
      $contractAspectIds = $contractAspect->pluck('id_contract_aspect')->toArray();
      $relationshipsEvaluate = ['question.form.matter', 'question.form.aspect', 'applicability.answer_question', 'comment'];
      $evaluates = EvaluateApplicabilityQuestion::with($relationshipsEvaluate)->whereIn('id_contract_aspect', $contractAspectIds)->customOrderEvaluateQuestion()->get();

      $data['questions'] = $evaluates->map(function($item) {
        return [
          'order' => $item['question']['order'],
          'matter' => $item['question']['form']['matter']['matter'],
          'aspect' => $item['question']['form']['aspect']['aspect'],
          'question' => $item['question']['question'],
          'answers' => $this->getAnswer($item),
          'comments' => $item['comment']['comment'] ?? '',
        ];
      });
      
      $documentName = "Respuestas de aplicabilidad - {$data['headers']['corp_trademark']} - {$today}.xlsx";

      return Excel::download(new ApplicabilityReportExcel($data), $documentName);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Get report
   * 
   * Params used in Middleware route
   */
  public function reportResults($idAuditProcess, $idAplicabilityRegister)
  {
    try {
      ini_set('max_execution_time', 18000);
      ini_set('memory_limit', '-1');
      $timezone = Config('enviroment.time_zone_carbon');
      $today = Carbon::now($timezone)->format('d-m-Y');
      $relationships = ['status', 'process.corporate.addresses', 'process.corporate.industry', 'process.auditors.person', 'process.scope', 'contract_matters.contract_aspects.aspect'];
      $aplicabilityRegister = AplicabilityRegister::with($relationships)->findOrFail($idAplicabilityRegister);
      $process = $aplicabilityRegister->process;
      $corporate = $process->corporate;
      $data['headers']['corp_tradename'] = $corporate->corp_tradename_format;
      $data['headers']['corp_trademark'] = $corporate->corp_trademark_format;
      $data['headers']['rfc'] = $corporate->rfc;
      $data['headers']['status'] = $aplicabilityRegister->status->status;
      $data['headers']['industry'] = $corporate->industry->industry;
      $data['headers']['users'] = $process->auditors->where('pivot.leader', 1)->pluck('person.full_name')->join(', ');
      $data['headers']['scope'] = $process->scope->scope;
      $address = $corporate->addresses->firstWhere('type', Address::PHYSICAL);
      $data['headers']['street'] = $address['street'];
      $data['headers']['suburb'] = $address['suburb'];
      $data['headers']['city'] = $address['city']['city'];
      $data['headers']['state'] = $address['state']['state'];
      $data['headers']['country'] = $address['country']['country'];
      $data['headers']['date'] = $today;
      $data['headers']['evaluate_risk'] = $process->evaluate_risk;
      // search all aspects
      $contractAspect = $aplicabilityRegister->contract_matters->pluck('contract_aspects')->collapse();
      $data['headers']['aspects_evaluated'] = $contractAspect->pluck('aspect.aspect')->join(', ');
      
      $relationshipsEvaluate = [
        'requirement.articles.guideline', 
        'subrequirement.articles.guideline',
        'requirement.condition', 
        'subrequirement.condition',
        'requirement.evidence', 
        'subrequirement.evidence',
      ];
      $records = EvaluateRequirement::with($relationshipsEvaluate)->where('aplicability_register_id', $idAplicabilityRegister)->customOrder()->get();

      $data['requirements'] = $records->map(fn($item) => $this->defineRecord($item))->toArray();
      $documentName = "Reporte de resultados de aplicabilidad - {$corporate->corp_trademark_format}.xlsx";
      
      return Excel::download(new AplicabilityResultReportExcel($data), $documentName);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  private function defineRecord($record) 
  {
    $legal = $this->getFieldRequirement($record, 'articles');
    $build['matter'] = $this->getFieldRequirement($record, 'matter');
    $build['aspect'] = $this->getFieldRequirement($record, 'aspect');
    $build['id_requirement'] = $record->id_requirement;
    $build['no_requirement'] = $this->getFieldRequirement($record, 'no_requirement');
    $build['requirement'] = $this->getFieldRequirement($record, 'requirement');
    $build['description'] = $this->getFieldRequirement($record, 'description');
    $build['evidence'] = $this->getFieldRequirement($record, 'evidence', '');
    $build['condition'] = $this->getFieldRequirement($record, 'condition');
    $build['document'] = $this->getFieldRequirement($record, 'document', 'N/A');
    $build['periodicity'] = $this->getFieldRequirement($record, 'periodicity', 'N/A');
    $build['link_legal'] = $legal['link_legal'];
    $build['legals_name'] = $legal['legals_name'];

    return $build;
  }

  private function getAnswer($record)
  {
    if( sizeof($record['applicability']) == 0 ) return ['Pendiente'];

    $answerLabels = [];
    foreach( $record['applicability'] as $answer ) {
      $answer = is_null($answer['answer_question']) ? 'No requerida' : $answer['answer_question']['description'];
      array_push($answerLabels, $answer);
    }
    return $answerLabels;
  }
}