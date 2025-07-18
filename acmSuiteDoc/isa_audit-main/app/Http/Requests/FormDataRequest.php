<?php

namespace App\Http\Requests;

use App\Rules\MatterOwnAspect;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormDataRequest extends FormRequest
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
		$rules =  [
			'name' => [
				'required',
				'min:1',
				'max:255',
				'unique:forms',
				Rule::unique('forms', 'name')->ignore($this->route('id'), 'id')
			],
			'matter_id' => 'required|integer|exists:c_matters,id_matter',
			'aspect_id' => [
				'required',
				'integer',
				'exists:c_aspects,id_aspect',
				new MatterOwnAspect( $this->matter_id )
			],
		];

		return $this->routeIs('forms.store') || $this->routeIs('forms.update') ? $rules : [];
	}
}
