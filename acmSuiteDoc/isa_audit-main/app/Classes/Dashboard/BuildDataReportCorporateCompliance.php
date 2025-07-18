<?php

namespace App\Classes\Dashboard;

use App\Classes\Dashboard\Service\DashboardCompliance;
use App\Classes\Dashboard\BuildStructure;
use App\Classes\Dashboard\Data\UtilitiesDashboard;
use App\Models\V2\Admin\Address;
use App\Models\V2\Audit\ProcessAudit;
use App\Services\QuickChartService;
use App\Traits\V2\EvaluateValueTrait;
use App\Traits\V2\RequirementTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BuildDataReportCorporateCompliance 
{
  use RequirementTrait, EvaluateValueTrait;

  protected $global;
  protected $action;
  protected $recordsTasks;
  protected $recordsTasksGrpuped;
  protected $historical;
  protected $charts;
  protected $structure;
  public $corporateName = '';
  protected $process;

  /**
   * @param int $idAuditProcess
   * @param int $idAplicabilityRegister
   * @param int $idAuditRegister
   */
  public function __construct($idAuditProcess, $idAplicabilityRegister, $idAuditRegister)
  {
    $dashboard = new DashboardCompliance();
    $this->global = $dashboard->getDataComplianceCorporate($idAuditProcess, $idAplicabilityRegister, $idAuditRegister);
    $this->action = $dashboard->getDataComplianceActionCorporate($idAuditProcess, $idAplicabilityRegister, $idAuditRegister);
    $this->recordsTasks = $dashboard->getDataActionPlanRecords($idAuditProcess, $idAplicabilityRegister, $idAuditRegister);
    $this->recordsTasksGrpuped = $dashboard->getDataActionPlanRecords($idAuditProcess, $idAplicabilityRegister, $idAuditRegister, true);
    $this->historical = [ 
      'historical_last_years' => $this->global['last_years'], 
      'historical_month' => $this->global['historical_month'],
      'historical_project' => $this->global['historical']
    ];
    $this->charts = new QuickChartService();
    $this->structure = new BuildStructure(); 
    $this->corporateName = $this->global['customer']['corp_tradename'];
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
      
      $sheetThree = $this->getDataSheetThreeAndFour(3);
      if ( !$sheetThree['success'] ) return $sheetThree;
      
      $sheetFour = $this->getDataSheetThreeAndFour(4);
      if ( !$sheetFour['success'] ) return $sheetFour;
      
      $sheetFive = $this->getDataSheetFive();
      if ( !$sheetFive['success'] ) return $sheetFive;
      
      $sheetSix = $this->getDataSheetSix();
      if ( !$sheetSix['success'] ) return $sheetSix;

      $sheetSeven = $this->getDataSheetSeven();
      if ( !$sheetSeven['success'] ) return $sheetSeven;

      $sheetEight = $this->getDataSheetEight();
      if ( !$sheetEight['success'] ) return $sheetEight;

      $sheetNine = $this->getDataSheetNine();
      if ( !$sheetNine['success'] ) return $sheetNine;

      $sheetTen = $this->getDataSheetTen();
      if ( !$sheetTen['success'] ) return $sheetTen;

      $sheetEleven = $this->getDataSheetEleven();
      if ( !$sheetEleven['success'] ) return $sheetEleven;

      $info['sheet_one'] = $sheetOne;
      $info['sheet_two'] = $sheetTwo;
      $info['sheet_three'] = $sheetThree;
      $info['sheet_four'] = $sheetFour;
      $info['sheet_five'] = $sheetFive;
      $info['sheet_six'] = $sheetSix;
      $info['sheet_seven'] = $sheetSeven;
      $info['sheet_eight'] = $sheetEight;
      $info['sheet_nine'] = $sheetNine;
      $info['sheet_ten'] = $sheetTen;
      $info['sheet_eleven'] = $sheetEleven;
      
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
      // Bar charts
      $info['compliance']['matters'] = collect($this->global['customer']['matters'])->map(function($matter) {
        $chartStructure = $this->structure->barChartAspects($matter);
        $config = null;
        $config['headers'] = [
          'title' => "Aspectos de {$matter['matter']}", 
          'subtitle' => "{$matter['total']}%"
        ];
        $config['type'] = 'bar-vertical';
        $config['data'] = $chartStructure;
        $config['dimensions'] = ['width' => 450, 'height' => 300];
        $config['overwrite_config'] = [
          ['options.plugins.legend.display', false], 
          ['options.plugins.subtitle.position', 'bottom'],
          ['options.plugins.tickFormat.suffix', ' %'],
        ];
        
        $chart = $this->charts->getShortUrl($config);
        $infoMatter['matter'] = $matter['matter'];
        $infoMatter = array_merge($infoMatter, $chart['chart']);
        return $infoMatter;
      })->toArray();
      
      // Pie chart
      $chartStructure = $this->structure->pieChart($this->global['compliance']);
      $config = null;
      $config['headers'] = [ 'title' => "Cumplimiento EHS {$this->global['year']}" ];
      $config['type'] = 'pie';
      $config['data'] = $chartStructure;
      $config['dimensions'] = ['width' => 350, 'height' => 300];
      $config['overwrite_config'] = [ 
        ['options.plugins.legend.position', 'bottom'],
        ['options.plugins.tickFormat.suffix', ' %'],
      ];
      $chart = $this->charts->getShortUrl($config);
      $info['compliance']['compliance'] = $chart['chart'];
      
      // historical chart last year
      $chartStructure = $this->structure->barMultipleChartLastYearCorporate($this->historical['historical_last_years']);
      $config = null;
      $config['headers'] = [ 'title' => "Histórico Cumplimiento EHS", 'subtitle' => $this->corporateName ];
      $config['type'] = 'bar-vertical';
      $config['data'] = $chartStructure;
      $config['dimensions'] = ['width' => 500, 'height' => 300];
      $chart = $this->charts->getShortUrl($config);
      $info['compliance']['compliance_per_year'] = $chart['chart'];
      
      // historical chart last year per corporate
      $chartStructure = $this->structure->areaChart($this->historical['historical_month']);
      $config = null;
      $config['headers'] = [ 'title' => "Cumplimiento Mensual EHS {$this->global['year']}" ];
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
      $info['compliance']['compliance_monthly'] = $chart['chart'];
      
      if ( !$this->action['with_data'] ) {
        $info['success'] = false;
        $info['message'] = 'No hay información de plan de acción disponible aún';
        return $info;
      }

      // Pie chart
      $chartStructure = $this->structure->pieChart($this->action['compliance']);
      $config = null;
      $config['headers'] = [ 'title' => "Estatus de Plan de Acción" ];
      $config['type'] = 'pie';
      $config['data'] = $chartStructure;
      $config['overwrite_config'] = [ 
        ['options.plugins.legend.position', 'bottom'],
      ];
      $pieChart = $this->charts->getShortUrl($config);
      $info['action']['compliance'] = $pieChart['chart'];
      
      // Bar multiple chart
      $chartStructure = $this->structure->barGroupChartMonthly($this->action['monthly'], $this->action['counts']);
      $config = null;
      $config['headers'] = [ 'title' => "Estatus Mensual" ];
      $config['type'] = 'bar-vertical';
      $config['data'] = $chartStructure;
      $config['dimensions'] = ['width' => 700, 'height' => 300];
      $chart = $this->charts->getShortUrl($config);
      $info['action']['status_matter_monthly'] = $chart['chart'];
      
      // bar chart
      $chartStructure = $this->structure->barChartAllAspectsCritical($this->action['counts']);
      $config = null;
      $config['headers'] = [ 'title' => "Número de Hallazgos del Cumplimiento" ];
      $config['type'] = 'bar-vertical';
      $config['data'] = $chartStructure;
      $config['dimensions'] = ['width' => 700, 'height' => 300];
      $config['overwrite_config'] = [ ['options.plugins.legend.display', false] ];
      $chart = $this->charts->getShortUrl($config);
      $info['action']['obligation_critical_counts'] = $chart['chart'];
      
      // Bar charts
      $info['action']['matters_counts'] = collect($this->action['counts'])->map(function($matter) {
        $infoMatter['matter'] = $matter['matter'];
        $chartStructure = $this->structure->barChartStatusMatter($matter);
        $config = null;
        $config['headers'] = [ 'title' => "Aspectos de {$matter['matter']}" ];
        $config['type'] = 'bar-horizontal';
        $config['data'] = $chartStructure;
        $config['dimensions'] = ['width' => 320, 'height' => 300];
        $config['overwrite_config'] = [
          ['options.plugins.legend.display', false],
          ['options.plugins.scales.x.title.display', false]
        ];
        $chart = $this->charts->getShortUrl($config);
        $infoMatter= array_merge($infoMatter, $chart['chart']);
        return $infoMatter;
      })->toArray();
      
      // Bar charts
      $info['action']['evaluate_risk'] = $this->action['risk']['evaluate_risk'];
      $info['action']['category_risk'] = collect($this->action['risk']['categories'])->map(function($category) {
        $infoCategory['category'] = $category['risk_category'];
        $chartStructure = $this->structure->barChartStatusCategory($category);
        $config = null;
        $config['headers'] = [ 'title' => "Hallzgos", 'subtitle' => "Nivel De Riesgo {$category['risk_category']}" ];
        $config['type'] = 'bar-vertical';
        $config['data'] = $chartStructure;
        $config['dimensions'] = ['width' => 430, 'height' => 300];
        $config['overwrite_config'] = [
          ['options.plugins.legend.display', false],
          ['options.plugins.scales.y.title.display', false]
        ];
        $chart = $this->charts->getShortUrl($config);
        $infoCategory= array_merge($infoCategory, $chart['chart']);
        return $infoCategory;
      })->toArray();
      
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
      $corporateName = $this->corporateName;

      $info['title_sheet'] = "Gráficos Cumplimiento Permisos";
      $info['header'] = "REPORTE | CUMPLIMIENTO EHS {$year}";
      $info['subheader'] = $corporateName;
      // place
      $info['place']['address'] = $this->global['customer']['address'];
      $info['place']['name'] = $corporateName;
      $info['place']['full_path'] = $this->global['customer']['cust_full_path'];
      // matters
      $info['charts']['matters'] = $this->charts['compliance']['matters'];
      // charts
      $info['charts']['compliance'] = $this->charts['compliance']['compliance'];
      $info['charts']['compliance_per_year'] = $this->charts['compliance']['compliance_per_year'];
      $info['charts']['compliance_monthly'] = $this->charts['compliance']['compliance_monthly'];
      
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
      $year = $this->global['year'];
      $info['title_sheet'] = "Cumplimiento ML";
      // title 
      $info['header'] = "REPORTE DE CUMPLIMIENTO EHS";
      $info['subheader'] = $this->corporateName;
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
      $mattersCollection = collect($this->global['customer']['matters']);
      // global totals
      $globalsValues['totals']['title'] = 'CUMPLIMIENTO GLOBAL';
      $globalsValues['totals']['subtitle'] = 'Detalle de Cumplimiento';
      $globalsValues['totals']['total'] = round($mattersCollection->avg('total'), 2);
      // global total counts
      $globalsValues['total_counts']['title'] = 'HALLAZGOS TOTALES';
      $globalsValues['total_counts']['subtitle'] = 'Detalle de hallazgos';
      $globalsValues['total_counts']['total'] = $mattersCollection->sum('total_count');
      // matters
      $info['global'] = $globalsValues;
      $info['matters'] = $this->global['customer']['matters'];
      $info['success'] = true;
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al generar la segunda hoja del reporte';
      return $info;
    }
  }

  private function getDataSheetThreeAndFour($sheet)
  {
    try {
      // title sheet
      $info['title_sheet'] = $sheet == 3 ? 'Cumplimiento Aspectos' : 'No. Hallazgos';
      // title 
      $info['header'] = $sheet == 3 ? 'REPORTE DE CUMPLIMIENTO EHS' : 'No. HALLAZGOS DE AUDITORÍA';
      $info['subheader'] = $this->corporateName;
      $info['description'] = "ASPECTOS POR MATERIA LEGAL";
      // matters
      $info['matters'] = $this->global['customer']['matters'];
      $info['success'] = true;
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al generar la tercera / cuarta hoja del reporte';
      return $info;
    }
  }

  private function getDataSheetFive()
  {
    try {
      // title sheet
      $info['title_sheet'] = "Mensual promedio planta";
      // title 
      $info['header'] = "REPORTE DE CUMPLIMIENTO EHS MENSUAL";
      $info['subheader'] = $this->corporateName;

      $year = $this->global['year'];
      $info['year'] = $year;
      $info['total'] = collect($this->historical['historical_last_years']['years'])->where('year', $year)->first();
      // build struture matters
      $matters = collect($this->historical['historical_project']['matters'])->map(function($matter) {
        $matter['matter'] = Str::upper($matter['matter']);
        return $matter;
      });
      $info['matters'] = $matters;
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
      $year = $this->global['year'];
      // title sheet
      $info['title_sheet'] = "Plan de Acción";
      // title 
      $info['header'] = "ESTATUS PLAN DE ACCIÓN CUMPLIMIENTO EHS {$year}";
      $info['subheader'] = $this->corporateName;
      // place
      $info['place']['address'] = $this->global['customer']['address'];
      $info['place']['name'] = $this->corporateName;
      $info['place']['full_path'] = $this->global['customer']['cust_full_path'];

      $info['charts'] = $this->charts['action'];
      $info['success'] = true;
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al generar la sexta hoja del reporte';
      return $info;
    }
  }

  private function getDataSheetSeven()
  {
    try {
      // title sheet
      $info['title_sheet'] = "Estatus Plan de Acción";
      // title 
      $info['header'] = "REPORTE DE PLAN DE ACCIÓN CUMPLIMIENTO EHS";
      $info['subheader'] = $this->corporateName;
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
      $info['headers']['period'] = $this->process->dates_format;
      $info['headers']['evaluate_risk'] = $this->process->evaluate_risk;
      $matters = collect($this->action['project']['matters']);
      $info['headers']['aspects'] = $matters->flatMap(fn($item) => $item['aspects'])->pluck('aspect')->join(', ');
      // for requirement table
      $info['action_plan']['header_table_requirement'] = 'Detalles de estatus de plan de acción';
      $info['action_plan']['header_requirements'] = 'Estatus de Plan de acción';
      // for task table
      $recordsTasks = collect($this->action['matter_action_tasks'])->first();
      $info['action_plan']['header_table_tasks'] = $recordsTasks['count_status'];
      $info['action_plan']['header_tasks'] = 'Estatus de Tareas de Plan de acción de Cumplimiento EHS';
      // for same table
      $mattersTaskCollect = collect( $this->action['matter_action_tasks'] );
      $mattersReqCollect = collect( $this->action['matter_action_requirement'] );
      $matters = $mattersReqCollect->map(function($matter) use ($mattersTaskCollect) {
        $currentMatter = $mattersTaskCollect->where('id_matter', $matter['id_matter'])->first();
        $matterTmp['id_matter'] = $matter['id_matter'];
        $matterTmp['matter'] = $matter['matter'];
        $matterTmp['total'] = $matter['total'];
        $matterTmp['count_status'] = $currentMatter['count_status'];
        return $matterTmp;
      }); 
      $info['action_plan']['matters'] = $matters;
      $info['success'] = true;
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al generar la septima hoja del reporte';
      return $info;
    }
  }

  private function getDataSheetEight()
  {
    try {
      // title sheet
      $info['title_sheet'] = 'Mensual promedio planta Plan de acción';
      // title 
      $info['header'] = 'ESTATUS MENSUAL DE PLAN DE ACCIÓN: CUMPLIMIENTO EHS';
      $info['subheader'] = $this->corporateName;
      // matters
      $year = $this->action['year'];
      $info['year'] = $year;
      $info['total'] = collect($this->action['historical_last_years']['years'])->where('year', $year)->first();
      // build struture matters
      $matters = collect($this->action['historical_project']['matters'])->map(function($matter) {
        $matter['matter'] = Str::upper($matter['matter']);
        return $matter;
      });
      $info['matters'] = $matters;

      $info['success'] = true;
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al generar la octava hoja del reporte';
      return $info;
    }
  }

  private function getDataSheetNine()
  {
    try {
      // title sheet
      $info['title_sheet'] = 'Tareas Plan de Acción';
      // title 
      $info['header'] = 'TAREAS DE PLAN DE ACCIÓN DE CUMPLIMIENTO EHS';
      $info['subheader'] = $this->corporateName;
      $info['matters'] = $this->recordsTasksGrpuped['records'];

      $info['success'] = true; 
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al generar la novena hoja del reporte';
      return $info;
    }
  }


  private function getDataSheetTen()
  {
    try {
      // title sheet
      $info['title_sheet'] = 'Estatus Aspectos Plan de Acción';
      // title 
      $info['header'] = 'REPORTE DE PLAN DE ACCIÓN DE CUMPLIMIENTO EHS';
      $info['subheader'] = $this->corporateName;
      $info['description'] = "ASPECTOS POR MATERIA LEGAL";
      // matters
      $info['matters'] = $this->action['counts'];
      $info['success'] = true;
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al generar la decima hoja del reporte';
      return $info;
    }
  }

  private function getDataSheetEleven()
  {
    try {
      // title sheet
      $info['title_sheet'] = 'Reporte de Plan de Acción';
      // title 
      $info['header'] = 'REPORTE DE TAREAS  DE PLAN DE ACCIÓN DE CUMPLIMIENTO EHS';
      $info['subheader'] = $this->corporateName;
      // records tasks
      $recordsFormat = (new UtilitiesDashboard())->getRecordFormatTasks($this->recordsTasks['records']);
      $info['records'] = $recordsFormat;
      $info['success'] = true;

      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al generar la primera decima hoja del reporte';
      return $info;
    }
  }
}