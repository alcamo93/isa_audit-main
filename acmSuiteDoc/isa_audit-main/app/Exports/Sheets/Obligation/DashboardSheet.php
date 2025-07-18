<?php

namespace App\Exports\Sheets\Obligation;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DashboardSheet implements FromView, WithTitle, WithDrawings, WithStyles, ShouldAutoSize
{
	use Exportable;

	private $data;
	private $percentages;

	public function __construct(array $data, array $percentages)
	{
		$this->data = $data;
		$this->percentages = $percentages;
	}

	public function view(): View
	{
		return view('exports.obligation.dashboard', [
			'data' => $this->data,
			'percentages' => $this->percentages
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
		$logo->setHeight(50);
		$logo->setCoordinates('D2');

		array_push($draws, $logo);

		$i = 0;
		foreach ($this->percentages['matters'] as $matter) {
			$row = 14 + $i;
			$imgStyleMatter = new Drawing();
			$imgStyleMatter->setName($matter['matter']);
			$imgStyleMatter->setDescription("Logo {$matter['matter']}");
			$imgStyleMatter->setPath($matter['image']);
			$imgStyleMatter->setHeight(80);
			$imgStyleMatter->setCoordinates("B{$row}");

			$imgStyleAction = new Drawing();
			$imgStyleAction->setName($matter['matter']);
			$imgStyleAction->setDescription("Logo {$matter['matter']}");
			$imgStyleAction->setPath($matter['image']);
			$imgStyleAction->setHeight(80);
			$imgStyleAction->setCoordinates("L{$row}");

			array_push($draws, $imgStyleMatter, $imgStyleAction);
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
		$sheet->getStyle('D2:N2')->applyFromArray($styleArray);

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
		foreach ($this->percentages['matters'] as $e) {
			$row = 14 + $i;
			$sheet->getRowDimension($row)->setRowHeight(60);
			$i = $i + 2;
		}

		// underlined rows
		$sheet->getStyle('B4:T4')->applyFromArray($styleArray);
		$sheet->getStyle('B5:T5')->applyFromArray($styleArray);
		$sheet->getStyle('B6:O6')->applyFromArray($styleArray);
		$sheet->getStyle('B7:O7')->applyFromArray($styleArray);
		$sheet->getStyle('B8:O8')->applyFromArray($styleArray);
		$sheet->getStyle('F10:G10')->applyFromArray($styleArray);
		$sheet->getStyle('R10:S10')->applyFromArray($styleArray);
		// Aligment cell
		$sheet->getStyle('B10:S10')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		$sheet->getStyle('B10:U10')->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
		$sheet->getStyle('C12:U12')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		$sheet->getStyle('C12:U12')->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);

		$i = 0;
		foreach ($this->percentages['matters'] as $e) {
			$row = 14 + $i;
			$sheet->getStyle("G{$row}:H{$row}")->applyFromArray($styleArray)->getAlignment()->setWrapText(true);;
			$sheet->getStyle("R{$row}:S{$row}")->applyFromArray($styleArray)->getAlignment()->setWrapText(true);;
			$sheet->getStyle("B{$row}:S{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
			$sheet->getStyle("B{$row}:S{$row}")->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
			$i = $i + 2;
		}
	}
}
