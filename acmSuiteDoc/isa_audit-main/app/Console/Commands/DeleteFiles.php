<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Classes\Files\HandlerDeleteFiles;

class DeleteFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:delete-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete files for expired contracts and that have passed 3 months since the expiration date';

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
        new HandlerDeleteFiles('files:contracts');
    }
}
