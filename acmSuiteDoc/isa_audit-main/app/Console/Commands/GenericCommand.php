<?php

namespace App\Console\Commands;

use App\Classes\Process\Renewals\ApplicabilityRenewal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenericCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'generic:test';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Test Class';

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
    $applicabilityRenewal = new ApplicabilityRenewal(118);
    dd('applicabilityRenewal');
  }
}