<?php

namespace App\Classes\Process\SpecificRequirements;

use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Audit\Audit;
use App\Models\V2\Catalogs\EvaluationType;
use App\Models\V2\Audit\EvaluateRequirement;
use App\Models\V2\Audit\EvaluateAuditRequirement;
use Carbon\Carbon;

class SpecificRequirementEvaluate
{
  private $type = null;
  private $model = null;
  private $skipProcess = [];
  private $request = null;

  public function __construct($type, $model, $request = null) 
  {
    $this->type = $type;
    $this->model = $model;
    $this->request = $request;
  }

  private function requirementUsed()
  {
    try {  
      if ($this->type == 'store') {
        $data['success'] = true;
        $data['message'] = 'No es necesario validar de pendendecnias';
        return $data;
      }

      $this->usedInSomeProcess();

      if ($this->type == 'update') {
        $sameCustomer = $this->model->id_customer == $this->request['id_customer'];
        $sameCorporate = $this->model->id_corporate == $this->request['id_corporate'];
        $sameMatter = $this->model->id_matter == $this->request['id_matter'];
        $sameAspect = $this->model->id_aspect == $this->request['id_aspect'];
        
        $canUpdateData = $sameCustomer && $sameCorporate && $sameMatter && $sameAspect;
        
        $allowUpdateAndAdd = sizeof($this->skipProcess) == 0 && $canUpdateData;

        $data['success'] = $allowUpdateAndAdd;
        $data['message'] = $allowUpdateAndAdd ? 'El requermiento puede ser editado' : 'No se puede cambiar de planta o aspecto porque ya esta en uso';
        return $data;
      }
      
      if ($this->type == 'destroy') {
        $allowDestroy = sizeof($this->skipProcess) == 0;
        $data['success'] = $allowDestroy;
        $data['message'] = $allowDestroy ? 'El requermiento puede ser eliminado' : 'No se puede eliminar porque ya esta en uso';
        return $data;
      }
    } catch (\Throwable $th) {
      $data['success'] = false;
      $data['message'] = 'Este método solo se utiliza para requerimientos que son creados por primera vez';
      return $data;
    }
  }

  public function setSpecificInProgressAudit() 
  {
    try {
      $validate = $this->requirementUsed();
      if ( !$validate['success'] ) {
        return $validate;
      }
      
      if ($this->type == 'store' || $this->type == 'update') {
        return $this->addRequirements();
      }

      if ($this->type == 'destroy') {
        $data['success'] = true;
        $data['message'] = 'Puede ser eliminado el requerimiento';
        return $data;
      }

      $data['success'] = false;
      $data['message'] = 'No se encontro acción valida';
      return $data;
    } catch (\Throwable $th) {
      $data['success'] = false;
      $data['message'] = 'Algo salio mal al tratar de seguir el proceso de agregar a ejercicios';
      return $data;
    }
  }

  private function addRequirements()
  {
    try {

      $allProcess = $this->findProcess();
      if ( !$allProcess['success'] || $allProcess['is_empty'] ) {
        return $allProcess;
      }

      $process = $allProcess['allProcess'];

      $audits = $process->map(fn($project) => $project->aplicability_register->audit_register)->collapse();
      $aspectsAuditIds = $audits->map(function($item) {
        return $item->audit_matters->pluck('audit_aspects')->collapse()->map(function($aspect) use ($item) {
          return [
            'aplicability_register_id' => $item->id_aplicability_register, 
            'id_audit_aspect' => $aspect->id_audit_aspect
          ];
        })->collapse();
      });
      
      $aplicabilities = $process->pluck('aplicability_register');
      $aspectsAplicabilityIds = $aplicabilities->map(function($item) {
        return $item->contract_matters->pluck('contract_aspects')->collapse()->map(function($aspect) use ($item) {
          return [
            'aplicability_register_id' => $item->id_aplicability_register, 
            'id_contract_aspect' => $aspect->id_contract_aspect
          ];
        })->collapse();
      });
      
      $recordToCreate = $aspectsAuditIds->map(function($item) use ($aspectsAplicabilityIds) {
        $findContractAspect = $aspectsAplicabilityIds->firstWhere('aplicability_register_id', $item['aplicability_register_id']);
        return [
          'complete' => 0,
          'id_contract_aspect' => $findContractAspect['id_contract_aspect'],
          'id_audit_aspect' => $item['id_audit_aspect'],
          'id_requirement' => $this->model->id_requirement,
          'id_subrequirement' => null,
          'aplicability_register_id' => $item['aplicability_register_id'],
        ];
      });
      
      $recordToCreate->each(function($record) {
        EvaluateRequirement::create($record);
        EvaluateAuditRequirement::create($record);
      });

      $data['success'] = true;
      $data['message'] = 'Requerimientos agregados correctamente';
      return $data;
    } catch (\Throwable $th) {
      $data['success'] = false;
      $data['message'] = 'Algo salio mal al agregar requerimientos';
      return $data;
    }
  }

  private function usedInSomeProcess() 
  {
    try {
      $usedInApplicabilityFile = EvaluateRequirement::where('id_requirement', $this->model->id_requirement)->get();
      $usedInAudit = EvaluateAuditRequirement::where('id_requirement', $this->model->id_requirement)->get();

      $aplicabilityRegisterIds = $usedInApplicabilityFile->pluck('aplicability_register_id')->toArray();
      $auditAspectIds = $usedInAudit->pluck('id_audit_aspect')->toArray();

      $processInUsed = ProcessAudit::whereHas('aplicability_register', function($query) use ($aplicabilityRegisterIds) {
        $query->whereIn('id_aplicability_register', $aplicabilityRegisterIds);
      })->orWhereHas('aplicability_register.audit_register.audit_matters.audit_aspects', function($query) use ($auditAspectIds) {
        $query->whereIn('id_audit_aspect', $auditAspectIds);
      })
      ->get();

      $this->skipProcess = $processInUsed->pluck('id_audit_processes')->toArray();

      $data['success'] = true;
      $data['message'] = 'Verificación exitosa';
      return $data;
    } catch (\Throwable $th) {
      $data['success'] = false;
      $data['message'] = 'Algo salio mal al verificar si el requerimiento esta en uso en algun Ejercicio';
      return $data;
    }
  }
  private function findProcess()
  {
    try {
      $timezone = Config('enviroment.time_zone_carbon');
			$currentDate = Carbon::now($timezone)->toDateString();
			$filterByDate = fn($subquery) => $subquery->whereDate('init_date', '<=', $currentDate)->whereDate('end_date', '>=', $currentDate);
      $filterAudits = fn($query) => $query->whereIn('id_status', [Audit::NOT_AUDITED_AUDIT, Audit::AUDITING_AUDIT]);
      $filterAspect = fn($query) => $query->where('id_aspect', $this->model->id_aspect);
      
      $relationships = [
        'aplicability_register.audit_register' => $filterAudits,
        'aplicability_register.audit_register' => $filterByDate,
        'aplicability_register.audit_register.audit_matters.audit_aspects' => $filterAspect,
        'aplicability_register.contract_matters.contract_aspects' => $filterAspect
      ];

      $queryProcess = ProcessAudit::with($relationships)->where('id_corporate', $this->model->id_corporate)
        ->where('evaluate_especific', ProcessAudit::YES_EVALUATE_SPECIFIC)
        ->whereDate('date', '<=', $currentDate)->whereDate('end_date', '>=', $currentDate)
        ->whereIn('evaluation_type_id', [EvaluationType::EVALUATE_AUDIT, EvaluationType::EVALUATE_BOTH])
        ->whereHas('aplicability_register.audit_register', $filterAudits)
        ->whereHas('aplicability_register.audit_register', $filterByDate)
        ->whereHas('aplicability_register.audit_register.audit_matters.audit_aspects', $filterAspect)
        ->whereHas('aplicability_register.contract_matters.contract_aspects', $filterAspect);
      
      if ($this->type == 'update') {
        $queryProcess->whereNotIn('id_audit_processes', $this->skipProcess);
      }
      
      $process = $queryProcess->get();

      $data['success'] = $process->isEmpty() ? false : true;
      $data['message'] = $process->isEmpty() ? 'No hay ejercicios a los cuales agregar el requremiento' : 'Ejercicios encontrados';
      $data['is_empty'] = $process->isEmpty();
      $data['allProcess'] = $process;
      return $data;
    } catch (\Throwable $th) {
      $data['success'] = false;
      $data['message'] = 'Algo salio mal al consultar ejercicios';
      return $data;
    }
  }
}