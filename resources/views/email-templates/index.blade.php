@extends('layouts.app')
@section('title', 'Email Templates')

@section('breadcrumb')
    <li class="breadcrumb-item active">Email Templates</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>Email Templates</h1>
        <p class="text-muted small mb-0">Design and manage reusable email templates.</p>
    </div>
    <a href="{{ route('email-templates.create') }}" class="btn btn-accent">
        <i class="bi bi-plus-lg me-1"></i> New Template
    </a>
</div>

<div class="page-content">
    <!-- Category Filters -->
    <div class="d-flex gap-2 mb-3 flex-wrap">
        <a href="{{ route('email-templates.index') }}" class="btn btn-sm {{ !request('category') ? 'btn-accent' : 'btn-outline-secondary' }}">All</a>
        @foreach($categories as $cat)
            <a href="{{ route('email-templates.index', ['category' => $cat]) }}"
               class="btn btn-sm {{ request('category') === $cat ? 'btn-accent' : 'btn-outline-secondary' }}">
                {{ ucfirst($cat) }}
            </a>
        @endforeach
    </div>

    <div class="row g-3">
        @forelse($templates as $template)
        <div class="col-md-6 col-xl-4">
            <div class="crm-card h-100">
                <!-- Template Preview Thumbnail -->
                <div class="mb-3 rounded overflow-hidden position-relative" style="background: white; height: 180px;">
                    <iframe srcdoc="{!! htmlspecialchars(Str::limit($template->html_content ?? '', 3000), ENT_QUOTES, 'UTF-8') !!}"
                            style="width: 200%; height: 360px; transform: scale(0.5); transform-origin: 0 0; border: none; pointer-events: none;"
                            scrolling="no"></iframe>
                    <div class="position-absolute top-0 start-0 w-100 h-100"></div>
                </div>

                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="fw-semibold mb-0">{{ $template->name }}</h6>
                        <div class="small text-muted">{{ $template->subject }}</div>
                    </div>
                    <span class="badge bg-secondary">{{ $template->category }}</span>
                </div>

                <div class="d-flex gap-2 mt-3 pt-3" style="border-top: 1px solid #1e2130;">
                    <a href="{{ route('email-templates.show', $template) }}" class="btn btn-sm btn-outline-secondary flex-grow-1">
                        <i class="bi bi-eye me-1"></i> Preview
                    </a>
                    <a href="{{ route('email-templates.edit', $template) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('email-templates.destroy', $template) }}" method="POST" onsubmit="return confirm('Delete template?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="crm-card text-center py-5">
                <i class="bi bi-envelope-paper text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3 mb-0">No templates found. <a href="{{ route('email-templates.create') }}" style="color: #6366f1;">Create your first template</a>.</p>
            </div>
        </div>
        @endforelse
    </div>

    <div class="mt-3">{{ $templates->links() }}</div>
</div>
@endsection
