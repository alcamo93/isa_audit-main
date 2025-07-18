<?php

namespace App\Classes\Process;

use App\Classes\Process\HandlerEvaluateQuestion;
use App\Models\V2\Admin\Address;
use App\Models\V2\Admin\Contract;
use App\Models\V2\Admin\Corporate;
use App\Models\V2\Audit\AplicabilityRegister;
use App\Models\V2\Audit\ContractAspect;
use App\Models\V2\Audit\ContractMatter;
use App\Models\V2\Audit\EvaluationRenewal;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Catalogs\EvaluationType;
use App\Models\V2\Catalogs\Form;
use App\Models\V2\Catalogs\Scope;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class HandlerProcess
{
  protected $request = null;
  protected $idAuditProcess = null;
  protected $process = null;
  protected $aplicabilityRegister = null;
  protected $isRenewal = null;
  protected $method = null;
  protected $corporate = null;
  protected $address = null;
  protected $handlerEvaluateQuestion = null;
  protected $renewal = [];
  protected $timezone = null;

  /**
   * 
   * @param array $request
   * @param integer $idAuditProcess
   * @param boolean $isRenewal
   * 
   */
  public function __construct($request, $idAuditProcess = null, $isRenewal = false)
  {
    $this->timezone = Config('enviroment.time_zone_carbon');
    $this->request = $request;
    $this->idAuditProcess = $idAuditProcess;
    $this->isRenewal = $isRenewal;
    $this->method = $this->getAction();
    $this->setDataRenewal();
  }

  /**
   * complete data for register 
   */
  private function setDataRenewal() 
  {
    try {
      if ( $this->method != 'renewal' ) {
        $info['success'] = true;
        $info['message'] = 'No es renovación, continua ';
        return $info;
      }
  
      $this->process = ProcessAudit::noLoadRelationships()->findOrFail($this->idAuditProcess);
      
      $infoKeep['id_customer'] = $this->process['id_customer'];
      $infoKeep['id_corporate'] = $this->process['id_corporate'];
      $infoKeep['evaluation_type_id'] = $this->process['evaluation_type_id'];
      $infoKeep['id_scope'] = $this->process['id_scope'];
      $infoKeep['specification_scope'] = $this->process['specification_scope'];
      $infoKeep['per_year'] = $this->process['per_year'];
  
      $renewalFormsIds = Form::where('is_current', 1)->whereIn('id', $this->request['forms'])->pluck('id')->toArray();
      $this->request['forms'] = $renewalFormsIds;
  
      $this->request = collect($infoKeep)->merge($this->request)->toArray();
      
      $info['success'] = true;
      $info['message'] = 'Se completo la información de request de renovación';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al completar información de renovación';
      return $info;
    }
  }

  /**
   * Get type action in controller (create, update renewal)
   */
  private function getAction() 
  {
    if ( is_null($this->idAuditProcess) && !$this->isRenewal ) return 'create';
    if ( !is_null($this->idAuditProcess) && !$this->isRenewal ) return 'update';
    if ( !is_null($this->idAuditProcess) && $this->isRenewal ) return 'renewal';
    return '';
  }

  /**
   * 
   */
  private function normalizeFields($request) 
  {
    $initDateStr = strlen($request['date']) <= 10 ? "{$request['date']} 00:00:00" : $request['date'];
		$initDate = Carbon::createFromFormat('Y-m-d H:i:s', $initDateStr, $this->timezone);
    $endDate = $initDate->addYear()->toDateString();
    $request = Arr::add($request, 'end_date', $endDate);

    $normalFields = [
      'id_customer','id_corporate','audit_processes','evaluation_type_id','id_scope','specification_scope',
      'evaluate_risk','evaluate_especific','date','end_date','per_year','auditors','forms','use_kpi'
    ];

    if ( $this->method == 'renewal' ) {
      $this->renewal = Arr::only($request, ['keep_risk','keep_files']);
      $renewalFields = Arr::except($normalFields, ['id_customer','id_corporate','evaluation_type_id','id_scope','specification_scope','per_year']);
      return Arr::only($request, $renewalFields);
    }

    return Arr::only($request, $normalFields);
  }

  /**
   * Verify extra data in evaluation
   */
  public function verifyData() 
  {
    try {
      // verify can update
      if ($this->method == 'update') {
        $process = ProcessAudit::noLoadRelationships()->findOrFail($this->idAuditProcess);
        $status = $process->aplicability_register->contract_matters->pluck('contract_aspects')
          ->collapse()->pluck('id_status')->unique();

        if ( $status->count() > 1 || $status->first() != AplicabilityRegister::NOT_CLASSIFIED_APLICABILITY ) {
          $info['success'] = false;
          $info['message'] = "La sección de aplicabilidad de la evaluación '{$process->audit_processes}' ha sido inicida. No se puede editar el ejercicio ya que esta en proceso";
          return $info;
        }
      }
      
      // Verify address
      $corporate = Corporate::with(['addresses', 'contract'])->findOrFail($this->request['id_corporate']);
      $address = $corporate->addresses->firstWhere('type', Address::PHYSICAL);
      if ( is_null($address) ) {
        $info['success'] = false;
        $info['message'] = 'La planta no cuenta con una dirección Física, comuníquese con el administrador';
        return $info;
      }

      // verify contract
      if ( is_null($corporate->contract) || $corporate->contract->id_status == Contract::INACTIVE ) {
        $info['success'] = false;
        $info['message'] = 'La planta no cuenta con Contrato, no es vigente o esta inactivo, comunícate con el administrador';
        return $info;
      }

      // Verify evaluate all
      $handlerUseLikeKPI = $this->handlerUseLikeKPI();
      if ( !$handlerUseLikeKPI['success'] ) return $handlerUseLikeKPI['message'];

      // normalize fields
      $this->request = $this->normalizeFields( $this->request );
      $this->corporate = $corporate;
      $this->address = $address;

      $info['success'] = true;
      $info['message'] = 'Verificación exitosa';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al verificar su solicitud';
      return $info;
    }
  }

  /**
   * store new evaluation 
   */
  public function storeProcess() 
  {
    if ( $this->method != 'create' ) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al verificar la acción';
      return $info;
    }
    $this->process = ProcessAudit::create($this->request);
    $this->idAuditProcess = $this->process->id_audit_processes;
    $this->handlerEvaluateQuestion = new HandlerEvaluateQuestion($this->process);
    // handler auditors
    $handlerAuditors = $this->handlerAuditors();
    if (!$handlerAuditors['success']) return $handlerAuditors;
    // create: aplicability registers
    $dataAplicabilityRegister = [
      'id_contract' => $this->corporate->contract->id_contract,
      'id_corporate' => $this->corporate->id_corporate,
      'id_audit_processes' => $this->process->id_audit_processes,
      'id_status' => AplicabilityRegister::NOT_CLASSIFIED_APLICABILITY,
    ];
    $this->aplicabilityRegister = AplicabilityRegister::create($dataAplicabilityRegister);
    // handler matter and aspects
    $handlerEvaluates = $this->handlerEvaluates();
    if ( !$handlerEvaluates['success'] ) return $handlerEvaluates;

    $info['success'] = true;
    $info['message'] = 'Registro creado exitosamente';
    $info['instance'] = $this->process->toArray();
    return $info;
  }

  /**
   * update new evaluation
   */
  public function updateProcess() 
  {
    if ( $this->method != 'update' ) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al verificar la acción';
      return $info;
    }
    // check init prject 
    $this->process = ProcessAudit::noLoadRelationships()->findOrFail($this->idAuditProcess);
    $this->handlerEvaluateQuestion = new HandlerEvaluateQuestion($this->process);
    // update: process
    $this->process->update($this->request);
    // handler auditors
    $handlerAuditors = $this->handlerAuditors($this->process, $this->request['auditors']);
    if (!$handlerAuditors['success']) return $handlerAuditors['message'];
    // update: aplicability registers
    $dataAplicabilityRegister = [
      'id_contract' => $this->corporate->contract->id_contract,
      'id_corporate' => $this->process->id_corporate,
      'id_audit_processes' => $this->process->id_audit_processes,
      'id_status' => AplicabilityRegister::NOT_CLASSIFIED_APLICABILITY,
    ];
    $this->aplicabilityRegister = AplicabilityRegister::findOrFail($this->process->aplicability_register->id_aplicability_register);
    $this->aplicabilityRegister->update($dataAplicabilityRegister);
    // handler matter and aspects
    $handlerEvaluates = $this->handlerEvaluates();
    if (!$handlerEvaluates['success']) return $handlerEvaluates;

    $info['success'] = true;
    $info['message'] = 'Registro actualizado exitosamente';
    $info['instance'] = $this->process->toArray();
    return $info;
  }

  /**
   * renewal new evaluation 
   */
  public function renewalProcess() 
  {
    if ( $this->method != 'renewal' ) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al verificar la acción';
      return $info;
    }
    $this->process = ProcessAudit::create($this->request);
    $this->idAuditProcess = $this->process->id_audit_processes;
    $this->handlerEvaluateQuestion = new HandlerEvaluateQuestion($this->process);
    // handler auditors
    $handlerAuditors = $this->handlerAuditors();
    if (!$handlerAuditors['success']) return $handlerAuditors;
    // create: aplicability registers
    $dataAplicabilityRegister = [
      'id_contract' => $this->corporate->contract->id_contract,
      'id_corporate' => $this->corporate->id_corporate,
      'id_audit_processes' => $this->process->id_audit_processes,
      'id_status' => AplicabilityRegister::NOT_CLASSIFIED_APLICABILITY,
    ];
    $this->aplicabilityRegister = AplicabilityRegister::create($dataAplicabilityRegister);
    // handler matter and aspects
    $handlerEvaluates = $this->handlerEvaluates();
    if ( !$handlerEvaluates['success'] ) return $handlerEvaluates;

    // handler matter and aspects
    $handlerDataRenewal = $this->handlerDataRenewal();
    if ( !$handlerDataRenewal['success'] ) return $handlerDataRenewal;

    $info['success'] = true;
    $info['message'] = 'Renovación creada exitosamente';
    $info['instance'] = $this->process->toArray();
    return $info;
  }

  /**
   * add or remove auditors
   */
  private function handlerAuditors()
  {
    try {
      $auditorsStructure = [];
      foreach ($this->request['auditors'] as $item) {
        $auditorsStructure[intval($item['id_user'])] = ['leader' => intval($item['leader'])];
      }
      $this->process->auditors()->sync($auditorsStructure);

      $info['success'] = true;
      $info['message'] = 'Auditores registrados exitosamente';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al gestionar a los auditores';
      return $info;
    }
  }

  /**
   * add or removes evaluate forms
   */
  private function handlerEvaluates()
  {
    try {
      if ($this->method == 'update') {
        $currentMatters = $this->process->aplicability_register->contract_matters->pluck('id_matter')->values();
        $contractAspects = $this->process->aplicability_register->contract_matters->flatMap(fn($item) => $item->contract_aspects);
        $currentAspects = $contractAspects->pluck('id_aspect')->values();
      }
      $forms = Form::with(['matter', 'aspect'])->whereIn('id', $this->request['forms'])->get();
      $matters = $forms->map(function($item) {
        return [
          'self_audit' => 0,
          'id_aplicability_register' => $this->aplicabilityRegister->id_aplicability_register,
          'id_contract' => $this->corporate->contract->id_contract,
          'id_matter' => $item->matter->id_matter,
          'id_audit_processes' => $this->idAuditProcess,
          'id_status' => AplicabilityRegister::NOT_CLASSIFIED_APLICABILITY,
        ];
      })->unique();

      $aspects = $forms->map(function($item) {
        return [
          'form_id' => $item->id,
          'self_audit' => 0,
          'id_contract_matter' => null,
          'id_contract' => $this->corporate->contract->id_contract,
          'id_matter' => $item->matter_id,
          'id_aspect' => $item->aspect_id,
          'id_audit_processes' => $this->idAuditProcess,
          'id_status' => AplicabilityRegister::NOT_CLASSIFIED_APLICABILITY,
          'id_state' => $this->address->id_state,
        ];
      });

      foreach ($matters as $matter) {
        $tmpMatter = ContractMatter::updateOrCreate($matter, $matter);
        $tmpAspects = $aspects->where('id_matter', $tmpMatter['id_matter']);
        foreach ($tmpAspects as $aspect) {
          $aspect['id_contract_matter'] = $tmpMatter->id_contract_matter;
          ContractAspect::updateOrCreate($aspect, $aspect);
        }
      }

      if ($this->method == 'update') {
        // aspects
        $newAspects = $aspects->pluck('id_aspect')->values()->toArray();
        $deleteAspects = $currentAspects->diff( $newAspects )->values()->toArray();
        // remove 
        $currentContractAspects = $contractAspects->pluck('id_contract_aspect')->values();
        $newContractAspects = $aspects->pluck('id_contract_aspect')->values()->toArray();
        $deleteContractAspects = $currentContractAspects->diff( $newContractAspects )->values()->toArray();
        
        $removeItems = $this->handlerEvaluateQuestion->removeAplicabilityAnswer($deleteContractAspects);
        if (!$removeItems['success']) return $removeItems;
        // remove contract aspects
        ContractAspect::where('id_audit_processes', $this->idAuditProcess)->whereIn('id_aspect', $deleteAspects)->delete();
        // remove contract matters
        $newMatters = $matters->pluck('id_matter')->values()->toArray();
        $deleteMatters = $currentMatters->diff( $newMatters )->values()->toArray();
        ContractMatter::where('id_audit_processes', $this->idAuditProcess)->whereIn('id_matter', $deleteMatters)->delete();
      }

      // handler evaluate question
      $evaluates = $this->handlerEvaluateQuestion->handlerQuestions();
      if (!$evaluates['success']) return $evaluates;

      $info['success'] = true;
      $info['message'] = 'Evaluaciones registradas exitosamente';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al gestionar los elementos a evaluar';
      return $info;
    }
  }

  /**
   * set kpi flag
   */
  private function handlerUseLikeKPI()
  {
    try {
      // verify aspects
      $formsRequest = Form::with(['matter', 'aspect'])->whereIn('id', $this->request['forms'])->get();;
      $formsInDB = Form::where('is_current', 1)->get();
      $aspectsDBIds = $formsInDB->pluck('aspect_id')->unique()->sort()->values()->toArray();
      $aspectsRequestIds = $formsRequest->pluck('aspect_id')->unique()->sort()->values()->toArray();
      // validations
      $isValidScope = $this->request['id_scope'] == Scope::CORPORATE;
      $isValidEvaluationType = $this->request['evaluation_type_id'] == EvaluationType::EVALUATE_BOTH;
      $isEvalluatingAllCurrentAspect = $aspectsDBIds === $aspectsRequestIds;

      $useKPI = $isValidScope && $isValidEvaluationType && $isEvalluatingAllCurrentAspect;
      $this->request = Arr::add($this->request, 'use_kpi', $useKPI);

      $info['success'] = true;
      $info['message'] = 'Verificación de KPI exitosa';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al verificar condiciones para indicador de KPI';
      return $info;
    }
  }

  /**
   * only evaluation renewals set data
   */
  private function handlerDataRenewal() 
  {
    try {
      $today = Carbon::now($this->timezone);
      $datetime = $today->toDatetimeString();
      
      $create = EvaluationRenewal::create([
        'keep_files' => $this->renewal['keep_files'],
        'keep_risk' => $this->renewal['keep_risk'],
        'date' => $datetime,
        'id_audit_processes' => $this->process['id_audit_processes'],
        'id_user' => Auth::id()
      ]);

      if ( is_null($create) ) {
        $info['success'] = false;
        $info['message'] = 'Algo salio mal al registrar los datos de renovación';
        return $info;
      }

      $info['success'] = true;
      $info['message'] = 'Datos de renovación registrados exitosamente';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal en el proceso de registrar los datos de renovación';
      return $info;
    }
  }
}