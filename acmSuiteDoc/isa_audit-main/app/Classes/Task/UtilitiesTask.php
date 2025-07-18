<?php

namespace App\Classes\Task;

use App\Classes\Utilities\DataSection;
use App\Models\V2\Admin\User;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Audit\Task;
use App\Notifications\TaskAssignmentNotifications;

class UtilitiesTask
{
  public function __construct()
  {
    
  }

	/**
	 * Build structure notify user by task
	 */
	private function buildTaskUserNotify($idTask, $id_user)
	{
		$processCallback = function ($query) {
			$query->addSelect([
				'audit_processes' => ProcessAudit::select('audit_processes')
					->whereColumn('id_audit_processes', 't_action_plans.id_audit_processes')
					->take(1)
			]);
		};
		$relationships = [
			'auditors',
			'action' => $processCallback,
			'action.requirement',
			'action.subrequirement',
		];
		$task = Task::with($relationships)->find($idTask);
		$forName = $task->action;
		$origin = $task->action->action_register->origin;
		$reqName = (is_null($forName->subrequirement)) ? $forName->requirement->no_requirement : $forName->requirement->no_requirement . ' - ' . $forName->subrequirement->no_subrequirement;
		$taskUser = collect($task->auditors);
		$userFind = $taskUser->firstWhere('id_user', $id_user);
		$link = (new DataSection('task', $task->id_task))->getSectionPath();
		$typeLevels = [
			1 => 'de Aprobación',
			2 => 'de Aprobación Secundario',
			3 => 'de Aprobación Terciario'
		];
		$isMainTask = boolval($task->main_task) ? 'Tarea' : 'Subtarea';
		$data = [
			'title' => 'Le ha sido asignada una tarea',
			'body' => "Ha sido asignado como 
                <b>Responsable {$typeLevels[$userFind->pivot->level]}</b>
                de la <b>{$isMainTask} {$task->title}</b> 
                para el <b>No. req - {$reqName}</b> del <b>{$origin}</b> correspodiente 
                a la aplicabilidad <b>{$forName->audit_processes}</b>",
			'description' => '',
			'link' => $link

		];
		return $data;
	}
	/**
	 * send notification to user
	 */
	public function notifyTaskUser($idTask, $auditors)
	{
		$userIds = collect($auditors)->pluck('id_user')->toArray();
		$users = User::whereIn('id_user', $userIds)->get();
		foreach ($users as $user) {
			$build = $this->buildTaskUserNotify($idTask, $user['id_user']);
			$user->notify(new TaskAssignmentNotifications($build));
		}
	}
}
