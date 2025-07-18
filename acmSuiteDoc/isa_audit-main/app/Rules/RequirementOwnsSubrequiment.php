<?php

namespace App\Rules;

use App\Models\V2\Catalogs\Subrequirement;
use Illuminate\Contracts\Validation\Rule;

class RequirementOwnsSubrequiment implements Rule
{
	public $id_requirement = null;
	/**
	 * Create a new rule instance.
	 *
	 * @param integer $idRequirment
	 * @return void
	 */
	public function __construct($idRequirment)
	{
		$this->id_requirement = $idRequirment;
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
		return Subrequirement::where('id_subrequirement', $value)
			->where('id_requirement', $this->id_requirement)
			->exists();
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'Verifica que los campos: id_requirement, id_subrequirement; sean correctos';
	}
}
