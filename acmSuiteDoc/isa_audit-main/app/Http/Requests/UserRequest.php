<?php

namespace App\Http\Requests;

use App\Rules\CustomerOwnsCorporate;
use App\Rules\PasswordRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
			'id_status' => 'required|integer|exists:c_status,id_status',
			'id_profile' => 'required|integer|exists:t_profiles,id_profile',
			'first_name' => 'required|string|max:255',
			'second_name' => 'required|string|max:255',
			'last_name' => 'nullable|string|max:255',
			'email' => [
				'required', 
				'email', 
				'max:255', 
				Rule::unique('t_users', 'email')->ignore($this->route('id'), 'id_user')
			],
		];

		if ( $this->routeIs('v2.user.update.password') ) {
			return [
				'password' => [
					'required',
					'string',
					'min:8',
					'confirmed',
					new PasswordRequest()
				]
			];
		}

		return $this->routeIs('v2.user.store') || $this->routeIs('v2.user.update') ? $rules : [];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages()
	{
		return [
			'email.unique' => 'El valor de :attribute ha sido utilizado en algún usuario de otra Planta o Cliente',
		];
	}
}
