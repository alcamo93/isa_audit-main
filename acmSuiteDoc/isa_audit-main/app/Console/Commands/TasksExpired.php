<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Audit\ActionPlansModel;
use App\Models\V2\Audit\Task;
use App\Classes\StatusConstants;
use Carbon\Carbon;
use Config;
use App\Traits\V2\HelpersActionPlanTrait;

class TasksExpired extends Command
{
    use HelpersActionPlanTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'action:tasks-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily check close date if expired task also change status of the requirement to belong';

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
        Log::channel('task_normal')->info('************ action:tasks-expired ************');
        DB::beginTransaction();
        $timezone = Config('enviroment.time_zone_carbon');
        $today = Carbon::now($timezone)->toDateString();
        $todayLate = Carbon::now($timezone)->add(1, 'days')->toDateString();
        $taskExpired = Task::with('action.action_register')
        ->where([
            ['block', 0],
            ['close_date', '!=', $today.' 00:00:00'],
            ['close_date', '<=', $todayLate.' 23:59:59']
        ])
        ->whereIn('id_status', [Task::NO_STARTED_TASK, Task::PROGRESS_TASK, Task::REVIEW_TASK, Task::EXPIRED_TASK])
        ->whereHas('action.action_register', function($query) use ($today) {
            $query->whereDate('init_date', '<=', $today)
                ->whereDate('end_date', '>=', $today);
        });
        // Get requirements with at least one expired task
        $requirementsUpdate = $taskExpired->get()->pluck('id_action_plan')->unique();
        try {
            $taskExpired->update(['id_status' => Task::EXPIRED_TASK, 'block' => Task::BLOCK_ENABLED]);
            foreach ($requirementsUpdate as $key => $req) {
                $this->statusActionByTask($req);
            }
            DB::commit();
            $data['status'] = StatusConstants::SUCCESS;
            $data['msg'] = 'Cambio de estatus exitosa';
            Log::channel('task_normal')->info($data);
        } catch (\Throwable $th) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Error en el cambio de estatus';
            Log::channel('task_normal')->info($data);
        }
    }
}
