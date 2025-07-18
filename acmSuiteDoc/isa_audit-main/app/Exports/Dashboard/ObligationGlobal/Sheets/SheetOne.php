<?php

namespace App\Exports\Dashboard\ObligationGlobal\Sheets;

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
		return view('exports.dashboard.obligationGlobal.sheets.sheetOne', [
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
    
    $imageRemotePlace = $this->data['main']['place']['full_path'];
    $contains = Str::contains($imageRemotePlace, ['http:', 'localhost', '127.0.0.1']);
    $imageExist = $this->checkImageExists($imageRemotePlace);
    if ( $imageExist && !$contains && !is_null($imageRemotePlace) && $imagePlace = imagecreatefromstring( file_get_contents($imageRemotePlace) ) ) {
      $place = new MemoryDrawing();
      $place->setName($this->data['main']['place']['name']);
      $place->setDescription($this->data['main']['place']['name']);
      $place->setImageResource($imagePlace);
      $place->setHeight(Variable::IMAGE_SIZE_LOGO_CUSTOMER);
      $place->setCoordinates('C6');
      $place->setOffsetY(5);
      $place->setOffsetX(10);
      array_push($draws, $place);
    }
    
    $matters = $this->data['main']['matters'];
    $columns = collect()->range('G', 'V');
    $numberGroupColumn = intval($columns->count() / sizeof($matters));
    $columnPerGroup = $columns->chunk($numberGroupColumn)->map(fn($item) => $item->values());
    foreach ($matters as $index => $matter) {
      // $imageMatter = @imagecreatefromstring(file_get_contents($matter['full_path']));
      $imageMatter = public_path($matter['image']);
      $currentColumn = $columnPerGroup[$index][1];
      if ( $imageMatter && !is_null($imageMatter) ) {
        $place = new Drawing();
        $place->setName($matter['matter']);
        $place->setDescription($matter['matter']);
        $place->setPath($imageMatter);
        $place->setHeight(Variable::IMAGE_SIZE_LOGO_CUSTOMER);
        $place->setCoordinates("{$currentColumn}6");
        $place->setOffsetY(10);
        $place->setOffsetX(10);
        array_push($draws, $place);
      }
    }

    $imageRemote = $this->data['main']['charts']['compliance_global'];
    if (!is_null($imageRemote) && $image = imagecreatefromstring(file_get_contents($imageRemote['url']) ) ) {
      $chartOne = new MemoryDrawing();
      $chartOne->setName($imageRemote['title']);
      $chartOne->setDescription($imageRemote['title']);
      $chartOne->setImageResource($image);
      $chartOne->setHeight(Variable::IMAGE_SIZE_CHART);
      $chartOne->setCoordinates('B9');
      $chartOne->setOffsetY(20);
      $chartOne->setOffsetX(20);
      array_push($draws, $chartOne);
    }

    $imageRemote = $this->data['main']['charts']['compliance_global_per_corporate'];
    if (!is_null($imageRemote) && $image = imagecreatefromstring(file_get_contents($imageRemote['url']) ) ) {
      $chartTwo = new MemoryDrawing();
      $chartTwo->setName($imageRemote['title']);
      $chartTwo->setDescription($imageRemote['title']);
      $chartTwo->setImageResource($image);
      $chartTwo->setHeight(Variable::IMAGE_SIZE_CHART);
      $chartTwo->setCoordinates('K9');
      $chartTwo->setOffsetY(20);
      $chartTwo->setOffsetX(20);
      array_push($draws, $chartTwo);
    }

    $imageRemote = $this->data['main']['charts']['compliance_global_monthly'];
    if (!is_null($imageRemote) && $image = imagecreatefromstring(file_get_contents($imageRemote['url']) ) ) {
      $chartThree = new MemoryDrawing();
      $chartThree->setName($imageRemote['title']);
      $chartThree->setDescription($imageRemote['title']);
      $chartThree->setImageResource($image);
      $chartThree->setHeight(Variable::IMAGE_SIZE_CHART_LARGE);
      $chartThree->setCoordinates('F27');
      $chartThree->setOffsetY(20);
      $chartThree->setOffsetX(20);
      array_push($draws, $chartThree);
    }

    $imageRemote = $this->data['historical']['charts']['compliance_global_per_year'];
    if (!is_null($imageRemote) && $image = imagecreatefromstring(file_get_contents($imageRemote['url']) ) ) {
      $chartOne = new MemoryDrawing();
      $chartOne->setName($imageRemote['title']);
      $chartOne->setDescription($imageRemote['title']);
      $chartOne->setImageResource($image);
      $chartOne->setHeight(Variable::IMAGE_SIZE_CHART);
      $chartOne->setCoordinates('B48');
      $chartOne->setOffsetY(20);
      $chartOne->setOffsetX(20);
      array_push($draws, $chartOne);
    }

    $imageRemote = $this->data['historical']['charts']['compliance_global_year_per_corporate'];
    if (!is_null($imageRemote) && $image = imagecreatefromstring(file_get_contents($imageRemote['url']) ) ) {
      $chartTwo = new MemoryDrawing();
      $chartTwo->setName($imageRemote['title']);
      $chartTwo->setDescription($imageRemote['title']);
      $chartTwo->setImageResource($image);
      $chartTwo->setHeight(Variable::IMAGE_SIZE_CHART);
      $chartTwo->setCoordinates('K48');
      $chartTwo->setOffsetY(20);
      $chartTwo->setOffsetX(20);
      array_push($draws, $chartTwo);
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
    $sheet->getRowDimension(6)->setRowHeight(85); // image
    $sheet->getRowDimension(7)->setRowHeight(25); // address or porcentage
    $sheet->getRowDimension(5)->setRowHeight($this->calculateRowHeight($sheet, 'B5:E5'));
    $sheet->getRowDimension(7)->setRowHeight($this->calculateRowHeight($sheet, 'B7:E7'));
    // styles border place and logo
    $sheet->getStyle('B5:E7')->applyFromArray(Style::STYLE_BORDER_MAIN)->applyFromArray(Style::STYLE_CARD_CUSTOMER);
    $sheet->getStyle('G5:V7')->applyFromArray(Style::STYLE_BORDER_MAIN)->applyFromArray(Style::STYLE_CARD_HEADERS);
    // Borders charts
    $sheet->getStyle('B9:I25')->applyFromArray(Style::STYLE_BORDER_MAIN);
    $sheet->getStyle('K9:V25')->applyFromArray(Style::STYLE_BORDER_MAIN);
    $sheet->getStyle('B27:V43')->applyFromArray(Style::STYLE_BORDER_MAIN);
    // historical header
    $sheet->getStyle('B45:V46')->applyFromArray(Style::STYLE_TITLE_DOCUMENT)->applyFromArray(Style::STYLE_BORDER_MAIN);
    $sheet->getStyle('B48:I64')->applyFromArray(Style::STYLE_BORDER_MAIN);
    $sheet->getStyle('K48:V64')->applyFromArray(Style::STYLE_BORDER_MAIN);
	}
}
