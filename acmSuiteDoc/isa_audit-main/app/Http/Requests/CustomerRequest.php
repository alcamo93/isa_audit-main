<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
			'cust_tradename' => 'required|string|max:100',
			'cust_trademark' => 'required|string|max:100'
		];

		return $this->routeIs('v2.customer.store') || $this->routeIs('v2.customer.update') ? $rules : [];
	}
}
