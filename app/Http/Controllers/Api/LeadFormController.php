<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeadForm\StoreLeadFormRequest;
use App\Http\Resources\LeadFormResource;
use App\Models\LeadForm;
use App\Models\LeadSubmission;
use App\Services\LeadFormService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LeadFormController extends Controller
{
    public function __construct(private readonly LeadFormService $leadFormService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $forms = $this->leadFormService->getAllForUser($request->user()->id);

        return LeadFormResource::collection($forms);
    }

    public function store(StoreLeadFormRequest $request): JsonResponse
    {
        $form = $this->leadFormService->create($request->user()->id, $request->validated());

        return response()->json(new LeadFormResource($form), 201);
    }

    public function show(LeadForm $leadForm): JsonResponse
    {
        $this->authorizeOwner($leadForm);

        return response()->json(new LeadFormResource($leadForm->loadCount('submissions')));
    }

    public function update(StoreLeadFormRequest $request, LeadForm $leadForm): JsonResponse
    {
        $this->authorizeOwner($leadForm);

        $updated = $this->leadFormService->update($leadForm, $request->validated());

        return response()->json(new LeadFormResource($updated));
    }

    public function destroy(LeadForm $leadForm): JsonResponse
    {
        $this->authorizeOwner($leadForm);

        $this->leadFormService->delete($leadForm);

        return response()->json(['message' => 'Form deleted successfully.']);
    }

    /**
     * Public endpoint — no auth required.
     */
    public function submit(Request $request, string $slug): JsonResponse
    {
        $form = LeadForm::where('slug', $slug)->where('is_active', true)->firstOrFail();

        // Validate required fields defined in the form
        $rules = [];
        foreach ($form->fields as $field) {
            $fieldRules = ['nullable'];
            if (!empty($field['required'])) {
                $fieldRules = ['required'];
            }
            if ($field['type'] === 'email') {
                $fieldRules[] = 'email';
            }
            $rules["data.{$field['name']}"] = $fieldRules;
        }

        $validated = $request->validate($rules);

        $submission = $this->leadFormService->submit(
            $form,
            $validated['data'] ?? [],
            $request->ip(),
            $request->userAgent() ?? '',
            $request->header('Referer')
        );

        return response()->json([
            'message' => $form->settings['success_message'] ?? 'Thank you for your submission!',
        ], 201);
    }

    public function submissions(Request $request, LeadForm $leadForm): JsonResponse
    {
        $this->authorizeOwner($leadForm);

        $submissions = LeadSubmission::where('form_id', $leadForm->id)
            ->latest()
            ->paginate((int) $request->get('per_page', 15));

        return response()->json($submissions);
    }

    private function authorizeOwner(LeadForm $form): void
    {
        if ($form->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }
    }
}
