<?php

namespace App\Http\Requests\Contact;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'         => ['required', 'email', 'max:255'],
            'first_name'    => ['nullable', 'string', 'max:100'],
            'last_name'     => ['nullable', 'string', 'max:100'],
            'phone'         => ['nullable', 'string', 'max:30'],
            'location'      => ['nullable', 'string', 'max:255'],
            'age'           => ['nullable', 'integer', 'min:1', 'max:120'],
            'gender'        => ['nullable', Rule::in(['male', 'female', 'other', 'prefer_not_to_say'])],
            'status'        => ['nullable', Rule::in(['active', 'unsubscribed', 'bounced'])],
            'custom_fields' => ['nullable', 'array'],
        ];
    }
}
