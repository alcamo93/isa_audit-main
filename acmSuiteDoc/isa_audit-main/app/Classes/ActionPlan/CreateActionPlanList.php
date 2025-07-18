<?php

namespace App\Classes\ActionPlan;

use App\Models\V2\Audit\ActionPlan;
use App\Models\V2\Audit\EvaluateRequirement;

class CreateActionPlanList
{
  public $aplicabilityRegister = null;
  public $recordForPlan = null;
  public $actionRegister = null;

  public function __construct($actionRegister, $aplicabilityRegister, $recordForPlan)
  {
    $this->actionRegister = $actionRegister;
    $this->aplicabilityRegister = $aplicabilityRegister;
    $this->recordForPlan = $recordForPlan;
  }

  public function createRecordOfObligations() 
  {
    try {
      $createRecords = $this->recordForPlan->map(function($item) {
        $noRequirement = !is_null($item->subrequirement) ? "{$item->requirement->no_requirement} - {$item->subrequirement->no_subrequirement}" : $item->requirement->no_requirement;
        $requirement = !is_null($item->subrequirement) ? $item->subrequirement->subrequirement : $item->requirement->requirement;
        $dataRiskAnswers = $item->risk_answers()->pluck('id_risk_answer')->toArray();
        $dataRiskTotal = $item->risk_totals()->pluck('id_risk_total')->toArray();
        $evidenceLoaded = $this->findEvaluateEvidenceLoaded($this->aplicabilityRegister->id_aplicability_register, $item->id_requirement, $item->id_subrequirement);
        return [
          'id_audit_processes' => $this->aplicabilityRegister->id_audit_processes,
          'id_aspect' => $item->requirement->id_aspect,
          'id_requirement' => $item->id_requirement,
          'id_subrequirement' => $item->id_subrequirement,
          'id_status' => ActionPlan::UNASSIGNED_AP,
          'total_tasks' => 1,
          'finding' => $item->finding,
          'id_priority' => 1,
          'task' => [
            'title' => $noRequirement, 
            'task' => $requirement, 
            'main_task' => 1,
            'evaluate_requirement' => $evidenceLoaded
          ],
          'risk_answers_ids' => $dataRiskAnswers,
          'risk_totals_ids' => $dataRiskTotal,
        ];
      });
      
      $subrequirements = $createRecords->whereNotNull('id_subrequirement')->groupBy('id_requirement');
      $parents = $subrequirements->map(function($item) {
        $parent = $item->first();
        $parent['id_subrequirement'] = null;
        $parent['total_tasks'] = $item->count();
        $parent['task'] = null;
        return $parent;
      })->values()->toArray();
  
      $merged = $createRecords->merge($parents);

      $toCreate = $merged->sortBy([ 
        ['id_requirement', 'asc'], 
        ['id_subrequirement', 'asc'] 
      ]);
      
      $records = collect([]);
      $toCreate->each(function($item) use ($records) {
        $action = $this->actionRegister->action_plans()->create($item);
        if ( !is_null($item['task']) ) {
          $taskCreate = $action->tasks()->create($item['task']);
          // relation with library
          if ( !is_null($taskCreate) && !is_null($item['task']['evaluate_requirement'])) {
            $taskCreate->evaluates()->attach($item['task']['evaluate_requirement']);
          }
        }
        if ( !is_null($item['risk_answers_ids']) && sizeof($item['risk_answers_ids']) ) {
          $action->risk_answers()->attach($item['risk_answers_ids']);
        }
        if ( !is_null($item['risk_totals_ids']) && sizeof($item['risk_totals_ids']) ) {
          $action->risk_totals()->attach($item['risk_totals_ids']);
        }
        $records->push($action);
      });

      $data['success'] = true;
      $data['message'] = 'Plan de Acción Creado';
      $data['records'] = $records;
      return $data;
    } catch (\Throwable $th) {
      $data['success'] = false;
      $data['message'] = 'Error al crear registros en Plan de Acción (PC)';
      return $data;
    }
  }

  public function createRecordOfAudits() 
  {
    try {
      $createRecords = $this->recordForPlan->map(function($item) {
        $noRequirement = !is_null($item->subrequirement) ? "{$item->requirement->no_requirement} - {$item->subrequirement->no_subrequirement}" : $item->requirement->no_requirement;
        $requirement = !is_null($item->subrequirement) ? $item->subrequirement->subrequirement : $item->requirement->requirement;
        $isParent = $item->requirement->has_subrequirement == 1 && is_null($item->id_subrequirement);
        $total = $isParent ? $this->recordForPlan->where('id_requirement', $item->id_requirement)->count() : 1;
        $dataTask = $isParent ? null : ['title' => $noRequirement, 'task' => $requirement, 'main_task' => 1];
        $dataRiskAnswers = $item->risk_answers()->pluck('id_risk_answer')->toArray();
        $dataRiskTotal = $item->risk_totals()->pluck('id_risk_total')->toArray();
        return [
          'id_audit_processes' => $this->aplicabilityRegister->id_audit_processes,
          'id_aspect' => $item->id_aspect,
          'id_requirement' => $item->id_requirement,
          'id_subrequirement' => $item->id_subrequirement,
          'id_status' => ActionPlan::UNASSIGNED_AP,
          'total_tasks' => $total,
          'finding' => $item->finding,
          'id_priority' => 1,
          'task' => $dataTask,
          'risk_answers_ids' => $dataRiskAnswers,
          'risk_totals_ids' => $dataRiskTotal,
        ];
      });

      $records = collect([]);
      $createRecords->each(function($item) use ($records) {
        $action = $this->actionRegister->action_plans()->create($item);
        if ( !is_null($item['task']) ) {
          $action->tasks()->create($item['task']);
        }
        if ( !is_null($item['risk_answers_ids']) && sizeof($item['risk_answers_ids']) ) {
          $action->risk_answers()->attach($item['risk_answers_ids']);
        }
        if ( !is_null($item['risk_totals_ids']) && sizeof($item['risk_totals_ids']) ) {
          $action->risk_totals()->attach($item['risk_totals_ids']);
        }
        $records->push($action);
      });

      $data['success'] = true;
      $data['message'] = 'Plan de Acción Creado';
      $data['records'] = $records;
      return $data;
    } catch (\Throwable $th) {
      $data['success'] = false;
      $data['message'] = 'Error al crear registros en Plan de Acción (AD)';
      return $data;
    }
  }

  public function findEvaluateEvidenceLoaded($idAplicabilityRegister, $idRequirement, $idSubrequirement) 
  {
    $evaluate = EvaluateRequirement::where('aplicability_register_id', $idAplicabilityRegister)
      ->where('id_requirement', $idRequirement)->where('id_subrequirement', $idSubrequirement)
      ->where('show_library', 1)->first();

    $hasLibrary = is_null($evaluate) || !is_null($evaluate->library);

    return $hasLibrary ? $evaluate->id_evaluate_requirement : null;
  }
}