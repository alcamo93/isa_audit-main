<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\V2\Catalogs\ApplicationType;

class LegalBasiRequest extends FormRequest
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
      'legal_basis' => 'required|string|max:100',
      'legal_quote' => 'required|string',
      'publish' => 'required|boolean',
      'order' => 'required|numeric'
    ];

    return $this->routeIs('v2.legal_basi.store') || $this->routeIs('v2.legal_basi.update') ? $rule : [];
  }
}
