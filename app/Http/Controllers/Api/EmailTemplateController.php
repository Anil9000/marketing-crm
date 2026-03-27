<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailTemplate\StoreEmailTemplateRequest;
use App\Http\Resources\EmailTemplateResource;
use App\Models\EmailTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmailTemplateController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $templates = EmailTemplate::where(function ($q) use ($request) {
            $q->where('user_id', $request->user()->id)
                ->orWhere('is_public', true);
        })
            ->when($request->filled('category'), fn($q) => $q->where('category', $request->category))
            ->when($request->filled('search'), fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate((int) $request->get('per_page', 12));

        return EmailTemplateResource::collection($templates);
    }

    public function store(StoreEmailTemplateRequest $request): JsonResponse
    {
        $template = EmailTemplate::create(
            array_merge($request->validated(), ['user_id' => $request->user()->id])
        );

        return response()->json(new EmailTemplateResource($template), 201);
    }

    public function show(EmailTemplate $emailTemplate): JsonResponse
    {
        $this->authorizeAccess($emailTemplate);

        return response()->json(new EmailTemplateResource($emailTemplate));
    }

    public function update(StoreEmailTemplateRequest $request, EmailTemplate $emailTemplate): JsonResponse
    {
        $this->authorizeOwner($emailTemplate);

        $emailTemplate->update($request->validated());

        return response()->json(new EmailTemplateResource($emailTemplate->fresh()));
    }

    public function destroy(EmailTemplate $emailTemplate): JsonResponse
    {
        $this->authorizeOwner($emailTemplate);

        $emailTemplate->delete();

        return response()->json(['message' => 'Template deleted successfully.']);
    }

    private function authorizeAccess(EmailTemplate $template): void
    {
        if (!$template->is_public && $template->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }
    }

    private function authorizeOwner(EmailTemplate $template): void
    {
        if ($template->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }
    }
}
