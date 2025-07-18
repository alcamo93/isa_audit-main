<?php

namespace App\Exports\Dashboard\AuditGlobal;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Dashboard\AuditGlobal\Sheets\SheetOne;
use App\Exports\Dashboard\AuditGlobal\Sheets\SheetTwo;
use App\Exports\Dashboard\AuditGlobal\Sheets\SheetThree;
use App\Exports\Dashboard\AuditGlobal\Sheets\SheetFour;
use App\Exports\Dashboard\AuditGlobal\Sheets\SheetFive;
use App\Exports\Dashboard\AuditGlobal\Sheets\SheetSix;

class AuditGlobalExcel implements WithMultipleSheets
{
	use Exportable;

	protected $sheetOne = null;
	protected $sheetTwo = null;
	protected $sheetThree = null;
	protected $sheetFour = null;
	protected $sheetFive = null;
	protected $sheetSix = null;
	protected $sheetSeven = null;

	public function __construct(array $data)
	{
		$this->sheetOne = $data['sheet_one'];
		$this->sheetTwo = $data['sheet_two'];
		$this->sheetThree = $data['sheet_three'];
		$this->sheetFour = $data['sheet_four'];
		$this->sheetFive = $data['sheet_five'];
		$this->sheetSix = $data['sheet_six'];
	}

	public function sheets(): array
	{
		$sheets = [];
    
		$sheets[] = new SheetOne($this->sheetOne);
		$sheets[] = new SheetTwo($this->sheetTwo);
		$sheets[] = new SheetThree($this->sheetThree);
		$sheets[] = new SheetFour($this->sheetFour);
		$sheets[] = new SheetFive($this->sheetFive);
		$sheets[] = new SheetSix($this->sheetSix);

		return $sheets;
	}
}
