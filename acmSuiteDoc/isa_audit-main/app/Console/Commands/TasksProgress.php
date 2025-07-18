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

class TasksProgress extends Command
{
    use HelpersActionPlanTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'action:tasks-progress';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily check init date if progress task also change status of the requirement to belong';

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
        Log::channel('task_normal')->info('************ action:tasks-progress ************');
        DB::beginTransaction();
        $timezone = Config('enviroment.time_zone_carbon');
        $today = Carbon::now($timezone)->toDateString();
        $taskProgress = Task::with('action.action_register')
        ->where('block', 0)
        ->where('init_date', '>=', $today.' 00:00:00')
        ->where('id_status', Task::NO_STARTED_TASK)
        ->whereHas('action.action_register', function($query) use ($today) {
            $query->whereDate('init_date', '<=', $today)
                ->whereDate('end_date', '>=', $today);
        });
        // Get requirements with at least one progress task
        $requirementsUpdate = $taskProgress->get()->pluck('id_action_plan')->unique();
        try {
            $taskProgress->update(['id_status' => Task::PROGRESS_TASK]);
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
