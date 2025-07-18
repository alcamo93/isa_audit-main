<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BackupRequest extends FormRequest
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
  	$rules = [
      'id_audit_processes' => 'required|integer|exists:t_audit_processes,id_audit_processes',
    ];
		
		return $this->routeIs('v2.backup.store') ? $rules : [];
	}
}
