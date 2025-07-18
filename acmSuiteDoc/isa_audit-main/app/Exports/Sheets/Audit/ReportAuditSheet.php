<?php

namespace App\Exports\Sheets\Audit;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportAuditSheet implements FromView, WithTitle, WithStyles, ShouldAutoSize, WithColumnWidths
{
	use Exportable;

	private $data, $risk;

	public function __construct(array $data, int $evaluateRisk)
	{	
		$this->data = $data;
		$this->risk = ($evaluateRisk == 1) ? true : false;
	}

	public function view(): View
	{
		return view('exports.audit.report_audit', [
			'data' => $this->data,
			'risk' => $this->risk
		]);
	}

	/**
	 * @return string
	 */
	public function title(): string
	{
		return 'Reporte de auditoría';
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
			'H' => 60,
			'I' => 30,
			'J' => 60,
			'K' => 30,
		];
	}

	public function styles(Worksheet $sheet)
	{
		// Show/hide gridlines when printing
		$columnInit = 'B';
		$columnRisk = $this->risk ? 'K' : 'J';
		$sheet->setShowGridlines(false);
		foreach (range($columnInit, $columnRisk) as $columnID) {
			$sheet->getColumnDimension($columnID)->setAutoSize(false);
		}
		
		// title styles
		$titleStyleArray = [
			'borders' => [
				'outline' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => '00003e52'],
				],
			],
		];
		$sheet->getStyle("{$columnInit}2:{$columnRisk}2")->applyFromArray($titleStyleArray);

		$headersStyleArray = [
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
		$sheet->getStyle("{$columnInit}3:{$columnRisk}3")->applyFromArray($headersStyleArray);

		// other rows stiles
		$bodyTablesSyleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => '00003e52'],
				]
			],
		];
		
		$aspects = collect($this->data)->pluck('aspects')->collapse();
		$counterMainRow = $aspects->sum( fn($item) => sizeof($item['audits']) );
		$counterSubRow = $aspects->pluck('audits')->collapse()->sum( fn($item) => sizeof($item['childs']) );
		$totalRows = $counterMainRow + $counterSubRow;

		for ($i = 0; $i < $totalRows; $i++) { 
			$row = $i + 4;
			$sheet->getStyle("{$columnInit}{$row}:{$columnRisk}{$row}")->getFont()->setSize(8);
			$sheet->getStyle("{$columnInit}{$row}:{$columnRisk}{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
			$sheet->getStyle("{$columnInit}{$row}:{$columnRisk}{$row}")->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
			$sheet->getStyle("{$columnInit}{$row}:{$columnRisk}{$row}")->applyFromArray($bodyTablesSyleArray)->getAlignment()->setWrapText(true);
			$sheet->getStyle("E{$row}:F{$row}")->getAlignment()->setHorizontal(Alignment::VERTICAL_JUSTIFY);
			$sheet->getStyle("J{$row}")->getAlignment()->setHorizontal(Alignment::VERTICAL_JUSTIFY);
			$sheet->getRowDimension($row)->setRowHeight(100);
		}

	}
}
