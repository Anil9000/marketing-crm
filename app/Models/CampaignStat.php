<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignStat extends Model
{
    protected $table = 'campaign_stats';

    protected $fillable = [
        'campaign_id',
        'opens',
        'clicks',
        'conversions',
        'bounces',
        'unsubscribes',
        'sent_count',
    ];

    protected function casts(): array
    {
        return [
            'opens'         => 'integer',
            'clicks'        => 'integer',
            'conversions'   => 'integer',
            'bounces'       => 'integer',
            'unsubscribes'  => 'integer',
            'sent_count'    => 'integer',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function getOpenRateAttribute(): float
    {
        if ($this->sent_count === 0) {
            return 0.0;
        }
        return round(($this->opens / $this->sent_count) * 100, 2);
    }

    public function getClickRateAttribute(): float
    {
        if ($this->sent_count === 0) {
            return 0.0;
        }
        return round(($this->clicks / $this->sent_count) * 100, 2);
    }

    public function getConversionRateAttribute(): float
    {
        if ($this->sent_count === 0) {
            return 0.0;
        }
        return round(($this->conversions / $this->sent_count) * 100, 2);
    }
}
