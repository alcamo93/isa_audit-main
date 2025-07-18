<?php

namespace App\Classes\Reminders;

use App\Models\V2\Admin\Contract;
use App\Models\V2\Admin\User;
use App\Models\V2\Catalogs\ProfileType;
use App\Notifications\ReminderContracts;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HandlerReminderContracts
{
  protected $timezone = '';
  protected $limitDays = 90;
  protected $todayDate = null;
  protected $futureDate = null;
  protected $path = null;
  protected $admins = null;

  public function __construct() 
  {
    $this->timezone = Config('enviroment.time_zone_carbon');
    $today = Carbon::now($this->timezone);
    $this->todayDate = $today->toDateString();
    $this->futureDate = $today->addDays($this->limitDays)->toDateString();
    $domain = Config('enviroment.domain_frontend');
    $this->path = "{$domain}v2/contract/view";
    $this->getAdmins();
  }

  private function getAdmins() 
  {
    $filter = fn($query) => $query->where('id_profile_type', ProfileType::ADMIN_GLOBAL);
    $relationships = ['profile' => $filter];
    $noLoadRelationships = ['status','image'];
    $this->admins = User::with($relationships)->without($noLoadRelationships)
      ->whereHas('profile', $filter)->where('id_status', User::ACTIVE)->get();
  }

  public function notifyContract() 
  {
    $relationships = ['license','customer','corporate.contact'];
    $contracts = Contract::with($relationships)->where('id_status', Contract::ACTIVE)
      ->whereDate( 'end_date', '>=', $this->todayDate )
      ->whereDate( 'end_date', '<=', $this->futureDate )
      ->whereHas('corporate.contact')
      ->get();
    
    $dataContracts = $contracts->map(function($contract) {
      $endDate = Carbon::parse($contract->end_date, $this->timezone);
      $info['instance'] = $contract->corporate->contact;
      $info['info']['cust_tradename'] = $contract->customer->cust_tradename_format;
      $info['info']['corp_tradename'] = $contract->corporate->corp_tradename_format;
      $info['info']['license'] = $contract->license->name;
      $info['info']['contract'] = $contract->contract;
      $info['info']['start_date'] = $contract->start_date_format;
      $info['info']['end_date'] = $contract->end_date_format;
      $info['info']['days'] = $endDate->diffInDays($this->todayDate);
      $info['info']['full_name'] = $contract->corporate->contact->full_name;
      $info['info']['path'] = $this->path;
      return $info;
    });

    Log::channel('contract_notification_reminder')->info('************ reminder:contracts ************');

    $dataContracts->each(function($contract) {
      Log::channel('contract_notification_reminder')->info($contract['info']);
      $contract['instance']->notify( new ReminderContracts($contract['info']) );
    });

    $infoForAdmins = $dataContracts->pluck('info');
    $this->admins->each(function($admin) use ($infoForAdmins) {
      $infoNotify['full_name'] = $admin->person->full_name;
      $infoNotify['contracts'] = $infoForAdmins;
      Log::channel('contract_notification_reminder')->info($infoNotify);
      $admin->notify( new ReminderContracts($infoNotify, true) );
    });
  }
}