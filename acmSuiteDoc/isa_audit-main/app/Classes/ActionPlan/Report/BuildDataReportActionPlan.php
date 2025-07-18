<?php

namespace App\Classes\ActionPlan\Report;

use App\Classes\ActionPlan\Report\BuildFilterReportActionPlan;
use App\Classes\Dashboard\Data\DashboardActionPlan;
use App\Classes\Dashboard\Data\UtilitiesDashboard;
use App\Models\V2\Admin\Address;
use App\Models\V2\Audit\ProcessAudit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BuildDataReportActionPlan 
{
  protected $action;
  protected $recordsTasks;
  protected $recordsTasksGrpuped;
  protected $filters;
  public $corporateName = '';
  public $originName = '';
  public $currentYear;

  /**
   * @param int $idActionRegister
   * @param array $filters
   */
  public function __construct($idActionRegister, $filters)
  {
    $actionPlan = new DashboardActionPlan($idActionRegister);
    $this->filters = new BuildFilterReportActionPlan($filters);

    $projectFilter = $this->filters->getProject( $actionPlan->getProject() );
    $this->action['project'] = $projectFilter;
    
    $evaluatedMattersFilter = $this->filters->filterEvaluatedMatters( $projectFilter['matters'] );
    $this->action['counts'] = $evaluatedMattersFilter;
    $this->action['matter_action_requirement'] = $evaluatedMattersFilter;

    $valueTaskGroupedPerMatterFilter = $this->filters->filterEvaluatedMattersTask( $actionPlan->filterEvaluatedMattersTask() );
    $this->action['matter_action_tasks'] = $valueTaskGroupedPerMatterFilter;

    $historicalPerMattersFilter = $this->filters->getHistoricalPerMatters( $actionPlan->getHistoricalPerMatters() );
    $this->action['historical_last_years'] = $actionPlan->getHistoricalLastYears();
    $this->action['historical_project'] = $historicalPerMattersFilter;

    $recordTaskFilter = $this->filters->getRecordsTasks( $actionPlan->getRecords() );
    $this->recordsTasks = $recordTaskFilter;

    $recordTaskGroupedPerMatterFilter = $this->filters->getRecordsPerMatter( $actionPlan->getRecords(true) );
    $this->recordsTasksGrpuped = $recordTaskGroupedPerMatterFilter;

    $this->corporateName = $this->action['project']['corp_tradename'];
    $this->originName = $this->action['project']['origin'];
    $timezone = Config('enviroment.time_zone_carbon');
    $this->currentYear = Carbon::now($timezone)->format('Y');
  }

  public function getDataReport() 
  {
    try {
      $sheetGeneral = $this->getDataSheetGeneral();
      if ( !$sheetGeneral['success'] ) return $sheetGeneral;

      $sheetActionPerAspects = $this->getDataSheetActionPerAspects();
      if ( !$sheetActionPerAspects['success'] ) return $sheetActionPerAspects;

      $sheetActionMonthly = $this->getDataSheetActionMonthly();
      if ( !$sheetActionMonthly['success'] ) return $sheetActionMonthly;

      $sheetTaskPerAspects = $this->getDataSheetTaskPerAspects();
      if ( !$sheetTaskPerAspects['success'] ) return $sheetTaskPerAspects;

      $sheetListTasks = $this->getDataSheetListTasks();
      if ( !$sheetListTasks['success'] ) return $sheetListTasks;

      $info['sheet_general'] = $sheetGeneral;
      $info['sheet_action_per_aspects'] = $sheetActionPerAspects;
      $info['sheet_action_monthly'] = $sheetActionMonthly;
      $info['sheet_task_per_aspects'] = $sheetTaskPerAspects;
      $info['sheet_list_tasks'] = $sheetListTasks;
      
      $info['success'] = true;
      $info['message'] = 'Datos de reporte generados';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Error, no se pudo obtener datos para el reporte';
      return $info;
    }
  }

  private function getDataSheetGeneral()
  {
    try {
      // title sheet
      $info['title_sheet'] = "Estatus Plan de Acción";
      // title 
      $section = Str::upper($this->originName);
      $info['header'] = "REPORTE DE PLAN DE ACCIÓN {$section}";
      $info['subheader'] = $this->corporateName;
      // headers
      $timezone = Config('enviroment.time_zone_carbon');
      $today = Carbon::now($timezone)->format('d-m-Y');
      $process = ProcessAudit::findOrFail($this->action['project']['id_audit_processes']);
      $corporate = $process->corporate;
      $info['headers']['corp_tradename'] = $corporate->corp_tradename_format;
      $info['headers']['corp_trademark'] = $corporate->corp_trademark_format;
      $info['headers']['rfc'] = $corporate->rfc;
      $info['headers']['status'] = $this->filters->wichStatusFilter();
      $info['headers']['industry'] = $corporate->industry->industry;
      $info['headers']['users'] = $process->auditors->where('pivot.leader', 1)->pluck('person.full_name')->join(', ');
      $info['headers']['scope'] = $process->scope->scope;
      $address = $corporate->addresses->firstWhere('type', Address::PHYSICAL);
      $info['headers']['street'] = $address['street'];
      $info['headers']['suburb'] = $address['suburb'];
      $info['headers']['city'] = $address['city']['city'];
      $info['headers']['state'] = $address['state']['state'];
      $info['headers']['country'] = $address['country']['country'];
      $info['headers']['date'] = $today;
      $info['headers']['period'] = $process->dates_format;
      $info['headers']['evaluate_risk'] = $process->evaluate_risk;
      $info['headers']['aspects'] = $this->filters->wichMatterAspectFilter( $this->action['project']['matters'] );
      // for requirement table
      $info['action_plan']['header_table_requirement'] = 'Detalles de estatus de plan de acción';
      $info['action_plan']['header_requirements'] = 'Estatus de Plan de acción';
      // for task table
      $recordsTasks = collect($this->action['matter_action_tasks'])->first();
      $info['action_plan']['header_table_tasks'] = $recordsTasks['count_status'];
      $info['action_plan']['header_tasks'] = "Estatus de Tareas de Plan de acción de {$this->originName}";
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
      $info['message'] = 'Algo salio mal al generar la hoja general del reporte';
      return $info;
    }
  }

  private function getDataSheetActionPerAspects()
  {
    try {
      // title sheet
      $info['title_sheet'] = 'Estatus Aspectos Plan de Acción';
      // title 
      $section = Str::upper($this->originName);
      $info['header'] = "REPORTE DE PLAN DE ACCIÓN DE {$section}";
      $info['subheader'] = $this->corporateName;
      $info['description'] = "ASPECTOS POR MATERIA LEGAL";
      // matters
      $info['matters'] = $this->action['counts'];
      $info['success'] = true;
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al generar la hoja de estatus del reporte';
      return $info;
    }
  }

  private function getDataSheetActionMonthly()
  {
    try {
      // title sheet
      $info['title_sheet'] = 'Mensual promedio planta Plan de acción';
      // title 
      $section = Str::upper($this->originName);
      $info['header'] = "ESTATUS MENSUAL DE PLAN DE ACCIÓN: {$section}";
      $info['subheader'] = $this->corporateName;
      // matters
      $info['year'] = $this->currentYear;

      $info['total'] = collect($this->action['historical_last_years']['years'])->where('year', $this->currentYear)->first();
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
      $info['message'] = 'Algo salio mal al generar la hoja mensual del reporte';
      return $info;
    }
  }

  private function getDataSheetTaskPerAspects()
  {
    try {
      // title sheet
      $info['title_sheet'] = 'Tareas Plan de Acción';
      // title 
      $section = Str::upper($this->originName);
      $info['header'] = "TAREAS DE PLAN DE ACCIÓN DE {$section}";
      $info['subheader'] = $this->corporateName;
      $info['matters'] = $this->recordsTasksGrpuped;

      $info['success'] = true; 
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al generar la hoja tareas del reporte';
      return $info;
    }
  }

  private function getDataSheetListTasks()
  {
    try {
      // title sheet
      $info['title_sheet'] = 'Reporte de Plan de Acción';
      // title 
      $section = Str::upper($this->originName);
      $info['header'] = "REPORTE DE TAREAS  DE PLAN DE ACCIÓN DE {$section}";
      $info['subheader'] = $this->corporateName;
      // records tasks
      $recordsFormat = (new UtilitiesDashboard())->getRecordFormatTasks($this->recordsTasks);
      $info['with_level_risk'] = $this->filters->filters['with_level_risk'];
      $info['records'] = $recordsFormat;
      $info['success'] = true; 
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al generar la hoja lista de tareas del reporte';
      return $info;
    }
  }
}