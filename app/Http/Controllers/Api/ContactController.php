<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\ImportContactRequest;
use App\Http\Requests\Contact\StoreContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContactController extends Controller
{
    public function __construct(private readonly ContactService $contactService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $contacts = $this->contactService->getAllForUser(
            $request->user()->id,
            $request->only(['search', 'status', 'gender', 'location', 'segment_id']),
            (int) $request->get('per_page', 15)
        );

        return ContactResource::collection($contacts);
    }

    public function store(StoreContactRequest $request): JsonResponse
    {
        $contact = $this->contactService->create($request->user()->id, $request->validated());

        return response()->json(new ContactResource($contact), 201);
    }

    public function show(Contact $contact): JsonResponse
    {
        $this->authorizeOwner($contact);

        $contact->load('segments');

        return response()->json(new ContactResource($contact));
    }

    public function update(StoreContactRequest $request, Contact $contact): JsonResponse
    {
        $this->authorizeOwner($contact);

        $updated = $this->contactService->update($contact, $request->validated());

        return response()->json(new ContactResource($updated));
    }

    public function destroy(Contact $contact): JsonResponse
    {
        $this->authorizeOwner($contact);

        $this->contactService->delete($contact);

        return response()->json(['message' => 'Contact deleted successfully.']);
    }

    public function import(ImportContactRequest $request): JsonResponse
    {
        $result = $this->contactService->importFromCsv(
            $request->user()->id,
            $request->file('file')
        );

        return response()->json([
            'message'  => "Import complete. {$result['imported']} contacts processed.",
            'imported' => $result['imported'],
            'total'    => $result['total'],
            'errors'   => $result['errors'],
        ]);
    }

    private function authorizeOwner(Contact $contact): void
    {
        if ($contact->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have access to this contact.');
        }
    }
}
