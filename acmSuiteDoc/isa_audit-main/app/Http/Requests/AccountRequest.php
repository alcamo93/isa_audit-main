<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AccountRequest extends FormRequest
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
			'first_name' => 'nullable|string|max:255',
			'second_name' => 'nullable|string|max:255',
			'last_name' => 'nullable|string|max:255',
			'email' => [
				'nullable',
				'email', 
				'max:255', 
				Rule::unique('t_users', 'email')->ignore(Auth::id(), 'id_user')
			],
      'secondary_email' => [
				'nullable',
				'email', 
				'max:255', 
				Rule::unique('t_users', 'secondary_email')->ignore(Auth::id(), 'id_user')
			],
      'gender' => [
        'nullable',
        Rule::in(['Femenino', 'Masculino'])
      ],
      'phone' => 'nullable|digits:10',
      'rfc' => [
        'nullable',
        'string',
        'size:13',
        'regex:/^[A-ZÑ&]{4}[0-9]{6}[A-Z0-9]{3}$/i',
      ],
      'birthdate' => 'nullable|date'
		];

		return $this->routeIs('v2.account.update') ? $rules : [];
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
      'secondary_email.unique' => 'El valor de :attribute ha sido utilizado en algún usuario de otra Planta o Cliente',
		];
	}
}
