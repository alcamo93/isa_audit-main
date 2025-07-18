<?php

namespace App\Classes\ActionPlan\Report;

use App\Models\V2\Audit\Task;
use App\Models\V2\Catalogs\Status;

class BuildFilterReportActionPlan 
{
  public $filters = [];
  public $mattersStructure = [];

  /**
   * @param array $filtersQuery
   */
  public function __construct($filtersQuery)
  {
    $this->filters['matters'] = isset( $filtersQuery['matters'] ) ? explode(',', $filtersQuery['matters']) : [];
    $this->filters['aspects'] = isset( $filtersQuery['aspects'] ) ? explode(',', $filtersQuery['aspects']) : [];
    $this->filters['status'] = isset( $filtersQuery['status'] ) ? explode(',', $filtersQuery['status']) : [];
    $this->filters['priorities'] = isset( $filtersQuery['priorities'] ) ? explode(',', $filtersQuery['priorities']) : [];
    $this->filters['users'] = isset( $filtersQuery['users'] ) ? explode(',', $filtersQuery['users']) : [];
    $this->filters['range'] = isset( $filtersQuery['range'] ) ? explode(',', $filtersQuery['range']) : [];
    $this->filters['with_task'] = isset( $filtersQuery['with_task'] ) ? filter_var( $filtersQuery['with_task'], FILTER_VALIDATE_BOOLEAN ) : true;
    $this->filters['with_subtask'] = isset( $filtersQuery['with_subtask'] ) ? filter_var( $filtersQuery['with_subtask'], FILTER_VALIDATE_BOOLEAN ) : true;
    $this->filters['with_level_risk'] = isset( $filtersQuery['with_level_risk'] ) ? filter_var( $filtersQuery['with_level_risk'], FILTER_VALIDATE_BOOLEAN ) : true;
  }

  public function wichMatterAspectFilter($matterItems)
  {
    if ( !sizeof($this->filters['matters']) && !sizeof($this->filters['aspects']) ) return 'Todos';

    $matters = collect($matterItems);
    return $matters->flatMap(fn($item) => $item['aspects'])->pluck('aspect')->join(', ');
  }

  public function wichStatusFilter() 
  {
    if ( !sizeof($this->filters['status']) ) return 'Todos';

    return Status::whereIn('id_status', $this->filters['status'])->get()->pluck('status')->join(', ');
  }

  /**
   * @param array $projectItem
   */
  public function getProject($projectItem)
  {
    if ( !sizeof($this->filters['matters']) && !sizeof($this->filters['aspects']) && !sizeof($this->filters['status']) ) return $projectItem;

    $mattersFtr = $this->setFilterStructureMatters($projectItem['matters']);
    $mattersFtr = $this->setFilterStructureAspects($mattersFtr);
    $mattersFtr = $this->setFilterStructureStatus($mattersFtr);
    $projectItem['matters'] = $mattersFtr;

    return $projectItem;
  }

  /**
   * @param array $mattersItem
   */
  public function filterEvaluatedMatters($mattersItem)
  {
    if ( !sizeof($this->filters['matters']) && !sizeof($this->filters['aspects']) && !sizeof($this->filters['status']) ) return $mattersItem;

    $mattersFtr = $this->setFilterStructureMatters($mattersItem);
    $mattersFtr = $this->setFilterStructureAspects($mattersFtr);
    $mattersFtr = $this->setFilterStructureStatus($mattersFtr);
    
    return $mattersFtr;
  }

  /**
   * @param array $mattersItem
   */
  public function filterEvaluatedMattersTask($mattersItem)
  {
    if ( !sizeof($this->filters['matters']) && !sizeof($this->filters['aspects']) && !sizeof($this->filters['status']) ) return $mattersItem;

    $mattersFtr = $this->setFilterStructureMatters($mattersItem);
    $mattersFtr = $this->setFilterStructureAspects($mattersFtr);
    
    return $mattersFtr;
  }

  /**
   * @param array $historicalItem
   */
  public  function getHistoricalPerMatters($historicalItem)
  {
    if ( !sizeof($this->filters['matters']) && !sizeof($this->filters['aspects']) ) return $historicalItem;

    $mattersFtr = $this->setFilterStructureMatters($historicalItem['matters']);
    $mattersFtr = $this->setFilterStructureAspects($mattersFtr);
    $historicalItem['matters'] = $mattersFtr;

    return $historicalItem;
  }

  /**
   * @param array $taskPerMatterItem
   */
  public  function getRecordsPerMatter($taskPerMatterItem)
  {
    if ( !sizeof($this->filters['matters']) && !sizeof($this->filters['aspects']) ) return $taskPerMatterItem;

    $mattersFtr = $this->setFilterStructureMatters($taskPerMatterItem);
    $mattersFtr = $this->setFilterStructureAspects($mattersFtr);

    return $mattersFtr;
  }

  /**
   * @param mixed $records
   */
  public function getRecordsTasks($records)
  {
    $recordsFilters = $records->toArray();
    if ( sizeof($this->filters['matters']) ) {
      $matterInFilter = collect($this->filters['matters']);
      $recordsFilters = collect($recordsFilters)->filter(function($record) use ($matterInFilter) {
        return $matterInFilter->contains( $record['requirement']['id_matter'] );
      })->values()->toArray();
    }
    if ( sizeof($this->filters['aspects']) ) {
      $aspectsInFilter = collect($this->filters['aspects']);
      $recordsFilters = collect($recordsFilters)->filter(function($record) use ($aspectsInFilter) {
        return $aspectsInFilter->contains( $record['requirement']['id_aspect'] );
      })->values()->toArray();
    }
    if ( sizeof($this->filters['status']) ) {
      $recordsFilters = collect($recordsFilters)->whereIn('id_status', $this->filters['status'])->values()->toArray();
    }
    if ( sizeof($this->filters['priorities']) ) {
      $recordsFilters = collect($recordsFilters)->whereIn('id_priority', $this->filters['priorities'])->values()->toArray();
    }
    if ( sizeof($this->filters['range']) ) {
      $startDate = $this->filters['range'][0];
      $endDate = $this->filters['range'][1];
      $recordsFilters = collect($recordsFilters)->filter(function ($item) use ($startDate, $endDate) {
        $initDate = $item['init_date'];
        $closeDate = $item['close_date'];
        return $initDate >= $startDate && $closeDate <= $endDate;
      });
    }
    if ( sizeof($this->filters['users']) ) {
      $recordsFilters = collect($recordsFilters)->map(function($record) {
        $recordFtr = $record;
        $recordFtr['tasks'] = collect($record['tasks'])->filter(function($task) {
          $userInTask = collect($task['auditors'])->whereIn('id_user', $this->filters['users']);
          return $userInTask->isNotEmpty();
        })->values()->toArray();
        return $recordFtr;
      })->filter(fn($record) => sizeof($record['tasks']))->values()->toArray();
    }
    if ( !$this->filters['with_task'] ) {
      $recordsFilters = collect($recordsFilters)->map(function($record) {
        $recordFtr = $record;
        $recordFtr['tasks'] = collect($record['tasks'])->whereNotIn('main_task', [Task::MAIN_TASK])->values()->toArray();
        return $recordFtr;
      })->filter(fn($record) => sizeof($record['tasks']))->values()->toArray();
    }
    if ( !$this->filters['with_subtask'] ) {
      $recordsFilters = collect($recordsFilters)->map(function($record) {
        $recordFtr = $record;
        $recordFtr['tasks'] = collect($record['tasks'])->whereIn('main_task', [Task::NO_MAIN_TASK])->values()->toArray();
        return $recordFtr;
      })->filter(fn($record) => sizeof($record['tasks']))->values();
    }

    return $recordsFilters;
  }

  private function setFilterStructureMatters($matterItems)
  {
    if ( !sizeof($this->filters['matters']) ) return $matterItems;

    return collect($matterItems)->whereIn('id_matter', $this->filters['matters'])->values()->toArray();
  }

  private function setFilterStructureAspects($matterItems)
  {
    if ( !sizeof($this->filters['aspects']) ) return $matterItems;

    return collect($matterItems)->map(function($matter) {
      $matterFtr = $matter;
      $matterFtr['aspects'] = collect($matter['aspects'])->whereIn('id_aspect', $this->filters['aspects'])->values()->toArray();
      return $matterFtr;
    })->filter(fn($matter) => sizeof($matter['aspects']) )->values()->toArray();
  }

  private function setFilterStructureStatus($matterItems)
  {
    if ( !sizeof($this->filters['status']) ) return $matterItems;

    return collect($matterItems)->map(function($matter) {
      $matterFtr = $matter;
      $matterFtr['count_status'] = collect($matter['count_status'])->whereIn('id_status', $this->filters['status'])->values()->toArray();
      $matterFtr['aspects'] = collect($matter['aspects'])->map(function($aspect) {
        $aspectFtr = $aspect;
        $aspectFtr['count_status'] = collect($aspect['count_status'])->whereIn('id_status', $this->filters['status'])->values()->toArray();
        return $aspectFtr;
      })->values()->toArray();
      return $matterFtr;
    })->filter(fn($matter) => sizeof($matter['aspects']) )->values()->toArray();
  }
}