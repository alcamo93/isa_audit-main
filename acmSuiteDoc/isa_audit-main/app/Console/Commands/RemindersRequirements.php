<?php

namespace App\Console\Commands;

use App\Classes\Reminders\HandlerReminderActionPlan; 
use Illuminate\Bus\Queueable;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemindersRequirements extends Command implements ShouldQueue
{
    use Queueable;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:requirements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders for requirements';

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
     * @return mixed
     */
    public function handle()
    {
        $project = new HandlerReminderActionPlan('reminder:requirements');
        $project->checkDates();
    }
}
