<?php

namespace App\Listeners;

use App\Classes\Utilities\DataSection;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\CheckObligation;
use App\Notifications\ObligationsReminderNotification;

class SendEmailReminderObligation implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param CheckObligation $event
     * @return void
     */
    public function handle(CheckObligation $event)
    {
        $user = $event->obligation->auditor->user;
        $all = $event->obligation->load('obligation_register.aplicability_register.process');
        $requirement = $event->obligation->requirement;
        $subrequirement = $event->obligation->subrequirement;
        $noRequirement = !is_null($subrequirement) ? $subrequirement->no_subrequirement : $requirement->no_requirement;
        $requirement = !is_null($subrequirement) ? $subrequirement->subrequirement : $requirement->requirement;

        $data['full_name'] = $event->obligation->auditor->user->person->full_name;
        $data['corp_trademark'] = $all->aplicability_register->process->corporate->corp_trademark;
        $data['audit_processes'] = $all->aplicability_register->process->audit_processes;
        $data['no_requirement'] = "{$noRequirement} {$requirement}";
        $data['end_date'] = $event->obligation->end_date_format;
        $data['days'] = $event->obligation->auditor->days;
        $data['path'] = (new DataSection('obligation', $event->obligation->id_obligation))->getSectionPath();
        
        $user->notify( new ObligationsReminderNotification($data) );
    }
}
