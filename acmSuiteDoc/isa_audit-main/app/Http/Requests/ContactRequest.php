<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
			'ct_email' => 'required|email|max:45',
			'ct_phone_office' => 'nullable|digits_between:1,10',
			'ct_ext' => 'nullable|digits_between:1,16',
			'ct_cell' => 'required|digits_between:1,16',
			'ct_first_name' => 'required|string|max:255',
			'ct_second_name' => 'required|string|max:255',
			'ct_last_name' => 'nullable|string|max:255',
			'degree' => 'required|string|max:45',
		];

		return $this->routeIs('v2.contact.store') || $this->routeIs('v2.contact.update') ? $rules : [];
	}
}
