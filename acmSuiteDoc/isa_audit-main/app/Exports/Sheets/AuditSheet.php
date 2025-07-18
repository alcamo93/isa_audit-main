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

//use Maatwebsite\Excel\Concerns\WithCharts;
// use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
// use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
// use PhpOffice\PhpSpreadsheet\Chart\Layout;
// use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
// use PhpOffice\PhpSpreadsheet\Chart\Title;
// use PhpOffice\PhpSpreadsheet\Chart\Chart;

class AuditSheet implements FromView, WithTitle, WithDrawings, WithStyles
{
    use Exportable; 

    private $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function view(): View {
        return view('exports.audit', [
            'data' => $this->data
        ]);
    }
    
    /**
     * @return string
     */
    public function title(): string {
        return 'Auditoría';
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
                $row = 7 + $i;
                $sheet->getStyle('C'.$row.':N'.$row)->applyFromArray($styleArray);
                $sheet->getRowDimension($row)->setRowHeight(20);
                if ( end($m['aspects']) ) {
                    $sheet->getStyle('C'.($row+1).':N'.($row+1))->applyFromArray($styleArray);
                }
                $i++;
            }
            $i = $i + 3;
        }
    }


/*
    public function charts()
    {
        $totalData = count($this->data);

        // Set the Data values for each data series we want to plot
        //     Datatype
        //     Cell reference for data
        //     Format Code
        //     Number of datapoints in series
        //     Data values
        //     Data Marker
        //     Custom colors

        $label = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Totales!$A$2:B$2' , null, 1), // label
        ];
        
        $categories = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Totales!$A$4:A$'.(3+$totalData) , null, $totalData), // categories
        ];

        $values = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Totales!$B$4:$B$'.(3+$totalData), null, $totalData), // Values
        ];

        //dd($values);

        // Build the dataseries
        $series = new DataSeries(
            DataSeries::TYPE_BARCHART, // plotType
            null, // plotGrouping (Pie charts don't have any grouping)
            range(0, count($values) - 1), // plotOrder
            $label, // plotLabel
            $categories, // plotCategory
            $values          // plotValues
        );

        $layout = new Layout();
        $layout->setShowVal(true);
        $layout->setShowCatName(false);
        
        $plotArea = new PlotArea($layout, [$series]);

        $title = new Title('Auditoría');

        // Create the chart
        $chart = new Chart(
            'Auditoría', // name
            $title, // title
            null, // legend
            $plotArea, // plotArea
            true, // plotVisibleOnly
            DataSeries::EMPTY_AS_GAP, // displayBlanksAs
            null, // xAxisLabel
            null   // yAxisLabel - Like Pie charts, Donut charts don't have a Y-Axis
        );

        $chart->setTopLeftPosition('F2');
        $chart->setBottomRightPosition('N14');

        return $chart;
    }
*/

}