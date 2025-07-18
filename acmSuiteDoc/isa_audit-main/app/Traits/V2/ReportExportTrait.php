<?php

namespace App\Traits\V2;

use Illuminate\Http\Response;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

trait ReportExportTrait 
{
  /**
   * @param string $url
   */
  private function checkImageExists($url) 
  {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $httpCode === Response::HTTP_OK;
  }

  /**
   * Calcula la altura de la fila en función del contenido.
   *
   * @param Worksheet $sheet
   * @param string $mergedCells
   * @return float
   */
  private function calculateRowHeight(Worksheet $sheet, string $mergedCells): float
  {
    $initialColumns = collect(explode(':', $mergedCells));
    $cellValue = $sheet->getCell($initialColumns->first())->getValue();
    $onlyLetters = $initialColumns->map(fn($item) => str_split($item)[0]);
    $columns = range($onlyLetters->first(), $onlyLetters->last());
    $totalWidth = 0;
    $defaultWidth = 8.33;

    foreach ($columns as $column) {
      $columnWidth = $sheet->getColumnDimension($column)->getWidth();
      $totalWidth += ($columnWidth == -1) ? $defaultWidth : $columnWidth;
    }
    
    $fontSize = 10;
    $lineHeight = 2;
    
    $averageCharWidth = 0.9;
    $maxCharsPerLine = $totalWidth / $averageCharWidth;
    $lines = ceil(strlen($cellValue) / $maxCharsPerLine);

  
    $rowHeight = $lines * $fontSize * $lineHeight;

    return $rowHeight;
  }

  /**
   * @param string $initLetter
   * @param int $size
   */
  private function defineColumn($initLetter, $size)
  {
    $letters = collect(range('A', 'Z'));
    $groups = $letters
      ->filter(fn($letter) => $letter >= $initLetter)
      ->values()
      ->chunk($size + 1)
      ->map(fn($group) => $group->take($size))
      ->filter(fn($group) => $group->count() === $size)
      ->values()
      ->filter(fn($group) => $group->count() == $size);

    return $groups;
  }

}