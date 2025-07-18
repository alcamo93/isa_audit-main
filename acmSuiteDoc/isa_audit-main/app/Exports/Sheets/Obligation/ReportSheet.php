<?php

namespace App\Exports\Sheets\Obligation;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportSheet implements FromView, WithTitle, WithStyles, ShouldAutoSize, WithColumnWidths
{
	use Exportable;

	private $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function view(): View
	{
		return view('exports.obligation.report', [
			'data' => $this->data
		]);
	}

	/**
	 * @return string
	 */
	public function title(): string
	{
		return 'Reporte';
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
			'F' => 70,
			'G' => 30,
			'H' => 30,
			'I' => 30,
			'J' => 30,
			'K' => 30,
			'L' => 30,
			'M' => 30,
		];
	}

	public function styles(Worksheet $sheet)
	{
		// Show/hide gridlines when printing
		$sheet->setShowGridlines(false);
		foreach (range('B', 'M') as $columnID) {
			$sheet->getColumnDimension($columnID)->setAutoSize(false);
		}
		$styleHeaderArray = [
			'borders' => [
				'outline' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => '00003e52'],
				],
			],
		];
		$styleHeaderTableArray = [
			'alignment' => [
				'vertical' => Alignment::VERTICAL_CENTER,
				'horizontal' => Alignment::HORIZONTAL_CENTER
			],
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => '00003e52'],
				]
			],
			'fill' => [
				'fillType' => Fill::FILL_SOLID,
				'color' => [
					'argb' => '7DA8C3',
				],
			],
			'font' => [
				'color' => [
					'argb' => 'ffffff'
				],
			],
		];
		$bodyTablesSyleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => '00003e52'],
				]
			],
		];

		$sheet->getStyle('B2:M2')->applyFromArray($styleHeaderArray);
		$sheet->getStyle('B3:M3')->applyFromArray($styleHeaderTableArray);
		
		$totalRows = sizeof($this->data);
		for ($i = 0; $i < $totalRows; $i++) { 
			$row = $i + 4;
			$sheet->getStyle("B{$row}:M{$row}")->getFont()->setSize(8);
			$sheet->getStyle("B{$row}:M{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
			$sheet->getStyle("B{$row}:M{$row}")->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
			$sheet->getStyle("B{$row}:M{$row}")->applyFromArray($bodyTablesSyleArray)->getAlignment()->setWrapText(true);
			$sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::VERTICAL_JUSTIFY);
			$sheet->getRowDimension($row)->setRowHeight(100);
		}
	}
}
