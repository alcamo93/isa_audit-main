<?php

namespace App\Classes;

use Carbon\Carbon;
use App\Models\Notifications\RemindersModel;
use App\Models\Audit\ActionPlansModel;
use App\User;

class ReminderStrcuture
{
    /**
     * Structure reminder
     */
    public function structure($idUser){
        $today = Carbon::now('America/Mexico_City')->toDateString();
        $dataUser = User::GetUserInfo($idUser);
        $data['user'] = $dataUser[0];
        $requirements = RemindersModel::getDataByUserAP($idUser, $today);
        $subrequirements = RemindersModel::GetDataByUserAPSub($idUser, $today);
        $data['actionPlan'] = $this->structureAP($requirements, $subrequirements);
        $data['obligations'] = RemindersModel::getDataByUserObligations($idUser, $today);
        $tasks = RemindersModel::getDataByUserTasks($idUser, $today);
        $data['tasks'] = $this->structureTask($tasks);
        if (sizeof($data['actionPlan']) == 0  && sizeof($data['obligations']) == 0  && sizeof($data['tasks']) == 0){
            $data = [];
        }
        return $data;
    }
    /**
     * add requiremnts and subrequiremnts 
     */
    public function structureAP($requirements, $subrequirements){
        $data = [];
        foreach ($requirements as $req) {
            $reqTemp['id_reminder'] = $req['id_reminder'];
            $reqTemp['name'] = $req['no_requirement'];
            $reqTemp['close_date'] = $req['close_date'];
            $reqTemp['real_close_date'] = $req['real_close_date'];
            array_push($data, $reqTemp);
        }
        foreach ($subrequirements as $sub) {

            $infoAP = ActionPlansModel::GetActionPlan($sub['id_action_plan']);
            $subTemp['id_reminder'] = $sub['id_reminder'];
            $subTemp['name'] = $infoAP[0]['no_requirement'].' '.$sub['no_subrequirement'];
            $subTemp['close_date'] = $sub['close_date'];
            $subTemp['real_close_date'] = $sub['real_close_date'];
            array_push($data, $subTemp);
        }
        return $data;
    }
    /**
     * add date close in tasks
     */
    public function structureTask($tasks){
        $data = [];
        $object = new Periods();
        foreach ($tasks as $task) {
            $dates = $object->createDates($task['date'], $task['id_period']);
            $taskTemp['id_reminder'] = $task['id_reminder'];
            $taskTemp['title'] = $task['title'];
            $taskTemp['close_date'] = $dates['closeDate'];
            array_push($data, $taskTemp);
        }
        return $data;
    }
}