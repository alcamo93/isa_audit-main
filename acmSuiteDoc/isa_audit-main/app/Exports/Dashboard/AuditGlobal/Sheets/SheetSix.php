<?php

namespace App\Exports\Dashboard\AuditGlobal\Sheets;

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

	private $data, $numberColumns, $columns;

	public function __construct(array $data)
	{
		$this->data = $data;
		$this->numberColumns = sizeof($data['columns_name']) + 1;
		$rangeLetters = range('B', 'Z');
		$this->columns = array_slice($rangeLetters, 0, $this->numberColumns);
	}

	public function view(): View
	{
		return view('exports.dashboard.auditGlobal.sheets.sheetSix', [
			'numberColumns' => $this->numberColumns,
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
		$columnImage = intval( sizeof($this->columns)/2 );
		$column = $this->columns[$columnImage];
		$offsetX = sizeof($this->columns) * 15;
    $draws = [];

    $logo = new Drawing();
		$logo->setName(Variable::IMAGE_LOGO_NAME);
		$logo->setDescription(Variable::IMAGE_LOGO_DESCRIPTION);
		$logo->setPath( public_path(Variable::IMAGE_PATH) );
		$logo->setHeight(Variable::IMAGE_SIZE_LOGO);
		$logo->setCoordinates("{$column}3");
    $logo->setOffsetY(15);
		$logo->setOffsetX($offsetX);
    array_push($draws, $logo);

		return $draws;
	}

	/**
	 * @return array
	 */
	public function columnWidths(): array
	{
		return collect($this->columns)->map(function($item) {
			$size = $item == 'B' ? 33 : 30;
			return [$item => $size];
		})->collapse()->toArray();
	}


	public function styles(Worksheet $sheet)
	{
		$columns = collect($this->columns);
		$initColumn = $columns->first();
		$lastColumn = $columns->last();
		// Show/hide gridlines when printing
		$sheet->setShowGridlines(false);
    // header
    $sheet->getRowDimension(2)->setRowHeight(75);
		$sheet->getStyle("{$initColumn}2:{$lastColumn}2")->applyFromArray(Style::STYLE_TITLE_DOCUMENT)->applyFromArray(Style::STYLE_BORDER_MAIN);
    // logo
		$sheet->getRowDimension(3)->setRowHeight(45);
		// matters
		$row = 5;
		$matters = $this->data['matters'];
		for ($idxMatter=0; $idxMatter < sizeof($matters); $idxMatter++) { 
			$sheet->getStyle("{$initColumn}{$row}:{$lastColumn}{$row}")->applyFromArray(Style::STYLE_TITLE_PLAIN);
			++$row;
			$sheet->getStyle("{$initColumn}{$row}:{$lastColumn}{$row}")->applyFromArray(Style::STYLE_HEADER_TABLE);
			++$row;

			$aspects = $matters[$idxMatter]['aspects'];
			$firstRowAspect = $row;
			$lastRowAspect = ($row + sizeof($aspects)) - 1;
			for ($idxAspect=0; $idxAspect < sizeof($aspects); $idxAspect++) { 
				$sheet->getStyle("{$initColumn}{$row}:{$lastColumn}{$row}")->applyFromArray(Style::STYLE_HEADER_TABLE_NO_BORDER);
				++$row;
			}
			$sheet->getStyle("{$initColumn}{$firstRowAspect}:{$lastColumn}{$lastRowAspect}")->applyFromArray(Style::STYLE_BORDER_MAIN);
			$sheet->getStyle("{$initColumn}{$lastRowAspect}:{$lastColumn}{$lastRowAspect}")->applyFromArray(Style::STYLE_HEADER_TABLE_NO_BORDER_FOTTER);
			++$row;
		}
	}
}
