<?php

namespace App\Http\Requests;

use App\Rules\CustomerOwnsCorporate;
use Illuminate\Foundation\Http\FormRequest;

class ContractRequest extends FormRequest
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
			'id_customer' => 'required|integer|exists:t_customers,id_customer',
			'id_corporate' => [
				'required', 
				'integer', 
				'exists:t_corporates,id_corporate', 
				new CustomerOwnsCorporate( request('id_customer') )
			],
			'contract' => 'required|string|max:50',
			'id_license' => 'required|integer|exists:licenses,id',
			'start_date' => 'required|date',
			'end_date' => 'required|date',
		];

		return $this->routeIs('v2.contract.store') || $this->routeIs('v2.contract.update') ? $rules : [];
	}
}
