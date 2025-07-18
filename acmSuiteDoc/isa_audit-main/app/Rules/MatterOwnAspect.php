<?php

namespace App\Rules;

use App\Models\V2\Catalogs\Aspect;
use Illuminate\Contracts\Validation\Rule;

class MatterOwnAspect implements Rule
{
	public $id_matter = null;
	/**
	 * Create a new rule instance.
	 *
	 * @param integer $idMatter
	 * @return void
	 */
	public function __construct($idMatter)
	{
		$this->id_matter = $idMatter;
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
		return Aspect::where('id_aspect', $value)
			->where('id_matter', $this->id_matter)
			->exists();
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'Verifica que los campos: id_matter, id_aspect; sean correctos';
	}
}
