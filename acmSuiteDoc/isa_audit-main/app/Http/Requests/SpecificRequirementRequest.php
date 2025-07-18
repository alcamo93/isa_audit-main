<?php

namespace App\Http\Requests;

use App\Models\V2\Catalogs\ApplicationType;
use App\Rules\CustomerOwnsCorporate;
use App\Rules\MatterOwnAspect;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SpecificRequirementRequest extends FormRequest
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
		$rule = [
			'id_customer' => 'required|integer|exists:t_customers,id_customer',
			'id_corporate' => [
				'required', 
				'integer', 
				'exists:t_corporates,id_corporate',
				new CustomerOwnsCorporate( request('id_customer') )
			],
			'id_matter' => 'required|integer|exists:c_matters,id_matter',
			'id_aspect' => [
				'required', 
				'integer', 
				'exists:c_aspects,id_aspect',
				new MatterOwnAspect( request('id_matter') )
			],
			'order' => 'required|numeric',
			'id_application_type' => [
				'required',
				'integer',
				Rule::exists('c_application_types', 'id_application_type')->where(function ($query) {
					$query->whereIn('id_application_type', [
						ApplicationType::CORPORATE,
						ApplicationType::CONDITIONER,
						ApplicationType::ACT
					]);
				})
			],
			'no_requirement' => 'required|string|max:255',
			'requirement' => 'required|string',
			'description' => 'nullable|string',
		];

		return $this->routeIs('v2.specific.requirement.store') || $this->routeIs('v2.specific.requirement.update') ? $rule : [];
	}
}
