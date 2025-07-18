<?php

namespace App\Console\Commands;

use App\Jobs\TransactionObligationActionPlanJob;
use App\Models\V2\Audit\ObligationRegister;
use App\Models\V2\Audit\Obligation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TransactionObligationActionPlan extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'transaction:obligation-action';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Evaluate obligations that are expired or similar status that are not in Action Plan';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    $timezone = Config('enviroment.time_zone_carbon');
    $today = Carbon::now($timezone)->toDateString();

    $status = [
      Obligation::FOR_EXPIRED_OBLIGATION, 
      Obligation::EXPIRED_OBLIGATION, 
      Obligation::NO_EVIDENCE_OBLIGATION
    ];

    $filterObligations = function($query) use ($status) {
      $query->whereIn('id_status', $status);
    };

    $relationships = ['action_plan_register.action_plans', 'obligations' => $filterObligations];
    $obligationRegister = ObligationRegister::with($relationships)
      ->whereDate('init_date', '<=', $today)->whereDate('end_date', '>=', $today)
      ->whereHas('obligations', $filterObligations)->get();
    
    // all obligations that are not included in the action plan 
    $allObligations = $obligationRegister->map(function($registerItem) {
      $actionPlanList = $registerItem->action_plan_register->action_plans ?? collect([]);
      $obligationList = $registerItem->obligations;
      return $obligationList->reject(function($obligation) use ($actionPlanList) {
        $actionPlanList->contains(function($action) use ($obligation) {
          $sameReq = $action->id_requirement == $obligation->id_requirement;
          $sameSub = $action->id_subrequirement == $obligation->id_subrequirement;
          return !($sameReq == $sameSub);
        });
      });
    })->collapse();

    foreach ($allObligations as $obligation) {
      TransactionObligationActionPlanJob::dispatch($obligation);
    }
    
  }
}