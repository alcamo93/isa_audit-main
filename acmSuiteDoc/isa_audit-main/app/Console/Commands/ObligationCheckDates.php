<?php

namespace App\Console\Commands;

use App\Classes\Obligation\HandlerDatesObligation;
use Illuminate\Console\Command;

class ObligationCheckDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'obligation:check-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily check dates for update status in each record in obligation';

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
        $projects = new HandlerDatesObligation('obligation:check-dates');
        $projects->checkdates();
    }
}
