<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\V2\Audit\ObligationRegister;
use App\Models\V2\Audit\ActionPlanRegister;
use App\Models\V2\Audit\AuditRegister;
use App\Jobs\Historicals;
use Carbon\Carbon;

class HistoricalCheck extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'historical:historical';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Execute a cut-off of what is currently in Obligations, and Action Plans';

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

    $actionsRegister = ActionPlanRegister::whereDate('init_date', '<=', $today)->whereDate('end_date', '>=', $today)->get();
    $obligationRegister = ObligationRegister::whereDate('init_date', '<=', $today)->whereDate('end_date', '>=', $today)->get();
    $auditRegister = AuditRegister::whereDate('init_date', '<=', $today)->whereDate('end_date', '>=', $today)->get();
    
    foreach ($actionsRegister as $action) {
      Historicals::dispatch($action, 'action');
    }
    foreach ($obligationRegister as $obligation) {
      Historicals::dispatch($obligation, 'obligation');
    }
    foreach ($auditRegister as $audit) {
      Historicals::dispatch($audit, 'audit');
    }
  }
}