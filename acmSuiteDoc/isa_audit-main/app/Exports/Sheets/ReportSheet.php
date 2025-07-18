<?php

namespace App\Exports\Sheets;

use App\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// excel styles
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportSheet implements FromView, WithTitle, WithStyles, ShouldAutoSize
{
    use Exportable;

    private $data;

    public function __construct(array $data, int $allowRisk) {
        $dataOrder = [];
        foreach ($data as $m) {
            foreach ($m['aspects'] as $a) {
                foreach ($a['action'] as $ac) {
                    array_push($dataOrder, $ac);
                }
            }
        }
        //array_push($dataOrder, $dataEvalrisk);
        $this->data = $dataOrder;
        $this->risk = ($allowRisk == 1) ? true : false;
    }

    public function view(): View {
        //dd($this->data);
        return view('exports.report', [
            'data' => $this->data,
            'risk' => $this->risk
        ]);
    }

    /**
     * @return string
     */
    public function title(): string {
        return 'Reporte';
    }

    public function styles(Worksheet $sheet) {
        // Show/hide gridlines when printing
        $columnRisk = $this->risk ? 'Q': 'N';

        $sheet->setShowGridlines(false);
        foreach(range('C','E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        foreach(range('F', $columnRisk) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(false);
            $sheet->getColumnDimension($columnID)->setWidth(30);
        }
        // title styles
        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00003e52'],
                ],
            ],
        ];
        $sheet->getStyle('C2:'.$columnRisk.'2')->applyFromArray($styleArray);
        $styleArray = [
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00003e52'],
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
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
        $sheet->getStyle('C3:'.$columnRisk.'3')->applyFromArray($styleArray);
        // other rows stiles
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00003e52'],
                ]
            ], 
        ];
        $i = 0;

        foreach ($this->data as $e) {
            $row = 4 + $i;
            $sheet->getStyle('C'.$row.':'.$columnRisk.''.$row)->getFont()->setSize(8);
            $sheet->getStyle('C'.$row.':'.$columnRisk.''.$row)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);
            $sheet->getStyle('C'.$row.':'.$columnRisk.''.$row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle('C'.$row.':E'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle('F'.$row.':H'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_JUSTIFY);
            $sheet->getStyle('I'.$row.':K'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle('L'.$row.':N'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_JUSTIFY);
            if ($this->risk) {
                $sheet->getStyle('O'.$row.':'.$columnRisk.''.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            }
            $sheet->getRowDimension($row)->setRowHeight(100);
            $i++;
        }
    }
}
