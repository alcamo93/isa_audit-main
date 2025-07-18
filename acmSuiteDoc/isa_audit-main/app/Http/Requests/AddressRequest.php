<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\V2\Admin\Address;

class AddressRequest extends FormRequest
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
			'street' => 'required|string|max:150',
			'ext_num' => 'nullable|string|max:60',
			'int_num' => 'nullable|string|max:60',
			'zip' => 'nullable|digits_between:1,10',
			'suburb' => 'required|string|max:50',
			'type' => [ 'required', Rule::in([Address::PHYSICAL, Address::FISCAL]) ],
			'id_country' => 'required|integer|exists:c_countries,id_country',
			'id_state' => [
				'required',
				'integer',
				Rule::exists('c_states', 'id_state')->where(function ($query) {
					$query->where('id_country', $this->id_country);
				}),
			],
			'id_state' => 'required|integer|exists:c_states,id_state',
			'id_city' => [
				'required',
				'integer',
				Rule::exists('c_cities', 'id_city')->where(function ($query) {
					$query->where('id_state', $this->id_state);
				}),
			]
		];

		return $this->routeIs('v2.address.store') || $this->routeIs('v2.address.update') ? $rules : [];
	}
}
