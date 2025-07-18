<?php

namespace App\Http\Requests;

use App\Rules\DatesInDateRange;
use App\Rules\DaysInDateRange;
use App\Rules\RequirementOwnsSubrequiment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class LibraryRequest extends FormRequest
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
	 * Prepare the data for validation.
	 *
	 * @return void
	 */
	protected function prepareForValidation()
	{
		$this->merge([
			'has_end_date' => filter_var($this->has_end_date, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
			'show_library' => filter_var($this->show_library, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
			'id_subrequirement' => $this->id_subrequirement === 'null' ? null : $this->id_subrequirement,
			'id_task' => $this->id_task === 'null' ? null : $this->id_task,
		]);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		if ( $this->routeIs('v2.library.approve') ) {
			return [
				'approve' => 'required|boolean',
				'id_task' => 'required|integer'
			];
		}

		$rules = [
			'name' => 'required|string|max:500',
			'id_category' => [
				'required',
				'integer',
				Rule::exists('c_categories', 'id_category'),
			],
			'has_end_date' => 'required|boolean',
			'init_date' => 'required|date',
			'end_date' => [
				'nullable', 
				'required_if:has_end_date,1,true', 
				'date', 
				'after_or_equal:init_date'
			],
			'days' => [
				'nullable',
				'required_if:has_end_date,1,true',
				'integer',
				new DaysInDateRange( $this->init_date, $this->end_date, $this->has_end_date )
			],
			'notify_days' => [
				'nullable',
				'required_if:has_end_date,1,true',
				'array'
			],
			'notify_days.*' => [
				'nullable',
				'required_if:has_end_date,1,true',
				new DatesInDateRange( $this->init_date, $this->end_date, $this->has_end_date )
			],
			'id_aplicability_register' => [
				'required',
				'integer',
				Rule::exists('t_aplicability_registers', 'id_aplicability_register'),
			],
			'id_requirement' => [
				'required',
				'integer',
				Rule::exists('t_requirements', 'id_requirement'),
			],
			'id_subrequirement' => [
				'nullable',
				'integer',
				new RequirementOwnsSubrequiment( $this->id_requirement ),
			],
			'show_library' => 'required|boolean',
			'evaluateable_type' => [
				'required',
				'string',
				Rule::in(['Obligation', 'Task', 'Library']),
			],
			'evaluateable_id' => [
				'required',
				'integer',
			],
			// 'id_task' => [ // Note: check 
			// 	'nullable',
			// 	'integer',
			// 	Rule::exists('t_tasks', 'id_task'),
			// ],
			'file' => 'array',
			/**
			 * in php.ini
			 * upload_max_filesize = 20M
			 * post_max_size = 25M
			 */
			'file.*' => [
				'required', 
				'file', 
				'max:15360', // 15MB en kb (1MB = 1024KB)
			],
		];

		if ( $this->routeIs('v2.library.update') ) 
		{
			$exceptValues = [
				'id_aplicability_register', 
				'id_requirement', 
				'id_subrequirement', 
				'evaluateable_type', 
				'evaluateable_id', 
				'id_task'
			];
			$rulesUpdate = Arr::except($rules, $exceptValues);
			return $rulesUpdate;
		}

		return $this->routeIs('v2.library.store') ? $rules : [];
	}

	public function messages()
	{
		return [
			'file.*.required' => 'Debes seleccionar al menos un archivo.',
			'file.*.file' => 'Cada archivo debe ser un archivo válido.',
			'file.*.max' => 'Cada archivo no debe superar los 15MB.',
		];
	}

	public function withValidator($validator)
	{
		$validator->after(function ($validator) {
			$files = $this->file('file');

			if ( is_null($files) ) return;

			foreach ($files as $index => $uploadedFile) {
				$extension = $uploadedFile->getClientOriginalExtension();
				$mime = $uploadedFile->getMimeType();
				// Checks if the file has an incorrect extension or suspicious MIME type
				$allowedMimes = [
					'application/pdf',
					'image/png',
					'image/jpeg',
					'application/msword',
					'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
					'application/vnd.ms-powerpoint',
					'application/vnd.openxmlformats-officedocument.presentationml.presentation',
					'application/vnd.ms-excel',
					'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
				];

				$isPdfWithGenericMime = $mime === 'application/octet-stream' && $extension === 'pdf';

				if (!in_array($mime, $allowedMimes) && !$isPdfWithGenericMime) {
					$validator->errors()->add(
						"file.$index",
						"El archivo '{$uploadedFile->getClientOriginalName()}' tiene un tipo no permitido, está dañado o se sospecha ser no seguro. Solo se permiten archivos: PDF, Word, Excel, PowerPoint o imágenes (JPG, PNG)."
					);
				}
			}
		});
	}
}
