<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Segment\StoreSegmentRequest;
use App\Http\Resources\ContactResource;
use App\Http\Resources\SegmentResource;
use App\Models\Segment;
use App\Services\ContactService;
use App\Services\SegmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SegmentController extends Controller
{
    public function __construct(
        private readonly SegmentService $segmentService,
        private readonly ContactService $contactService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $segments = $this->segmentService->getAllForUser($request->user()->id);

        return SegmentResource::collection($segments);
    }

    public function store(StoreSegmentRequest $request): JsonResponse
    {
        $segment = $this->segmentService->create($request->user()->id, $request->validated());

        return response()->json(new SegmentResource($segment), 201);
    }

    public function show(Segment $segment): JsonResponse
    {
        $this->authorizeOwner($segment);

        return response()->json(new SegmentResource($segment));
    }

    public function update(StoreSegmentRequest $request, Segment $segment): JsonResponse
    {
        $this->authorizeOwner($segment);

        $updated = $this->segmentService->update($segment, $request->validated());

        return response()->json(new SegmentResource($updated));
    }

    public function destroy(Segment $segment): JsonResponse
    {
        $this->authorizeOwner($segment);

        $this->segmentService->delete($segment);

        return response()->json(['message' => 'Segment deleted successfully.']);
    }

    public function contacts(Request $request, Segment $segment): AnonymousResourceCollection
    {
        $this->authorizeOwner($segment);

        $contacts = $this->segmentService->getContacts($segment, (int) $request->get('per_page', 15));

        return ContactResource::collection($contacts);
    }

    public function export(Segment $segment, Request $request): StreamedResponse
    {
        $this->authorizeOwner($segment);

        $csvData = $this->contactService->exportSegmentToCsv($segment->id, $request->user()->id);

        return response()->streamDownload(function () use ($csvData) {
            echo $csvData;
        }, "segment-{$segment->id}-contacts.csv", [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function previewCount(Request $request): JsonResponse
    {
        $request->validate([
            'filters' => ['required', 'array'],
        ]);

        $count = $this->segmentService->previewCount(
            $request->input('filters'),
            $request->user()->id
        );

        return response()->json(['count' => $count]);
    }

    private function authorizeOwner(Segment $segment): void
    {
        if ($segment->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have access to this segment.');
        }
    }
}
