<?php

namespace App\Classes\Files;

use Illuminate\Support\Facades\Log;
use App\Models\V2\Admin\Contract;
use Illuminate\Support\Facades\Storage;
use App\Models\V2\Audit\File;
use App\Traits\V2\FileTrait;
use Carbon\Carbon;
use App\Models\V2\Audit\EvaluateRequirement;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Audit\Library;

class HandlerDeleteFiles
{
  use FileTrait;

  public $comandName = '';
  public $timezone = '';
  public $projects = null;

  public function __construct($comandName) 
  {
    $this->comandName = $comandName;
    $this->timezone = Config('enviroment.time_zone_carbon');
    $this->projects = $this->getContractsActives();
  }

  private function getContractsActives()
  {
    $currentDate = Carbon::now($this->timezone)->format('Y-m-d');
    $contracts = Contract::where('end_date', '<', $currentDate)
        ->where('id_status', '<>', 1)->get();

    foreach ($contracts as $contract) {
        $processPerContract = ProcessAudit::where('id_corporate', $contract->id_corporate)->get();
        $endDate = Carbon::parse($contract->end_date);
        $daysLeft = $endDate->diffInDays($currentDate);
        if($daysLeft >= 90){
          foreach ($processPerContract as $process) {
            $data = EvaluateRequirement::with(['requirement', 'subrequirement', 'library', 'obligations', 'tasks'])
            ->where('aplicability_register_id', $process->aplicability_register->id_aplicability_register)
            ->where('show_library', 1)->get();

            foreach($data as $key => $library){
              if($library->library != null){
                $libraryId = $library->library->id;
                $this->deleteFiles($libraryId);
              }
            }

          }
        }
    }
  }

  public function deleteFiles($removeId)
  {
    try {
      $libraries = Library::where('id', $removeId)->get();
      $files = $libraries->pluck('files')->collapse();

      foreach ($files as $file) {
        $disk = $this->getStorageEnviroment($file->store_origin);
        Storage::disk($disk)->delete($file->directory_path);
      }

    } catch (\Throwable $th) {
      return false;
    }
  }
}