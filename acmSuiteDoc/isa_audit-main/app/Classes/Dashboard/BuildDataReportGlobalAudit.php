<?php

namespace App\Classes\Dashboard;

use App\Classes\Dashboard\Service\DashboardAudit;
use App\Classes\Dashboard\BuildStructure;
use App\Models\V2\Admin\Address;
use App\Models\V2\Audit\ProcessAudit;
use App\Services\QuickChartService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BuildDataReportGlobalAudit 
{
  protected $global;
  protected $actionPlan;
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
    $dashboard = new DashboardAudit();
    $this->global = $dashboard->getDataByYearAudit($idCustomer);
    $this->historical = $dashboard->getAuditDataHistoricalCustomer($idCustomer);
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

      $fiveSheet = $this->getDataSheetFive();
      if ( !$fiveSheet['success'] ) return $fiveSheet;

      $sixSheet = $this->getDataSheetSix();
      if ( !$sixSheet['success'] ) return $sixSheet;
      
      $info['sheet_one'] = $sheetOne;
      $info['sheet_two'] = $sheetTwo;
      $info['sheet_three'] = $sheetThree;
      $info['sheet_four'] = $sheetFour;
      $info['sheet_five'] = $fiveSheet;
      $info['sheet_six'] = $sixSheet;
      
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
      $config['headers'] = [ 'title' => "Resultados De Auditorías {$this->global['year']}" ];
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
      $config['headers'] = [ 'title' => "Resultados De Auditorías {$this->global['year']}", 'subtitle' => 'Por Plantas' ];
      $config['type'] = 'bar-vertical';
      $config['data'] = $chartStructure;
      $config['dimensions'] = ['width' => 700, 'height' => 300];
      $config['overwrite_config'] = [
        ['options.plugins.tickFormat.suffix', ' %'],
      ];
      $chart = $this->charts->getShortUrl($config);
      $info['compliance_global_per_corporate'] = $chart['chart'];

      if ( !$this->historical['with_data'] ) {
        $info['success'] = false;
        $info['message'] = 'No hay información historica disponible aún';
        return $info;
      }

      // historical chart last year
      $chartStructure = $this->structure->barMultipleChartLastYear($this->historical['last_years']);
      $config = null;
      $config['headers'] = [ 'title' => "AUDITORÍAS RESULTADO GLOBAL", 'subtitle' => 'REGISTRO ANUAL' ];
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
      $config['headers'] = [ 'title' => "AUDITORÍAS RESULTADOS POR PLANTA", 'subtitle' => 'REGISTRO ANUAL' ];
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
      $info['title_sheet'] = "Gráficos Auditorías";
      $info['main']['header'] = "Auditorías $year";
      // place
      $info['main']['place']['address'] = $this->global['customer']['address'];
      $info['main']['place']['name'] = $this->global['customer']['cust_tradename'];
      $info['main']['place']['full_path'] = $this->global['customer']['cust_full_path'];
      // matters
      $info['main']['matters'] = $this->global['global_matters'];
      // charts
      $info['main']['charts']['compliance_global'] = $this->charts['compliance_global'];
      $info['main']['charts']['compliance_global_per_corporate'] = $this->charts['compliance_global_per_corporate'];
      // title historical
      $info['historical']['header'] = 'AUDITORÍAS';
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
      $info['title_sheet'] = "Histórico Auditorías";
      // title
      $info['header'] = 'AUDITORÍAS';
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
      $info['title_sheet'] = "Resultado Global de Auditorías";
      // title 
      $info['header'] = 'REPORTE DE RESULTADOS GLOBAL';
      $info['subheader'] = "AUDITORÍAS {$year}";
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
      $globalsValues['totals']['title'] = 'CUMPLIMIENTO GLOBAL';
      $globalsValues['totals']['subtitle'] = 'Detalle de Cumplimiento';
      $globalsValues['totals']['total'] = round($mattersCollection->avg('total'), 2);
      // global total counts
      $globalsValues['total_counts']['title'] = 'HALLAZGOS TOTALES';
      $globalsValues['total_counts']['subtitle'] = 'Detalle de Hallazgos';
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
      $info['title_sheet'] = "Resultados Auditorías G Aspecto";
      // title 
      $info['header'] = 'RESULTADOS GLOBAL DE AUDITORÍAS';
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

  private function getDataSheetFive()
  {
    try {
      // title sheet
      $info['title_sheet'] = "Resultados por planta";
      // title 
      $info['header'] = 'RESULTADOS DE AUDITORÍAS';
      $info['subheader'] = "POR PLANTA";
      // projects
      $year = $this->global['year'];
      $projects = $this->global['global_projects'];
      $mattersColumns = collect([]);
      $records = collect($projects)->map(function($project) use ($year, $mattersColumns) {
        $inItem['name'] = $project['corp_tradename'];
        $inItem['year'] = $year;
        $inItem['total'] = $project['total'];
        $inItem['total_count'] = $project['total_count'];
        $mattersCollection = collect($project['matters']);
        $mattersColumnsName = $mattersCollection->map(function($item) {
          return ['key' => "matter_{$item['id_matter']}", 'matter' => $item['matter']];
        });
        $mattersColumns->push($mattersColumnsName);
        $matters = $mattersCollection->pluck('id_matter')->map(function($item) use ($mattersCollection) {
          $content = $mattersCollection->firstWhere('id_matter', $item);
          return ["matter_{$item}" => $content];
        })->collapse()->toArray();
        $inItem = array_merge($inItem, $matters);
        return $inItem;
      });

      $mattersColumns = $mattersColumns->unique()->collapse();
      $global['name'] = 'Promedio (Global)';
      $global['year'] = $year;
      $global['total'] = round($records->avg('total'), 2);
      $global['total_count'] = $records->sum('total_count');
      $globalMatters = $mattersColumns->map(function($item) use ($records) {
        $info[$item['key']]['total'] = round( $records->pluck($item['key'])->avg('total') );
        $info[$item['key']]['total_count'] = $records->pluck($item['key'])->sum('total_count');
        return $info;
      })->collapse();
      $global = array_merge($global, $globalMatters->toArray());
      $records->push($global);

      $info['projects'] = $records;
      $info['name_matters'] = $mattersColumns;
      $info['success'] = true;
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al generar la quinta hoja del reporte';
      return $info;
    }
  }

  private function getDataSheetSix()
  {
    try {
      // title sheet
      $info['title_sheet'] = "No. Hallazgos por planta";
      // title 
      $info['header'] = 'No. HALLAZGOS DE AUDITORÍA';
      $info['subheader'] = "";

      $mattersCollect = collect($this->global['global_matters']);
      $projectsCollect = collect($this->global['global_projects']);
      $columnsName = collect([]);
      $info['matters'] = $mattersCollect->map(function($matter) use ($columnsName, $projectsCollect) {
        $inMatter = $matter;
        $valuesPerAspects = collect($matter['aspects'])->map(function($aspect) use ($matter, $columnsName, $projectsCollect) {
          $inAspect = $aspect;
          $valuesPerProject = $projectsCollect->map(function($project) use ($matter, $aspect, $columnsName) {
            $keyProject = "project_{$project['id_audit_processes']}";
            $inProject['key'] = $keyProject;
            $inProject['name'] = $project['corp_tradename'];
            $columnsName->push([ 'key' => $keyProject, 'name' => $project['corp_tradename'] ]);
            $foundMatter = collect($project['matters'])->firstWhere('id_matter', $matter['id_matter']);
            $foundAspect = !isset($foundMatter['aspects']) ? collect($foundMatter['aspects'])->firstWhere('id_aspect', $aspect['id_aspect']) : collect([]);
            $inProject['total'] = $foundAspect['total'] ?? 0;
            $inProject['total_count'] = $foundAspect['total_count'] ?? 0;
            return [$keyProject => $inProject];
          })->collapse();
          $keyProjectAspect = 'project_0';
          $inProjectTotal[$keyProjectAspect]['key'] = $keyProjectAspect;
          $inProjectTotal[$keyProjectAspect]['name'] = 'Total';
          $inProjectTotal[$keyProjectAspect]['total'] = round($valuesPerProject->avg('total'), 2);
          $inProjectTotal[$keyProjectAspect]['total_count'] = $valuesPerProject->sum('total_count');
          $valuesPerProject = array_merge($valuesPerProject->toArray(), $inProjectTotal);
          $inAspect['values'] = $valuesPerProject;
          return $inAspect;
        })->toArray();
        $columnsNewAspect = $columnsName->unique()->push([ 'key' => 'project_0', 'name' => 'Total' ])->values();
        $totalsPerAspects = $columnsNewAspect->map(function($column) use ($valuesPerAspects) {
          $values = collect($valuesPerAspects)->map(fn($recordAspect) => $recordAspect['values'][ $column['key'] ]);
          $key = $values->pluck('key')->first();
          return [$key => [
            'key' => $key,
            'name' => $values->pluck('name')->first(),
            'total' => round($values->avg('total'), 2),
            'total_count' => $values->sum('total_count')
          ]];
        })->collapse()->values()->groupBy('key')->map(fn($group) => $group->first())->toArray();
        $inAspectTotal['aspect'] = 'Total';
        $inAspectTotal['values'] = $totalsPerAspects;
        array_push($valuesPerAspects, $inAspectTotal);
        $inMatter['aspects'] = $valuesPerAspects;
        $inMatter['matter'] = Str::upper($matter['matter']);
        return $inMatter;
      })->toArray();
      $info['columns_name'] = $columnsName->unique()->push([ 'key' => 'project_0', 'name' => 'Total' ])->values()->toArray();
      $info['success'] = true;
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al generar la sexta hoja del reporte';
      return $info;
    }
  }
}