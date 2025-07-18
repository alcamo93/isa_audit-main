<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RiskHelpRequest extends FormRequest
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
      'risk_help' => 'required|string|max:255',
      'standard' => 'required|string',
      'value' => 'required|numeric',
      'id_risk_attribute' => 'required|integer|exists:c_risk_attributes,id_risk_attribute'
    ];

    return $this->routeIs('v2.risk.help.store') || $this->routeIs('v2.risk.help.update') ? $rule : [];
  }
}