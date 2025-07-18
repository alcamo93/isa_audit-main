<?php

namespace App\Jobs;

use App\Classes\Library\BackupFilesProcess;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BuildBackupFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $idAuditProcess = null;
    protected $processName = null;
    protected $idUser = null;
    protected $evaluateIds = null;    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($idAuditProcess, $processName, $idUser, $evaluateIds)
    {
        $this->idAuditProcess = $idAuditProcess;
        $this->processName = $processName;
        $this->idUser = $idUser;
        $this->evaluateIds = $evaluateIds;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $backup = new BackupFilesProcess($this->idAuditProcess, $this->processName, $this->idUser, $this->evaluateIds);
        $backup->buildBackup();
    }
}
