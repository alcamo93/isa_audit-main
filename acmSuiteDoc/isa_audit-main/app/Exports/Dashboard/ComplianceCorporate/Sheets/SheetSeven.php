<?php

namespace App\Exports\Dashboard\ComplianceCorporate\Sheets;

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

class SheetSeven implements FromView, WithTitle, WithDrawings, WithStyles
{
	use Exportable;

	private $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function view(): View
	{
		return view('exports.dashboard.ComplianceCorporate.sheets.sheetSeven', [
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
		$logo->setCoordinates('J1');
    $logo->setOffsetY(15);
    array_push($draws, $logo);

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
		$sheet->getStyle('B2:S3')->applyFromArray(Style::STYLE_TITLE_DOCUMENT)->applyFromArray(Style::STYLE_BORDER_MAIN);
    // headers
		$sheet->getStyle('B5:J11')->applyFromArray(Style::STYLE_BORDER_MAIN);
    $sheet->getStyle('L5:S11')->applyFromArray(Style::STYLE_BORDER_MAIN);
    $sheet->getRowDimension(10)->setRowHeight(60);
		$numberRowHeaders = 7;
		for ($i=0; $i < $numberRowHeaders; $i++) { 
			$row = $i + 5;
			$sheet->getStyle("B{$row}")->applyFromArray(Style::STYLE_NAME_HEADERS)->getAlignment()->setWrapText(true);
			$sheet->getStyle("D{$row}:I{$row}")->applyFromArray(Style::STYLE_VALUE_HEADERS)->getAlignment()->setWrapText(true);
			$sheet->getStyle("L{$row}")->applyFromArray(Style::STYLE_NAME_HEADERS)->getAlignment()->setWrapText(true);
			$sheet->getStyle("N{$row}:R{$row}")->applyFromArray(Style::STYLE_VALUE_HEADERS)->getAlignment()->setWrapText(true);
		}
		
		$rowTitleTables = $row + 2;
		$rowHeadersTable = $rowTitleTables + 1;
		$rowInitBody = $rowHeadersTable + 1;

		$headerTableTasks = $this->data['action_plan']['header_table_tasks'];
		$headerTableTasksSize = ( sizeof($headerTableTasks) * 2 ) + 2;
		$lastColumn = collect()->range('G', 'Z')->chunk($headerTableTasksSize)->first()->last();

		$sheet->getStyle("B{$rowTitleTables}:E{$rowTitleTables}")->applyFromArray(Style::STYLE_BODY_TABLE_NO_BORDER);
		$sheet->getStyle("G{$rowTitleTables}:{$lastColumn}{$rowTitleTables}")->applyFromArray(Style::STYLE_BODY_TABLE_NO_BORDER);

		$sheet->getStyle("B{$rowHeadersTable}:E{$rowHeadersTable}")->applyFromArray(Style::STYLE_BODY_TABLE_NO_BORDER);
		$sheet->getStyle("G{$rowHeadersTable}:{$lastColumn}{$rowHeadersTable}")->applyFromArray(Style::STYLE_BODY_TABLE_NO_BORDER);

		$mattersRequirements = $this->data['action_plan']['matters'];
		for ($i=0; $i < sizeof($mattersRequirements); $i++) { 
			$sheet->getStyle("B{$rowInitBody}:E{$rowInitBody}")->applyFromArray(Style::STYLE_BODY_TABLE_NO_BORDER);
			$sheet->getStyle("G{$rowInitBody}:{$lastColumn}{$rowInitBody}")->applyFromArray(Style::STYLE_BODY_TABLE_NO_BORDER);
			++$rowInitBody;
		}
		
		$lastRow = $rowInitBody - 1;
		$sheet->getStyle("B{$rowHeadersTable}:E{$lastRow}")->applyFromArray(Style::STYLE_BORDER_MAIN);
		$sheet->getStyle("G{$rowHeadersTable}:{$lastColumn}{$lastRow}")->applyFromArray(Style::STYLE_BORDER_MAIN);
	}
}
