<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'type'             => $this->type->value,
            'type_label'       => $this->type->label(),
            'type_icon'        => $this->type->icon(),
            'status'           => $this->status->value,
            'status_label'     => $this->status->label(),
            'status_badge'     => $this->status->badgeClass(),
            'subject'          => $this->subject,
            'content'          => $this->when(!$request->is('*/campaigns'), $this->content),
            'budget'           => $this->budget,
            'spent'            => $this->spent,
            'scheduled_at'     => $this->scheduled_at?->toISOString(),
            'sent_at'          => $this->sent_at?->toISOString(),
            'ab_test_enabled'  => $this->ab_test_enabled,
            'frequency'        => $this->frequency,
            'open_rate'        => $this->open_rate,
            'click_rate'       => $this->click_rate,
            'stats'            => new CampaignStatResource($this->whenLoaded('stats')),
            'segment'          => new SegmentResource($this->whenLoaded('segment')),
            'ab_test'          => new AbTestResource($this->whenLoaded('abTest')),
            'created_at'       => $this->created_at->toISOString(),
            'updated_at'       => $this->updated_at->toISOString(),
        ];
    }
}
