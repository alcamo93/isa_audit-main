<?php

namespace App\Exports\Dashboard\ObligationCorporate\Sheets;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use App\Exports\Template\Style;
use App\Exports\Template\Variable;
use Illuminate\Support\Str;
use App\Traits\V2\ReportExportTrait;

class SheetOne implements FromView, WithTitle, WithDrawings, WithStyles
{
	use Exportable, ReportExportTrait;

	private $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function view(): View
	{
		return view('exports.dashboard.obligationCorporate.sheets.sheetOne', [
			'data' => $this->data
		]);
	}

	/**
	 * @return string
	 */
	public function title(): string
	{
		return $this->data['title_sheet'];
	}

	public function drawings()
	{
    $draws = [];

    $logo = new Drawing();
		$logo->setName(Variable::IMAGE_LOGO_NAME);
		$logo->setDescription(Variable::IMAGE_LOGO_DESCRIPTION);
		$logo->setPath( public_path(Variable::IMAGE_PATH) );
		$logo->setHeight(Variable::IMAGE_SIZE_LOGO); 
		$logo->setCoordinates('K1');
    $logo->setOffsetY(15);
    array_push($draws, $logo);
    
    $imageRemotePlace = $this->data['place']['full_path'];
    $contains = Str::contains($imageRemotePlace, ['http:', 'localhost', '127.0.0.1']);
    $imageExist = $this->checkImageExists($imageRemotePlace);
    if ( $imageExist && !$contains && !is_null($imageRemotePlace) && $imagePlace = imagecreatefromstring(file_get_contents($imageRemotePlace) ) ) {
      $place = new MemoryDrawing();
      $place->setName($this->data['place']['name']);
      $place->setDescription($this->data['place']['name']);
      $place->setImageResource($imagePlace);
      $place->setHeight(Variable::IMAGE_SIZE_LOGO_CORPORATE);
      $place->setCoordinates('C6');
      $place->setOffsetY(15);
      // $place->setOffsetX(30);
      array_push($draws, $place);
    }

    $matters = $this->data['charts']['matters'];
    $numberGroupColumn = 7;
    $columns = collect()->range('I', 'V');
    $columnPerGroup = $columns->chunk($numberGroupColumn)->map(fn($item) => $item->values())->map(fn($item) => $item[0]);
    
    $row = 5;
    $itemPerRow = 1;
    foreach ($matters as $index => $matter) {
      if ($index == 2) {
        $imageRemote = $this->data['charts']['compliance'];
        if (!is_null($imageRemote) && $image = imagecreatefromstring(file_get_contents($imageRemote['url']) ) ) {
          $chart = new MemoryDrawing();
          $chart->setName($imageRemote['title']);
          $chart->setDescription($imageRemote['title']);
          $chart->setImageResource($image);
          $chart->setHeight(Variable::IMAGE_SIZE_CHART);
          $chart->setCoordinates("B{$row}");
          $chart->setOffsetY(20);
          $chart->setOffsetX(20);
          array_push($draws, $chart);
        }
      }

      $columnSelected = $itemPerRow - 1;
      
      $imageRemote = $matter;
      if (!is_null($imageRemote) && $image = imagecreatefromstring(file_get_contents($imageRemote['url']) ) ) {
        $chart = new MemoryDrawing();
        $chart->setName($imageRemote['title']);
        $chart->setDescription($imageRemote['title']);
        $chart->setImageResource($image);
        $chart->setHeight(Variable::IMAGE_SIZE_CHART);
        $chart->setCoordinates("$columnPerGroup[$columnSelected]{$row}");
        $chart->setOffsetY(20);
        $chart->setOffsetX(20);
        array_push($draws, $chart);
      }
      $row = $itemPerRow == 2 ? $row + 17 : $row;
      $itemPerRow = $itemPerRow == 2 ? 1 : $itemPerRow + 1;
    }

    $columns = collect()->range('B', 'V');
    $numberGroupColumn = intval($columns->count() / 2);
    $space = $numberGroupColumn + 1;
    $columnPerGroup = $columns->filter(fn($item) => $item != $columns[$space])->chunk($numberGroupColumn)
      ->map(fn($item) => $item->values())->map(fn($item) => $item[0]);

    $imageRemote = $this->data['charts']['compliance_per_year'];
    if (!is_null($imageRemote) && $image = imagecreatefromstring(file_get_contents($imageRemote['url']) ) ) {
      $chartOne = new MemoryDrawing();
      $chartOne->setName($imageRemote['title']);
      $chartOne->setDescription($imageRemote['title']);
      $chartOne->setImageResource($image);
      $chartOne->setHeight(Variable::IMAGE_SIZE_CHART);
      $chartOne->setCoordinates("$columnPerGroup[0]{$row}");
      $chartOne->setOffsetY(20);
      $chartOne->setOffsetX(20);
      array_push($draws, $chartOne);
    }

    $imageRemote = $this->data['charts']['compliance_monthly'];
    if (!is_null($imageRemote) && $image = imagecreatefromstring(file_get_contents($imageRemote['url']) ) ) {
      $chartOne = new MemoryDrawing();
      $chartOne->setName($imageRemote['title']);
      $chartOne->setDescription($imageRemote['title']);
      $chartOne->setImageResource($image);
      $chartOne->setHeight(Variable::IMAGE_SIZE_CHART);
      $chartOne->setCoordinates("$columnPerGroup[1]{$row}");
      $chartOne->setOffsetY(20);
      $chartOne->setOffsetX(20);
      array_push($draws, $chartOne);
    }

		return $draws;
	}

	public function styles(Worksheet $sheet)
	{
		// Show/hide gridlines when printing
		$sheet->setShowGridlines(false);
		// logo
		$sheet->getRowDimension(1)->setRowHeight(60);
    // header
    $sheet->getRowDimension(2)->setRowHeight(45);
    $sheet->getRowDimension(3)->setRowHeight(45);
		$sheet->getStyle('B2:V3')->applyFromArray(Style::STYLE_TITLE_DOCUMENT)->applyFromArray(Style::STYLE_BORDER_MAIN);
    // place and logo
    $sheet->getRowDimension(5)->setRowHeight(30); // title card
    $sheet->getRowDimension(20)->setRowHeight(30); // address or porcentage
    $sheet->getRowDimension(5)->setRowHeight($this->calculateRowHeight($sheet, 'B5:G5'));
    $sheet->getRowDimension(20)->setRowHeight($this->calculateRowHeight($sheet, 'B20:G20'));
    // styles border place and logo
    $rowInit = 5;
    $heightInRows = 15;
    $rowRepit = 3;
    for ($i=0; $i < $rowRepit ; $i++) { 
      $currentRow = $rowInit;
      $lastRow = $rowInit + $heightInRows;
      if ($i == 2) {
        $sheet->getStyle("B{$currentRow}:J$lastRow")->applyFromArray(Style::STYLE_BORDER_MAIN)->applyFromArray(Style::STYLE_CARD_HEADERS);
        $sheet->getStyle("L{$currentRow}:V$lastRow")->applyFromArray(Style::STYLE_BORDER_MAIN)->applyFromArray(Style::STYLE_CARD_HEADERS);
        break;
      }
      $sheet->getStyle("B{$currentRow}:G$lastRow")->applyFromArray(Style::STYLE_BORDER_MAIN)->applyFromArray(Style::STYLE_CARD_HEADERS);
      $sheet->getStyle("I{$currentRow}:V$lastRow")->applyFromArray(Style::STYLE_BORDER_MAIN)->applyFromArray(Style::STYLE_CARD_HEADERS);
      $rowInit = $rowInit + $heightInRows + 2;
    }
	}
}
