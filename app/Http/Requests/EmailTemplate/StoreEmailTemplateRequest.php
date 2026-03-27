<?php

namespace App\Http\Requests\EmailTemplate;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmailTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'category'     => ['nullable', 'string', 'max:100'],
            'subject'      => ['required', 'string', 'max:500'],
            'html_content' => ['required', 'string'],
            'is_public'    => ['boolean'],
        ];
    }
}
