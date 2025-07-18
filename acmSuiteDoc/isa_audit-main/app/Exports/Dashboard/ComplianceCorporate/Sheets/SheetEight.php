<?php

namespace App\Exports\Dashboard\ComplianceCorporate\Sheets;

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

class SheetEight implements FromView, WithTitle, WithDrawings, WithStyles, WithColumnWidths
{
	use Exportable;

	private $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function view(): View
	{
		return view('exports.dashboard.complianceCorporate.sheets.sheetEight', [
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
		$logo->setCoordinates('F4');
    $logo->setOffsetY(15);
		$logo->setOffsetX(15);
    array_push($draws, $logo);

		// matters table
		$rowInit = 8;
		foreach ($this->data['matters'] as $matter) {
			$imageMatter = public_path($matter['image']);
			if ( $imageMatter && !is_null($imageMatter) ) {
				$matterDraw = new Drawing();
				$matterDraw->setName($matter['matter']);
				$matterDraw->setDescription($matter['matter']);
				$matterDraw->setPath($imageMatter);
				$matterDraw->setHeight(Variable::IMAGE_SIZE_LOGO_ICON);
				$matterDraw->setCoordinates("B{$rowInit}");
				$matterDraw->setOffsetY(5);
				$matterDraw->setOffsetX(40);
				array_push($draws, $matterDraw);
			}
			$rowInit = $rowInit + ( sizeof($matter['aspects']) + 3 );
		}

		return $draws;
	}

	/**
	 * @return array
	 */
	public function columnWidths(): array
	{
		$range = range('B', 'O');
		$columns = collect($range)->map(fn($item) => [$item => 10])->collapse()->toArray();
		$columns['B'] = 30;
		return $columns;
	}

	public function styles(Worksheet $sheet)
	{
		// Show/hide gridlines when printing
		$sheet->setShowGridlines(false);
    // header
		$sheet->getRowDimension(2)->setRowHeight(70);
    $sheet->getRowDimension(3)->setRowHeight(45);
		$sheet->getStyle('B2:O3')->applyFromArray(Style::STYLE_TITLE_DOCUMENT)->applyFromArray(Style::STYLE_BORDER_MAIN);
		// LOGO
		$sheet->getRowDimension(4)->setRowHeight(60);
		// total table
		$sheet->getStyle('B5:O5')->applyFromArray(Style::STYLE_HEADER_TABLE);
		$sheet->getStyle("B6:O6")->applyFromArray(Style::STYLE_HEADER_TABLE_NO_BORDER);
		// matters table
		$rowInit = 8;
		foreach ($this->data['matters'] as $matter) {
			$sheet->getRowDimension($rowInit)->setRowHeight(30);
			$sheet->getStyle("B{$rowInit}:M{$rowInit}")->applyFromArray(Style::STYLE_TITLE_PLAIN);
			$sheet->getStyle("N{$rowInit}:O{$rowInit}")->applyFromArray(Style::STYLE_HEADER_TABLE);
			++$rowInit;
			$rowInitAspects = $rowInit;
			foreach ($matter['aspects'] as $aspect) {
				$sheet->getStyle("B{$rowInit}")->applyFromArray(Style::STYLE_HEADER_TABLE);
				$sheet->getStyle("C{$rowInit}:O{$rowInit}")->applyFromArray(Style::STYLE_TITLE_PLAIN);
				++$rowInit;
			}
			$rowEndAspect = $rowInit;
			$sheet->getStyle("B{$rowInit}")->applyFromArray(Style::STYLE_HEADER_TABLE);
			$sheet->getStyle("C{$rowInit}:O{$rowInit}")->applyFromArray(Style::STYLE_TITLE_PLAIN);
			$sheet->getStyle("B{$rowInitAspects}:O{$rowEndAspect}")->applyFromArray(Style::STYLE_BORDER_MAIN);
			++$rowInit;
			++$rowInit;
		}
	}
}
