<?php

namespace App\Console\Commands;

use App\Classes\Reminders\HandlerReminderObligation;
use Illuminate\Console\Command;

class RemindersObligations extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'reminder:obligations';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send email reminders for obligations';

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
		$projects = new HandlerReminderObligation('reminder:obligations');
		$projects->checkDates();
	}
}
