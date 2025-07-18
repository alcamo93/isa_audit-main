<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\Obligation\DashboardSheet;
use App\Exports\Sheets\Obligation\ComplianceSheet;
use App\Exports\Sheets\Obligation\ObligationsSheet;
use App\Exports\Sheets\Obligation\ReportSheet;

class ObligationReportExcel implements WithMultipleSheets
{
	use Exportable; 

	public $data;

	public function __construct(array $data) 
	{
		$this->data = $data;
	}

	public function sheets(): array 
	{
		$sheets = [];
		$sheets[] = new DashboardSheet($this->data['dashboard'], $this->data['percentages']);
		$sheets[] = new ComplianceSheet($this->data['percentages']);
		$sheets[] = new ObligationsSheet($this->data['percentages']);
		$sheets[] = new ReportSheet($this->data['report']);

		return $sheets;
}
}
