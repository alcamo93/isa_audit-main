<?php

namespace App\Classes\Reminders;

use App\Classes\Utilities\DataSection;
use App\Models\V2\Admin\User;
use App\Models\V2\Audit\TaskNotification;
use App\Notifications\RemindersTasksNotifications;
use App\Traits\V2\RequirementTrait;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class HandlerReminderTaskDay
{
  use RequirementTrait;

  protected $timezone = '';

  public function __construct() 
  {
    $this->timezone = Config('enviroment.time_zone_carbon');
  }

  public function notifyTask()
  {
    Log::channel('task_reminder')->info('************ reminder:tasks ************');
  
    $todayDate = Carbon::now($this->timezone);

    $relationships = [
      'task.action.requirement',
      'task.action.subrequirement',
      'task.action.action_register.process.customer',
      'task.action.action_register.process.corporate',
    ];

    $notifies = TaskNotification::with($relationships)->whereDate('date', $todayDate)
      ->where('done', TaskNotification::NO_DONE)->get();
    
    $notifiesForUpdate = $notifies->pluck('id');

    $allLists = $notifies->map(function ($notify) use ($todayDate) {

      $endDate = Carbon::parse($notify->task->close_date);
      $diffDays = $endDate->diffInDays($todayDate);

			$task = $notify['task'];
      $usersIds = $task['auditors']->pluck('id_user');
      $recordActionPlan = $task['action'];
      $recordActionRegister = $recordActionPlan['action_register'];

      return [
        'id_task' => $task['id_task'],
        'corp_tradename' => $recordActionRegister['process']['corporate']['corp_tradename'],
        'audit_processes' => $recordActionRegister['process']['audit_processes'],
        'init_date_format' => $recordActionRegister['init_date_format'],
        'end_date_format' => $recordActionRegister['end_date_format'],
        'origin' => "Plan de acción de ".$recordActionRegister['origin'],
        'no_requirement' => $this->getFieldRequirement($recordActionPlan, 'no_requirement'),
        'requirement' => $this->getFieldRequirement($recordActionPlan, 'requirement'),
        'title' => $task['title'],
        'body' => $task['task'],
        'type' => $task['type_task'],
        'close_date' => $task['close_date_format'],
        'days' => $diffDays,
        'id_action_plan' => $task['id_action_plan'],
        'path' => (new DataSection('task', $task['id_task']))->getSectionPath(),
        'user_ids' => $usersIds,
      ];
    });

    $usersIds = $allLists->pluck('user_ids')->collapse()->unique()->values()->toArray();
    $users = User::whereIn('id_user', $usersIds)->get();

    $users->each(function($user) use ($allLists) {
      $list = $allLists->filter(fn($item) => $item['user_ids']->contains($user->id_user));
      Log::channel('task_reminder')->info([ 'id_user' => $user->id_user, 'task_notification' => Arr::pluck($list, 'id_task') ]);
      $data['full_name'] = $user->person->full_name;
      $data['tasks'] = $list;
      $user->notify( new RemindersTasksNotifications($data) );
    });
    
    TaskNotification::whereIn('id', $notifiesForUpdate)->update([ 'done' => TaskNotification::DONE ]);
  }
}