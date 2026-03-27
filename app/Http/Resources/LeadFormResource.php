<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadFormResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'fields'            => $this->fields,
            'settings'          => $this->settings,
            'is_active'         => $this->is_active,
            'submissions_count' => $this->submissions_count ?? $this->submissions()->count(),
            'embed_url'         => url("/lead-forms/{$this->slug}/embed"),
            'created_at'        => $this->created_at->toISOString(),
        ];
    }
}
