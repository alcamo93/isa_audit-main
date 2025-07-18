<?php

namespace App\Exports\Dashboard\ComplianceCorporate;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Dashboard\ComplianceCorporate\Sheets\SheetOne;
use App\Exports\Dashboard\ComplianceCorporate\Sheets\SheetTwo;
use App\Exports\Dashboard\ComplianceCorporate\Sheets\SheetThree;
use App\Exports\Dashboard\ComplianceCorporate\Sheets\SheetFour;
use App\Exports\Dashboard\ComplianceCorporate\Sheets\SheetFive;
use App\Exports\Dashboard\ComplianceCorporate\Sheets\SheetSix;
use App\Exports\Dashboard\ComplianceCorporate\Sheets\SheetSeven;
use App\Exports\Dashboard\ComplianceCorporate\Sheets\SheetEight;
use App\Exports\Dashboard\ComplianceCorporate\Sheets\SheetNine;
use App\Exports\Dashboard\ComplianceCorporate\Sheets\SheetTen;
use App\Exports\Dashboard\ComplianceCorporate\Sheets\SheetEleven;

class ComplianceCorporateExcel implements WithMultipleSheets
{
	use Exportable;

	protected $sheetOne = null;
	protected $sheetTwo = null;
	protected $sheetThree = null;
	protected $sheetFour = null;
	protected $sheetFive = null;
	protected $sheetSix = null;
	protected $sheetSeven = null;
	protected $sheetEight = null;
	protected $sheetNine = null;
	protected $sheetTen = null;
	protected $sheetEleven = null;

	public function __construct(array $data)
	{
		$this->sheetOne = $data['sheet_one'];
		$this->sheetTwo = $data['sheet_two'];
		$this->sheetThree = $data['sheet_three'];
		$this->sheetFour = $data['sheet_four'];
		$this->sheetFive = $data['sheet_five'];
		$this->sheetSix = $data['sheet_six'];
		$this->sheetSeven = $data['sheet_seven'];
		$this->sheetEight = $data['sheet_eight'];
		$this->sheetNine = $data['sheet_nine'];
		$this->sheetTen = $data['sheet_ten'];
		$this->sheetEleven = $data['sheet_eleven'];
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
		$sheets[] = new SheetSeven($this->sheetSeven);
		$sheets[] = new SheetEight($this->sheetEight);
		$sheets[] = new SheetNine($this->sheetNine);
		$sheets[] = new SheetTen($this->sheetTen);
		$sheets[] = new SheetEleven($this->sheetEleven);

		return $sheets;
	}
}
