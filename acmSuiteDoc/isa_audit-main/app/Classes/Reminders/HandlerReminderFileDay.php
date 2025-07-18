<?php

namespace App\Classes\Reminders;

use App\Classes\Utilities\DataSection;
use App\Models\V2\Audit\FilesNotification;
use App\Models\V2\Admin\User;
use App\Notifications\ReminderFileDays;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class HandlerReminderFileDay
{
  protected $timezone = '';

  public function __construct() 
  {
    $this->timezone = Config('enviroment.time_zone_carbon');
  }

  public function notifyTask()
  {
    Log::channel('file_notification_reminder')->info('************ reminder:file-days ************');
  
    $todayDate = Carbon::now($this->timezone);

    $relationships = [ 
      'library.category',
      'library.auditor.person',
      'library.evaluate.aplicability_register.process.customer',
      'library.evaluate.aplicability_register.process.corporate',
      'library.evaluate.tasks.status',
      'library.evaluate.tasks.action.action_register',
      'library.evaluate.obligations.status',
    ];

    $notifies = FilesNotification::with($relationships)
      ->whereDate('date', $todayDate)
      ->where('done', FilesNotification::NO_DONE)->get();
    
    $notifiesForUpdate = $notifies->pluck('id');

    $allLists = $notifies->map(function ($notify) use ($todayDate) {

      $endDate = Carbon::parse($notify->library->endDate);
      $diffDays = $endDate->diffInDays($todayDate);

      $sections = [];
      $obligationRegisterId = null;
      $taskRegisterId = null;

      foreach ($notify->library->evaluate->obligations as $obligation) {
        $obligationRegisterId = $obligation->obligation_register_id;
        array_push($sections, [
          'id' => $obligation->id_obligation, 
          'section_name' => 'Permisos Críticos',
          'section' => 'obligation',
          'id_section_register' => $obligationRegisterId, 
          'status' => $obligation->status->status
        ]);
      }

      foreach ($notify->library->evaluate->tasks as $task) {
        $taskRegisterId = $task->id_action_plan;
        $sectionName = "Tareas de Plan de Acción de {$task->action->action_register->origin}";
        array_push($sections, [
          'id' => $task->id_task, 
          'section_name' => $sectionName,
          'section' => 'task',
          'id_section_register' => $taskRegisterId, 
          'status' => $task->status->status ?? '---'
        ]);
      }

      $path = '';
      $sectionCollection = collect( $sections );
      $hasObligation = $sectionCollection->where('section', 'obligation')->first();
      if ( $hasObligation ) {
        $path = (new DataSection($hasObligation['section'], $hasObligation['id']))->getSectionPath();
      }
      $hasTask = $sectionCollection->where('section', 'task')->first();
      if ( $hasTask ) {
        $path = (new DataSection($hasTask['section'], $hasTask['id']))->getSectionPath();
      }
      
      if ( sizeof($sections) == 0 ) {
        array_push($sections, [
          'id' => $notify->library->id, 
          'section_name' => 'Mis archivos',
          'section' => 'library',
          'id_section_register' => null, 
          'status' => '---'
        ]);
        $domain = Config('enviroment.domain_frontend');
        $path = "{$domain}v2/library/view";
      }

      return [
        'id_library' => $notify->library->id,
        'id_user' => $notify->library->id_user,
        'cust_tradename' => $notify->library->evaluate->aplicability_register->process->customer->cust_tradename,
        'corp_tradename' => $notify->library->evaluate->aplicability_register->process->corporate->corp_tradename,
        'audit_processes' => $notify->library->evaluate->aplicability_register->process->audit_processes,
        'evaluation_init_date_format' => $notify->library->evaluate->aplicability_register->process->date_format,
        'evaluation_end_date_format' => $notify->library->evaluate->aplicability_register->process->end_date_format,
        'no_requirement' => $notify->library->evaluate->requirement->no_requirement,
        'requirement' => $notify->library->evaluate->requirement->requirement,
        'library_name' => $notify->library->name,
        'init_date_format' => $notify->library->init_date_format,
        'end_date_format' => $notify->library->end_date_format,
        'days' => $diffDays,
        'all_status' => $sections,
        'auditor_full_name' => $notify->library->auditor->person->full_name,
        'auditor_email' => $notify->library->auditor->email,
        'category_name' => $notify->library->category->category,
        'path' => $path,
      ];
    });
    
    $usersIds = $allLists->pluck('id_user')->unique()->values()->toArray();

    $lists = $allLists->groupBy('id_user')->toArray();
    
    $users = User::whereIn('id_user', $usersIds)->get();
    $users->each(function($user) use ($lists) {
      $list = $lists[$user->id_user];
      Log::channel('file_notification_reminder')->info([ 'id_user' => $user->id_user, 'library_notification' => Arr::pluck($list, 'id_library') ]);
      $data['full_name'] = $user->person->full_name;
      $data['files'] = $list;
      $user->notify( new ReminderFileDays($data) );
    });
    
    FilesNotification::whereIn('id', $notifiesForUpdate)->update([ 'done' => FilesNotification::DONE ]);
  }
}