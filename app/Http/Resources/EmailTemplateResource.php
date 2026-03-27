<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmailTemplateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'category'     => $this->category,
            'subject'      => $this->subject,
            'html_content' => $this->when($request->routeIs('*.email-templates.show'), $this->html_content),
            'thumbnail'    => $this->thumbnail,
            'is_public'    => $this->is_public,
            'user'         => new UserResource($this->whenLoaded('user')),
            'created_at'   => $this->created_at->toISOString(),
        ];
    }
}
