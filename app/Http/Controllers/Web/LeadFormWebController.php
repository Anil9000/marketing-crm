<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LeadForm;
use App\Models\LeadSubmission;
use App\Services\LeadFormService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class LeadFormWebController extends Controller
{
    public function __construct(private readonly LeadFormService $leadFormService) {}

    public function index(Request $request): View
    {
        $forms = $this->leadFormService->getAllForUser($request->user()->id);

        return view('lead-forms.index', compact('forms'));
    }

    public function create(): View
    {
        return view('lead-forms.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'fields' => 'required|array',
        ]);

        $this->leadFormService->create($request->user()->id, $request->all());

        return redirect()->route('lead-forms.index')
            ->with('success', 'Form created successfully.');
    }

    public function show(LeadForm $leadForm): View
    {
        $submissions = LeadSubmission::where('form_id', $leadForm->id)->latest()->paginate(20);
        $embedCode   = $this->leadFormService->getEmbedCode($leadForm);

        return view('lead-forms.show', compact('leadForm', 'submissions', 'embedCode'));
    }

    public function edit(LeadForm $leadForm): View
    {
        return view('lead-forms.edit', compact('leadForm'));
    }

    public function update(Request $request, LeadForm $leadForm): RedirectResponse
    {
        $this->leadFormService->update($leadForm, $request->all());

        return redirect()->route('lead-forms.show', $leadForm)
            ->with('success', 'Form updated.');
    }

    public function destroy(LeadForm $leadForm): RedirectResponse
    {
        $this->leadFormService->delete($leadForm);

        return redirect()->route('lead-forms.index')
            ->with('success', 'Form deleted.');
    }

    /**
     * Export all submissions for a lead form as a CSV download.
     */
    public function exportSubmissions(LeadForm $leadForm): Response
    {
        $submissions = LeadSubmission::where('form_id', $leadForm->id)->latest()->get();
        $fields      = $leadForm->fields ?? [];

        $headers = array_map(fn($f) => $f['label'] ?? $f['name'], $fields);
        $csv     = implode(',', array_merge(['#', ...$headers, 'IP Address', 'Submitted At'])) . "\n";

        foreach ($submissions as $submission) {
            $row = [$submission->id];
            foreach ($fields as $field) {
                $value = $submission->data[$field['name']] ?? '';
                // Wrap values with commas or quotes in double-quotes
                $row[] = str_contains((string) $value, ',') ? '"' . str_replace('"', '""', $value) . '"' : $value;
            }
            $row[] = $submission->ip_address ?? '';
            $row[] = $submission->created_at->format('Y-m-d H:i:s');
            $csv  .= implode(',', $row) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="lead-form-' . $leadForm->id . '-submissions.csv"',
        ]);
    }

    /**
     * Public embeddable form page — no auth required.
     */
    public function embed(string $slug): View|Response
    {
        $form = LeadForm::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return view('lead-forms.embed', compact('form'));
    }

    /**
     * Public form submission handler.
     */
    public function publicSubmit(Request $request, string $slug): RedirectResponse
    {
        $form = LeadForm::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $this->leadFormService->submit(
            $form,
            $request->except(['_token']),
            $request->ip(),
            $request->userAgent() ?? '',
            $request->header('Referer')
        );

        return back()->with('success', $form->settings['success_message'] ?? 'Thank you for your submission!');
    }
}
