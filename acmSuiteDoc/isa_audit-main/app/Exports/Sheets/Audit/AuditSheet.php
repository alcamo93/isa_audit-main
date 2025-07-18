<?php

namespace App\Exports\Sheets\Audit;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class AuditSheet implements FromView, WithTitle, WithDrawings, WithStyles
{
	use Exportable;

	private $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function view(): View
	{
		return view('exports.audit.audit', [
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

	public function drawings()
	{
		$logo = new Drawing();
		$logo->setName('Logo');
		$logo->setDescription('ISA Ambiental Logo');
		$logo->setPath(public_path('logos/acm_suite_logo_h.png'));
		$logo->setHeight(80);
		$logo->setCoordinates('E2');

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
		$sheet->getStyle('E2:L2')->applyFromArray($styleArray);

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
		foreach ($this->data as $matter) {
			foreach ($matter['aspects'] as $aspect) {
				$row = 7 + $i;
				$sheet->getStyle("C{$row}:N{$row}")->applyFromArray($styleArray);
				$sheet->getRowDimension($row)->setRowHeight(20);
				if (end($matter['aspects'])) {
					$nextRow = $row + 1;
					$sheet->getStyle("C{$nextRow}:N{$nextRow}")->applyFromArray($styleArray);
				}
				$i++;
			}
			$i = $i + 3;
		}
	}
}
