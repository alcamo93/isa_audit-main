<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\DashboardSheet;
use App\Exports\Sheets\AuditSheet;
use App\Exports\Sheets\ActionPlanSheet;
use App\Exports\Sheets\ReportSheet;

class AuditReportExcelOld implements WithMultipleSheets
{
    use Exportable; 

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function sheets(): array {
        $sheets = [];
        
        $sheets[] = new DashboardSheet($this->data['dashboard']);
        $sheets[] = new AuditSheet($this->data['audit']);
        $sheets[] = new ActionPlanSheet($this->data['action']);
        $sheets[] = new ReportSheet($this->data['action'], $this->data['allowRisk']);

        return $sheets;
    }
}