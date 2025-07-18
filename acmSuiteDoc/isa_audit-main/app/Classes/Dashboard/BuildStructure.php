<?php

namespace App\Classes\Dashboard;

use Illuminate\Support\Arr;

class BuildStructure
{
	public function __construct()
	{
    //  
  }

  /**
   * @param mixed $data
   * @return array 
   */
  public function pieChart($data)
  {
    $info['labels'] = Arr::pluck($data, 'label');
		$info['series'] = [
      [
        'data' => Arr::pluck($data, 'total'),
        'backgroundColor' => Arr::pluck($data, 'color'),
        'borderColor' => '#fff',
        'borderWidth' => 0.2
      ]
    ];

    return $info;
  }

  /**
   * @param mixed $data
   * @return array 
   */
  public function barChartAspects($data)
  {
    $matter = (gettype($data) == 'array') ? collect($data) : $data;
    $aspects = (gettype($matter['aspects']) == 'array') ? collect($matter['aspects']) : $matter['aspects'];
    $info['colors'] = $matter['color'];
    $info['labels'] = $aspects->map(fn($aspect) => $aspect['aspect'])->toArray();
    $info['series'] = [
      [
        'label' => "Aspectos de {$matter['matter']}",
        'backgroundColor' => $aspects->map(fn($aspect) => $aspect['color'])->toArray(),
        'data' => $aspects->map(fn($aspect) => $aspect['total'])->toArray(),
      ]
    ];

    return $info;
  }

  /**
   * @param mixed $data
   * @return array 
   */
  public function barMultipleChartMatters($data)
  {
    $projectsCollect = collect($data);
    $globalMatters = $projectsCollect->pluck('matters')->collapse()->map(fn($matter) => ([ 
        'id_matter' => $matter['id_matter'], 'matter' => $matter['matter'], 
        'full_path' => $matter['full_path'], 'color' => $matter['color'] ])
      )->unique();

    $info['colors'] = $globalMatters->pluck('color')->toArray();
    $info['labels'] = $projectsCollect->map(fn($item) => "{$item['corp_tradename']} ({$item['total']}%)")->toArray();
    $info['series'] = $globalMatters->map(function($matter) use ($projectsCollect) {
      return [
        'label' => $matter['matter'],
        'backgroundColor' => $matter['color'],
        'data' => $projectsCollect->map(function($project) use ($matter) {
          $matterCurrent = collect($project['matters'])->where('id_matter', $matter['id_matter'])->first();
          return is_null($matterCurrent) ? 0 : $matterCurrent['total'];
        })->toArray()
      ];
    })->toArray();

    return $info;
  }

  /**
   * @param mixed $data
   * @return array 
   */
  public function areaChart($data)
  {
    $absolutes = collect($data)->map(fn($item) => floor($item['total']));
    $info['labels'] = Arr::pluck($data, 'key');
    $info['series'] = [
      [
        'label' => "Mensual",
        'backgroundColor' => '#4eaf8f',
        'data' => $absolutes,
        'borderWidth' => 1,
        "tension" => 0,
        "fill" => true,
      ]
    ];
    return $info;
  }

  /**
   * @param mixed $data
   * @return array 
   */
  public function barMultipleChartLastYear($data)
  {
    $years = collect($data);
    $info['colors'] = $years->map(fn($item) => $item['color'])->toArray();
    $info['labels'] = ['3 últimos años'];
    $info['series'] = $years->map(function($item) use ($years)  {
      return [
        'label' => $item['year'],
        'backgroundColor' => $item['color'],
        'data' => $years->filter(fn($itemInYear) => $itemInYear['year'] == $item['year'])
          ->values()->map(fn($itemInYear) => $itemInYear['total'] )->toArray()
      ];
    })->toArray();
    
    return $info;
  }

  /**
   * @param mixed $data
   * @return array 
   */
  public function barMultipleChartCorporateYear($data)
  {
    $projectsCollect = collect($data);
    $uniqueYears = $projectsCollect->pluck('years')->collapse()->map(fn($matter) => 
      ([ 'year' => $matter['year'], 'color' => $matter['color'] ])
    )->unique();

    $info['colors'] = $uniqueYears->pluck('color')->toArray();
    $info['labels'] = $projectsCollect->pluck('corp_tradename')->toArray();
    $info['series'] = $uniqueYears->map(function($year) use ($projectsCollect) {
      return [
        'label' => $year['year'],
        'backgroundColor' => $year['color'],
        'data' => $projectsCollect->map(function($project) use ($year) {
          $yearCurrent = collect($project['years'])->where('year', $year['year'])->first();
          return $yearCurrent['total'];
        })->toArray()
      ];
    })->toArray();
    
    return $info;
  }

  public function barMultipleChartLastYearCorporate($data)
  {
    $years = collect($data['years']);
    $info['colors'] = $years->map(fn($item) => $item['color'])->toArray();
    $info['labels'] = ['3 últimos años'];
    $info['series'] = $years->map(function($item) use ($years)  {
      return [
        'label' => $item['year'],
        'backgroundColor' => $item['color'],
        'data' => $years->filter(fn($itemInYear) => $itemInYear['year'] == $item['year'])
          ->values()->map(fn($itemInYear) => $itemInYear['total'] )->toArray()
      ];
    })->toArray();
    
    return $info;
  }

  public function barGroupChartMonthly($monthly, $matters)
  {
    $matters = collect($matters);
    $monthly = collect($monthly);

    $info['labels'] = $monthly->pluck('name')->toArray();
    $info['series'] = $matters->map(function($matter) use ($monthly) {
      return [
        'label' => $matter['matter'],
        'backgroundColor' => $matter['color'],
        'data' => $monthly->map(function($month) use ($matter) {
          $currentMonth = isset($month['historical_total']) ? collect([]) : $month['historical_total'];
          $find = $currentMonth->where(fn($item) => $item['matter_id'] == $matter['id_matter'])->first();
          return is_null($find) ? 0 : $find['total'];
        })->toArray()
      ];
    })->toArray();
   
    return $info;
  }

  public function barChartAllAspectsCritical($matters)
  {
    $matters = collect($matters);
    $aspects = $matters->flatMap(fn($matter) => $matter['aspects']);

    $info['labels'] = $aspects->pluck('aspect')->toArray();
    $info['series'] = [
      [
        'label' => 'Aspectos',
        'backgroundColor' => $aspects->map(fn($aspect) => $aspect['color']),
        'data' => $aspects->map(fn($aspect) => $aspect['total_critical'])
      ]
    ];
   
    return $info;
  }

  public function barChartStatusMatter($matter)
  {
    $status = collect($matter['count_status']);
    
    $info['labels'] = $status->pluck('status')->toArray();
    $info['series'] = [
      [
        'label' => "Estatus de Hallazgos {$matter['matter']}",
        'backgroundColor' => $status->map(fn($status) => $status['color_hexadecimal'])->toArray(),
        'data' => $status->map(fn($status) => $status['count'])->toArray()
      ]
    ];
    
    return $info;
  }

  public function barChartStatusCategory($category)
  {
    $interpretations = collect($category['interpretations']);
    
    $info['labels'] = $interpretations->pluck('interpretation')->toArray();
    $info['series'] = [
      [
        'label' => "Nivel de riesgo {$category['risk_category']}",
        'backgroundColor' => $interpretations->map(fn($interpretation) => $interpretation['color'])->toArray(),
        'data' => $interpretations->map(fn($interpretation) => $interpretation['count'])->toArray()
      ]
    ];
    
    return $info;
  }
}