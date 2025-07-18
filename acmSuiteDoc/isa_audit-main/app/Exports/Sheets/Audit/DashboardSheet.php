<?php

namespace App\Exports\Sheets\Audit;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class DashboardSheet implements FromView, WithTitle, WithDrawings, WithStyles, ShouldAutoSize
{
	use Exportable;

	private $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function view(): View
	{
		return view('exports.audit.dashboard', [
			'data' => $this->data
		]);
	}
	/**
	 * @return string
	 */
	public function title(): string
	{
		return 'Dashboard';
	}

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

		$i = 0;
		foreach ($this->data['matters'] as $matter) {
			$row = 14 + $i;
			$imgStyleAudit = new Drawing();
			$imgStyleAudit->setName($matter['matter']);
			$imgStyleAudit->setDescription('Logo ' . $matter['matter']);
			$imgStyleAudit->setPath(public_path($matter['image']));
			$imgStyleAudit->setHeight(80);
			$imgStyleAudit->setCoordinates('C' . $row);

			$imgStyleAction = new Drawing();
			$imgStyleAction->setName($matter['matter']);
			$imgStyleAction->setDescription('Logo ' . $matter['matter']);
			$imgStyleAction->setPath(public_path($matter['image']));
			$imgStyleAction->setHeight(80);
			$imgStyleAction->setCoordinates('N' . $row);

			array_push($draws, $imgStyleAudit, $imgStyleAction);
			$i = $i + 2;
		}

		return $draws;
	}

	public function styles(Worksheet $sheet)
	{
		// Show/hide gridlines when printing
		$sheet->setShowGridlines(false);
		//  title
		$styleArray = [
			'borders' => [
				'outline' => [
					'borderStyle' => Border::BORDER_THICK,
					'color' => ['argb' => '00003e52'],
				],
			],
		];
		// underlined title
		$sheet->getStyle('E2:S2')->applyFromArray($styleArray);

		// other rows stiles
		$styleArray = [
			'borders' => [
				'bottom' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => '00003e52'],
				],
			],
		];

		// row heights
		$sheet->getRowDimension('8')->setRowHeight(20);
		$sheet->getRowDimension('9')->setRowHeight(40);
		$sheet->getRowDimension('11')->setRowHeight(10);
		$sheet->getRowDimension('13')->setRowHeight(10);

		$i = 0;
		foreach ($this->data['matters'] as $matter) {
			$row = 14 + $i;
			$sheet->getRowDimension($row)->setRowHeight(60);
			$i = $i + 2;
		}

		// underlined rows
		$sheet->getStyle('D4:T4')->applyFromArray($styleArray);
		$sheet->getStyle('D5:T5')->applyFromArray($styleArray);
		$sheet->getStyle('D6:O6')->applyFromArray($styleArray);
		$sheet->getStyle('D7:O7')->applyFromArray($styleArray);
		$sheet->getStyle('D8:O8')->applyFromArray($styleArray);
		$sheet->getStyle('H10:I10')->applyFromArray($styleArray);
		$sheet->getStyle('R10:S10')->applyFromArray($styleArray);
		// Aligment cell
		$sheet->getStyle('D10:S10')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		$sheet->getStyle('D10:S10')->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
		$sheet->getStyle('C12:U12')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		$sheet->getStyle('C12:U12')->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);

		$i = 0;
		foreach ($this->data['matters'] as $matter) {
			$row = 14 + $i;
			$sheet->getStyle("I{$row}:J{$row}")->applyFromArray($styleArray)->getAlignment()->setWrapText(true);;
			$sheet->getStyle("T{$row}:U{$row}")->applyFromArray($styleArray)->getAlignment()->setWrapText(true);;
			$sheet->getStyle("C{$row}:U{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
			$sheet->getStyle("C{$row}:U{$row}")->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
			$i = $i + 2;
		}
	}
}
