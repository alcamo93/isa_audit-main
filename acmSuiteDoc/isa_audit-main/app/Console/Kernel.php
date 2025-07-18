<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\InactiveContracts::class,
        Commands\ActiveContracts::class,
        Commands\ExtendContracts::class,
        Commands\TasksExpired::class,
        Commands\ActionExpired::class,
        Commands\RemindersRequirements::class,
        Commands\RemindersTasks::class,
        Commands\TasksCheckExpired::class,
        Commands\ActionCheckExpired::class,
        commands\TasksProgress::class,
        commands\ObligationCheckDates::class,
        commands\RemindersObligations::class,
        commands\HistoricalCheck::class,
        commands\TransactionObligationActionPlan::class,
        commands\DeleteEvidence::class,
        commands\ReminderFileDays::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    /**
     * Cron entry: * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
     */
    protected function schedule(Schedule $schedule)
    {
        $timezone = Config('enviroment.time_zone_carbon');
        // Historical
        $schedule->command('historical:historical')->dailyAt('23:50')->timezone($timezone);
        // Changes status contracts
        $schedule->command('contracts:inactive')->daily()->timezone($timezone);
        $schedule->command('contracts:active')->daily()->timezone($timezone);
        $schedule->command('contracts:extend')->daily()->timezone($timezone);
        // Changes in tasks
        $schedule->command('action:tasks-progress')->dailyAt('00:01')->timezone($timezone);
        $schedule->command('action:tasks-expired')->dailyAt('00:02')->timezone($timezone);
        $schedule->command('expired:tasks-expired')->dailyAt('00:03')->timezone($timezone);
        // Changes in requirements 
        $schedule->command('action:action-expired')->dailyAt('00:04')->timezone($timezone);
        $schedule->command('expired:action-expired')->dailyAt('00:05')->timezone($timezone);
        $schedule->command('obligation:check-dates')->dailyAt('00:06')->timezone($timezone);
        // Reminders
        $schedule->command('reminder:obligations')->dailyAt('07:55')->timezone($timezone);
        $schedule->command('reminder:requirements')->dailyAt('08:00')->timezone($timezone);
        $schedule->command('reminder:tasks')->dailyAt('08:05')->timezone($timezone);
        $schedule->command('reminder:contracts')->weeklyOn(Schedule::MONDAY, '08:00')->timezone($timezone);
        $schedule->command('reminder:file-days')->dailyAt('08:15')->timezone($timezone);
        // transaction in sections
        $schedule->command('transaction:obligation-action')->dailyAt('00:07')->timezone($timezone);
        // delete evidence in all sections
        $schedule->command('files:delete-evidence')->dailyAt('02:00')->timezone($timezone);
        // delete files
        $schedule->command('files:delete-files')->dailyAt('03:00')->timezone($timezone);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
