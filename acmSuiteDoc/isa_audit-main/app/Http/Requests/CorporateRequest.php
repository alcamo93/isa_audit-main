<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\V2\Admin\Corporate;

class CorporateRequest extends FormRequest
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
			'corp_tradename' => 'required|string|max:100',
			'corp_trademark' => 'required|string|max:100',
			'rfc' => 'required|alpha_num|min:12|max:13',
			'id_status' => 'required|integer|exists:c_status,id_status',
			'type' => [ 'required', Rule::in([Corporate::NEW_TYPE, Corporate::OPERATIVE_TYPE]) ],
			'add_new' => 'required|boolean',
			'id_industry' => 'required_if:add_new,false|nullable|integer|exists:c_industries,id_industry',
			'new_industry' => 'required_if:add_new,true|nullable|max:255',
		];

		return $this->routeIs('v2.corporate.store') || $this->routeIs('v2.corporate.update') ? $rules : [];
	}
}
