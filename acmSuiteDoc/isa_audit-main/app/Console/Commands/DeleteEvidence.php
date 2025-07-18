<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\V2\Audit\File;
use App\Traits\V2\FileTrait;
use Carbon\Carbon;

class DeleteEvidence extends Command
{
	use FileTrait;
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'files:delete-evidence';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Deletes 5 years old files';

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
		Log::channel('delete_evidence')->info('************ files:delete-evidence ************');
		$timezone = Config('enviroment.time_zone_carbon');
		$today = Carbon::now($timezone)->toDateString();

		$files = File::whereDate('drop_date', $today)->get();
		foreach ($files as $file) {
			$disk = $this->getStorageEnviroment($file->store_origin);
			$deleteFile = Storage::disk($disk)->delete($file->directory_path);
			Log::channel('delete_evidence')->info($file);
		}
	}
}
