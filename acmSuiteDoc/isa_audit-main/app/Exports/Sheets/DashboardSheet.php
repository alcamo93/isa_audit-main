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

// use Maatwebsite\Excel\Concerns\WithCharts;
// use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
// use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
// use PhpOffice\PhpSpreadsheet\Chart\Layout;
// use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
// use PhpOffice\PhpSpreadsheet\Chart\Title;
// use PhpOffice\PhpSpreadsheet\Chart\Chart;

class DashboardSheet implements FromView, WithTitle, WithDrawings, WithStyles, ShouldAutoSize
{
    use Exportable; 

    private $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function view(): View {
        return view('exports.dashboard', [
            'data' => $this->data
        ]);
    }
    /**
     * @return string
     */
    public function title(): string {
        return 'Dashboard';
    }

    public function drawings() {
        $draws = [];
        
        $logo = new Drawing();
        $logo->setName('Logo');
        $logo->setDescription('ISA Ambiental Logo');
        $logo->setPath(public_path('logos/acm_suite_logo_h.png'));
        $logo->setHeight(80);
        $logo->setCoordinates('F2');

        array_push($draws, $logo);
        
        $i = 0;
        foreach ($this->data['matters'] as $e) {
            $row = 14 + $i;
            $imgStyleAudit = new Drawing();
            $imgStyleAudit->setName($e['matter']);
            $imgStyleAudit->setDescription('Logo '.$e['matter']);
            $imgStyleAudit->setPath($e['img']);
            $imgStyleAudit->setHeight(80);
            $imgStyleAudit->setCoordinates('C'.$row);
            
            $imgStyleAction = new Drawing();
            $imgStyleAction->setName($e['matter']);
            $imgStyleAction->setDescription('Logo '.$e['matter']);
            $imgStyleAction->setPath($e['img']);
            $imgStyleAction->setHeight(80);
            $imgStyleAction->setCoordinates('N'.$row);

            array_push($draws, $imgStyleAudit, $imgStyleAction);
            $i = $i + 2;
        }

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
        $sheet->getStyle('F2:S2')->applyFromArray($styleArray);

        // other rows stiles
        $styleArray = [
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
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
        foreach ($this->data['matters'] as $e) {
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
        $sheet->getStyle('D10:S10')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('D10:S10')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('C12:U12')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('C12:U12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        
        $i = 0;
        foreach ($this->data['matters'] as $e) {
            $row = 14 + $i;
            $sheet->getStyle('I'.$row.':J'.$row)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);;
            $sheet->getStyle('T'.$row.':U'.$row)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);;
            $sheet->getStyle('C'.$row.':U'.$row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle('C'.$row.':U'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $i = $i + 2;
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