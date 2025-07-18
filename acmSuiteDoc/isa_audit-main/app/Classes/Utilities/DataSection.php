<?php

namespace App\Classes\Utilities;

use App\Models\V2\Audit\Obligation;
use App\Models\V2\Audit\ActionPlan;
use App\Models\V2\Audit\ActionPlanRegister;
use App\Models\V2\Audit\AplicabilityRegister;
use App\Models\V2\Audit\AuditRegister;
use App\Models\V2\Audit\ObligationRegister;
use App\Models\V2\Audit\Task; 

class DataSection
{
  private $isEntityAllowed = false;
  private $idRecord;
  private $domain = '';
  private $path = '';

  public function __construct($entity, $id)
  {
    $this->domain = Config('enviroment.domain_frontend');
    $entitysAllowed = collect(['obligation', 'action_plan', 'task']);
    $this->isEntityAllowed = $entitysAllowed->contains($entity);
    $this->idRecord = $id;

    if ($entity == 'obligation') {
      $this->getObligationPath();
    }

    if ($entity == 'action_plan') {
      $this->getActionPlanPath();
    }

    if ($entity == 'task') {
      $this->getTaskPath();
    }
    
  }

  public function getSectionPath()
  {
    return "{$this->domain}{$this->path}";
  }

  private function getObligationPath() {
    if ( !$this->isEntityAllowed ) return '';

    $section = 'obligation';
    $obligation = Obligation::select('obligation_register_id')->find($this->idRecord);
    $originData = $this->getDataSectionRegister($section, $obligation['obligation_register_id']);
    $aplicabilityRegister = AplicabilityRegister::find($originData['id_aplicability_register']);

    $idAuditPorcess = $aplicabilityRegister['id_audit_processes'];
    $idAplicabilityRegister = $originData['id_aplicability_register'];
    $idSectionRegister = $originData['id_section_register'];

    $this->path = "v2/process/{$idAuditPorcess}/applicability/{$idAplicabilityRegister}/{$section}/{$idSectionRegister}/view";
  }

  private function getActionPlanPath() {
    if ( !$this->isEntityAllowed ) return '';

    $actionPlan = ActionPlan::select('id_action_register', 'id_audit_processes', 'id_action_register')->find($this->idRecord);
    $actionRegister =  ActionPlanRegister::without('action_plans')->find($actionPlan['id_action_register']);
    $originData = $this->getDataSectionRegister($actionRegister['origin_key'], $actionRegister['registerable_id']);

    $idAuditPorcess = $actionPlan['id_audit_processes'];
    $idAplicabilityRegister = $originData['id_aplicability_register'];
    $section = $actionRegister['origin_key'];
    $idSectionRegister = $originData['id_section_register'];
    $idActionRegister = $actionPlan['id_action_register'];

    $this->path = "v2/process/{$idAuditPorcess}/applicability/{$idAplicabilityRegister}/{$section}/{$idSectionRegister}/action/{$idActionRegister}/plan/view";
  }

  private function getTaskPath()
  {
    if ( !$this->isEntityAllowed ) return '';

    $relatioshipsTask = [ 'action' => fn($query) => $query->without('expired') ];
    $task = Task::with($relatioshipsTask)->without(['evaluates', 'auditors'])->find($this->idRecord);
    $actionRegister =  ActionPlanRegister::without('action_plans')->find($task['action']['id_action_register']);
    $originData = $this->getDataSectionRegister($actionRegister['origin_key'], $actionRegister['registerable_id']);

    $idAuditPorcess = $task['action']['id_audit_processes'];
    $idAplicabilityRegister = $originData['id_aplicability_register'];
    $section = $actionRegister['origin_key'];
    $idSectionRegister = $originData['id_section_register'];
    $idActionRegister = $task['action']['id_action_register'];
    $idActionPlan = $task['id_action_plan'];
    $this->path = "v2/process/{$idAuditPorcess}/applicability/{$idAplicabilityRegister}/{$section}/{$idSectionRegister}/action/{$idActionRegister}/plan/{$idActionPlan}/task/view";
  }

  private function getDataSectionRegister($origin, $idSectionOrigin)
  {
    if ($origin == 'audit') {
      $data = AuditRegister::select('id_audit_register', 'id_aplicability_register')->find($idSectionOrigin);
      $info['id_section_register'] = $data['id_audit_register'];
      $info['id_aplicability_register']= $data['id_aplicability_register'];
      return $info;
    }
    if ($origin == 'obligation') {
      $data = ObligationRegister::select('id', 'id_aplicability_register')->find($idSectionOrigin);
      $info['id_section_register'] = $data['id'];
      $info['id_aplicability_register']= $data['id_aplicability_register'];
      return $info;
    }
  }
}