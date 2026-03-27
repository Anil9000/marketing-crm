@extends('layouts.app')
@section('title', 'Lead Forms')

@section('breadcrumb')
    <li class="breadcrumb-item active">Lead Forms</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>Lead Forms</h1>
        <p class="text-muted small mb-0">Build embeddable forms to capture leads.</p>
    </div>
    <a href="{{ route('lead-forms.create') }}" class="btn btn-accent">
        <i class="bi bi-plus-lg me-1"></i> New Form
    </a>
</div>

<div class="page-content">
    <div class="row g-3">
        @forelse($forms as $form)
        <div class="col-md-6 col-xl-4">
            <div class="crm-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="fw-semibold mb-0">{{ $form->name }}</h6>
                        <div class="small text-muted">/{{ $form->slug }}</div>
                    </div>
                    <span class="badge {{ $form->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $form->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <div class="p-2 rounded text-center flex-grow-1" style="background: rgba(99,102,241,0.1);">
                        <div class="fw-bold">{{ count($form->fields ?? []) }}</div>
                        <div class="small text-muted">Fields</div>
                    </div>
                    <div class="p-2 rounded text-center flex-grow-1" style="background: rgba(16,185,129,0.1);">
                        <div class="fw-bold">{{ $form->submissions_count ?? 0 }}</div>
                        <div class="small text-muted">Submissions</div>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="small text-muted mb-1">Embed Code:</div>
                    <div class="p-2 rounded" style="background: #0a0c12; font-family: monospace; font-size: 0.7rem; color: #a5b4fc; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
                        &lt;script src="{{ url("/lead-forms/{$form->slug}/embed") }}"&gt;&lt;/script&gt;
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3 pt-3" style="border-top: 1px solid #1e2130;">
                    <a href="{{ route('lead-forms.show', $form) }}" class="btn btn-sm btn-outline-secondary flex-grow-1">
                        <i class="bi bi-bar-chart me-1"></i> Results
                    </a>
                    <a href="{{ route('lead-forms.edit', $form) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('lead-forms.destroy', $form) }}" method="POST" onsubmit="return confirm('Delete form?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="crm-card text-center py-5">
                <i class="bi bi-ui-checks text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3 mb-0">No lead forms yet. <a href="{{ route('lead-forms.create') }}" style="color: #6366f1;">Create your first one</a>.</p>
            </div>
        </div>
        @endforelse
    </div>

    <div class="mt-3">{{ $forms->links() }}</div>
</div>
@endsection
