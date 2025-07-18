<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AspectRequest extends FormRequest
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
      'aspect' => [
        'required',
        'string',
        'max:50',
        Rule::unique('c_aspects', 'aspect')
      ],
      'order' => 'required|integer'
    ];

    return $this->routeIs('v2.matter.aspect.store') || $this->routeIs('v2.matter.aspect.update') ? $rule : [];
  }
}