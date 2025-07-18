<?php

namespace App\Http\Controllers\V2\Dashboard;

use App\Http\Controllers\Controller;
use App\Traits\V2\ResponseApiTrait;
use App\Classes\Dashboard\BuildDataReportGlobalCompliance;
use App\Classes\Dashboard\BuildDataReportGlobalAudit;
use App\Classes\Dashboard\BuildDataReportGlobalObligation;
use App\Classes\Dashboard\BuildDataReportCorporateObligation;
use App\Classes\Dashboard\BuildDataReportCorporateAudit;
use App\Classes\Dashboard\BuildDataReportCorporateCompliance;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Dashboard\ComplianceGlobal\ComplianceGlobalExcel;
use App\Exports\Dashboard\AuditGlobal\AuditGlobalExcel;
use App\Exports\Dashboard\ObligationGlobal\ObligationGlobalExcel;
use App\Exports\Dashboard\ObligationCorporate\ObligationCorporateExcel;
use App\Exports\Dashboard\AuditCorporate\AuditCorporateExcel;
use App\Exports\Dashboard\ComplianceCorporate\ComplianceCorporateExcel;

class ReportDashboardController extends Controller
{
  use ResponseApiTrait;

  public function getReportObligationCustomer($idCustomer) 
  {
    try {
      ini_set('max_execution_time', 18000);
      ini_set('memory_limit', '-1');
      
      $report = new BuildDataReportGlobalObligation($idCustomer);
      $data = $report->getDataReport();
      
      if (!$data['success']) {
        return $this->errorResponse($data['message']);
      }
      
      $documentName = "Reporte Global de Permisos Críticos - {$report->customerName}.xlsx";
      return Excel::download(new ObligationGlobalExcel($data), $documentName);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function getReportAuditCustomer($idCustomer)
  {
    try {
      ini_set('max_execution_time', 18000);
      ini_set('memory_limit', '-1');
      
      $report = new BuildDataReportGlobalAudit($idCustomer);
      $data = $report->getDataReport();
      
      if (!$data['success']) {
        return $this->errorResponse($data['message']);
      }

      $documentName = "Reporte Global de Auditoria - {$report->customerName}.xlsx";
      return Excel::download(new AuditGlobalExcel($data), $documentName);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function getReportComplianceCustomer($idCustomer)
  {
    try {
      ini_set('max_execution_time', 18000);
      ini_set('memory_limit', '-1');
      
      $report = new BuildDataReportGlobalCompliance($idCustomer);
      $data = $report->getDataReport();
      
      if (!$data['success']) {
        return $this->errorResponse($data['message']);
      }

      $documentName = "Reporte Global de Cumplimiento EHS - {$report->customerName}.xlsx";
      return Excel::download(new ComplianceGlobalExcel($data), $documentName);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function getReportObligationCorporate($idAuditProcess, $idApplicabilityRegister, $idObligationRegister)
  {
    try {
      ini_set('max_execution_time', 18000);
      ini_set('memory_limit', '-1');
      
      $report = new BuildDataReportCorporateObligation($idAuditProcess, $idApplicabilityRegister);
      $data = $report->getDataReport();
      
      if (!$data['success']) {
        return $this->errorResponse($data['message']);
      }

      $documentName = "Reporte Planta de Permisos Críticos - {$report->corporateName}.xlsx";
      return Excel::download(new ObligationCorporateExcel($data), $documentName);
    } catch (\Throwable $th) {
      throw $th;
    }
  }
  public function getReportAuditCorporate($idAuditProcess, $idApplicabilityRegister, $idAuditRegister)
  {
    try {
      ini_set('max_execution_time', 18000);
      ini_set('memory_limit', '-1');
      
      $report = new BuildDataReportCorporateAudit($idAuditProcess, $idApplicabilityRegister, $idAuditRegister);
      $data = $report->getDataReport();
      
      if (!$data['success']) {
        return $this->errorResponse($data['message']);
      }

      $documentName = "Reporte Planta de Auditoría - {$report->corporateName}.xlsx";
      return Excel::download(new AuditCorporateExcel($data), $documentName);
    } catch (\Throwable $th) {
      throw $th;
    }
  }
  public function getReportComplianceCorporate($idAuditProcess, $idApplicabilityRegister, $idAuditRegister)
  {
    try {
      ini_set('max_execution_time', 18000);
      ini_set('memory_limit', '-1');
      
      $report = new BuildDataReportCorporateCompliance($idAuditProcess, $idApplicabilityRegister, $idAuditRegister);
      $data = $report->getDataReport();
      
      if (!$data['success']) {
        return $this->errorResponse($data['message']);
      }

      $documentName = "Reporte Planta de Cumplimiento EHS - {$report->corporateName}.xlsx";
      return Excel::download(new ComplianceCorporateExcel($data), $documentName);
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}
