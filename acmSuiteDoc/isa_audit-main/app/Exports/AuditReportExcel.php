<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\Audit\DashboardSheet;
use App\Exports\Sheets\Audit\AuditSheet;
use App\Exports\Sheets\Audit\FindingSheet;
use App\Exports\Sheets\Audit\ReportAuditSheet;
use App\Exports\Sheets\Audit\ReportFindingSheet;

class AuditReportExcel implements WithMultipleSheets
{
	use Exportable;

	public $data = null;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function sheets(): array
	{
		$sheets = [];

		$sheets[] = new DashboardSheet($this->data['dashboard']);
		$sheets[] = new AuditSheet($this->data['dashboard']['matters']);
		$sheets[] = new FindingSheet($this->data['dashboard']['matters']);
		$sheets[] = new ReportAuditSheet($this->data['dashboard']['matters'], $this->data['dashboard']['evaluate_risk']);
		$sheets[] = new ReportFindingSheet($this->data['dashboard']['matters'], $this->data['dashboard']['evaluate_risk']);

		return $sheets;
	}
}
