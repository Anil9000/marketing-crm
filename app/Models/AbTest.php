<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbTest extends Model
{
    protected $table = 'ab_tests';

    protected $fillable = [
        'campaign_id',
        'variant_a_subject',
        'variant_a_content',
        'variant_b_subject',
        'variant_b_content',
        'winner',
        'variant_a_opens',
        'variant_b_opens',
        'variant_a_clicks',
        'variant_b_clicks',
    ];

    protected function casts(): array
    {
        return [
            'variant_a_opens'  => 'integer',
            'variant_b_opens'  => 'integer',
            'variant_a_clicks' => 'integer',
            'variant_b_clicks' => 'integer',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function determineWinner(): ?string
    {
        $aScore = $this->variant_a_opens + ($this->variant_a_clicks * 2);
        $bScore = $this->variant_b_opens + ($this->variant_b_clicks * 2);

        if ($aScore === $bScore) {
            return null;
        }

        return $aScore > $bScore ? 'a' : 'b';
    }
}
