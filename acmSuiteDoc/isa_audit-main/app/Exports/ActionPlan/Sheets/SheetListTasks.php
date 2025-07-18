<?php

namespace App\Exports\ActionPlan\Sheets;

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

class SheetListTasks implements FromView, WithTitle, WithDrawings, WithStyles, WithColumnWidths
{
	use Exportable;

	private $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function view(): View
	{
		return view('exports.action.plan.sheets.sheetListTasks', [
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
		$logo->setHeight(Variable::IMAGE_SIZE_LOGO_ICON);
		$logo->setCoordinates('B1');
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
		$range = range('B', 'Z');
		$columns = collect($range)->map(fn($item) => [$item => 25])->collapse()->toArray();
		return $columns;
	}


	public function styles(Worksheet $sheet)
	{
		$lastColumn = $this->data['with_level_risk'] ? 'O' : 'N';
		// Show/hide gridlines when printing
		$sheet->setShowGridlines(false);
    // header
		$sheet->getRowDimension(1)->setRowHeight(30);
		$sheet->getRowDimension(2)->setRowHeight(30);
		$sheet->getStyle("B2:{$lastColumn}2")->applyFromArray(Style::STYLE_SUBTITLE_DOCUMENT);
    // table
		$sheet->getStyle("B3:{$lastColumn}3")->applyFromArray(Style::STYLE_HEADER_TABLE);
		for ($i=0; $i <sizeof($this->data['records']) ; $i++) { 
			$row = 4 + $i;
			$sheet->getStyle("B{$row}:{$lastColumn}{$row}")->applyFromArray(Style::STYLE_BODY_TABLE);
		}
	}
}
