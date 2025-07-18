<?php

namespace App\Classes\Dashboard\Data;

use App\Models\V2\Admin\Address;
use App\Traits\V2\RequirementTrait;
use Carbon\Carbon; 

class UtilitiesDashboard
{
  use RequirementTrait;

  public function __construct()
  {
    // 
  }
  
  /**
   * @param bool $getLikeArray
   */
  public function getMonths( $getLikeArray =  false ) 
  {
    $monthsArray = [
      ['id' => 1, 'name' => 'Enero', 'key' => 'ENE', 'total' => 0, 'total_count' => 0],
      ['id' => 2, 'name' => 'Febrero', 'key' => 'FEB', 'total' => 0, 'total_count' => 0],
      ['id' => 3, 'name' => 'Marzo', 'key' => 'MAR', 'total' => 0, 'total_count' => 0],
      ['id' => 4, 'name' => 'Abril', 'key' => 'ABR', 'total' => 0, 'total_count' => 0],
      ['id' => 5, 'name' => 'Mayo', 'key' => 'MAY', 'total' => 0, 'total_count' => 0],
      ['id' => 6, 'name' => 'Junio', 'key' => 'JUN', 'total' => 0, 'total_count' => 0],
      ['id' => 7, 'name' => 'Julio', 'key' => 'JUL', 'total' => 0, 'total_count' => 0],
      ['id' => 8, 'name' => 'Agosto', 'key' => 'AGO', 'total' => 0, 'total_count' => 0],
      ['id' => 9, 'name' => 'Septiembre', 'key' => 'SEP', 'total' => 0, 'total_count' => 0],
      ['id' => 10, 'name' => 'Octubre', 'key' => 'OCT', 'total' => 0, 'total_count' => 0],
      ['id' => 11, 'name' => 'Noviembre', 'key' => 'NOV', 'total' => 0, 'total_count' => 0],
      ['id' => 12, 'name' => 'Diciembre', 'key' => 'DIC', 'total' => 0, 'total_count' => 0],
    ];

    return $getLikeArray ? $monthsArray : collect($monthsArray);
  }

  /**
   * @param int $numberYears
   * @param bool $getLikeArray
   */
  public function getLastYears( $numberYears =  2, $getLikeArray = false ) 
  {
    $timezone = Config('enviroment.time_zone_carbon');
    $currentYear = intval(Carbon::now($timezone)->format('Y'));

    $colorsYears = ['#113c53', '#2581cc', '#4eaf8f'];
    $firstYear = $currentYear - $numberYears;
    $range = collect()->range($firstYear, $currentYear);

    $years = $range->map(function($year, $key) use ($colorsYears) {
      $otherColor = ($key + 1) * 3;
      $item['color'] = $colorsYears[$key] ?? "#1a{$otherColor}a1{$key}";
      $item['year'] = $year;
      return $item;
    });

    return $getLikeArray ? $years : collect($years);
  }

  /**
   * @param App\Models\V2\Audit\ProcessAudit $project
   */
  public function getCustomer($project)
  {
    $corporate = $project->corporate ?? null;
    $customer = $project->customer ?? null;
    $customerImage = isset($customer->images) && $customer->images->isNotEmpty() ? $customer->images->firstWhere('usage', 'dashboard')->full_path : null;
    $corporateImage = isset($corporate->image) && !is_null($corporate->image) ? $corporate->image->full_path : null;
    $data['id_audit_processes'] = $project->id_audit_processes ?? '';
    $data['audit_processes'] = $project->audit_processes ?? '';
    $data['id_aplicability_register'] = $project->aplicability_register->id_aplicability_register ?? '';
    $data['cust_tradename'] = $customer->cust_tradename_format ?? '';
    $data['corp_tradename'] = $corporate->corp_tradename_format ?? '';
    $data['cust_full_path'] = $customerImage;
    $data['corp_full_path'] = $corporateImage;
    $data['address'] = is_null($corporate) ? '' : $this->getAddress($corporate);
    
    return $data;
  }

  /**
   * @param App\Models\V2\Admin\Corporate $corporate
   */
  private function getAddress($corporate)
  {
    if ( is_null($corporate) ) return null; 
    $data = $corporate->addresses->firstWhere('type', Address::PHYSICAL);
    $address = "{$data->full_address}, {$data->city->city}, {$data->state->state} {$data->country->country}";
    return $address;
  }

  /**
   * @param array $recordTasks 
   * structure action plan table
   */
  public function getRecordFormatTasks($recordTasks)
  {
    $records = collect($recordTasks)->map(function($requirement) {
      return collect($requirement['tasks'])->map(function($task) use ($requirement) {
        $userAP = collect($requirement['auditors'])->filter(fn($user) => $user['pivot']['level'] == 1)->first();
        $userTask = collect($task['auditors'])->filter(fn($user) => $user['pivot']['level'] == 1)->first();
        $risk = collect($requirement['risk_totals'])->map(fn($risk) => "{$risk['category']['risk_category']}: {$risk['interpretation']}");
        $riskTotals = $risk->count() > 0 ? $risk->toArray() : ['No evaluado'];
        return [
          'matter' => $this->getFieldRequirement($requirement, 'matter'),
          'aspect' => $this->getFieldRequirement($requirement, 'aspect'),
          'no_requirement' => $this->getFieldRequirement($requirement, 'no_requirement'),
          'requirement' => $this->getFieldRequirement($requirement, 'requirement'),
          'risk_totals' => $riskTotals,
          'priority' => $requirement['priority']['priority'],
          'type_task' => $task['type_task'],
          'task' => $task['task'],
          'init_date_format' => $task['init_date_format'] ?? '--/--/----',
          'close_date_format' => $task['close_date_format'] ?? '--/--/----',
          'status' => $task['status']['status'] ?? '---',
          'user_ap' => $userAP['person']['full_name'] ?? '---',
          'user_task' => $userTask['person']['full_name'] ?? '---',
          'comments' => collect($task['comments'])->map(fn($comment) => $comment['comment'])->toArray()
        ];
      })->values()->toArray();
    })->collapse()->toArray();
    
    return $records;
  }
}