<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PasswordRequest implements Rule
{
	/**
	 * Create a new rule instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Determine if the validation rule passes.
	 *
	 * @param  string  $attribute
	 * @param  mixed  $value
	 * @return bool
	 */
	public function passes($attribute, $value)
	{
		return preg_match_all('/\d/', $value) >= 2 &&
			preg_match_all('/[A-Z]/', $value) >= 2 &&
			preg_match_all('/[a-z]/', $value) >= 2 &&
			preg_match('/[\W_]/', $value);
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'La contraseña debe contener al menos 2 números, 2 letras mayúsculas, 2 letras minúsculas y 1 carácter especial.';
	}
}
