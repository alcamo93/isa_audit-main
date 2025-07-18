<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ObligationRequest extends FormRequest
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
		$rules = [
			'id_user' => 'required|integer|exists:t_users,id_user',
		];

		return $this->routeIs('v2.process.applicability.obligation.records.file') || $this->routeIs('v2.process.applicability.obligation.records.user') ? $rules : [];
	}
}
