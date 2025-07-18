<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswerQuestionRequest extends FormRequest
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
      'description' => 'required|string',
      'id_answer_value' => 'required|integer|exists:t_answer_values,id_answer_value',
    ];

    return $this->routeIs('v2.answer.store') || $this->routeIs('v2.answer.update') ? $rule : [];
  }
}
