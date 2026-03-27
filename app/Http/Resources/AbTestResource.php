<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AbTestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'variant_a_subject'  => $this->variant_a_subject,
            'variant_a_content'  => $this->variant_a_content,
            'variant_b_subject'  => $this->variant_b_subject,
            'variant_b_content'  => $this->variant_b_content,
            'winner'             => $this->winner,
            'variant_a_opens'    => $this->variant_a_opens,
            'variant_b_opens'    => $this->variant_b_opens,
            'variant_a_clicks'   => $this->variant_a_clicks,
            'variant_b_clicks'   => $this->variant_b_clicks,
        ];
    }
}
