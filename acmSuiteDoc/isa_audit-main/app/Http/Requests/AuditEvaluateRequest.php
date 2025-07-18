<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\V2\Audit\Audit;

class AuditEvaluateRequest extends FormRequest
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
    if ( $this->routeIs('v2.process.applicability.matter.aspect.evaluate.finding') ) {
      return [
        'finding' => 'required|string',
      ];
    }

    if ( $this->routeIs('v2.process.applicability.matter.aspect.evaluate.answer') ) {
      return [
        'recursive' => 'nullable|boolean',
        'answer' => [
          'required',
          'integer',
          Rule::in([Audit::NEGATIVE, Audit::AFFIRMATIVE, Audit::NO_APPLY]),
        ],
      ];
    }

    if ( $this->routeIs('v2.process.applicability.matter.aspect.evaluate.images') ) {
      /**
			 * in php.ini
			 * upload_max_filesize = 20M
			 * post_max_size = 25M
			 */
      return [
        'file.*' => [
          'required', 
          'file', 
          'max:20480', // 20MB en kb (1MB = 1024KB)
          'mimes:jpeg,png,jpg'
        ],
      ];
    }

    return [];
  }
}