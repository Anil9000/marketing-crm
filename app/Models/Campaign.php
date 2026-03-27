<?php

namespace App\Models;

use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'segment_id',
        'name',
        'type',
        'status',
        'subject',
        'content',
        'budget',
        'spent',
        'scheduled_at',
        'sent_at',
        'ab_test_enabled',
        'variant_a',
        'variant_b',
        'frequency',
    ];

    protected function casts(): array
    {
        return [
            'type'             => CampaignType::class,
            'status'           => CampaignStatus::class,
            'scheduled_at'     => 'datetime',
            'sent_at'          => 'datetime',
            'ab_test_enabled'  => 'boolean',
            'budget'           => 'decimal:2',
            'spent'            => 'decimal:2',
        ];
    }

    // --- Relationships ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function segment(): BelongsTo
    {
        return $this->belongsTo(Segment::class);
    }

    public function stats(): HasOne
    {
        return $this->hasOne(CampaignStat::class);
    }

    public function emailEvents(): HasMany
    {
        return $this->hasMany(EmailEvent::class);
    }

    public function abTest(): HasOne
    {
        return $this->hasOne(AbTest::class);
    }

    // --- Scopes ---

    public function scopeActive($query)
    {
        return $query->where('status', CampaignStatus::Active);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // --- Accessors ---

    public function getOpenRateAttribute(): float
    {
        $stats = $this->stats;
        if (!$stats || $stats->sent_count === 0) {
            return 0.0;
        }
        return round(($stats->opens / $stats->sent_count) * 100, 2);
    }

    public function getClickRateAttribute(): float
    {
        $stats = $this->stats;
        if (!$stats || $stats->sent_count === 0) {
            return 0.0;
        }
        return round(($stats->clicks / $stats->sent_count) * 100, 2);
    }
}
