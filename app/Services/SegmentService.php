<?php

namespace App\Services;

use App\Models\Segment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SegmentService
{
    public function getAllForUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Segment::where('user_id', $userId)
            ->withCount('contacts')
            ->latest()
            ->paginate($perPage);
    }

    public function create(int $userId, array $data): Segment
    {
        $data['user_id'] = $userId;
        $segment         = Segment::create($data);

        if ($segment->is_dynamic) {
            $segment->refreshContactCount();
        }

        return $segment;
    }

    public function update(Segment $segment, array $data): Segment
    {
        $segment->update($data);

        if ($segment->is_dynamic) {
            $segment->refreshContactCount();
        }

        return $segment->fresh();
    }

    public function delete(Segment $segment): bool
    {
        return $segment->delete();
    }

    public function getContacts(Segment $segment, int $perPage = 15)
    {
        if ($segment->is_dynamic) {
            return $segment->getMatchingContacts()->paginate($perPage);
        }

        return $segment->contacts()->paginate($perPage);
    }

    public function previewCount(array $filters, int $userId): int
    {
        $tempSegment = new Segment([
            'user_id'    => $userId,
            'filters'    => $filters,
            'is_dynamic' => true,
        ]);

        return $tempSegment->getMatchingContacts()->count();
    }
}
