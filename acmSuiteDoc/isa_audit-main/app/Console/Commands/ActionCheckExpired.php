<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\V2\Audit\ActionPlan;
use App\Models\V2\Audit\ActionExpired;
use App\Classes\StatusConstants;
use Carbon\Carbon;
use Config;

class ActionCheckExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expired:action-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily check requirements (t_action_expired) with expired tasks if contain expired tasks (block 0) change status of this register to Expired';

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
        Log::channel('action_expired')->info('************ expired:action-expired ************');
        DB::beginTransaction();
        $timezone = Config('enviroment.time_zone_carbon');
        $today = Carbon::now($timezone)->toDateString();
        $todayLate = Carbon::now($timezone)->add(1, 'days')->toDateString();
        $requirementsExpired = ActionExpired::with('action.action_register')->where([
                ['id_status', '!=', ActionPlan::EXPIRED_AP],
                ['real_close_date', '!=', $today.' 00:00:00'],
                ['real_close_date', '<=', $todayLate.' 23:59:59']
            ])
            ->whereHas('action.action_register', function($query) use ($today) {
                $query->whereDate('init_date', '<=', $today)
                    ->whereDate('end_date', '>=', $today);
            })
            ->get()->pluck('id_action_expired')->unique();
        $requirementsUpdate = ActionExpired::whereIn('id_action_expired', $requirementsExpired);
        try {
            $requirementsUpdate->update(['id_status' => ActionPlan::EXPIRED_AP]);
            DB::commit();
            $data['status'] = StatusConstants::SUCCESS;
            $data['msg'] = 'Cambio de estatus exitosa';
            Log::channel('action_expired')->info($data);
        } catch (\Throwable $th) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Error en el cambio de estatus';
            Log::channel('action_expired')->info($data);
        }
    }
}
