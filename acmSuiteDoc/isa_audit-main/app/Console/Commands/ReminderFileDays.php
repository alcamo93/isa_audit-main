<?php

namespace App\Console\Commands;

use App\Classes\Reminders\HandlerReminderFileDay;
use Illuminate\Console\Command;

class ReminderFileDays extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'reminder:file-days';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send email reminders for file days';

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
		$handler = new HandlerReminderFileDay();
		$handler->notifyTask();
	}
}
