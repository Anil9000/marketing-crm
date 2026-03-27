<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'email',
        'first_name',
        'last_name',
        'phone',
        'location',
        'age',
        'gender',
        'status',
        'custom_fields',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'custom_fields'    => 'array',
            'last_activity_at' => 'datetime',
            'age'              => 'integer',
        ];
    }

    // --- Relationships ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function segments(): BelongsToMany
    {
        return $this->belongsToMany(Segment::class, 'segment_contacts')
            ->withPivot('added_at');
    }

    public function emailEvents(): HasMany
    {
        return $this->hasMany(EmailEvent::class);
    }

    // --- Accessors ---

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    // --- Scopes ---

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('email', 'like', "%{$search}%")
                ->orWhere('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        });
    }
}
