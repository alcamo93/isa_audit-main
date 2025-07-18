<?php

namespace App\Exports\Applicability;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Exports\Template\Style;
use App\Exports\Template\Variable;

class ApplicabilityReportExcel implements FromView, WithTitle, WithDrawings, WithStyles, ShouldAutoSize
{
	use Exportable;

	private $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function view(): View
	{
		return view('exports.applicability.applicability_answer_report', [
			'title' => 'RESPUESTAS DE APLICABILIDAD',
			'data' => $this->data
		]);
	}

	/**
	 * @return string
	 */
	public function title(): string
	{
		return 'Aplicabilidad';
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

	public function styles(Worksheet $sheet)
	{
		// Show/hide gridlines when printing
		$sheet->setShowGridlines(false);
		
		$sheet->getRowDimension(2)->setRowHeight(60);
		$sheet->getStyle('B2:U2')->applyFromArray(Style::STYLE_TITLE_DOCUMENT)->applyFromArray(Style::STYLE_BORDER_MAIN);
		$sheet->getStyle('B4:U9')->applyFromArray(Style::STYLE_BORDER_MAIN);
		$sheet->getRowDimension(11)->setRowHeight(20);
		$sheet->getStyle('B11:U11')->applyFromArray(Style::STYLE_HEADERS_DESCRIPTION)->applyFromArray(Style::STYLE_BORDER_MAIN);
		$sheet->getStyle('B12:U12')->applyFromArray(Style::STYLE_HEADER_TABLE);

		$numberRowHeaders = 6;
		for ($i=0; $i < $numberRowHeaders; $i++) { 
			$row = $i + 4;
			$sheet->getStyle("B{$row}:C{$row}")->applyFromArray(Style::STYLE_NAME_HEADERS)->getAlignment()->setWrapText(true);
			$sheet->getStyle("D{$row}:E{$row}")->applyFromArray(Style::STYLE_VALUE_HEADERS)->getAlignment()->setWrapText(true);
			$sheet->getStyle("H{$row}:I{$row}")->applyFromArray(Style::STYLE_NAME_HEADERS)->getAlignment()->setWrapText(true);
			$sheet->getStyle("J{$row}:M{$row}")->applyFromArray(Style::STYLE_VALUE_HEADERS)->getAlignment()->setWrapText(true);
			$sheet->getStyle("N{$row}:O{$row}")->applyFromArray(Style::STYLE_NAME_HEADERS)->getAlignment()->setWrapText(true);
			if ($row <= 6) {
				$sheet->getStyle("P{$row}:U{$row}")->applyFromArray(Style::STYLE_VALUE_HEADERS)->getAlignment()->setWrapText(true);
			}
			if ($row == 9) {
				$sheet->getRowDimension($row)->setRowHeight(30);
			}
		}
		
		for ($i=0; $i < sizeof($this->data['questions']) ; $i++) { 
			$row = $i + 13;
			$sheet->getRowDimension($row)->setRowHeight(80);
			$sheet->getStyle("B{$row}:U{$row}")->applyFromArray(Style::STYLE_BODY_TABLE)->getAlignment()->setWrapText(true);
		}
	}
}
