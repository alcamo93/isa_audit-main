<?php

namespace App\Exports\Applicability;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Exports\Template\Style;
use App\Exports\Template\Variable;

class AplicabilityResultReportExcel implements FromView, WithTitle, WithDrawings, WithStyles, ShouldAutoSize, WithColumnWidths
{
	use Exportable;

	private $data;

	public function __construct(array $data)
	{	
		$this->data = $data;
	}

	public function view(): View
	{
		return view('exports.applicability.applicability_result_report', [
			'title' => 'REPORTE DE RESULTADOS DE APLICABILIDAD',
			'data' => $this->data,
		]);
	}

	/**
	 * @return string
	 */
	public function title(): string
	{
		return 'Resultados';
	}

	public function drawings()
	{
		$draws = [];

		$logo = new Drawing();
		$logo->setName(Variable::IMAGE_LOGO_NAME);
		$logo->setDescription(Variable::IMAGE_LOGO_DESCRIPTION);
		$logo->setPath( public_path(Variable::IMAGE_PATH) );
		$logo->setHeight(Variable::IMAGE_SIZE_LOGO);
		$logo->setCoordinates('B2');

		array_push($draws, $logo);

		return $draws;
	}

	/**
	 * @return array
	 */
	public function columnWidths(): array
	{
		return [
			'A' => 5,
			'B' => 30,
			'C' => 30,
			'D' => 30,
			'E' => 60,
			'F' => 60,
			'G' => 30,
			'H' => 30,
      'I' => 30,
			'J' => 30,
			'K' => 30,
		];
	}

	public function styles(Worksheet $sheet)
	{
		// Show/hide gridlines when printing
		$sheet->setShowGridlines(false);

		$columnInit = 'B';
		$columnLast = 'K';
		foreach (range($columnInit, $columnLast) as $columnID) {
			$sheet->getColumnDimension($columnID)->setAutoSize(false);
		}
		
		$sheet->getRowDimension(2)->setRowHeight(60);
		$sheet->getStyle("{$columnInit}2:{$columnLast}2")->applyFromArray(Style::STYLE_TITLE_DOCUMENT)->applyFromArray(Style::STYLE_BORDER_MAIN);
		$sheet->getStyle("{$columnInit}4:{$columnLast}9")->applyFromArray(Style::STYLE_BORDER_MAIN);
		$sheet->getRowDimension(11)->setRowHeight(20);
		$sheet->getStyle("{$columnInit}11:{$columnLast}11")->applyFromArray(Style::STYLE_HEADERS_DESCRIPTION)->applyFromArray(Style::STYLE_BORDER_MAIN);
		$sheet->getStyle("{$columnInit}12:{$columnLast}12")->applyFromArray(Style::STYLE_HEADER_TABLE);

		$numberRowHeaders = 6;
		for ($i=0; $i < $numberRowHeaders; $i++) { 
			$row = $i + 4;
			$sheet->getStyle("{$columnInit}{$row}")->applyFromArray(Style::STYLE_NAME_HEADERS)->getAlignment()->setWrapText(true);
			$sheet->getStyle("C{$row}:E{$row}")->applyFromArray(Style::STYLE_VALUE_HEADERS)->getAlignment()->setWrapText(true);
			$sheet->getStyle("F{$row}")->applyFromArray(Style::STYLE_NAME_HEADERS)->getAlignment()->setWrapText(true);
			$sheet->getStyle("G{$row}:H{$row}")->applyFromArray(Style::STYLE_VALUE_HEADERS)->getAlignment()->setWrapText(true);
			$sheet->getStyle("I{$row}")->applyFromArray(Style::STYLE_NAME_HEADERS)->getAlignment()->setWrapText(true);
			if ($row <= 6) {
				$sheet->getStyle("J{$row}:{$columnLast}{$row}")->applyFromArray(Style::STYLE_VALUE_HEADERS)->getAlignment()->setWrapText(true);
			}
			if ($row == 9) {
				$sheet->getRowDimension($row)->setRowHeight(30);
			}
		}
		
		for ($i=0; $i < sizeof($this->data['requirements']); $i++) { 
			$row = $i + 13;
			$sheet->getRowDimension($row)->setRowHeight(80);
			$sheet->getStyle("{$columnInit}{$row}:{$columnLast}{$row}")->applyFromArray(Style::STYLE_BODY_TABLE)->getAlignment()->setWrapText(true);
		}
	}
}
