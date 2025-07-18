<?php

namespace App\Exports\Dashboard\ObligationCorporate\Sheets;

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

class SheetThree implements FromView, WithTitle, WithDrawings, WithStyles, WithColumnWidths
{
	use Exportable;

	private $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function view(): View
	{
		return view('exports.dashboard.obligationCorporate.sheets.sheetThree', [
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
		$logo->setCoordinates('G5');
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
		$range = range('B', 'M');
		$columns = collect($range)->map(fn($item) => [$item => 10])->collapse()->toArray();
		return $columns;
	}


	public function styles(Worksheet $sheet)
	{
		// Show/hide gridlines when printing
		$sheet->setShowGridlines(false);
    // header
    $sheet->getRowDimension(2)->setRowHeight(70);
		$sheet->getRowDimension(3)->setRowHeight(45);
		$sheet->getRowDimension(4)->setRowHeight(45);
		$sheet->getStyle('B2:M4')->applyFromArray(Style::STYLE_TITLE_DOCUMENT)->applyFromArray(Style::STYLE_BORDER_MAIN);
    // logo
		$sheet->getRowDimension(5)->setRowHeight(60);
		// matters
		$row = 6;
		$matters = $this->data['matters'];
		for ($idxMatter=0; $idxMatter < sizeof($matters); $idxMatter++) { 
			$sheet->getStyle("B{$row}:M{$row}")->applyFromArray(Style::STYLE_TITLE_PLAIN);
			++$row;
			$sheet->getStyle("B{$row}:M{$row}")->applyFromArray(Style::STYLE_HEADER_TABLE);
			++$row;
			$aspects = $matters[$idxMatter]['aspects'];
			$firstRowAspect = $row;
			for ($idxAspect=0; $idxAspect < sizeof($aspects); $idxAspect++) { 
				$sheet->getStyle("B{$row}:M{$row}")->applyFromArray(Style::STYLE_HEADER_TABLE_NO_BORDER);
				++$row;
			}
			$lastRowAspect = $row;
			$sheet->getStyle("B{$firstRowAspect}:M{$lastRowAspect}")->applyFromArray(Style::STYLE_BORDER_MAIN);
			$sheet->getStyle("B{$row}:M{$row}")->applyFromArray(Style::STYLE_HEADER_TABLE_NO_BORDER_FOTTER);
			++$row;
			++$row;
		}
	}
}
