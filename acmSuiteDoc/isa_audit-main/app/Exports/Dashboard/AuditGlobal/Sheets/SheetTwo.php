<?php

namespace App\Exports\Dashboard\AuditGlobal\Sheets;

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

class SheetTwo implements FromView, WithTitle, WithDrawings, WithStyles
{
	use Exportable;

	private $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function view(): View
	{
		return view('exports.dashboard.auditGlobal.sheets.sheetTwo', [
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

    $imageRemote = $this->data['charts']['compliance_global_per_year'];
    if (!is_null($imageRemote) && $image = imagecreatefromstring(file_get_contents($imageRemote['url']) ) ) {
      $chartOne = new MemoryDrawing();
      $chartOne->setName($imageRemote['title']);
      $chartOne->setDescription($imageRemote['title']);
      $chartOne->setImageResource($image);
      $chartOne->setHeight(Variable::IMAGE_SIZE_CHART);
      $chartOne->setCoordinates('B4');
      $chartOne->setOffsetY(20);
      $chartOne->setOffsetX(20);
      array_push($draws, $chartOne);
    }

    $imageRemote = $this->data['charts']['compliance_global_year_per_corporate'];
    if (!is_null($imageRemote) && $image = imagecreatefromstring(file_get_contents($imageRemote['url']) ) ) {
      $chartTwo = new MemoryDrawing();
      $chartTwo->setName($imageRemote['title']);
      $chartTwo->setDescription($imageRemote['title']);
      $chartTwo->setImageResource($image);
      $chartTwo->setHeight(Variable::IMAGE_SIZE_CHART);
      $chartTwo->setCoordinates('K4');
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
    $sheet->getRowDimension(2)->setRowHeight(60);
		$sheet->getStyle('B2:U2')->applyFromArray(Style::STYLE_TITLE_DOCUMENT)->applyFromArray(Style::STYLE_BORDER_MAIN);
    
    $sheet->getStyle('B4:I20')->applyFromArray(Style::STYLE_BORDER_MAIN);
    $sheet->getStyle('K4:U20')->applyFromArray(Style::STYLE_BORDER_MAIN);
	}
}
