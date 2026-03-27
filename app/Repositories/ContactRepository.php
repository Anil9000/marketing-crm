<?php

namespace App\Repositories;

use App\Models\Contact;
use App\Repositories\Interfaces\ContactRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ContactRepository implements ContactRepositoryInterface
{
    public function allForUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Contact::where('user_id', $userId);

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        if (!empty($filters['location'])) {
            $query->where('location', 'like', "%{$filters['location']}%");
        }

        if (!empty($filters['segment_id'])) {
            $query->whereHas('segments', function ($q) use ($filters) {
                $q->where('segments.id', $filters['segment_id']);
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function findById(int $id): ?Contact
    {
        return Contact::with(['segments'])->find($id);
    }

    public function create(array $data): Contact
    {
        return Contact::create($data);
    }

    public function update(Contact $contact, array $data): Contact
    {
        $contact->update($data);
        return $contact->fresh();
    }

    public function delete(Contact $contact): bool
    {
        return $contact->delete();
    }

    public function bulkCreate(int $userId, array $records): int
    {
        $inserted = 0;
        $chunks   = array_chunk($records, 500);

        foreach ($chunks as $chunk) {
            $rows = array_map(function ($record) use ($userId) {
                return array_merge($record, [
                    'user_id'    => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }, $chunk);

            // Use upsert to avoid duplicate emails per user
            DB::table('contacts')->upsert(
                $rows,
                ['user_id', 'email'],
                ['first_name', 'last_name', 'phone', 'location', 'updated_at']
            );

            $inserted += count($rows);
        }

        return $inserted;
    }
}
