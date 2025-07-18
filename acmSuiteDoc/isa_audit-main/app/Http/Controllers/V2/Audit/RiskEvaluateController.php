<?php

namespace App\Http\Controllers\V2\Audit;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Requests\RiskEvaluateRequest;
use Illuminate\Http\Response;
use App\Models\V2\Audit\Audit;
use App\Models\V2\Audit\Obligation;
use App\Models\V2\Audit\AuditRegister;
use App\Models\V2\Audit\ObligationRegister;
use App\Models\V2\Catalogs\RiskCategory;
use App\Classes\Process\Risk\Risk;
use App\Traits\V2\ResponseApiTrait;

class RiskEvaluateController extends Controller
{
	use ResponseApiTrait;

  /**
	 * Store a newly created resource in storage.
	 *
   * @param int idAuditProcess
   * @param int idAplicabilityRegister
   * @param string sectionKey
   * @param int registerableId
	 * @return \Illuminate\Http\Response
	 */
  public function show($idAuditProcess, $idAplicabilityRegister, $sectionKey, $idRegisterSection, $registerableId)
  {
    try {
      $validate = $this->validateRelations('show', $idAuditProcess, $idAplicabilityRegister, $sectionKey, $idRegisterSection, $registerableId);
			if (!$validate['success']) {
				return $this->errorResponse($validate['message']);
			}
      if ($sectionKey == 'audit') {
        $record = Audit::included()->getRisk()->findOrFail($registerableId);
      }
      if ($sectionKey == 'obligation') {
        $record = Obligation::included()->getRisk()->findOrFail($registerableId);
      }
      return $this->successResponse($record);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
	 * Store a newly created resource in storage.
	 *
	 * @param App\Http\Requests\RiskEvaluateRequest $request
   * @param int idAuditProcess
   * @param int idAplicabilityRegister
   * @param string sectionKey
   * @param int registerableId
	 * @return \Illuminate\Http\Response
	 */
  public function evaluate(RiskEvaluateRequest $request, $idAuditProcess, $idAplicabilityRegister, $sectionKey, $idRegisterSection, $registerableId)
  {
    try {
      $validate = $this->validateRelations('evaluate', $idAuditProcess, $idAplicabilityRegister, $sectionKey, $idRegisterSection, $registerableId);
			if (!$validate['success']) {
				return $this->errorResponse($validate['message']);
			}

      $verifyRequest = $this->verifyRequest($request->all());
			if (!$verifyRequest['success']) {
				return $this->errorResponse($verifyRequest['message']);
			}
      $requestData = $verifyRequest['request'];
      $riskInstance = new Risk($registerableId, $sectionKey);
      $setRisk = $riskInstance->setRiskAnswer($requestData['id_risk_category'], $requestData['id_risk_attribute'], $requestData['answer']);
      if( !$setRisk['success'] ) {
        return $this->errorResponse($setRisk['message']);
      }

      $auditUpdate = $riskInstance->getParent();
      $info = $setRisk['info'] ?? null;
      return $this->successResponse($auditUpdate, Response::HTTP_OK, $setRisk['message'], $info);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

	/**
	 * validate guideline
	 */
	private function validateRelations($method, $idAuditProcess, $idAplicabilityRegister, $sectionKey, $idRegisterSection, $registerableId)
	{
    $sections = collect(['obligation', 'audit']);
    if ( !$sections->contains($sectionKey) ) {
      $info['success'] = false;
			$info['message'] = 'No es valida la sección que se especifica';
			return $info;
    }

    if ($sectionKey == 'obligation') {
      $obligationFilter = fn($query) => $query->where('id_obligation', $registerableId);
      $relationships = ['aplicability_register.process', 'obligations' => $obligationFilter];
      $mainSection = ObligationRegister::with($relationships)
        ->whereHas('obligations', $obligationFilter)->find($idRegisterSection);
    }
    if ($sectionKey == 'audit') {
      $auditFilter = fn($query) => $query->where('id_audit', $registerableId);
      $relationships = ['aplicability_register.process', 'audit_matters.audit_aspects.audits' => $auditFilter];
      $mainSection = AuditRegister::with($relationships)
        ->whereHas('audit_matters.audit_aspects.audits', $auditFilter)->find($idRegisterSection);
      
      if ( !is_null($mainSection) && $method != 'show' ) {
        $auditAspect = $mainSection->audit_matters->first()->audit_aspects->first();
        if ($auditAspect->id_status == Audit::FINISHED_AUDIT_AUDIT) {
          $statusName = $auditAspect->status->status;
          $response['success'] = false;
          $response['message'] = "El aspecto esta en estatus {$statusName}, no es posible modificar el registro";
          return $response;
        }
      }
    }
    if ( is_null($mainSection) ) {
      $info['success'] = false;
			$info['message'] = 'No se ha encontrado registro especificado';
			return $info;
    }

    $sameAplicability = $mainSection->aplicability_register->id_aplicability_register == $idAplicabilityRegister;
    $sameProcess = $mainSection->aplicability_register->process->id_audit_processes == $idAuditProcess;
		if ( !$sameAplicability || !$sameProcess ) {
			$info['success'] = false;
			$info['message'] = 'Datos incorrectos, verifica por favor';
			return $info;
		}

    $info['success'] = true;
    $info['message'] = 'Verificación exitosa';
		return $info;
	}

  /**
   * Verify data
   */
  private function verifyRequest($requestData)
  {
    try {
      $riskCategory = RiskCategory::with('attributes')->find($requestData['id_risk_category']);
      $hasAttribute = $riskCategory->attributes->pluck('id_risk_attribute')->contains($requestData['id_risk_attribute']);
      if ( is_null($riskCategory) || !$hasAttribute ) {
        $response['success'] = false;
        $response['message'] = 'La categoría o atributo de nivel de riesgo no son validos, verifica por favor';
        return $response;
      }
      $requestVerify = Arr::only($requestData, ['id_risk_category', 'id_risk_attribute', 'answer']);
      $response['success'] = true;
      $response['message'] = 'Registro verificado correctamente';
      $response['request'] = $requestVerify;
      return $response;
    } catch (\Throwable $th) {
      $response['success'] = false;
      $response['message'] = 'Algo salio mal.';
      return $response;
    }
  }
}
