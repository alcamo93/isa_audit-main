<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\V2\Catalogs\ApplicationType;
use App\Rules\SameGuidelineType;

class GuidelineRequest extends FormRequest
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
      'guideline' => 'required|string|max:800',
      'initials_guideline' => 'required|string|max:250',
      'last_date' => 'required|date',
      'id_application_type' => [
        'required',
        'integer',
        'exists:c_application_types,id_application_type',
        Rule::in([ApplicationType::FEDERAL, ApplicationType::STATE, ApplicationType::LOCAL]),
      ],
      'id_legal_c' => 'required|exists:c_legal_classification,id_legal_c',
      'id_state' => 'required_if:id_application_type,2,4|nullable|exists:c_states,id_state',
      'id_city' => [
        Rule::requiredIf($this->id_application_type == ApplicationType::LOCAL),
        'nullable', 
        Rule::exists('c_cities', 'id_city')->where(function ($query) {
          $query->where('id_state', $this->id_state);
        }),
      ],
      'guidelines_ext' => 'nullable|array',
      'guidelines_ext.*' => [
        'integer',
        'distinct',
        new SameGuidelineType( $this->id_application_type, $this->route('idGuideline') )
      ],
      'objective' => 'nullable|string'
    ];
    return $this->routeIs('v2.guideline.store') || $this->routeIs('v2.guideline.update') ? $rule : [];
  }
}
