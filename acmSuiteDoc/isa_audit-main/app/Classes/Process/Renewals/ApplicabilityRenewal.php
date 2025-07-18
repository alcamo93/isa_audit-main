<?php 

namespace App\Classes\Process\Renewals;

use App\Models\V2\Audit\ProcessAudit;

class ApplicabilityRenewal 
{
  protected $renewalProcess = null;
  protected $originalProcess = null;

  /**
   * requires the evaluation id created as a renewal
   * 
   * @param int $idAuditProcess
   */
  public function __construct($idAuditProcess)
  {
    $this->renewalProcess = ProcessAudit::with(['renewal'])->findOrFail($idAuditProcess);
    $this->originalProcess = ProcessAudit::findOrFail($this->renewalProcess['renewal']['id_audit_processes']);
    $this->getPreviousData();
  }

  private function getPreviousData()
  {
    dd('previous', $this->renewalProcess->toArray(), $this->originalProcess->toArray());
  }
}