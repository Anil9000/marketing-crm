@extends('layouts.app')
@section('title', $template->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('email-templates.index') }}" class="text-decoration-none text-muted">Email Templates</a></li>
    <li class="breadcrumb-item active">{{ $template->name }}</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1>{{ $template->name }}</h1>
            @if($template->category)
                <span class="badge bg-secondary">{{ ucfirst($template->category) }}</span>
            @endif
            @if($template->is_public)
                <span class="badge bg-info text-dark"><i class="bi bi-globe me-1"></i>Public</span>
            @endif
        </div>
        <p class="text-muted small mb-0">
            <i class="bi bi-envelope me-1"></i>{{ $template->subject }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('email-templates.edit', $template) }}" class="btn btn-outline-secondary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <form action="{{ route('email-templates.destroy', $template) }}" method="POST"
              onsubmit="return confirm('Permanently delete this template? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
        </form>
    </div>
</div>

<div class="page-content">
    <div class="row g-3">
        <!-- Preview -->
        <div class="col-lg-8">
            <div class="crm-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0">Email Preview</h6>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="desktopBtn" onclick="setPreviewWidth('100%', this)">
                            <i class="bi bi-monitor"></i> Desktop
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setPreviewWidth('480px', this)">
                            <i class="bi bi-phone"></i> Mobile
                        </button>
                    </div>
                </div>
                <div class="text-center" style="background: #1a1f2e; border-radius: 8px; padding: 12px;">
                    <iframe id="previewFrame"
                            srcdoc="{!! htmlspecialchars($template->html_content, ENT_QUOTES, 'UTF-8') !!}"
                            style="width: 100%; height: 600px; border: none; border-radius: 6px; background: white; transition: width 0.3s;"></iframe>
                </div>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="col-lg-4">
            <div class="crm-card mb-3">
                <h6 class="fw-semibold mb-3">Template Info</h6>
                <dl class="row small mb-0">
                    <dt class="col-5 text-muted">Name</dt>
                    <dd class="col-7">{{ $template->name }}</dd>

                    <dt class="col-5 text-muted">Category</dt>
                    <dd class="col-7">{{ $template->category ? ucfirst($template->category) : '—' }}</dd>

                    <dt class="col-5 text-muted">Subject</dt>
                    <dd class="col-7">{{ $template->subject }}</dd>

                    <dt class="col-5 text-muted">Visibility</dt>
                    <dd class="col-7">{{ $template->is_public ? 'Public' : 'Private' }}</dd>

                    <dt class="col-5 text-muted">Created</dt>
                    <dd class="col-7">{{ $template->created_at->format('M d, Y') }}</dd>

                    <dt class="col-5 text-muted">Updated</dt>
                    <dd class="col-7 mb-0">{{ $template->updated_at->diffForHumans() }}</dd>
                </dl>
            </div>

            <div class="crm-card mb-3">
                <h6 class="fw-semibold mb-3">Quick Actions</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('email-templates.edit', $template) }}" class="btn btn-accent">
                        <i class="bi bi-pencil me-1"></i> Edit Template
                    </a>
                    <a href="{{ route('campaigns.create') }}?template={{ $template->id }}" class="btn btn-outline-secondary">
                        <i class="bi bi-megaphone me-1"></i> Use in Campaign
                    </a>
                </div>
            </div>

            <div class="crm-card">
                <h6 class="fw-semibold mb-3">HTML Source</h6>
                <div class="position-relative">
                    <div class="p-3 rounded" style="background: #0a0c12; max-height: 280px; overflow-y: auto; border: 1px solid #1e2130;">
                        <pre style="color: #a5b4fc; font-size: 0.7rem; margin: 0; white-space: pre-wrap; word-break: break-all;">{{ htmlspecialchars($template->html_content) }}</pre>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2 w-100" onclick="copyHtml()">
                        <i class="bi bi-clipboard me-1"></i> Copy HTML
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function setPreviewWidth(width, btn) {
    document.getElementById('previewFrame').style.width = width;
    document.querySelectorAll('[onclick*="setPreviewWidth"]').forEach(b => {
        b.classList.remove('btn-accent');
        b.classList.add('btn-outline-secondary');
    });
    btn.classList.add('btn-accent');
    btn.classList.remove('btn-outline-secondary');
}

function copyHtml() {
    const html = @js($template->html_content);
    navigator.clipboard.writeText(html).then(() => {
        const btn = event.target.closest('button');
        const original = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Copied!';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-outline-secondary');
        setTimeout(() => {
            btn.innerHTML = original;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 2000);
    });
}

// Default desktop active
document.getElementById('desktopBtn').classList.add('btn-accent');
document.getElementById('desktopBtn').classList.remove('btn-outline-secondary');
</script>
@endpush
