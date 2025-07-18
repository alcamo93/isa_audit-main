<?php

namespace App\Exports\Sheets;

use App\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

// excel styles
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// image loader
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ActionPlanSheet implements FromView, WithTitle, WithDrawings, WithStyles
{
    use Exportable; 

    private $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function view(): View {
        return view('exports.action', [
            'data' => $this->data
        ]);
    }
    
    /**
     * @return string
     */
    public function title(): string {
        return 'Hallazgos';
    }

    public function drawings() {
        $logo = new Drawing();
        $logo->setName('Logo');
        $logo->setDescription('ISA Ambiental Logo');
        $logo->setPath(public_path('logos/acm_suite_logo_h.png'));
        $logo->setHeight(80);
        $logo->setCoordinates('F2');

        $draws = [$logo];

        return $draws;
    }

    public function styles(Worksheet $sheet) {
        // Show/hide gridlines when printing
        $sheet->setShowGridlines(false);
        //  title
        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => ['argb' => '00003e52'],
                ],
            ],
        ];
        // underlined title
        $sheet->getStyle('F2:L2')->applyFromArray($styleArray);
        // other rows styles
        $styleArray = [
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00003e52'],
                ],
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00003e52'],
                ],
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00003e52'],
                ],
            ],
        ];
        // border all but right
        $i = 0;
        foreach ($this->data as $key => $m) {
            foreach ($m['aspects'] as $a) {
                $row = 6 + $i;
                $sheet->getStyle('C'.$row.':N'.$row)->applyFromArray($styleArray);
                $sheet->getRowDimension($row)->setRowHeight(20);
                if ( end($m['aspects']) ) {
                    $sheet->getStyle('C'.($row+1).':N'.($row+1))->applyFromArray($styleArray);
                }
                $i++;
            }
            $i = $i + 2;
        }
    }
}