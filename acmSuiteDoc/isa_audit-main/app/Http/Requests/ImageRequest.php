<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImageRequest extends FormRequest
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
    return [
      'file' => [
        'required', 
        'file', 
        'max:5120', // 5MB en kb (1MB = 1024KB)
        'mimes:png,jpg,jpeg', 
        // 'dimensions:ratio=1.1',
      ]
    ];
  }
    public function messages()
	{
		return [
			'file.*.required' => 'Debes seleccionar al menos un archivo.',
			'file.*.file' => 'Cada archivo debe ser un archivo válido.',
			'file.*.max' => 'Cada archivo no debe superar los 5MB.',
			'file.*.mimes' => 'Uno o más archivos tienen un formato incorrecto o están dañados. Solo se permiten archivos: Imágenes (JPG, PNG).',
		];
	}

	public function withValidator($validator)
	{
		$validator->after(function ($validator) {
			$files = $this->file('file');

			foreach ($files as $index => $uploadedFile) {
				$extension = $uploadedFile->getClientOriginalExtension();
				$mime = $uploadedFile->getMimeType();

				// Verifica si el archivo tiene una extensión incorrecta o tipo MIME sospechoso
				$allowed = [
					'image/png',
					'image/jpeg',
				];

				if (!in_array($mime, $allowed)) {
					$validator->errors()->add(
						"file.$index",
						"El archivo '{$uploadedFile->getClientOriginalName()}' tiene un tipo no permitido, está dañado o se sospecha ser no seguro."
					);
				}
			}
		});
	}
}