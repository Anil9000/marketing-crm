<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'email'            => $this->email,
            'first_name'       => $this->first_name,
            'last_name'        => $this->last_name,
            'full_name'        => $this->full_name,
            'phone'            => $this->phone,
            'location'         => $this->location,
            'age'              => $this->age,
            'gender'           => $this->gender,
            'status'           => $this->status,
            'custom_fields'    => $this->custom_fields,
            'last_activity_at' => $this->last_activity_at?->toISOString(),
            'segments'         => SegmentResource::collection($this->whenLoaded('segments')),
            'created_at'       => $this->created_at->toISOString(),
            'updated_at'       => $this->updated_at->toISOString(),
        ];
    }
}
