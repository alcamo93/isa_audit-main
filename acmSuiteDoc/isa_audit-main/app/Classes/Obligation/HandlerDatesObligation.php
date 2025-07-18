<?php

namespace App\Classes\Obligation;

use App\Models\V2\Audit\Obligation;
use App\Models\V2\Audit\ProcessAudit;
use App\Traits\V2\ObligationTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HandlerDatesObligation
{
  use ObligationTrait;

  public $comandName = '';
  public $projects = null;

  public function __construct($comandName) 
  {
    $this->comandName = $comandName;
    $this->projects = $this->getProjectsActives();
  }

  private function getProjectsActives()
  {
    $timezone = Config('enviroment.time_zone_carbon');
    $currentYear = Carbon::now($timezone)->format('Y');
    // to filter by the most current year
    $filterByYear = function($query) use ($currentYear) {
      $query->whereYear('date', '<=', $currentYear)
        ->whereYear('end_date', '>=', $currentYear);
    };
    $relationships = [ 
      'aplicability_register',
      'aplicability_register.obligation_register'
    ];
    return ProcessAudit::with($relationships)->where($filterByYear)
      ->whereHas('aplicability_register.obligation_register')->get();

  }

  public function checkDates() 
  {
    Log::channel('obligations')->info("************ {$this->comandName} ************");

    $obligationsIds = $this->projects->pluck('aplicability_register')->pluck('obligation_register')->pluck('id');
    $obligations = Obligation::with(['evaluates.library'])->whereIn('obligation_register_id', $obligationsIds)->where([
        ['id_status', '!=', Obligation::NO_EVIDENCE_OBLIGATION], 
        ['id_status', '!=', Obligation::NO_DATES_OBLIGATION]
      ])->get();
    
    foreach ($obligations as $obligation) {
      $library = sizeof($obligation->evaluates) > 0 ? $obligation->evaluates->first()->library : null;
      $result = $this->setStatusObligation($obligation, $library);
      $currentStatus = $obligation->id_status ?? null;
      $updatedStatus = $result['id_status'] ?? null;
      $notification = $result['notification'] ?? null;
      Log::channel('obligations')->info("
        id: {$obligation->id_obligation} -> 
        current_status: {$currentStatus} ->
        updated_status: {$updatedStatus} ->
        result: {$notification} 
      ");
    }
  }
}