<?php

namespace App\Classes\Reminders;

use App\Classes\Utilities\DataSection;
use App\Models\V2\Audit\PlanUser;
use App\Models\V2\Audit\ActionPlan;
use App\Models\V2\Admin\User;
use App\Notifications\RemindersRequirementsNotifications;
use App\Traits\V2\ObligationTrait;
use App\Traits\V2\RequirementTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HandlerReminderActionPlan
{
  use ObligationTrait, RequirementTrait;

  protected $comandName = '';
  protected $timezone = '';

  public function __construct($comandName) 
  {
    $this->comandName = $comandName;
    $this->timezone = Config('enviroment.time_zone_carbon');
  }

  public function checkDates() 
  {
    Log::channel('action_reminder')->info("************ {$this->comandName} ************");
    
		$today = Carbon::now($this->timezone);

    $relationships = [
			'action.action_register.process.customer',
			'action.action_register.process.corporate',
			'action.requirement',
			'action.subrequirement'
		];
    $users = PlanUser::with($relationships)->without(['user'])
      ->whereHas('action', function($query) {
        $query->whereIn('id_status', [ActionPlan::PROGRESS_AP, ActionPlan::REVIEW_AP]);
      })
      ->whereHas('action.action_register', function ($query) use ($today) {
				$query->whereDate('init_date', '<=', $today->toDateString())
					->whereDate('end_date', '>=', $today->toDateString());
			})->get();
    
    $dates = $users->map(function ($item) use ($today) {
      $item->today = $today->toDateString();
      $item->close_date = Carbon::parse($item->action->close_date)->toDateString();
      $item->calculate_date = $today->add($item->days, 'day')->toDateString();
      return $item;
    });
    
    $reminders = $dates->filter(function ($item) {
      return $item->close_date == $item->calculate_date;
    });
    
    if ( $reminders->isEmpty() ) {
			Log::channel('action_reminder')->info([ 'messages' => 'Ningun registro para recordatorio ']);
			return;
		}
		// group by id_user
		$groups = $reminders->groupBy('id_user');
		$usersIds = $users->pluck('id_user')->unique()->values()->toArray();
		$users = User::whereIn('id_user', $usersIds)->get();
    
    $users->each(function($user) use ($groups) {
			$list = $groups[$user->id_user];
      $info['full_name'] = $user->person->full_name;
      $info['actions'] = $list->map(function($action) {
        $record = $action['action'];
        $item['corp_tradename'] = $record['action_register']['process']['corporate']['corp_tradename'];
        $item['audit_processes'] = $record['action_register']['process']['audit_processes'];
        $item['init_date_format'] = $record['action_register']['init_date_format']; 
        $item['end_date_format'] = $record['action_register']['end_date_format'];
        $item['type'] = $record['action_register']['origin'];
        $item['no_requirement'] = $this->getFieldRequirement($record, 'no_requirement');
				$item['requirement'] = $this->getFieldRequirement($record, 'requirement');
        $item['start_date'] = $record['init_date_format'];
        $item['close_date'] = $record['close_date_format'];
        $item['days'] = $action['days'];
        $item['path'] = (new DataSection('action_plan', $action['id_action_plan']))->getSectionPath();
        return $item;
      })->toArray();
      
      Log::channel('action_reminder')->info([ 'id_user' => $user->id_user, 'actions' => $list->pluck('id_action_plan')->toArray() ]);
      $user->notify( new RemindersRequirementsNotifications( $info ) );
    });
  }
}