<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailTemplateWebController extends Controller
{
    public function index(Request $request): View
    {
        $templates = EmailTemplate::where(function ($q) use ($request) {
            $q->where('user_id', $request->user()->id)->orWhere('is_public', true);
        })
            ->when($request->filled('category'), fn($q) => $q->where('category', $request->category))
            ->latest()
            ->paginate(12);

        $categories = EmailTemplate::distinct()->pluck('category')->filter()->values();

        return view('email-templates.index', compact('templates', 'categories'));
    }

    public function create(): View
    {
        return view('email-templates.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'subject'      => 'required|string|max:500',
            'html_content' => 'required|string',
            'category'     => 'nullable|string',
        ]);

        EmailTemplate::create(array_merge($request->all(), ['user_id' => $request->user()->id]));

        return redirect()->route('email-templates.index')
            ->with('success', 'Template created successfully.');
    }

    public function show(EmailTemplate $emailTemplate): View
    {
        return view('email-templates.show', ['template' => $emailTemplate]);
    }

    public function edit(EmailTemplate $emailTemplate): View
    {
        return view('email-templates.edit', ['template' => $emailTemplate]);
    }

    public function update(Request $request, EmailTemplate $emailTemplate): RedirectResponse
    {
        $emailTemplate->update($request->all());

        return redirect()->route('email-templates.show', $emailTemplate)
            ->with('success', 'Template updated.');
    }

    public function destroy(EmailTemplate $emailTemplate): RedirectResponse
    {
        $emailTemplate->delete();

        return redirect()->route('email-templates.index')
            ->with('success', 'Template deleted.');
    }
}
