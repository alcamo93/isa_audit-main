<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicabilityEvaluateRequest extends FormRequest
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
    if ( $this->routeIs('v2.process.applicability.matter.aspect.evaluate.answer') ) {
      return [
        '*.id_answer_question' => 'required|integer|distinct|exists:t_answers_question,id_answer_question',
      ];
    }

    if ( $this->routeIs('v2.process.applicability.matter.aspect.evaluate.comment') ) {
      return [
        'comment' => 'required|string|max:255',
      ];
    }

    return [];
  }
}
