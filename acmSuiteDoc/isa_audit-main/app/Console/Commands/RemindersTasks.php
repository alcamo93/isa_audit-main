<?php

namespace App\Console\Commands;

use App\Classes\Reminders\HandlerReminderTaskDay as HandlerReminderTaskDayClass;
use App\Jobs\HandlerReminderTaskDay;
use App\Traits\V2\RequirementTrait;
use Illuminate\Console\Command;

class RemindersTasks extends Command
{
	use RequirementTrait;
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'reminder:tasks';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send email reminders for tasks';

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
		$handler = new HandlerReminderTaskDayClass();
		$handler->notifyTask();
	}
}
