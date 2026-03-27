<?php

namespace App\Http\Requests\Campaign;

use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canManageCampaigns();
    }

    public function rules(): array
    {
        return [
            'name'             => ['sometimes', 'string', 'max:255'],
            'type'             => ['sometimes', Rule::enum(CampaignType::class)],
            'status'           => ['sometimes', Rule::enum(CampaignStatus::class)],
            'subject'          => ['nullable', 'string', 'max:500'],
            'content'          => ['nullable', 'string'],
            'budget'           => ['nullable', 'numeric', 'min:0'],
            'segment_id'       => ['nullable', 'exists:segments,id'],
            'scheduled_at'     => ['nullable', 'date'],
            'frequency'        => ['nullable', Rule::in(['one_time', 'daily', 'weekly', 'monthly'])],
            'ab_test_enabled'  => ['boolean'],
            'variant_a'        => ['nullable', 'string'],
            'variant_b'        => ['nullable', 'string'],
        ];
    }
}
