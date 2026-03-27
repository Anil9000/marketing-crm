<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadSubmission extends Model
{
    protected $fillable = [
        'form_id',
        'data',
        'ip_address',
        'user_agent',
        'referrer',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(LeadForm::class, 'form_id');
    }
}
