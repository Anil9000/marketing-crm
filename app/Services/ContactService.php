<?php

namespace App\Services;

use App\Models\Contact;
use App\Repositories\Interfaces\ContactRepositoryInterface;
use Illuminate\Http\UploadedFile;
use League\Csv\Reader;

class ContactService
{
    public function __construct(
        private readonly ContactRepositoryInterface $contactRepository
    ) {}

    public function getAllForUser(int $userId, array $filters = [], int $perPage = 15)
    {
        return $this->contactRepository->allForUser($userId, $filters, $perPage);
    }

    public function create(int $userId, array $data): Contact
    {
        $data['user_id'] = $userId;
        return $this->contactRepository->create($data);
    }

    public function update(Contact $contact, array $data): Contact
    {
        return $this->contactRepository->update($contact, $data);
    }

    public function delete(Contact $contact): bool
    {
        return $this->contactRepository->delete($contact);
    }

    public function importFromCsv(int $userId, UploadedFile $file): array
    {
        $csv = Reader::createFromPath($file->getPathname(), 'r');
        $csv->setHeaderOffset(0);

        $records  = [];
        $errors   = [];
        $rowIndex = 2; // 1-indexed, row 1 is header

        foreach ($csv->getRecords() as $record) {
            if (empty($record['email'])) {
                $errors[] = "Row {$rowIndex}: Email is required";
                $rowIndex++;
                continue;
            }

            if (!filter_var($record['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row {$rowIndex}: Invalid email '{$record['email']}'";
                $rowIndex++;
                continue;
            }

            $records[] = [
                'email'      => strtolower(trim($record['email'])),
                'first_name' => $record['first_name'] ?? null,
                'last_name'  => $record['last_name']  ?? null,
                'phone'      => $record['phone']       ?? null,
                'location'   => $record['location']    ?? null,
                'gender'     => $record['gender']      ?? null,
                'age'        => isset($record['age']) && is_numeric($record['age']) ? (int) $record['age'] : null,
                'status'     => 'active',
            ];

            $rowIndex++;
        }

        $imported = 0;
        if (!empty($records)) {
            $imported = $this->contactRepository->bulkCreate($userId, $records);
        }

        return [
            'imported' => $imported,
            'errors'   => $errors,
            'total'    => $rowIndex - 2,
        ];
    }

    public function unsubscribe(Contact $contact): void
    {
        $contact->update(['status' => 'unsubscribed']);
    }

    public function exportSegmentToCsv(int $segmentId, int $userId): string
    {
        $contacts = Contact::where('user_id', $userId)
            ->whereHas('segments', fn($q) => $q->where('segments.id', $segmentId))
            ->get(['email', 'first_name', 'last_name', 'phone', 'location', 'gender', 'age', 'status']);

        $csvData  = "email,first_name,last_name,phone,location,gender,age,status\n";
        foreach ($contacts as $contact) {
            $csvData .= implode(',', [
                $contact->email,
                $contact->first_name ?? '',
                $contact->last_name  ?? '',
                $contact->phone      ?? '',
                $contact->location   ?? '',
                $contact->gender     ?? '',
                $contact->age        ?? '',
                $contact->status,
            ]) . "\n";
        }

        return $csvData;
    }
}
