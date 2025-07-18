<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AuditProgressReportExcel implements FromView, WithTitle, WithDrawings, WithStyles
{
	use Exportable;

	private $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	/**
	 * @return View
	 */
	public function view(): View
	{
		return view('exports.auditProgress', [
			'data' => $this->data
		]);
	}

	/**
	 * @return string
	 */
	public function title(): string
	{
		return 'Auditoría';
	}

	/**
	 * @return array|BaseDrawing|BaseDrawing[]
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 */
	public function drawings()
	{
		$draws = [];

		$logo = new Drawing();
		$logo->setName('Logo');
		$logo->setDescription('ISA Ambiental Logo');
		$logo->setPath(public_path('logos/acm_suite_logo_h.png'));
		$logo->setHeight(80);
		$logo->setCoordinates('E2');

		array_push($draws, $logo);

		return $draws;
	}

	public function styles(Worksheet $sheet)
	{
		// Show/hide gridlines when printing
		$sheet->setShowGridlines(false);

		// underlined title
		$sheet->getStyle('E2:S2')->applyFromArray([
			'borders' => [
				'outline' => [
					'borderStyle' => Border::BORDER_THICK,
					'color' => ['argb' => '00003e52'],
				],
			],
		]);

		// other rows stiles
		$styleArray = [
			'borders' => [
				'bottom' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => '00003e52'],
				],
			],
		];

		// underlined rows
		$sheet->getStyle('D4:T4')->applyFromArray($styleArray);
		$sheet->getStyle('D5:T5')->applyFromArray($styleArray);
		$sheet->getStyle('D6:T6')->applyFromArray($styleArray);
		$sheet->getStyle('D7:T7')->applyFromArray($styleArray);
		$sheet->getStyle('D8:O8')->applyFromArray($styleArray);
		$sheet->getStyle('D9:O9')->applyFromArray($styleArray);
		// row heights
		$sheet->getRowDimension('9')->setRowHeight(20);

		// title table questions
		$sheet->getRowDimension('11')->setRowHeight(30);
		$sheet->getStyle('D11:T11')->applyFromArray([
			'alignment' => [
				'vertical' => Alignment::VERTICAL_CENTER,
				'horizontal' => Alignment::HORIZONTAL_CENTER
			],
			'font' => [
				'name' => 'Arial',
				'size' => '18',
				'color' => ['rgb' => '003e52']
			],
		]);

		// header table requerements
		$sheet->getStyle('D12:S12')->applyFromArray([
			'alignment' => [
				'vertical' => Alignment::VERTICAL_CENTER,
				'horizontal' => Alignment::HORIZONTAL_CENTER
			],
			'font' => [
				'name' => 'Arial',
				'size' => '12',
				'color' => ['rgb' => 'ffffff']
			],
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				],
			],
			'fill' => [
				'fillType' => Fill::FILL_SOLID,
				'color' => ['rgb' => '7da8c3']
			]
		]);

		// style for requeriments
		$styleArray = [
			'font' => [
				'name' => 'Arial',
				'size' => '8',
				'color' => ['rgb' => '767171']
			],
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				],
			],
			'alignment' => [
				'wrapText' => true,
				'vertical' => Alignment::VERTICAL_CENTER,
				'horizontal' => Alignment::HORIZONTAL_CENTER
			]
		];
		// set style for requirements
		foreach ($this->data['requirements'] as $key => $data) {
			$row = 12 + ($key + 1);
			$sheet->getRowDimension($row)->setRowHeight(80);
			$sheet->getStyle("D{$row}:S{$row}")->applyFromArray($styleArray);
		}

		// set style for totals
		$styleArray = [
			'alignment' => [
				'vertical' => Alignment::VERTICAL_CENTER,
				'horizontal' => Alignment::HORIZONTAL_CENTER
			],
			'font' => [
				'name' => 'Arial',
				'size' => '16',
				'color' => ['rgb' => 'a6a6a6']
			],
			'borders' => [
				'bottom' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => '00003e52'],
				],
			]
		];

		// set style for totals
		$totalRows = (12 + 1) + sizeof($this->data['requirements']) + 3;
		$sheet->getStyle("M{$totalRows}:T{$totalRows}")->applyFromArray($styleArray);
		$sheet->getStyle('M'.($totalRows + 1).':T'.($totalRows + 1))->applyFromArray($styleArray);
		$sheet->getStyle('M'.($totalRows + 2).':T'.($totalRows + 2))->applyFromArray($styleArray);
		$sheet->getStyle('M'.($totalRows + 3).':T'.($totalRows + 3))->applyFromArray($styleArray);
		$sheet->getStyle('M'.($totalRows + 4).':T'.($totalRows + 4))->applyFromArray($styleArray);
		$sheet->getStyle('M'.($totalRows + 5).':T'.($totalRows + 5))->applyFromArray($styleArray);
	}
}
