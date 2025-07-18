<?php

namespace App\Classes\Utilities;

use App\Models\V2\Audit\ActionPlan;
use App\Models\V2\Audit\AplicabilityRegister;
use App\Models\V2\Audit\AuditRegister;
use App\Models\V2\Audit\Obligation;
use App\Models\V2\Catalogs\Matter;
use App\Models\V2\Catalogs\Requirement;

class MattersSection 
{
  private $section = null;
  private $idSectionRegister = null;

  public function __construct($section, $idSectionRegister)
  {
    $this->section = $section;
    $this->idSectionRegister = $idSectionRegister;
  }

  public function getMatters()
  {
    if ($this->section == 'applicability') {
      return $this->getMatterApplicability();
    }
    if ($this->section == 'obligation') {
      return $this->getMatterObligation();
    }
    if ($this->section == 'audit') {
      return $this->getMatterAudit();
    }
    if ($this->section == 'action') {
      return $this->getMatterActionPlan();
    }
    return [];
  }

  private function getMatterApplicability()
  {
    $relationships = ['contract_matters.matter','contract_matters.contract_aspects.aspect'];
    $applicabilityRegister = AplicabilityRegister::with($relationships)->findOrFail($this->idSectionRegister);

    $filterMatter = $applicabilityRegister->contract_matters->map(function($matter) {
      $matterTmp = $matter->matter;
      $matterTmp['id_contract_matter'] = $matter->id_contract_matter;
      $matterTmp['aspects'] = $matter->contract_aspects->map(function($aspect) {
        $aspectTmp = $aspect->aspect;
        $aspectTmp['id_contract_aspect'] = $aspect->id_contract_aspect;
        return $aspectTmp;
      })->toArray();
      return $matterTmp;
    })->toArray();
    
    return $filterMatter;
  }

  private function getMatterObligation()
  {
    $requirementIds = Obligation::where('obligation_register_id', $this->idSectionRegister)->distinct()->pluck('id_requirement');
    $queryRequirement = Requirement::whereIn('id_requirement', $requirementIds)->distinct();

    $matterIds = $queryRequirement->pluck('id_matter')->toArray();
    $aspectIds = $queryRequirement->pluck('id_aspect')->toArray();
    
    $filterAspects = fn($query) => $query->whereIn('id_aspect', $aspectIds);
    $relationships = ['aspects' => $filterAspects];
    $filterMatter = Matter::with($relationships)->whereIn('id_matter', $matterIds)->get();

    return $filterMatter;
  }

  private function getMatterAudit()
  {
    $relationships = ['audit_matters.matter','audit_matters.audit_aspects.aspect'];
    $auditRegister = AuditRegister::with($relationships)->findOrFail($this->idSectionRegister);

    $filterMatter = $auditRegister->audit_matters->map(function($matter) {
      $matterTmp = $matter->matter;
      $matterTmp['id_audit_matter'] = $matter->id_audit_matter;
      $matterTmp['aspects'] = $matter->audit_aspects->map(function($aspect) {
        $aspectTmp = $aspect->aspect;
        $aspectTmp['id_audit_aspect'] = $aspect->id_audit_aspect;
        return $aspectTmp;
      })->toArray();
      return $matterTmp;
    })->toArray();
    
    return $filterMatter;
  }

  private function getMatterActionPlan()
  {
    $requirementIds = ActionPlan::where('id_action_register', $this->idSectionRegister)->distinct()->pluck('id_requirement');
    $queryRequirement = Requirement::whereIn('id_requirement', $requirementIds)->distinct();

    $matterIds = $queryRequirement->pluck('id_matter')->toArray();
    $aspectIds = $queryRequirement->pluck('id_aspect')->toArray();
    
    $filterAspects = fn($query) => $query->whereIn('id_aspect', $aspectIds);
    $relationships = ['aspects' => $filterAspects];
    $filterMatter = Matter::with($relationships)->whereIn('id_matter', $matterIds)->get();

    return $filterMatter;
  }

}
