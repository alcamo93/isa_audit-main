<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MatterRequest extends FormRequest
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
      'matter' => [
        'required',
        'string',
        'max:50',
        Rule::unique('c_matters', 'matter')
      ],
      'order' => 'required|integer'
    ];

    return $this->routeIs('v2.matter.store') || $this->routeIs('v2.matter.update') ? $rule : [];
  }
}
