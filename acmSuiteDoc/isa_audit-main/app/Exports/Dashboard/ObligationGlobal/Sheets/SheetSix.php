<?php

namespace App\Exports\Dashboard\ObligationGlobal\Sheets;

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

class SheetSix implements FromView, WithTitle, WithDrawings, WithStyles, WithColumnWidths
{
	use Exportable;

	private $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function view(): View
	{
		return view('exports.dashboard.obligationGlobal.sheets.sheetSix', [
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
		$columns = collect($range)->map(fn($item) => [$item => 15])->collapse()->toArray();
		$columns['B'] = 30;
		return $columns;
	}

	public function styles(Worksheet $sheet)
	{
		// Show/hide gridlines when printing
		$sheet->setShowGridlines(false);
    // header
		$sheet->getRowDimension(1)->setRowHeight(60);
    $sheet->getRowDimension(2)->setRowHeight(70);
		$sheet->getRowDimension(3)->setRowHeight(45);
		$sheet->getStyle('B2:P3')->applyFromArray(Style::STYLE_TITLE_DOCUMENT)->applyFromArray(Style::STYLE_BORDER_MAIN);
		// header table
		$row = 5;
		$sheet->getStyle("B{$row}:P{$row}")->applyFromArray(Style::STYLE_HEADER_TABLE);
		$initRow = $row + 1;
		for ($i=0; $i < sizeof($this->data['historical']); $i++) { 
			++$row;
			$sheet->getStyle("B{$row}:P{$row}")->applyFromArray(Style::STYLE_HEADER_TABLE_NO_BORDER);
		}
		$lastRow = $row;
		$sheet->getStyle("B{$initRow}:P{$lastRow}")->applyFromArray(Style::STYLE_BORDER_MAIN);
		$sheet->getStyle("B{$lastRow}:P{$lastRow}")->applyFromArray(Style::STYLE_HEADER_TABLE_NO_BORDER_FOTTER);
	}
}
