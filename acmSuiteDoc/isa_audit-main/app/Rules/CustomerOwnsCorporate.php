<?php

namespace App\Rules;

use App\Models\V2\Admin\Corporate;
use Illuminate\Contracts\Validation\Rule;

class CustomerOwnsCorporate implements Rule
{
	public $id_customer = null;
	/**
	 * Create a new rule instance.
	 *
	 * @param integer $idCustomer
	 * @return void
	 */
	public function __construct($idCustomer)
	{
		$this->id_customer = $idCustomer;
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
		return Corporate::where('id_corporate', $value)
			->where('id_customer', $this->id_customer)
			->exists();
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'Verifica que los campos: id_customer, id_corporate; sean correctos';
	}
}
