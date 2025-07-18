<?php

namespace App\Models\Notifications;

use App\Classes\StatusConstants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException as Exception;

class RemindersModel extends Model
{
    protected $table = 't_reminders';
    protected $primaryKey = 'id_reminder';
    /**
     * Get dates by table id
     */
    public function scopeGetReminders($query, $data){
        $query->where([
            ['id_action_plan', $data['idActionPlan']],
            ['id_obligation', $data['idObligation']],
            ['id_task', $data['idTask']],
            ['type_date', $data['typeDate']]
        ]);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Set dates
     */
    public function scopeSetReminders($query, $ids, $date){
        $model = new RemindersModel();
        $model->id_action_plan = $ids['idActionPlan'];
        $model->id_obligation = $ids['idObligation'];
        $model->id_task = $ids['idTask'];
        $model->date = $date;
        $model->type_date = $ids['typeDate'];
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Delete dates
     */
    public function scopeDeleteReminders($query, $ids, $date){
        $model = RemindersModel::where([
            ['id_action_plan', $ids['idActionPlan']],
            ['id_obligation', $ids['idObligation']],
            ['id_task', $ids['idTask']],
            ['date', $date],
            ['type_date', $ids['typeDate']]
        ]);
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::WARNING;
        }
    }
    /**
     * Get Action Plans reminders
     */
    public function scopeGetDataByUserAP($query, $idUser, $today){
        $query->join('t_action_plans', 't_action_plans.id_action_plan', 't_reminders.id_action_plan')
            ->join('t_requirements', 't_requirements.id_requirement', 't_action_plans.id_requirement')
            ->select(
                't_reminders.id_reminder', 't_requirements.no_requirement', 
                \DB::raw('DATE_FORMAT(t_action_plans.close_date, "%Y-%m-%d") AS close_date'),
                \DB::raw('DATE_FORMAT(t_action_plans.real_close_date, "%Y-%m-%d") AS real_close_date')
            )
            ->where('t_action_plans.id_user_asigned', $idUser)
            ->where('t_reminders.date', $today.' 00:00:00')
            ->whereNull('t_action_plans.id_subrequirement');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get Action Plans reminders
     */
    public function scopeGetDataByUserAPSub($query, $idUser, $today){
        $query->join('t_action_plans', 't_action_plans.id_action_plan', 't_reminders.id_action_plan')
            ->join('t_subrequirements', 't_subrequirements.id_subrequirement', 't_action_plans.id_subrequirement')
            ->select(
                't_reminders.id_reminder', 't_subrequirements.no_subrequirement', 't_reminders.id_action_plan',
                't_reminders.id_obligation', 't_reminders.id_task',
                \DB::raw('DATE_FORMAT(t_action_plans.close_date, "%Y-%m-%d") AS close_date'),
                \DB::raw('DATE_FORMAT(t_action_plans.real_close_date, "%Y-%m-%d") AS real_close_date')
            )
            ->where('t_action_plans.id_user_asigned', $idUser)
            ->where('t_reminders.date', $today.' 00:00:00')
            ->whereNotNull('t_action_plans.id_subrequirement');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get Obligations reminders
     */
    public function scopeGetDataByUserObligations($query, $idUser, $today){
        $query->join('t_obligations', 't_obligations.id_obligation', 't_reminders.id_obligation')
            ->select(
                't_reminders.id_reminder', 't_obligations.title',
                \DB::raw('DATE_FORMAT(t_obligations.renewal_date, "%Y-%m-%d") AS renewal_date'),
                \DB::raw('DATE_FORMAT(t_obligations.last_renewal_date, "%Y-%m-%d") AS last_renewal_date')
            )
            ->where('t_obligations.id_user_asigned', $idUser)
            ->where('t_reminders.date', $today.' 00:00:00');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get Tasks reminders
     */
    public function scopeGetDataByUserTasks($query, $idUser, $today){
        $query->join('t_tasks', 't_tasks.id_task', 't_reminders.id_task')
            ->select(
                't_reminders.id_reminder', 't_tasks.title', 't_tasks.id_period', 
                \DB::raw('DATE_FORMAT(t_reminders.date, "%Y-%m-%d") AS date')
            )
            ->where('t_tasks.id_user_asigned', $idUser)
            ->where('t_reminders.date', $today.' 00:00:00');
        $data = $query->get()->toArray();
        return $data;
    }
}