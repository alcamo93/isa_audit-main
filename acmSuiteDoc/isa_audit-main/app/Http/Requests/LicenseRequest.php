<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LicenseRequest extends FormRequest
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
			'name' => 'required|string|max:50',
			'num_period' => 'required|integer|min:1',
			'period_id' => 'required|integer|exists:periods,id',
			'status_id' => 'required|integer|exists:c_status,id_status',
			'quantity' => 'required|array',
			'quantity.*.id_profile_type' => 'required|integer|exists:t_profile_types,id_profile_type|distinct',
			'quantity.*.quantity' => 'required|integer|min:1',
		];

		return $this->routeIs('v2.license.store') || $this->routeIs('v2.license.update') ? $rules : [];
	}
}
