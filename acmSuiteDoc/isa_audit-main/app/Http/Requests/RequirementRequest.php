<?php

namespace App\Http\Requests;

use App\Models\V2\Catalogs\ApplicationType;
use App\Models\V2\Catalogs\Evidence;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\V2\Catalogs\RequirementType;

class RequirementRequest extends FormRequest
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
			'order' => 'required|numeric',
			'id_evidence' => [
				'required',
				'integer',
				Rule::exists('c_evidences', 'id_evidence')
			],
			'id_condition' => [
				'required',
				'integer',
				Rule::exists('c_conditions', 'id_condition')
			],
			'document' => [
				Rule::requiredIf( request('id_evidence') == Evidence::SPECIFIC ),
				'nullable',
				'string',
				'max:400',
			],
			'id_requirement_type' => [
				'required',
				'integer',
				Rule::exists('c_requirement_types', 'id_requirement_type')->where(function ($query) {
					$query->whereIn('id_requirement_type', [
						RequirementType::IDENTIFICATION_FEDERAL,
						RequirementType::IDENTIFICATION_STATE,
						RequirementType::REQUIREMENT_STATE,
						RequirementType::REQUIREMENT_COMPOSE,
						RequirementType::REQUIREMENT_LOCAL,
						RequirementType::IDENTIFICATION_LOCAL,
						RequirementType::REQUIREMENT_IDENTIFICATION_COMPOSE
					]);
				})
			],
			'id_application_type' => [
				'required',
				'integer',
				Rule::exists('c_application_types', 'id_application_type')
			],
			'id_state' => [
				Rule::requiredIf(function () {
          return in_array(request('id_application_type'), [ApplicationType::STATE, ApplicationType::LOCAL]);
        }),
				'nullable',
				'integer',
				Rule::exists('c_states', 'id_state')
			],
			'id_city' => [
				Rule::requiredIf( request('id_application_type') == ApplicationType::LOCAL ),
				'nullable',
				'integer',
				Rule::exists('c_cities', 'id_city')->where(function ($query) {
					$query->where('id_state', $this->id_state);
				}),
			],
			'no_requirement' => 'required|string|max:255',
			'requirement' => 'required|string',
			'description' => 'nullable|string',
			'help_requirement' => 'nullable|string',
			'acceptance' => 'nullable|string',
			'id_periodicity' => 'nullable|integer|exists:periodicities,id',
		];

		return $this->routeIs('v2.requirement.store') || $this->routeIs('v2.requirement.update') ? $rule : [];
	}
}
