<?php

namespace App\Http\Controllers\V2\Audit;

use App\Classes\Obligation\UtilitiesObligation;
use App\Classes\Process\CreateAuditObligation;
use App\Exports\ObligationReportExcel;
use App\Http\Controllers\Controller;
use App\Http\Requests\ObligationRegisterRequest;
use App\Models\V2\Audit\ObligationRegister;
use App\Models\V2\Audit\Obligation;
use App\Models\V2\Admin\Address;
use App\Models\V2\Audit\ProcessAudit;
use App\Traits\V2\ResponseApiTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ObligationRegisterController extends Controller
{
  use ResponseApiTrait;

  /**
   * Store the specified resource in storage.
   *
   * @param  \App\Http\Requests\ObligationRegisterRequest  $request
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @return \Illuminate\Http\Response
   * 
   * Params used in Middleware route
   */
  public function store(ObligationRegisterRequest $request, $idAuditProcess, $idAplicabilityRegister) 
  {
    try {
      DB::beginTransaction();
      $initDate = $request->input('init_date');
      $endDate = $request->input('end_date');
      $obligation = new CreateAuditObligation($idAplicabilityRegister, $initDate, $endDate);
      $initObligation = $obligation->initObligation();
      if ( !$initObligation ) {
        DB::rollback();
        return $this->errorResponse('Error al iniciar Obligaciones');
      }
      DB::commit();
      return $this->successResponse($request, Response::HTTP_CREATED, 'Obligaciones Activadas');
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  Request  $request
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  int  $idObligationRegister     
   * @return \Illuminate\Http\Response
   * 
   * Params used in Middleware route
   */
  public function report(Request $request, $idAuditProcess, $idAplicabilityRegister, $idObligationRegister) 
  {
    try {
      ini_set('max_execution_time', 18000);
      ini_set('memory_limit', '-1');
      $obligationRegister = ObligationRegister::findOrFail($idObligationRegister);
      $relationships = ['requirement.articles.guideline', 'subrequirement.articles.guideline'];
      $obligations = Obligation::with($relationships)->getRisk()->where('obligation_register_id', $idObligationRegister)->customOrder()->get();
      $relationshipsProcess = ['corporate.addresses', 'corporate.industry', 'auditors.person', 'scope'];
      $process = ProcessAudit::with($relationshipsProcess)->findOrFail($idAuditProcess);
      $evaluateRisk = boolval($process->evaluate_risk);
      $scope = $process->scope->scope;
      // page: Dashboard
      $data['dashboard']['corp_tradename'] = $process->corporate->corp_tradename;
      $data['dashboard']['corp_trademark'] = $process->corporate->corp_trademark;
      $data['dashboard']['rfc'] = $process->corporate->rfc;
      $data['dashboard']['status'] = '---';
      $data['dashboard']['industry'] = $process->corporate->industry->industry;
      $data['dashboard']['responsible'] = $process->auditors->pluck('person.full_name')->join(', ');
      $data['dashboard']['scope'] = $scope;
      $address = $process->corporate->addresses->firstWhere('type', Address::PHYSICAL);
      $data['dashboard']['street'] = $address['street'];
      $data['dashboard']['suburb'] = $address['suburb'];
      $data['dashboard']['city'] = $address['city']['city'];
      $data['dashboard']['state'] = $address['state']['state'];
      $data['dashboard']['country'] = $address['country']['country'];
      $data['dashboard']['date'] = $obligationRegister->init_date_format;
      $data['dashboard']['evaluate_risk'] = $evaluateRisk;
      // page: dashboard, compliance and obligations
      $useMatters = $obligations->map(fn($item) => $item->requirement->matter)->unique()->values();
      $useAspects = $obligations->map(fn($item) => $item->requirement->aspect)->unique()->values();
      $matters = $useMatters->map(function($matter) use ($useAspects, $obligations) {
        $matterTmp = $matter;
        $matterTmp->aspects = $useAspects->filter(fn($aspect) => $aspect['id_matter'] == $matter->id_matter)->map(function($aspect) use ($obligations) {
            $aspectTmp = $aspect;
            $obligationPerAspect = $obligations->where('requirement.id_aspect', $aspect->id_aspect);
            $countTotalPerAspect = $obligationPerAspect->count();
            $countComplicePerAspect = $obligationPerAspect->whereIn('id_status', [Obligation::FOR_EXPIRED_OBLIGATION, Obligation::APPROVED_OBLIGATION, Obligation::NO_DATES_OBLIGATION])->count();
            $countNoComplicePerAspect = $obligationPerAspect->whereIn('id_status', [Obligation::EXPIRED_OBLIGATION, Obligation::NO_EVIDENCE_OBLIGATION])->count();

            $totalPercentage = ( $countComplicePerAspect/ $countTotalPerAspect ) * 100;
            $aspectTmp->percentage = is_nan($totalPercentage) ? 0 : $totalPercentage;
            $aspectTmp->count = $countNoComplicePerAspect;
            return $aspect->toArray();
          })->values();
        $aspectsPerMatter = $matter->aspects;
        $sumCountPerMatter = $aspectsPerMatter->sum('count');
        $countTotalPerMatter = $aspectsPerMatter->count();
        $sumComplicePerMatter = $aspectsPerMatter->sum('percentage');
        $totalPercentage = $sumComplicePerMatter / $countTotalPerMatter;

        $matterTmp->percentage = is_nan($totalPercentage) ? 0 : $totalPercentage;
        $matterTmp->count = $sumCountPerMatter;
        return $matterTmp;
      });
      // global
      $aspectsPerMatter = collect($matters);
      $sumCountGlobal = $aspectsPerMatter->sum('count');
      $countTotalGlobal = $aspectsPerMatter->count();
      $sumCompliceGlobal = $aspectsPerMatter->sum('percentage');
      $totalPercentage = $sumCompliceGlobal / $countTotalGlobal;
      // structure
      $data['percentages']['global_percentage'] = is_nan($totalPercentage) ? 0 : $totalPercentage;;
      $data['percentages']['global_expired'] = $sumCountGlobal;
      $data['percentages']['matters'] = $matters;
      // page: Report
      $utilities = new UtilitiesObligation();
      $data['report'] = $obligations->map(fn($item) => $utilities->defineRecord($item, $scope, $evaluateRisk))->toArray();
      $documentName = "Reporte de Permisos Críticos - {$data['dashboard']['corp_trademark']}.xlsx";
      return Excel::download(new ObligationReportExcel($data), $documentName);
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}