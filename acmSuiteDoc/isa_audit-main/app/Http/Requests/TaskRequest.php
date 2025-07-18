<?php

namespace App\Http\Requests;

use App\Rules\DatesInDateRange;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class TaskRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Prepare the data for validation.
	 *
	 * @return void
	 */
	protected function prepareForValidation()
	{
		$this->merge([
			'stage' => 1,
		]);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$rules = [
      'title' => 'required|max:255',
      'task' => 'required|string',
      'init_date' => 'required|date', 
			'close_date' => 'required|date|after_or_equal:init_date',
      'main_task' => 'required|boolean',
			'auditors' => 'required|array',
      'auditors.*.id_user' => 'required|integer|exists:t_users,id_user',
			'auditors.*.level' => 'required|integer',
			'notify_dates' => 'required|array',
			'notify_dates.*' => [
				'distinct',
				new DatesInDateRange( request('init_date'), request('close_date'), true ),
			]
		];

		if ( $this->routeIs('v2.process.applicability.section.action.plan.task.main') ) 
		{
			$rulesMain = Arr::except($rules, ['title', 'task', 'main_task']);
			return $rulesMain;
		}

		if ( $this->routeIs('v2.process.applicability.section.action.plan.task.expired') ) {
			return [
				'cause'=> 'required|string',
        'extension_date'=> 'required|date',
			];
		}
		
		return $this->routeIs('v2.process.applicability.section.action.plan.task.store') || $this->routeIs('v2.process.applicability.section.action.plan.task.update') ? $rules : [];
	}
}
