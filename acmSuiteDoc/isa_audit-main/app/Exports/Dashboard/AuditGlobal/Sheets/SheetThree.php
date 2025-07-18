<?php

namespace App\Exports\Dashboard\AuditGlobal\Sheets;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use App\Exports\Template\Style;
use App\Exports\Template\Variable;

class SheetThree implements FromView, WithTitle, WithDrawings, WithStyles
{
	use Exportable;

	private $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function view(): View
	{
		return view('exports.dashboard.auditGlobal.sheets.sheetThree', [
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
		$logo->setCoordinates('I1');
    $logo->setOffsetY(15);
    array_push($draws, $logo);

		$matters = $this->data['matters'];
		$columns = ['B', 'K'];
		foreach ($columns as $column) {
			foreach ($matters as $index => $matter) {
				$row = $index + 14;
				// $imageMatter = @imagecreatefromstring(file_get_contents($matter['full_path']));
				$imageMatter = public_path($matter['image']);
				if ( $imageMatter && !is_null($imageMatter) ) {
					$matterDraw = new Drawing();
					$matterDraw->setName($matter['matter']);
					$matterDraw->setDescription($matter['matter']);
					$matterDraw->setPath($imageMatter);
					$matterDraw->setHeight(Variable::IMAGE_SIZE_LOGO_ICON);
					$matterDraw->setCoordinates("{$column}{$row}");
					$matterDraw->setOffsetY(10);
					$matterDraw->setOffsetX(15);
					array_push($draws, $matterDraw);
				}
			}
		}

		return $draws;
	}

	public function styles(Worksheet $sheet)
	{
		// Show/hide gridlines when printing
		$sheet->setShowGridlines(false);
		// logo
		$sheet->getRowDimension(1)->setRowHeight(60);
    // header
    $sheet->getRowDimension(2)->setRowHeight(45);
		$sheet->getRowDimension(3)->setRowHeight(45);
		$sheet->getStyle('B2:Q3')->applyFromArray(Style::STYLE_TITLE_DOCUMENT)->applyFromArray(Style::STYLE_BORDER_MAIN);
    // headers
		$sheet->getStyle('B5:Q9')->applyFromArray(Style::STYLE_BORDER_MAIN);
		$numberRowHeaders = 5;
		for ($i=0; $i < $numberRowHeaders; $i++) { 
			$row = $i + 5;
			$sheet->getStyle("B{$row}")->applyFromArray(Style::STYLE_NAME_HEADERS)->getAlignment()->setWrapText(true);
			$sheet->getStyle("D{$row}:G{$row}")->applyFromArray(Style::STYLE_VALUE_HEADERS)->getAlignment()->setWrapText(true);
			$sheet->getStyle("H{$row}")->applyFromArray(Style::STYLE_NAME_HEADERS)->getAlignment()->setWrapText(true);
			$sheet->getStyle("J{$row}:M{$row}")->applyFromArray(Style::STYLE_VALUE_HEADERS)->getAlignment()->setWrapText(true);
			if ($row < 8) {
				$sheet->getStyle("N{$row}")->applyFromArray(Style::STYLE_NAME_HEADERS)->getAlignment()->setWrapText(true);
				$sheet->getStyle("P{$row}:Q{$row}")->applyFromArray(Style::STYLE_VALUE_HEADERS)->getAlignment()->setWrapText(true);
			}
			if ($row == 8) {
				$sheet->getRowDimension($row)->setRowHeight(30);
			}
		}
		// headers totals
		$sheet->getStyle("B11:F11")->applyFromArray(Style::STYLE_VALUE_CARD)->getAlignment()->setWrapText(true);
		$sheet->getStyle("G11:H11")->applyFromArray(Style::STYLE_HEADER_CARD_ERROR)->getAlignment()->setWrapText(true);
		$sheet->getStyle("K11:O11")->applyFromArray(Style::STYLE_VALUE_CARD)->getAlignment()->setWrapText(true);
		$sheet->getStyle("P11:Q11")->applyFromArray(Style::STYLE_HEADER_CARD_ERROR)->getAlignment()->setWrapText(true);
		// headers matter
		$sheet->getStyle("B13:H13")->applyFromArray(Style::STYLE_VALUE_CARD)->getAlignment()->setWrapText(true);
		$sheet->getStyle("K13:Q13")->applyFromArray(Style::STYLE_VALUE_CARD)->getAlignment()->setWrapText(true);
		// matters
		$matters = $this->data['matters'];
		for ($i=0; $i < sizeof($matters); $i++) { 
			$row = $i + 14;
			$sheet->getStyle("C{$row}:F{$row}")->applyFromArray(Style::STYLE_VALUE_CARD_BODY)->getAlignment()->setWrapText(true);
			$sheet->getStyle("G{$row}:H{$row}")->applyFromArray(Style::STYLE_VALUE_CARD)->getAlignment()->setWrapText(true);
			$sheet->getStyle("L{$row}:O{$row}")->applyFromArray(Style::STYLE_VALUE_CARD_BODY)->getAlignment()->setWrapText(true);
			$sheet->getStyle("P{$row}:Q{$row}")->applyFromArray(Style::STYLE_VALUE_CARD)->getAlignment()->setWrapText(true);
			$sheet->getRowDimension($row)->setRowHeight(35);
		}
		$sheet->getStyle('B14:H17')->applyFromArray(Style::STYLE_BORDER_MAIN);
		$sheet->getStyle('K14:Q17')->applyFromArray(Style::STYLE_BORDER_MAIN);

		$sheet->getRowDimension(11)->setRowHeight(35);
		$sheet->getRowDimension(13)->setRowHeight(35);
	}
}
