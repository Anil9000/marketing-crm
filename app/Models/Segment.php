<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Segment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'filters',
        'is_dynamic',
        'contact_count',
    ];

    protected function casts(): array
    {
        return [
            'filters'       => 'array',
            'is_dynamic'    => 'boolean',
            'contact_count' => 'integer',
        ];
    }

    // --- Relationships ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'segment_contacts')
            ->withPivot('added_at');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    // --- Business Logic ---

    /**
     * Apply segment filters to a contact query and return matching contacts.
     */
    public function getMatchingContacts()
    {
        $query = Contact::where('user_id', $this->user_id)
            ->where('status', 'active');

        if (empty($this->filters)) {
            return $query;
        }

        foreach ($this->filters as $filter) {
            $field    = $filter['field']    ?? null;
            $operator = $filter['operator'] ?? '=';
            $value    = $filter['value']    ?? null;

            if (!$field || $value === null) {
                continue;
            }

            match($operator) {
                'equals'       => $query->where($field, $value),
                'not_equals'   => $query->where($field, '!=', $value),
                'contains'     => $query->where($field, 'like', "%{$value}%"),
                'greater_than' => $query->where($field, '>', $value),
                'less_than'    => $query->where($field, '<', $value),
                default        => $query->where($field, $value),
            };
        }

        return $query;
    }

    public function refreshContactCount(): void
    {
        if ($this->is_dynamic) {
            $count = $this->getMatchingContacts()->count();
        } else {
            $count = $this->contacts()->count();
        }

        $this->update(['contact_count' => $count]);
    }
}
