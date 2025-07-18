<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ObligationRegisterRequest extends FormRequest
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
			'init_date' => 'required|date',
			'end_date' => 'required|date|after:init_date',
		];

		return $this->routeIs('v2.process.applicability.obligation.register.store') ? $rules : [];
	}
}
