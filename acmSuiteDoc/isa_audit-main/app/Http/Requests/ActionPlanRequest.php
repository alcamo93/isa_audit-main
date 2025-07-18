<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActionPlanRequest extends FormRequest
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
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		if ( $this->routeIs('v2.process.applicability.section.action.plan.priority') ) {
			return [
				'id_priority' => 'required|integer|exists:c_priority,id_priority',
			];
		}

		if ( $this->routeIs('v2.process.applicability.section.action.plan.user') ) {
			return [
				'*.id_user' => 'required|integer|exists:t_users,id_user|distinct',
				'*.days' => 'required|integer',
				'*.level' => 'required|integer',
			];
		}
		
		if ( $this->routeIs('v2.process.applicability.section.action.plan.expired') ) {
			return [
				'cause'=> 'required|string',
        'extension_date'=> 'required|date',
			];
		}

		return [];
	}
}
