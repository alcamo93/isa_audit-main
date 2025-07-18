<?php

namespace App\Http\Requests;

use App\Models\V2\Catalogs\Evidence;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\V2\Catalogs\RequirementType;

class SubrequirementRequest extends FormRequest
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
              RequirementType::SUB_IDENTIFICATION_FEDERAL,
              RequirementType::SUB_IDENTIFICATION_STATE,
              RequirementType::SUBREQUIREMENT_STATE,
              RequirementType::SUBREQUIREMENT_LOCAL,
              RequirementType::SUB_IDENTIFICATION_LOCAL
            ]);
          })
      ],
      'no_subrequirement' => 'required|string|max:255',
      'subrequirement' => 'required|string',
      'description' => 'nullable|string',
      'help_subrequirement' => 'nullable|string',
      'acceptance' => 'nullable|string',
      'id_periodicity' => 'nullable|integer|exists:periodicities,id',
    ];

    return $this->routeIs('v2.subrequirement.store') || $this->routeIs('v2.subrequirement.update') ? $rule : [];
  }
}
