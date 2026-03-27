<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Segment;
use App\Services\SegmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SegmentWebController extends Controller
{
    public function __construct(private readonly SegmentService $segmentService) {}

    public function index(Request $request): View
    {
        $segments = $this->segmentService->getAllForUser($request->user()->id);

        return view('segments.index', compact('segments'));
    }

    public function create(): View
    {
        return view('segments.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'filters' => 'nullable|array',
        ]);

        $this->segmentService->create($request->user()->id, $request->all());

        return redirect()->route('segments.index')
            ->with('success', 'Segment created successfully.');
    }

    public function show(Segment $segment): View
    {
        $contacts = $this->segmentService->getContacts($segment);

        return view('segments.show', compact('segment', 'contacts'));
    }

    public function edit(Segment $segment): View
    {
        return view('segments.edit', compact('segment'));
    }

    public function update(Request $request, Segment $segment): RedirectResponse
    {
        $this->segmentService->update($segment, $request->all());

        return redirect()->route('segments.show', $segment)
            ->with('success', 'Segment updated.');
    }

    public function destroy(Segment $segment): RedirectResponse
    {
        $this->segmentService->delete($segment);

        return redirect()->route('segments.index')
            ->with('success', 'Segment deleted.');
    }

    public function export(Segment $segment): Response
    {
        // Get all contacts (no pagination) for full CSV export
        if ($segment->is_dynamic) {
            $contacts = $segment->getMatchingContacts()->get();
        } else {
            $contacts = $segment->contacts()->get();
        }

        $csv = "first_name,last_name,email,phone,location,gender,status\n";
        foreach ($contacts as $contact) {
            $csv .= implode(',', [
                $contact->first_name,
                $contact->last_name,
                $contact->email,
                $contact->phone  ?? '',
                $contact->location ?? '',
                $contact->gender ?? '',
                $contact->status ?? '',
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="segment-' . $segment->id . '-contacts.csv"',
        ]);
    }
}
