<?php

namespace App\Http\Requests\Contact;

use Illuminate\Foundation\Http\FormRequest;

class ImportContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'], // 10MB max
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'The file must be a CSV file.',
            'file.max'   => 'The file must not exceed 10MB.',
        ];
    }
}
