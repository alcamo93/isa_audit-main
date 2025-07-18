<?php

namespace App\Classes\Dashboard;

use App\Classes\Dashboard\Service\DashboardCompliance;
use App\Classes\Dashboard\BuildStructure;
use App\Models\V2\Admin\Address;
use App\Models\V2\Audit\ProcessAudit;
use App\Services\QuickChartService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BuildDataReportGlobalCompliance 
{
  protected $global;
  protected $historical;
  protected $historicalMonth;
  protected $charts;
  protected $structure;
  public $customerName = '';
  protected $process;

  /**
   * @param int $idCustomer
   */
  public function __construct($idCustomer)
  {
    $dashboard = new DashboardCompliance();
    $this->global = $dashboard->getDataByYearCompliance($idCustomer);
    $this->historical = $dashboard->getComplianceDataHistoricalCustomer($idCustomer);
    $this->charts = new QuickChartService();
    $this->structure = new BuildStructure(); 
    $this->customerName = $this->global['customer']['cust_tradename'];
    $relationships = [
      'corporate.industry', 'auditors.person', 'scope', 
      'corporate.addresses.country','corporate.addresses.state','corporate.addresses.city',
      'corporate.addresses' => fn($query) => $query->where('type', Address::PHYSICAL)
    ];
    $this->process = ProcessAudit::with($relationships)->find($this->global['customer']['id_audit_processes']);
  }

  public function getDataReport()
  {
    try {
      $this->charts = $this->generateCharts();
      if ( !$this->charts['success'] ) return $this->charts;

      $sheetOne = $this->getDataSheetOne();
      if ( !$sheetOne['success'] ) return $sheetOne;

      $sheetTwo = $this->getDataSheetTwo();
      if ( !$sheetTwo['success'] ) return $sheetTwo;

      $sheetThree = $this->getDataSheetThree();
      if ( !$sheetThree['success'] ) return $sheetThree;

      $sheetFour = $this->getDataSheetFour();
      if ( !$sheetFour['success'] ) return $sheetFour;

      $fiveSheet = $this->getDataSheetFiveAndSix(5);
      if ( !$fiveSheet['success'] ) return $fiveSheet;

      $sixSheet = $this->getDataSheetFiveAndSix(6);
      if ( !$sixSheet['success'] ) return $sixSheet;

      $sevenSheet = $this->getDataSevenSheet();
      if ( !$sevenSheet['success'] ) return $sevenSheet;
      
      $info['sheet_one'] = $sheetOne;
      $info['sheet_two'] = $sheetTwo;
      $info['sheet_three'] = $sheetThree;
      $info['sheet_four'] = $sheetFour;
      $info['sheet_five'] = $fiveSheet;
      $info['sheet_six'] = $sixSheet;
      $info['sheet_seven'] = $sevenSheet;
      
      $info['success'] = true;
      $info['message'] = 'Datos de reporte generados';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Error, no se pudo obtener datos para el reporte';
      return $info;
    }
  }

  private function generateCharts()
  {
    try {
      if ( !$this->global['with_data'] ) {
        $info['success'] = false;
        $info['message'] = 'No hay información disponible aún';
        return $info;
      }
      
      // Pie chart
      $chartStructure = $this->structure->pieChart($this->global['global_compliance']);
      $config = null;
      $config['headers'] = [ 'title' => "CUMPLIMIENTO EHS {$this->global['year']}" ];
      $config['type'] = 'pie';
      $config['data'] = $chartStructure;
      $config['overwrite_config'] = [
        ['options.plugins.legend.position', 'bottom'],
        ['options.plugins.tickFormat.suffix', ' %'],
      ];
      $chart = $this->charts->getShortUrl($config);
      $info['compliance_global'] = $chart['chart'];

      // Multiple bar chart
      $chartStructure = $this->structure->barMultipleChartMatters($this->global['global_projects']);
      $config = null;
      $config['headers'] = [ 'title' => "CUMPLIMIENTO EHS {$this->global['year']}", 'subtitle' => 'POR PLANTAS' ];
      $config['type'] = 'bar-vertical';
      $config['data'] = $chartStructure;
      $config['dimensions'] = ['width' => 700, 'height' => 300];
      $config['overwrite_config'] = [
        ['options.plugins.tickFormat.suffix', ' %'],
      ];
      $chart = $this->charts->getShortUrl($config);
      $info['compliance_global_per_corporate'] = $chart['chart'];

      // line area chart
      $chartStructure = $this->structure->areaChart($this->global['global_historical_month']);
      $config = null;
      $config['headers'] = [ 'title' => "CUMPLIMIENTO EHS MENSUAL {$this->global['year']}" ];
      $config['type'] = 'line-vertical';
      $config['data'] = $chartStructure;
      $config['dimensions'] = ['width' => 700, 'height' => 300];
      $config['overwrite_config'] = [
        ['options.plugins.legend.display', false],
        ['options.plugins.tickFormat.suffix', ' % '],
        ['options.plugins.datalabels.color', 'gray'],
        ['options.plugins.datalabels.align', 'end'],
      ];
      $chart = $this->charts->getShortUrl($config);
      $info['compliance_global_monthly'] = $chart['chart'];

      if ( !$this->historical['with_data'] ) {
        $info['success'] = false;
        $info['message'] = 'No hay información historica disponible aún';
        return $info;
      }

      // historical chart last year
      $chartStructure = $this->structure->barMultipleChartLastYear($this->historical['last_years']);
      $config = null;
      $config['headers'] = [ 'title' => "CUMPLIMIENTO EHS GLOBAL", 'subtitle' => 'REGISTRO ANUAL' ];
      $config['type'] = 'bar-vertical';
      $config['data'] = $chartStructure;
      $config['overwrite_config'] = [
        ['options.plugins.tickFormat.suffix', ' %'],
      ];
      $chart = $this->charts->getShortUrl($config);
      $info['compliance_global_per_year'] = $chart['chart'];

      // historical chart last year per corporate
      $chartStructure = $this->structure->barMultipleChartCorporateYear($this->historical['global_projects']);
      $config = null;
      $config['headers'] = [ 'title' => "CUMPLIMIENTO EHS POR PLANTA", 'subtitle' => 'REGISTRO ANUAL' ];
      $config['type'] = 'bar-vertical';
      $config['data'] = $chartStructure;
      $config['dimensions'] = ['width' => 700, 'height' => 300];
      $config['overwrite_config'] = [
        ['options.plugins.tickFormat.suffix', ' %'],
      ];
      $chart = $this->charts->getShortUrl($config);
      $info['compliance_global_year_per_corporate'] = $chart['chart'];

      $info['success'] = true;
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al generar gráficos';
      return $info;
    }
  }

  private function getDataSheetOne()
  {
    try {
      // title
      $year = $this->global['year'];
      $info['title_sheet'] = "Grafico Global EHS $year";
      $info['main']['header'] = "CUMPLIMIENTO GLOBAL EHS $year";
      // place
      $info['main']['place']['address'] = $this->global['customer']['address'];
      $info['main']['place']['name'] = $this->global['customer']['cust_tradename'];
      $info['main']['place']['full_path'] = $this->global['customer']['cust_full_path'];
      // matters
      $info['main']['matters'] = $this->global['global_matters'];
      // charts
      $info['main']['charts']['compliance_global'] = $this->charts['compliance_global'];
      $info['main']['charts']['compliance_global_per_corporate'] = $this->charts['compliance_global_per_corporate'];
      $info['main']['charts']['compliance_global_monthly'] = $this->charts['compliance_global_monthly'];
      // title historical
      $info['historical']['header'] = 'CUMPLIMIENTO GLOBAL EHS';
      $info['historical']['subheader'] = 'HISTÓRICO';
      // charts
      $info['historical']['charts']['compliance_global_per_year'] = $this->charts['compliance_global_per_year'];
      $info['historical']['charts']['compliance_global_year_per_corporate'] = $this->charts['compliance_global_year_per_corporate'];

      $info['success'] = true;
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al generar la primera hoja del reporte';
      return $info;
    }
  }

  private function getDataSheetTwo()
  {
    try {
      // title sheet
      $info['title_sheet'] = "Histórico Cumplimiento EHS";
      // title
      $info['header'] = 'CUMPLIMIENTO GLOBAL EHS';
      $info['subheader'] = 'HISTÓRICO';
      // charts
      $info['charts']['compliance_global_per_year'] = $this->charts['compliance_global_per_year'];
      $info['charts']['compliance_global_year_per_corporate'] = $this->charts['compliance_global_year_per_corporate'];

      $info['success'] = true;
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al generar la segunda hoja del reporte';
      return $info;
    }
  }

  private function getDataSheetThree()
  {
    try {
      // title sheet
      $year = $this->global['year'];
      $info['title_sheet'] = "Cumplimiento Global EHS ML";
      // title 
      $info['header'] = 'Reporte de Cumplimiento Global';
      $info['subheader'] = "EHS {$year}";
      // headers
      $timezone = Config('enviroment.time_zone_carbon');
      $today = Carbon::now($timezone)->format('d-m-Y');
      $corporate = $this->process->corporate;
      $info['headers']['corp_tradename'] = $corporate->corp_tradename_format;
      $info['headers']['corp_trademark'] = $corporate->corp_trademark_format;
      $info['headers']['rfc'] = $corporate->rfc;
      $info['headers']['status'] = '---';
      $info['headers']['industry'] = $corporate->industry->industry;
      $info['headers']['users'] = $this->process->auditors->where('pivot.leader', 1)->pluck('person.full_name')->join(', ');
      $info['headers']['scope'] = $this->process->scope->scope;
      $address = $corporate->addresses->first();
      $info['headers']['street'] = $address['street'];
      $info['headers']['suburb'] = $address['suburb'];
      $info['headers']['city'] = $address['city']['city'];
      $info['headers']['state'] = $address['state']['state'];
      $info['headers']['country'] = $address['country']['country'];
      $info['headers']['date'] = $today;
      $info['headers']['evaluate_risk'] = $this->process->evaluate_risk;
      $mattersCollection = collect($this->global['global_matters']);
      // global totals
      $globalsValues['totals']['title'] = 'CUMPLIMIENTO GLOBAL EHS';
      $globalsValues['totals']['subtitle'] = 'Detalle de Cumplimiento';
      $globalsValues['totals']['total'] = round($mattersCollection->avg('total'), 2);
      // global total counts
      $globalsValues['total_counts']['title'] = 'HALLAZGOS | RECOMENDACIONES';
      $globalsValues['total_counts']['subtitle'] = 'Detalle de Hallazgos | Recomendaciones';
      $globalsValues['total_counts']['total'] = $mattersCollection->sum('total_count');
      // matters
      $info['global'] = $globalsValues;
      $info['matters'] = $this->global['global_matters'];
      $info['success'] = true;
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al generar la tercer hoja del reporte';
      return $info;
    }
  }

  private function getDataSheetFour()
  {
    try {
      // title sheet
      $info['title_sheet'] = "Cumplimiento Global EHS Aspecto";
      // title 
      $info['header'] = 'CUMPLIMIENTO GLOBAL EHS';
      $info['subheader'] = "ASPECTOS DE MATERIA LEGAL";
      // matters
      $matters = collect($this->global['global_matters'])->map(function($matter) {
        $matter['matter'] = Str::upper($matter['matter']);
        return $matter;
      });
      $info['matters'] = $matters->toArray(); 
      $info['success'] = true;
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al generar la cuarta hoja del reporte';
      return $info;
    }
  }

  private function getDataSheetFiveAndSix($numberSheet)
  {
    try {
      if ($numberSheet == 5) {
        $info['title_sheet'] = "No. Hallazgos por planta mensual";
        $info['header'] = 'No. HALLAZGOS DE CUMPLIMIENTO EHS';
        $info['subheader'] = "MENSUAL POR PLANTA";
      } 

      if ($numberSheet == 6) {
        $info['title_sheet'] = "Mensual por planta";
        $info['header'] = 'CUMPLIMIENTO EHS MENSUAL';
        $info['subheader'] = "POR PLANTA";
      }

      // historicals
      $year = $this->historical['year'];
      $projects = $this->historical['global_projects'];
      // get data per project
      $monthsKeys = collect([]);
      $historicals = collect([]);
      foreach ($projects as $project) {
        $currentYear = collect($project['years'])->firstWhere('year', $year);
        $inItem['name'] = $project['corp_tradename'];
        $inItem['year'] = $year;
        $inItem['total'] = $currentYear['total'];
        $inItem['total_count'] = $currentYear['total_count'];
        $months = collect($currentYear['months'])->groupBy('key')->map(fn($item) => $item->first());
        $inItem = array_merge($inItem, $months->toArray());
        $monthsKeys->push($months);
        $historicals->push($inItem);
      }
      // get data global
      $global['name'] = 'Total Global';
      $global['year'] = $year;
      $global['total'] = round($historicals->avg('total'), 2);
      $global['total_count'] = $historicals->sum('total_count');
      $monthsKeys = $monthsKeys->collapse()->map(function($item) {
        $tmp = $item; $tmp['total'] = 0; $tmp['total_count'] = 0;
        return $tmp;
      })->map(function($item) use ($historicals) {
        $valuesPerMonth = $historicals->pluck($item['key']);
        $tmp = $item;
        $tmp['total'] = $valuesPerMonth->avg('total');
        $tmp['total_count'] = $valuesPerMonth->sum('total_count');
        return $tmp;
      });

      $globalMonths = $monthsKeys->groupBy('key')->map(fn($item) => $item->first())->toArray();
      $global = array_merge($global, $globalMonths);
      $historicals->push($global);

      $info['historical'] = $historicals->toArray();
      $info['success'] = true;
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al generar la quinta y sexta hoja del reporte';
      return $info;
    }
  }

  private function getDataSevenSheet()
  {
    try {
      // title sheet
      $info['title_sheet'] = "Mensual por planta ML";
      // title 
      $info['header'] = 'CUMPLIMIENTO EHS MENSUAL POR PLANTA';
      $info['subheader'] = "MATERIAS LEGALES";
      // matters
      $projects = $this->historical['matters_projects'];
      $year = $this->historical['year'];
      $monthsKeys = collect([]);
      $info['matters'] = collect($this->global['global_matters'])->map(function($matter) use ($year, $projects, $monthsKeys) {
        $historicals = collect( $projects )->map(function($project) use ($year, $matter, $monthsKeys) {
          $inItem['name'] = $project['corp_tradename'];
          $inItem['matter'] = $matter['matter'];
          $inItem['year'] = $year;
          $currentMatter = collect($project['matters'])->firstWhere('id_matter', $matter['id_matter']);
          $months = collect($currentMatter['months'])->groupBy('key')->map(fn($item) => $item->first());
          $monthsKeys->push($months);
          $inItem = array_merge($inItem, $months->toArray());
          return $inItem;
        });

        $global['name'] = 'Promedio';
        $global['matter'] = $matter['matter'];
        $global['year'] = $year;
        $global['total'] = round($historicals->avg('total'), 2);
        $global['total_count'] = $historicals->sum('total_count');

        $monthsKeys = $monthsKeys->collapse()->map(function($item) {
          $tmp = $item; $tmp['total'] = 0; $tmp['total_count'] = 0;
          return $tmp;
        })->map(function($item) use ($historicals) {
          $valuesPerMonth = $historicals->pluck($item['key']);
          $tmp = $item;
          $tmp['total'] = $valuesPerMonth->avg('total');
          $tmp['total_count'] = $valuesPerMonth->sum('total_count');
          return $tmp;
        });

        $globalMonths = $monthsKeys->groupBy('key')->map(fn($item) => $item->first())->toArray();
        $global = array_merge($global, $globalMonths);
        $historicals->push($global);

        $matter['historical'] = $historicals;
        $matter['matter'] = Str::upper($matter['matter']);
        return $matter;
      })->toArray();

      $info['success'] = true;
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al generar la septima hoja del reporte';
      return $info;
    }
  }
}