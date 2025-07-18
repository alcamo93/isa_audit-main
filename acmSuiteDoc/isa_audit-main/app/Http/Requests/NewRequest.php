<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class NewRequest extends FormRequest
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
        $isUpdate = $this->routeIs('v2.new.update') ? 'nullable' : 'required|mimes:jpeg,png,jpg';

        $rules = [
			'headline' => 'required|string',
            'file' => "{$isUpdate}",
			'topics' => 'required|array',
			'description' => 'required|string',
			'publication_start_date' => 'required|date',
			'publication_closing_date' => 'required|date|after_or_equal:publication_start_date',
            'historical_start_date' => 'required|date|after:publication_closing_date',
            'historical_closing_date' => 'required|date|after_or_equal:historical_start_date',
            'published' => 'boolean'
		];

		return $this->routeIs('v2.new.store') || $this->routeIs('v2.new.update') ? $rules : [];
    }
}
