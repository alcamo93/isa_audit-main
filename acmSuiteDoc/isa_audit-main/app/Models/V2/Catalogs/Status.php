<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\V2\UtilitiesTrait;

class Status extends Model
{
	use HasFactory, UtilitiesTrait;

	protected $table = 'c_status';
	protected $primaryKey = 'id_status';

	/**
	 * Remove timestamps
	 */
	public $timestamps = false;

	/**
	 * Attributes
	 */
	protected $appends = [
		'color',
		'color_hexadecimal',
	];

	/**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_filter = [
		'group' => ['field' => 'group', 'type' => 'number', 'relation' => null],
	];
	
	const GENERAL_GROUP = 1;
  const APLICABILITY_GROUP = 2;
	const AUDIT_GROUP = 3;
	const TASK_GROUP = 4;
	const OBLIGATION_GROUP = 6;
	const ACTION_PLAN_GROUP = 7;

	/**
	 * Color
	 */
	public function getColorAttribute()
	{
		$keyColor = [
			'ACTIVE_GENERAL' => 'success',
			'INCATIVE_GENERAL' => 'danger',
			'NOT_CLASSIFIED_APLICABILITY' => 'warning',
			'CLASSIFIED_APLICABILITY' => 'success',
			'EVALUATING_APLICABILITY' => 'info',
			'FINISHED_APLICABILITY' => 'success',
			'NOT_AUDITED_AUDIT' => 'warning',
			'AUDITED_AUDIT' => 'success',
			'AUDITING_AUDIT' => 'info',
			'FINISHED_AUDIT_AUDIT' => 'success',
			'NO_STARTED_TASK' => 'warning',
			'PROGRESS_TASK' => 'warning',
			'UNASSIGNED_AP' => 'secondary',
			'EXPIRED_TASK' => 'danger',
			'REVIEW_TASK' => 'primary',
			'PROGRESS_AP' => 'warning',
			'COMPLETED_AP' => 'success',
			'REVIEW_AP' => 'primary',
			'APPROVED_TASK' => 'success',
			'NO_STARTED_OBLIGATION' => 'secondary',
			'FOR_EXPIRED_OBLIGATION' => 'warning',
			'EXPIRED_OBLIGATION' => 'danger',
			'APPROVED_OBLIGATION' => 'success',
			'NO_DATES_OBLIGATION' => 'primary',
			'EXPIRED_AP' => 'danger',
			'REJECTED_TASK' => 'danger',
			'CLOSED_AP' => 'danger',
			'NO_EVIDENCE_OBLIGATION' => 'secondary',
			'REVIEW_OBLIGATION' => 'primary'
		];

		return $keyColor[$this->key];
	}

	/**
	 * Color hexadecimal
	 */
	public function getColorHexadecimalAttribute()
	{
		$keyColor = [
			'ACTIVE_GENERAL' => '#28a745',
			'INCATIVE_GENERAL' => '#dc3545',
			'NOT_CLASSIFIED_APLICABILITY' => '#ffc107',
			'CLASSIFIED_APLICABILITY' => '#28a745',
			'EVALUATING_APLICABILITY' => '#007bff',
			'FINISHED_APLICABILITY' => '#28a745',
			'NOT_AUDITED_AUDIT' => '#ffc107',
			'AUDITED_AUDIT' => '#28a745',
			'AUDITING_AUDIT' => '#007bff',
			'FINISHED_AUDIT_AUDIT' => '#28a745',
			'NO_STARTED_TASK' => '#ffc107',
			'PROGRESS_TASK' => '#ffc107',
			'UNASSIGNED_AP' => '#6c757d',
			'EXPIRED_TASK' => '#dc3545',
			'REVIEW_TASK' => '#007bff',
			'PROGRESS_AP' => '#ffc107',
			'COMPLETED_AP' => '#28a745',
			'REVIEW_AP' => '#007bff',
			'APPROVED_TASK' => '#28a745',
			'NO_STARTED_OBLIGATION' => '#6c757d',
			'FOR_EXPIRED_OBLIGATION' => '#ffc107',
			'EXPIRED_OBLIGATION' => '#dc3545',
			'APPROVED_OBLIGATION' => '#28a745',
			'NO_DATES_OBLIGATION' => '#007bff',
			'EXPIRED_AP' => '#dc3545',
			'REJECTED_TASK' => '#dc3545',
			'CLOSED_AP' => '#dc3545',
			'NO_EVIDENCE_OBLIGATION' => '#6c757d',
			'REVIEW_OBLIGATION' => '#007bff',
		];
		
		return $keyColor[$this->key];
	}
}