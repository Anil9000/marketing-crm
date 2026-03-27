<?php

namespace App\Http\Requests\Campaign;

use App\Enums\CampaignType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canManageCampaigns();
    }

    public function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'max:255'],
            'type'                => ['required', Rule::enum(CampaignType::class)],
            'subject'             => ['nullable', 'string', 'max:500'],
            'content'             => ['nullable', 'string'],
            'budget'              => ['nullable', 'numeric', 'min:0'],
            'segment_id'          => ['nullable', 'exists:segments,id'],
            'scheduled_at'        => ['nullable', 'date', 'after:now'],
            'frequency'           => ['nullable', Rule::in(['one_time', 'daily', 'weekly', 'monthly'])],
            'ab_test_enabled'     => ['boolean'],
            'variant_a'           => ['nullable', 'string'],
            'variant_b'           => ['nullable', 'string'],
            'variant_a_subject'   => ['nullable', 'string', 'max:500'],
            'variant_b_subject'   => ['nullable', 'string', 'max:500'],
        ];
    }
}
