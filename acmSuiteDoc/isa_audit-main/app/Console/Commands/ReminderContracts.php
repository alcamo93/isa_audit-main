<?php

namespace App\Console\Commands;

use App\Classes\Reminders\HandlerReminderContracts;
use Illuminate\Console\Command;

class ReminderContracts extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'reminder:contracts';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send email reminders for contracts';

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
		$handler = new HandlerReminderContracts('reminder:contracts');
		$handler->notifyContract();
	}
}
