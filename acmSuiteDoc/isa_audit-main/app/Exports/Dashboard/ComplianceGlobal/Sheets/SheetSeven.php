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

class SheetSeven implements FromView, WithTitle, WithDrawings, WithStyles, WithColumnWidths
{
	use Exportable;

	private $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function view(): View
	{
		return view('exports.dashboard.complianceGlobal.sheets.sheetSeven', [
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

		$matters = $this->data['matters'];
		$row = 5;
		foreach ($matters as $matter) {
			// $imageMatter = @imagecreatefromstring(file_get_contents($matter['full_path']));
			$imageMatter = public_path($matter['image']);
			if ( $imageMatter && !is_null($imageMatter) ) {
				$matterDraw = new Drawing();
				$matterDraw->setName($matter['matter']);
				$matterDraw->setDescription($matter['matter']);
				$matterDraw->setPath($imageMatter);
				$matterDraw->setHeight(Variable::IMAGE_SIZE_LOGO_ICON);
				$matterDraw->setCoordinates("B{$row}");
				$matterDraw->setOffsetY(10);
				$matterDraw->setOffsetX(20);
				array_push($draws, $matterDraw);
			}
			++$row;
			$row = $row + sizeof($matter['historical']) + 2;
		}

		return $draws;
	}

	/**
	 * @return array
	 */
	public function columnWidths(): array
	{
		return [
			'B' => 30,
		];
	}

	public function styles(Worksheet $sheet)
	{
		// Show/hide gridlines when printing
		$sheet->setShowGridlines(false);
    // header
		$sheet->getRowDimension(1)->setRowHeight(60);
    $sheet->getRowDimension(2)->setRowHeight(45);
		$sheet->getRowDimension(3)->setRowHeight(45);
		$sheet->getStyle('B2:O3')->applyFromArray(Style::STYLE_TITLE_DOCUMENT)->applyFromArray(Style::STYLE_BORDER_MAIN);
		// header table
		$row = 5;
		$matters = $this->data['matters'];
		for ($i=0; $i < sizeof($matters); $i++) { 
			$sheet->getRowDimension($row)->setRowHeight(35);
			$sheet->getStyle("B{$row}:M{$row}")->applyFromArray(Style::STYLE_HEADER_TABLE_NO_BORDER);
			$sheet->getStyle("N{$row}:O{$row}")->applyFromArray(Style::STYLE_HEADER_CARD_ERROR)->getAlignment()->setWrapText(true);
			++$row;
			$sheet->getStyle("B{$row}:O{$row}")->applyFromArray(Style::STYLE_HEADER_TABLE);
			$initRow = $row + 1;
			for ($j=0; $j < sizeof($matters[$i]['historical']); $j++) { 
				++$row;
				$sheet->getStyle("B{$row}:O{$row}")->applyFromArray(Style::STYLE_HEADER_TABLE_NO_BORDER);
			}
			$lastRow = $row;
			$sheet->getStyle("B{$initRow}:O{$lastRow}")->applyFromArray(Style::STYLE_BORDER_MAIN);
			$sheet->getStyle("B{$lastRow}:O{$lastRow}")->applyFromArray(Style::STYLE_HEADER_TABLE_NO_BORDER_FOTTER);
			++$row;
			++$row;
		}
	}
}
