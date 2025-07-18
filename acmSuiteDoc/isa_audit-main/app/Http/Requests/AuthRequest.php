<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
		if ( $this->routeIs('v2.auth.login') ) {
			return [
				'email' => 'required|email|max:255|exists:t_users,email',
				'password' => 'required|string|min:8|max:255',
			];
		}

		if ( $this->routeIs('v2.auth.refresh') ) {
			return [
				'refresh_token' => 'required|string'
			];
		}

		return [];
	}
}
