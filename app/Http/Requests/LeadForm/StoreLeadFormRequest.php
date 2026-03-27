<?php

namespace App\Http\Requests\LeadForm;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'               => ['required', 'string', 'max:255'],
            'fields'             => ['required', 'array', 'min:1'],
            'fields.*.name'      => ['required', 'string'],
            'fields.*.type'      => ['required', Rule::in(['text', 'email', 'phone', 'select', 'checkbox', 'textarea'])],
            'fields.*.label'     => ['required', 'string'],
            'fields.*.required'  => ['boolean'],
            'settings'           => ['nullable', 'array'],
            'is_active'          => ['boolean'],
        ];
    }
}
