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

class AuditDocumentsReportExcel implements FromView, WithTitle, WithDrawings, WithStyles
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
		return view('exports.auditDocuments', [
			'data' => $this->data
		]);
	}

	/**
	 * @return string
	 */
	public function title(): string
	{
		return 'Documentos Requeridos';
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
		$logo->setHeight(60);
		$logo->setCoordinates('D2');

		array_push($draws, $logo);

		return $draws;
	}

	public function styles(Worksheet $sheet)
	{
		// Show/hide gridlines when printing
		$sheet->setShowGridlines(false);

		// underlined title
		$sheet->getStyle('D2:U2')->applyFromArray([
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
		$sheet->getStyle('D4:U4')->applyFromArray($styleArray);
		$sheet->getStyle('D5:U5')->applyFromArray($styleArray);
		$sheet->getStyle('D6:U6')->applyFromArray($styleArray);
		$sheet->getStyle('D7:U7')->applyFromArray($styleArray);
		$sheet->getStyle('D8:U8')->applyFromArray($styleArray);
		$sheet->getStyle('D9:U9')->applyFromArray($styleArray);
		// row heights
		$sheet->getRowDimension('9')->setRowHeight(20);

		// header caption table
		$sheet->getRowDimension('11')->setRowHeight(30);
		$sheet->getStyle('D11:U11')->applyFromArray([
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

		// name columns
		$sheet->getStyle('D12:U12')->applyFromArray([
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

		// style for body table
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
		// set style for body table
		if (sizeof($this->data['requirements']) > 0) {
			foreach ($this->data['requirements'] as $key => $data) {
				$row = 12 + ($key + 1);
				$sheet->getRowDimension($row)->setRowHeight(80);
				$sheet->getStyle("D{$row}:U{$row}")->applyFromArray($styleArray);
			}
		} else {
			$row = 13;
			$sheet->getRowDimension($row)->setRowHeight(80);
			$sheet->getStyle("D{$row}:U{$row}")->applyFromArray($styleArray);
		}
	}
}
