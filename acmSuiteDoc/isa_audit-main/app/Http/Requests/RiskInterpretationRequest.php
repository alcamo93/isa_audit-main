<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RiskInterpretationRequest extends FormRequest
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
      'interpretations' => 'array|size:3',
      'interpretations.*.interpretation' => 'required|string|in:Bajo,Medio,Alto',
      'interpretations.*.interpretation_min' => 'required|integer|between:1,100',
      'interpretations.*.interpretation_max' => 'required|integer|between:1,100|gt:interpretations.*.interpretation_min',
    ];

    return $this->routeIs('v2.risk.interpretation.store') ? $rule : [];
  }
}