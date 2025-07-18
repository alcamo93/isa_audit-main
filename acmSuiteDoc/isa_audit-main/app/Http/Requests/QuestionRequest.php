<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\V2\Catalogs\QuestionType;

class QuestionRequest extends FormRequest
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
      'question' => 'required|string',
      'help_question' => 'nullable|string',
      'allow_multiple_answers' => 'required|boolean',
      'id_question_type' => [
        'required',
        'integer',
        Rule::exists('c_question_types', 'id_question_type')->where(function ($query) {
          $query->whereIn('id_question_type', [QuestionType::FEDERAL, QuestionType::STATE, QuestionType::LOCAL]);
        })
      ],
      'id_state' => [
        Rule::requiredIf(function () {
          return in_array(request('id_question_type'), [QuestionType::STATE, QuestionType::LOCAL]);
        }),
        'nullable',
        'integer',
        Rule::exists('c_states', 'id_state')
      ],
      'id_city' => [
        Rule::requiredIf( request('id_question_type') == QuestionType::LOCAL ),
        'nullable',
        'integer',
        Rule::exists('c_cities', 'id_city')->where(function ($query) {
					$query->where('id_state', $this->id_state);
				}),
      ],
    ];

    return $this->routeIs('v2.question.store') || $this->routeIs('v2.question.update') ? $rule : [];
  }
}
