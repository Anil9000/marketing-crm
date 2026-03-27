<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignStatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'opens'           => $this->opens,
            'clicks'          => $this->clicks,
            'conversions'     => $this->conversions,
            'bounces'         => $this->bounces,
            'unsubscribes'    => $this->unsubscribes,
            'sent_count'      => $this->sent_count,
            'open_rate'       => $this->open_rate,
            'click_rate'      => $this->click_rate,
            'conversion_rate' => $this->conversion_rate,
        ];
    }
}
