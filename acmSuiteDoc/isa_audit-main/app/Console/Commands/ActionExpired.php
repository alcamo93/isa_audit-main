<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\V2\Audit\ActionPlan;
use App\Models\V2\Audit\Task;
use App\Classes\StatusConstants;
use Carbon\Carbon;
use Config;

class ActionExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'action:action-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily check requirements with expired tasks if contain expired tasks change status of this register to Expired';

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
        Log::channel('action_normal')->info('************ action:action-expired ************');
        DB::beginTransaction();
        $timezone = Config('enviroment.time_zone_carbon');
        $today = Carbon::now($timezone)->toDateString();
        $todayLate = Carbon::now($timezone)->add(1, 'days')->toDateString();
        $requirementsExpired = ActionPlan::with(['expired', 'action_register'])
            ->where([
                ['id_status', '!=', ActionPlan::EXPIRED_AP],
                ['close_date', '!=', $today.' 00:00:00'],
                ['close_date', '<=', $todayLate.' 23:59:59']
            ])
            ->whereHas('action_register', function($query) use ($today) {
                $query->whereDate('init_date', '<=', $today)
                    ->whereDate('end_date', '>=', $today);
            })
            ->doesntHave('expired')->get()->pluck('id_action_plan')->unique();
        $requirementsUpdate = ActionPlan::whereIn('id_action_plan', $requirementsExpired);
        try {
            $requirementsUpdate->update(['id_status' => StatusConstants::EXPIRED_AP]);
            $arrayActionPlanId = $requirementsUpdate->get()->pluck('id_action_plan')->unique();
            $blockTasks = Task::whereIn('id_action_plan', $arrayActionPlanId)
                ->update(['block' => Task::BLOCK_ENABLED]);
            DB::commit();
            $data['status'] = StatusConstants::SUCCESS;
            $data['msg'] = 'Cambio de estatus exitosa';
            Log::channel('action_normal')->info($data);
        } catch (\Throwable $th) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Error en el cambio de estatus';
            Log::channel('action_normal')->info($data);
        }
    }
}
