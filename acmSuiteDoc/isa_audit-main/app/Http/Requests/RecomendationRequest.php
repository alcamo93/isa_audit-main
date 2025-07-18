<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecomendationRequest extends FormRequest
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
      'recomendation' => 'required|string',
    ];

    return $this->routeIs('req.recomendation.store') || $this->routeIs('req.recomendation.update') 
      || $this->routeIs('sub.recomendation.store') || $this->routeIs('sub.recomendation.update')
      || $this->routeIs('v2.specific.req.recomendation.store') || $this->routeIs('v2.specific.req.recomendation.update') ? $rule : [];
  }
}