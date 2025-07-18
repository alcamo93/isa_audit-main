<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin\ContractsModel;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActiveContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:active';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Active contracts with a date equal to the current';

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
        $today = Carbon::now('America/Mexico_City')->toDateString();
        $inactivedContracts = ContractsModel::GetContractsByDate($today, StatusConstants::INACTIVE);
        $arrayIdContracts = [];
        $status = StatusConstants::SUCCESS;
        $msg = 'unchanged';
        \Log::info('************ contracts:active ************');
        if ( sizeof($inactivedContracts) > 0) {
            foreach ($inactivedContracts as $id) {
                array_push($arrayIdContracts, $id['id_contract']); 
            }
            DB::beginTransaction();
            $updateContracts = ContractsModel::UpdateStatusByGroup($arrayIdContracts, StatusConstants::ACTIVE);
            switch ($updateContracts['status']) {
                case StatusConstants::SUCCESS:
                    DB::commit();
                    $status = $updateContracts['status'];
                    $msg = $updateContracts['msg'];
                    break;
                case StatusConstants::ERROR:
                    DB::rollBack();
                    $status = $updateContracts['status'];
                    $msg = $updateContracts['msg'][1].': '.$updateContracts['msg'][2];
                    break;
                default:
                    # code... 
                    break;
            }
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        \Log::info($data);
    }
}
