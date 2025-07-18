<?php

namespace App\Jobs;

use App\Classes\ActionPlan\CreateEvaluateObligationAP;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionObligationActionPlanJob implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	public $obligation;
	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct($obligation)
	{
		$this->obligation = $obligation;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		try {
			DB::beginTransaction();
			// create in action plan validating status
			$forActionPlan = new CreateEvaluateObligationAP($this->obligation);
			$createAP = $forActionPlan->createRecordAP();
			Log::channel('transaction_obligation_action_plan')->info(json_encode($createAP));
			DB::commit();
		} catch (\Throwable $th) {
			Log::channel('errorlog')->error(json_encode(['type' => 'transaction_ob_ap', 'id' => $this->obligation->id_obligation]));
      DB::rollback();
		}
		
	}
}
