<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
	protected $table = 't_reminders';
	protected $primaryKey = 'id_reminder';

	protected $fillable = [
		'date', 
		'type_date', 
		'id_action_plan', 
		'id_obligation',
		'id_task',
	];
}