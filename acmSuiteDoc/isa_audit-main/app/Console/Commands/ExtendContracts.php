<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin\ContractsModel;
use App\Models\Admin\ContractsExtensionModel;
use App\Models\Admin\LicensesModel;
use App\Models\Admin\ContractDetailsModel;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExtendContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:extend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Active contracts that have extensions in waiting for activation';

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
        $contracts = ContractsExtensionModel::GetContractsExtensionsByDate($today);
        $arrayIdExtensions = [];
        $status = StatusConstants::SUCCESS;
        $msg = 'unchanged';
        \Log::info('************ contracts:extends ************');
        if ( sizeof($contracts) > 0) {
            DB::beginTransaction();
            foreach ($contracts as $contract)
            {
                array_push($arrayIdExtensions, $contract['id_contract_extend']);
                $updateContracts = ContractsModel::ExtendContract($contract, StatusConstants::ACTIVE);
                if($updateContracts != StatusConstants::SUCCESS)
                {
                        $status = StatusConstants::ERROR;
                    break;
                }
                else {
                    $license = LicensesModel::GetLicense($contract['id_license']);
                    $license[0]['id_contract'] = $contract['id_contract'];
                    $updateDetails = ContractDetailsModel::UpdateContractDetailsByExtension($license[0]);
                    if($updateContracts != StatusConstants::SUCCESS)
                    {
                            $status = StatusConstants::ERROR;
                        break;
                    }
                    else StatusConstants::SUCCESS;
                }
            }
            
            if ($updateContracts == StatusConstants::SUCCESS)
            {
                $updateExtensions = ContractsExtensionModel::UpdateUsedExtensions($arrayIdExtensions);
                if($updateExtensions != StatusConstants::SUCCESS) $updateContracts = StatusConstants::ERROR;
            }
            
            switch ($updateContracts) {
                case StatusConstants::SUCCESS:
                    DB::commit();
                    //DB::rollBack();
                    $status = $updateContracts;
                    $msg = 'Contratos actualizados correctamente';
                    break;
                case StatusConstants::ERROR:
                    DB::rollBack();
                    $status = $updateContracts;
                    $msg = 'Contratos no actualizados correctamente';
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
