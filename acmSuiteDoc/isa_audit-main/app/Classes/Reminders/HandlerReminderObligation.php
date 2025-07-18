<?php

namespace App\Classes\Reminders;

use App\Classes\Utilities\DataSection;
use App\Models\V2\Audit\Obligation;
use App\Models\V2\Audit\ObligationUser;
use App\Models\V2\Admin\User;
use App\Notifications\ReminderObligation;
use App\Traits\V2\ObligationTrait;
use App\Traits\V2\RequirementTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HandlerReminderObligation
{
  use RequirementTrait, ObligationTrait;

  protected $comandName = '';
  protected $timezone = '';

  public function __construct($comandName) 
  {
    $this->comandName = $comandName;
    $this->timezone = Config('enviroment.time_zone_carbon');
  }

  public function checkDates() 
  {
    Log::channel('obligation_reminder')->info("************ {$this->comandName} ************");
    $today = Carbon::now($this->timezone);

    $relationships = [
			'obligation.obligation_register.process.customer',
			'obligation.obligation_register.process.corporate',
      'obligation' => fn($query) => $query->with(['evaluates.library']),
			'obligation.requirement',
			'obligation.subrequirement'
		];
    
    $users = ObligationUser::with($relationships)->whereNotNull('days')
      ->whereHas('obligation', function($query) {
        $query->whereNotIn('id_status', [Obligation::NO_STARTED_OBLIGATION, Obligation::EXPIRED_OBLIGATION, Obligation::NO_DATES_OBLIGATION]);
      })
      ->whereHas('obligation.obligation_register', function ($query) use ($today) {
				$query->whereDate('init_date', '<=', $today->toDateString())
					->whereDate('end_date', '>=', $today->toDateString());
			})->get();

    $dates = $users->map(function ($item) use ($today) {
      $item->close_date = Carbon::parse($item->obligation->end_date)->toDateString();
      $item->calculate_date = $today->add($item->days, 'day')->toDateString();
      return $item;
    });

    $reminders = $dates->filter(function ($item) {
      return $item->close_date == $item->calculate_date;
    });


    if ( $reminders->isEmpty() ) {
			Log::channel('obligation_reminder')->info([ 'messages' => 'Ningun registro para recordatorio ']);
			return;
		}
		// group by id_user
		$groups = $reminders->groupBy('id_user');
		$usersIds = $users->pluck('id_user')->unique()->values()->toArray();
		$users = User::whereIn('id_user', $usersIds)->get();

    $users->each(function($user) use ($groups) {
			$list = $groups[$user->id_user];
      $info['full_name'] = $user->person->full_name;
      $info['obligations'] = $list->map(function($obligation) {
        $record = $obligation['obligation']; 
        $fileName = sizeof($record['evaluates']) > 0 ? $record['evaluates'][0]['library']['name'] : '---';
        $item['corp_tradename'] = $record['obligation_register']['aplicability_register']['process']['corporate']['corp_tradename'];
        $item['audit_processes'] = $record['obligation_register']['aplicability_register']['process']['audit_processes'];
        $item['init_date_format'] = $record['obligation_register']['init_date_format']; 
        $item['end_date_format'] = $record['obligation_register']['end_date_format'];
        $item['no_requirement'] = $this->getFieldRequirement($record, 'no_requirement');
				$item['requirement'] = $this->getFieldRequirement($record, 'requirement');
        $item['start_date'] = $record['init_date_format'];
        $item['close_date'] = $record['end_date_format'];
        $item['days'] = $obligation['days'];
        $item['status'] = $record['status']['status'];
        $item['file_name'] = $fileName;
        $item['path'] = (new DataSection('obligation', $obligation['id_obligation']))->getSectionPath();
        return $item;
      })->toArray();
      
      Log::channel('obligation_reminder')->info([ 'id_user' => $user, 'obligations' => $list->pluck('id_obligation')->toArray() ]);
      $user->notify( new ReminderObligation( $info ) );
    });
  }
}