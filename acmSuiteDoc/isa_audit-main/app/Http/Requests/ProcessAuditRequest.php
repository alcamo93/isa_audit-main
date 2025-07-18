<?php

namespace App\Http\Requests;

use App\Models\V2\Catalogs\Scope;
use App\Rules\CustomerOwnsCorporate;
use App\Rules\UniqueAuditProcessName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class ProcessAuditRequest extends FormRequest
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
			'id_customer' => 'required|integer|exists:t_customers,id_customer',
			'id_corporate' => [
				'required',
				'integer',
				new CustomerOwnsCorporate( request('id_customer') )
			],
			'audit_processes' => [
				'required',
				'max:100',
				new UniqueAuditProcessName( request('id_corporate'), $this->route('id'), $this->routeIs('v2.process.renewal') )
			],
			'evaluation_type_id' => 'required|integer|exists:evaluation_types,id',
			'id_scope' => 'required|integer|exists:c_scope,id_scope',
			'specification_scope' => [
				Rule::requiredIf( request('id_scope') == Scope::AREA ),
				'max:100'
			],
			'evaluate_risk' => 'required|boolean',
			'evaluate_especific' => 'required|boolean',
			'date' => 'required|date', 
			'per_year' => 'required|integer|min:1',
			'auditors' => 'required|array',
			'auditors.*.leader' => 'required|boolean',
			'auditors.*.id_user' => 'required|integer|exists:t_users,id_user|distinct',
			'forms' => 'required|array',
			'forms.*' => 'required|integer|exists:forms,id|distinct',
		];

		if ( $this->routeIs('v2.process.renewal') ) 
		{
			$removeRules = Arr::except($rules, ['id_customer', 'id_corporate', 'evaluation_type_id', 'id_scope', 'specification_scope', 'per_year']);
			$addRules = [ 'keep_risk' => 'required|boolean', 'keep_files' => 'required|boolean' ];
			return $removeRules + $addRules;
		}

		return	$this->routeIs('v2.process.store') || $this->routeIs('v2.process.update') ? $rules : [];
	}
}
