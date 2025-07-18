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

class SheetSix implements FromView, WithTitle, WithDrawings, WithStyles
{
	use Exportable, ReportExportTrait;

	private $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function view(): View
	{
		return view('exports.dashboard.obligationCorporate.sheets.sheetSix', [
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
		$logo->setOffsetX(15);
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
      $place->setCoordinates('C23');
      $place->setOffsetY(15);
      $place->setOffsetX(30);
      array_push($draws, $place);
    }

		$imageRemote = $this->data['charts']['compliance'];
    if (!is_null($imageRemote) && $image = imagecreatefromstring(file_get_contents($imageRemote['url']) ) ) {
      $chartOne = new MemoryDrawing();
      $chartOne->setName($imageRemote['title']);
      $chartOne->setDescription($imageRemote['title']);
      $chartOne->setImageResource($image);
      $chartOne->setHeight(Variable::IMAGE_SIZE_CHART);
      $chartOne->setCoordinates("B5");
      $chartOne->setOffsetY(20);
      $chartOne->setOffsetX(20);
      array_push($draws, $chartOne);
    }

		$imageRemote = $this->data['charts']['status_matter_monthly'];
    if (!is_null($imageRemote) && $image = imagecreatefromstring(file_get_contents($imageRemote['url']) ) ) {
      $chartOne = new MemoryDrawing();
      $chartOne->setName($imageRemote['title']);
      $chartOne->setDescription($imageRemote['title']);
      $chartOne->setImageResource($image);
      $chartOne->setHeight(Variable::IMAGE_SIZE_CHART);
      $chartOne->setCoordinates("K5");
      $chartOne->setOffsetY(20);
      $chartOne->setOffsetX(20);
      array_push($draws, $chartOne);
    }

		$imageRemote = $this->data['charts']['obligation_critical_counts'];
    if (!is_null($imageRemote) && $image = imagecreatefromstring(file_get_contents($imageRemote['url']) ) ) {
      $chartOne = new MemoryDrawing();
      $chartOne->setName($imageRemote['title']);
      $chartOne->setDescription($imageRemote['title']);
      $chartOne->setImageResource($image);
      $chartOne->setHeight(Variable::IMAGE_SIZE_CHART);
      $chartOne->setCoordinates("K22");
      $chartOne->setOffsetY(20);
      $chartOne->setOffsetX(20);
      array_push($draws, $chartOne);
    }

    $matters = $this->data['charts']['matters_counts'];
    $initLetter = 'B';
    $lastLetter = 'V';
    $columns = collect()->range($initLetter, $lastLetter);
    $numberGroupColumn = intval($columns->count() / sizeof($matters));
    $columnPerGroup = $this->defineColumn($initLetter, $numberGroupColumn);

		foreach ($matters as $index => $matter) {
      $firstColumn = $columnPerGroup[$index]->first();
      if (!is_null($matter) && $image = imagecreatefromstring(file_get_contents($matter['url']) ) ) {
        $chartOne = new MemoryDrawing();
        $chartOne->setName($matter['title']);
        $chartOne->setDescription($matter['title']);
        $chartOne->setImageResource($image);
        $chartOne->setHeight(Variable::IMAGE_SIZE_CHART);
        $chartOne->setCoordinates("{$firstColumn}39");
        $chartOne->setOffsetY(20);
        $chartOne->setOffsetX(20);
        array_push($draws, $chartOne);
      }
		}

    $evaluateRisk = $this->data['charts']['evaluate_risk'];
    if ( !$evaluateRisk ) return $draws;

    $risks = $this->data['charts']['category_risk'];
    $initLetter = 'B';
    $lastLetter = 'V';
    $columns = collect()->range($initLetter, $lastLetter);
    $numberGroupColumn = intval($columns->count() / sizeof($risks));
    $columnPerGroup = $this->defineColumn($initLetter, $numberGroupColumn);
		foreach ($risks as $index => $risk) {
      $firstColumn = $columnPerGroup[$index]->first();
      if (!is_null($risk) && $image = imagecreatefromstring(file_get_contents($risk['url']) ) ) {
        $chartOne = new MemoryDrawing();
        $chartOne->setName($risk['title']);
        $chartOne->setDescription($risk['title']);
        $chartOne->setImageResource($image);
        $chartOne->setHeight(Variable::IMAGE_SIZE_CHART);
        $chartOne->setCoordinates("{$firstColumn}56");
        $chartOne->setOffsetY(20);
        $chartOne->setOffsetX(20);
        array_push($draws, $chartOne);
      }
		}
    
		return $draws;
	}

	public function styles(Worksheet $sheet)
	{
		// Show/hide gridlines when printing
		$sheet->setShowGridlines(false);
    // header
		$sheet->getRowDimension(1)->setRowHeight(60);
    $sheet->getRowDimension(2)->setRowHeight(70);
		$sheet->getRowDimension(3)->setRowHeight(45);
		$sheet->getStyle('B2:V3')->applyFromArray(Style::STYLE_TITLE_DOCUMENT)->applyFromArray(Style::STYLE_BORDER_MAIN);
		// border charts
		$sheet->getStyle('B5:I20')->applyFromArray(Style::STYLE_BORDER_MAIN)->applyFromArray(Style::STYLE_CARD_HEADERS);
    $sheet->getStyle('K5:V20')->applyFromArray(Style::STYLE_BORDER_MAIN)->applyFromArray(Style::STYLE_CARD_HEADERS);
		$sheet->getStyle('B22:I37')->applyFromArray(Style::STYLE_BORDER_MAIN)->applyFromArray(Style::STYLE_CARD_HEADERS);
    $sheet->getStyle('K22:V37')->applyFromArray(Style::STYLE_BORDER_MAIN)->applyFromArray(Style::STYLE_CARD_HEADERS);

    $sheet->getRowDimension(22)->setRowHeight($this->calculateRowHeight($sheet, 'B22:I22'));
    $sheet->getRowDimension(37)->setRowHeight($this->calculateRowHeight($sheet, 'B37:I37'));

		$matters = $this->data['charts']['matters_counts'];
    $initLetter = 'B';
    $lastLetter = 'V';
    $columns = collect()->range($initLetter, $lastLetter);
    $numberGroupColumn = intval($columns->count() / sizeof($matters));
    $columnPerGroup = $this->defineColumn($initLetter, $numberGroupColumn);
		foreach ($columnPerGroup as $columnPerGroup) {
      $firstColumn = $columnPerGroup->first();
      $lastColumn = $columnPerGroup->last();
			$sheet->getStyle("{$firstColumn}39:{$lastColumn}54")->applyFromArray(Style::STYLE_BORDER_MAIN);
		}

    $evaluateRisk = $this->data['charts']['evaluate_risk'];
    if ( !$evaluateRisk ) return;

    $risk = $this->data['charts']['category_risk'];
    $initLetter = 'B';
    $lastLetter = 'V';
    $columns = collect()->range($initLetter, $lastLetter);
    $numberGroupColumn = intval($columns->count() / sizeof($risk));
    $columnPerGroup = $this->defineColumn($initLetter, $numberGroupColumn);
		foreach ($columnPerGroup as $columnPerGroup) {
      $firstColumn = $columnPerGroup->first();
      $lastColumn = $columnPerGroup->last();
			$sheet->getStyle("{$firstColumn}56:{$lastColumn}71")->applyFromArray(Style::STYLE_BORDER_MAIN);
		}
	}
}
