<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RiskEvaluateRequest extends FormRequest
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
      'answer' => [
        'required',
        'numeric'
      ],
      'id_risk_category' => [
        'required',
        'integer',
        Rule::exists('c_risk_categories', 'id_risk_category')
      ],
      'id_risk_attribute' => [
        'required',
        'integer',
        Rule::exists('c_risk_attributes', 'id_risk_attribute')
      ],
    ];

    return $rule;
  }
}
