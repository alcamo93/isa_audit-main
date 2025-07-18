<?php

namespace App\Jobs;

use App\Classes\Process\Historicals\CreateHistorical;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Historicals implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public $record, $type;

  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($record, $type)
  {
    $this->record = $record;
    $this->type = $type;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    try {
      $types = [
        'action' => $this->record->id_action_register,
        'obligation' => $this->record->id,
        'audit' => $this->record->id_audit_register,
      ];
      
      DB::beginTransaction();
      $historical = new CreateHistorical($this->record, $this->type);
      $historical->setHistorical();
      $id = $types[$this->type];
      Log::channel('historical')->info(json_encode(['id' => $id, 'type' => $this->type]));
      DB::commit();
    } catch (\Throwable $th) {
      Log::channel('errorlog')->error(json_encode(['id' => $id, 'type' => $this->type]));
      DB::rollback();
    }
  }
}
