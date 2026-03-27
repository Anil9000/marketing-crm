<?php

namespace App\Http\Requests\Segment;

use Illuminate\Foundation\Http\FormRequest;

class StoreSegmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'is_dynamic'    => ['boolean'],
            'filters'       => ['nullable', 'array'],
            'filters.*.field'    => ['required_with:filters', 'string'],
            'filters.*.operator' => ['required_with:filters', 'string'],
            'filters.*.value'    => ['required_with:filters'],
        ];
    }
}
