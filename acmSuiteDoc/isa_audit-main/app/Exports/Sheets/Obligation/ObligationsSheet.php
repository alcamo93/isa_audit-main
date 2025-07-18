<?php

namespace App\Exports\Sheets\Obligation;

use App\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ObligationsSheet implements FromView, WithTitle, WithDrawings, WithStyles
{
	use Exportable;

	private $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function view(): View
	{
		return view('exports.obligation.obligations', [
			'data' => $this->data
		]);
	}

	/**
	 * @return string
	 */
	public function title(): string
	{
		return 'Permisos Críticos';
	}

	public function drawings()
	{
		$logo = new Drawing();
		$logo->setName('Logo');
		$logo->setDescription('ISA Ambiental Logo');
		$logo->setPath(public_path('logos/acm_suite_logo_h.png'));
		$logo->setHeight(50);
		$logo->setCoordinates('D2');

		$draws = [$logo];

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
		$sheet->getStyle('D2:Q2')->applyFromArray($styleArray);

		// other rows styles
		$styleArray = [
			'borders' => [
				'bottom' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => '00003e52'],
				],
				'left' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => '00003e52'],
				],
				'top' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => '00003e52'],
				],
			],
		];

		// border all but right
		$i = 0;
		foreach ($this->data['matters'] as $key => $matter) {
			foreach ($matter['aspects'] as $aspect) {
				$row = 7 + $i;
				$sheet->getStyle("C{$row}:N{$row}")->applyFromArray($styleArray);
				$sheet->getRowDimension($row)->setRowHeight(20);
				if (end($matter['aspects'])) {
					$row = $row + 1;
					$sheet->getStyle("C{$row}:N{$row}")->applyFromArray($styleArray);
				}
				$i++;
			}
			$i = $i + 3;
		}
	}
}
