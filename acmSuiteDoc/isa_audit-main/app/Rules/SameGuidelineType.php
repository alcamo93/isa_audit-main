<?php

namespace App\Rules;

use App\Models\V2\Catalogs\Guideline;
use App\Models\V2\Catalogs\LegalClassification;
use Illuminate\Contracts\Validation\Rule;

class SameGuidelineType implements Rule
{
	protected $idGuideline = null;
	protected $idApplicationType = null;

	/**
	 * Create a new rule instance.
	 *
	 * @param integer $idApplicationType
	 * @param integer $idGuideline
	 * @return void
	 */
	public function __construct($idApplicationType, $idGuideline)
	{
		$this->idApplicationType = $idApplicationType;
		$this->$idGuideline = $idGuideline;
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
		$exist = Guideline::where('id_application_type', $this->idApplicationType)
			->whereIn('id_legal_c', [ 
				LegalClassification::AGREEMENTS, 
				LegalClassification::DECREE, 
				LegalClassification::GUIDELINES, 
				LegalClassification::NOTICE, 
				LegalClassification::REFORM 
			]);

		if ( !is_null($this->idGuideline) ) {
			$exist->where('id_guideline', '<>', $this->idGuideline);
		}
		
		return $exist->exists();
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'Verifica que los registros de los id seleccionados tenga un id_legal_c de los valores [ 4,6,7,9,14 ], que el id_application_type sea el mismo que especificas y que no sea su mismo id_guideline';
	}
}
