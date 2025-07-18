<?php

namespace App\Exports\Dashboard\ComplianceGlobal\Sheets;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use App\Exports\Template\Style;
use App\Exports\Template\Variable;

class SheetFive implements FromView, WithTitle, WithDrawings, WithStyles, WithColumnWidths
{
	use Exportable;

	private $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function view(): View
	{
		return view('exports.dashboard.complianceGlobal.sheets.sheetFive', [
			'data' => $this->data
		]);
	}

	/**
	 * @return string
	 */
	public function title(): string
	{
		return $this->data['title_sheet'];
	}

	public function drawings()
	{
    $draws = [];

    $logo = new Drawing();
		$logo->setName(Variable::IMAGE_LOGO_NAME);
		$logo->setDescription(Variable::IMAGE_LOGO_DESCRIPTION);
		$logo->setPath( public_path(Variable::IMAGE_PATH) );
		$logo->setHeight(Variable::IMAGE_SIZE_LOGO);
		$logo->setCoordinates('G1');
    $logo->setOffsetY(15);
		$logo->setOffsetX(15);
    array_push($draws, $logo);

		return $draws;
	}

	/**
	 * @return array
	 */
	public function columnWidths(): array
	{
		$range = range('B', 'P');
		$columns = collect($range)->map(fn($item) => [$item => 10])->collapse()->toArray();
		$columns['B'] = 30;
		return $columns;
	}

	public function styles(Worksheet $sheet)
	{
		// Show/hide gridlines when printing
		$sheet->setShowGridlines(false);
    // header
		$sheet->getRowDimension(1)->setRowHeight(60);
    $sheet->getRowDimension(2)->setRowHeight(45);
		$sheet->getRowDimension(3)->setRowHeight(45);
		$sheet->getStyle('B2:P3')->applyFromArray(Style::STYLE_TITLE_DOCUMENT)->applyFromArray(Style::STYLE_BORDER_MAIN);
		// header table
		$sheet->getStyle('B5:P5')->applyFromArray(Style::STYLE_HEADER_TABLE);
		// historical
		$rowInit = 6;
		$historical = $this->data['historical'];
		$rowLast = ( $rowInit + sizeof($historical) ) - 1;
		for ($i=0; $i < sizeof($historical); $i++) { 
			$row = $i + $rowInit;
			$sheet->getStyle("B{$row}:P{$row}")->applyFromArray(Style::STYLE_HEADER_TABLE_NO_BORDER);
			if ($i === array_key_last($historical)) {
				$sheet->getStyle("B{$row}:P{$row}")->applyFromArray(Style::STYLE_HEADER_TABLE_NO_BORDER_FOTTER);
			}
		}
		$sheet->getStyle("B{$rowInit}:P{$rowLast}")->applyFromArray(Style::STYLE_BORDER_MAIN);
	}
}
