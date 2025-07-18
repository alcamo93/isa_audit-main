<?php

namespace App\Classes\Dashboard;

use App\Classes\Dashboard\Service\DashboardAudit;
use App\Classes\Dashboard\BuildStructure;
use App\Models\V2\Admin\Address;
use App\Models\V2\Audit\Audit;
use App\Models\V2\Audit\ProcessAudit;
use App\Services\QuickChartService;
use App\Traits\V2\EvaluateValueTrait;
use App\Traits\V2\RequirementTrait;
use Illuminate\Support\Carbon;

class BuildDataReportCorporateAudit 
{
  use RequirementTrait, EvaluateValueTrait;

  protected $global;
  protected $action;
  protected $records;
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
    $dashboard = new DashboardAudit();
    $this->global = $dashboard->getDataAuditCorporate($idAuditProcess, $idAplicabilityRegister, $idAuditRegister, true);
    // $this->action = $dashboard->getDataAuditActionCorporate($idAuditProcess, $idAplicabilityRegister, $idAuditRegister);
    $this->historical = [ 'last_years' => $this->global['last_years'] ];
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

      $info['sheet_one'] = $sheetOne;
      $info['sheet_two'] = $sheetTwo;
      $info['sheet_three'] = $sheetThree;
      $info['sheet_four'] = $sheetFour;
      $info['sheet_five'] = $sheetFive;
      $info['sheet_six'] = $sheetSix;
      
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
      $info['audit']['matters'] = collect($this->global['customer']['matters'])->map(function($matter) {
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
      $config['headers'] = [ 'title' => "Resultados de Auditoría {$this->global['year']}" ];
      $config['type'] = 'pie';
      $config['data'] = $chartStructure;
      $config['dimensions'] = ['width' => 350, 'height' => 300];
      $config['overwrite_config'] = [ 
        ['options.plugins.legend.position', 'bottom'],
        ['options.plugins.tickFormat.suffix', ' %'],
      ];
      $chart = $this->charts->getShortUrl($config);
      $info['audit']['compliance'] = $chart['chart'];
      
      // historical chart last year
      $chartStructure = $this->structure->barMultipleChartLastYearCorporate($this->historical['last_years']);
      $config = null;
      $config['headers'] = [ 'title' => "Históricos Auditorías" ];
      $config['type'] = 'bar-vertical';
      $config['data'] = $chartStructure;
      $config['dimensions'] = ['width' => 900, 'height' => 300];
      $config['overwrite_config'] = [ 
        ['options.plugins.tickFormat.suffix', ' %'],
      ];
      $chart = $this->charts->getShortUrl($config);
      $info['audit']['compliance_per_year'] = $chart['chart'];
      
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

      $info['title_sheet'] = "Gráficos Auditoría";
      $info['header'] = "REPORTE | AUDITORÍA {$year}";
      $info['subheader'] = $corporateName;
      // place
      $info['place']['address'] = $this->global['customer']['address'];
      $info['place']['name'] = $corporateName;
      $info['place']['full_path'] = $this->global['customer']['corp_full_path'];
      // matters
      $info['charts']['matters'] = $this->charts['audit']['matters'];
      // charts
      $info['charts']['compliance'] = $this->charts['audit']['compliance'];
      $info['charts']['compliance_per_year'] = $this->charts['audit']['compliance_per_year'];
      
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
      $info['title_sheet'] = "Resultados Auditoría";
      // title 
      $info['header'] = "REPORTE DE RESULTADOS DE AUDITORÍA {$year}";
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
      $info['header'] = $sheet == 3 ? 'REPORTE DE RESULTADOS DE AUDITORÍA' : 'No. HALLAZGOS DE AUDITORÍA';
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
      $info['title_sheet'] = "Reporte de auditoría";
      // title 
      $info['header'] = "REPORTE DE AUDITORÍA";
      $info['subheader'] = $this->corporateName;

      $year = $this->global['year'];
      $info['year'] = $year;
     
      $records = $this->global['records']->map(function($record) {
        $build['matter'] = $this->getFieldRequirement($record, 'matter');
        $build['aspect'] = $this->getFieldRequirement($record, 'aspect');
        $build['no_requirement'] = $this->getFieldRequirement($record, 'no_requirement');
        $build['requirement'] = $this->getFieldRequirement($record, 'requirement');
        $build['description'] = $this->getFieldRequirement($record, 'description');
        $build['evidence'] = $this->getFieldRequirement($record, 'evidence_document');
        $legal = $this->defineLegal($record);
        $build['link_legal'] = $legal['link_legal'];
        $build['legals_name'] = $legal['legals_name'];
        $answer = $this->defineAnswer($record, true);
        $build['answer'] = $answer['label'];
        $build['finding'] = $this->defineFinding($record, true);
        return $build;
      });

      $info['success'] = true;
      $info['records'] = $records;
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
      $info['title_sheet'] = "Reporte de hallazgos";
      // title 
      $info['header'] = "REPORTE DE HALLAZGOS DE AUDITORÍA";
      $info['subheader'] = $this->corporateName;
      
      $records = $this->global['records']->whereNotNull('audit')->where('audit.answer', Audit::NEGATIVE)->map(function($record) {
        $build['matter'] = $this->getFieldRequirement($record, 'matter');
        $build['aspect'] = $this->getFieldRequirement($record, 'aspect');
        $build['no_requirement'] = $this->getFieldRequirement($record, 'no_requirement');
        $build['requirement'] = $this->getFieldRequirement($record, 'requirement');
        $build['description'] = $this->getFieldRequirement($record, 'description');
        $build['finding'] = $this->defineFinding($record, true);
        $legal = $this->defineLegal($record);
        $build['link_legal'] = $legal['link_legal'];
        $build['legals_name'] = $legal['legals_name'];
        $build['risk'] = $this->defineRisk($record, true);
        return $build;
      });

      $info['success'] = true;
      $info['records'] = $records;
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al generar la sexta hoja del reporte';
      return $info;
    }
  }
}