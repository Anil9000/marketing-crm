<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactWebController extends Controller
{
    public function __construct(private readonly ContactService $contactService) {}

    public function index(Request $request): View
    {
        $contacts = $this->contactService->getAllForUser(
            $request->user()->id,
            $request->only(['search', 'status', 'gender', 'location'])
        );

        return view('contacts.index', compact('contacts'));
    }

    public function create(): View
    {
        return view('contacts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email'      => 'required|email',
            'first_name' => 'nullable|string|max:100',
            'last_name'  => 'nullable|string|max:100',
        ]);

        $this->contactService->create($request->user()->id, $request->all());

        return redirect()->route('contacts.index')
            ->with('success', 'Contact added successfully.');
    }

    public function show(Contact $contact): View
    {
        $contact->load(['segments', 'emailEvents' => fn($q) => $q->latest()->take(20)]);

        return view('contacts.show', compact('contact'));
    }

    public function edit(Contact $contact): View
    {
        return view('contacts.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact): RedirectResponse
    {
        $this->contactService->update($contact, $request->all());

        return redirect()->route('contacts.show', $contact)
            ->with('success', 'Contact updated.');
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $this->contactService->delete($contact);

        return redirect()->route('contacts.index')
            ->with('success', 'Contact deleted.');
    }

    public function importForm(): View
    {
        return view('contacts.import');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $result = $this->contactService->importFromCsv(
            $request->user()->id,
            $request->file('file')
        );

        return redirect()->route('contacts.index')
            ->with('success', "Imported {$result['imported']} contacts. Errors: " . count($result['errors']));
    }
}
