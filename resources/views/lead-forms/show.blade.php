@extends('layouts.app')
@section('title', $leadForm->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('lead-forms.index') }}" class="text-decoration-none text-muted">Lead Forms</a></li>
    <li class="breadcrumb-item active">{{ $leadForm->name }}</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $leadForm->name }}</h1>
        <p class="text-muted small mb-0">
            <span class="badge {{ $leadForm->is_active ? 'bg-success' : 'bg-secondary' }} me-2">
                {{ $leadForm->is_active ? 'Active' : 'Inactive' }}
            </span>
            Created {{ $leadForm->created_at->format('M d, Y') }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('lead-forms.embed', $leadForm->slug) }}" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-box-arrow-up-right me-1"></i> View Form
        </a>
        <a href="{{ route('lead-forms.edit', $leadForm) }}" class="btn btn-accent">
            <i class="bi bi-pencil me-1"></i> Edit Form
        </a>
    </div>
</div>

<div class="page-content">
    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="stat-card text-center">
                <div class="stat-value" style="color: #6366f1;">{{ $submissions->total() }}</div>
                <div class="stat-label mt-1">Total Submissions</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card text-center">
                <div class="stat-value">{{ count($leadForm->fields ?? []) }}</div>
                <div class="stat-label mt-1">Form Fields</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card text-center">
                <div class="stat-value {{ $leadForm->is_active ? 'text-success' : 'text-danger' }}">
                    {{ $leadForm->is_active ? 'Active' : 'Inactive' }}
                </div>
                <div class="stat-label mt-1">Form Status</div>
            </div>
        </div>
    </div>

    <!-- Embed Code -->
    <div class="crm-card mb-4">
        <h6 class="fw-semibold mb-3">Embed Code</h6>
        <p class="text-muted small mb-2">Copy and paste this snippet into any webpage to display your form:</p>
        <div class="row g-2 align-items-stretch">
            <div class="col-lg-10">
                <div class="p-3 rounded position-relative" style="background: #0a0c12; border: 1px solid #1e2130;">
                    <code id="embedCodeBlock" style="color: #a5b4fc; font-size: 0.8rem; word-break: break-all;">{{ $embedCode }}</code>
                </div>
            </div>
            <div class="col-lg-2">
                <button class="btn btn-outline-secondary w-100 h-100" onclick="copyEmbedCode()">
                    <i class="bi bi-clipboard me-1"></i> Copy
                </button>
            </div>
        </div>

        <div class="mt-2 d-flex gap-2">
            <a href="{{ route('lead-forms.embed', $leadForm->slug) }}" target="_blank" class="btn btn-sm btn-outline-info">
                <i class="bi bi-box-arrow-up-right me-1"></i> Open Public Form
            </a>
            @if($submissions->total() > 0)
            <form action="{{ route('lead-forms.export', $leadForm) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-download me-1"></i> Export Submissions CSV
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Field summary -->
    @if(!empty($leadForm->fields))
    <div class="crm-card mb-4">
        <h6 class="fw-semibold mb-3">Form Fields</h6>
        <div class="row g-2">
            @foreach($leadForm->fields as $field)
            <div class="col-sm-6 col-md-4">
                <div class="p-3 rounded" style="background: #1a1f2e; border: 1px solid #1e2130;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-medium small text-light">{{ $field['label'] }}</div>
                            <div class="text-muted" style="font-size: 0.7rem;">key: <code style="color: #a5b4fc;">{{ $field['name'] }}</code></div>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-1">
                            <span class="badge bg-secondary" style="font-size: 0.65rem;">{{ ucfirst($field['type']) }}</span>
                            @if(!empty($field['required']))
                                <span class="badge bg-danger" style="font-size: 0.65rem;">Required</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Submissions Table -->
    <div class="crm-table">
        <div class="px-3 py-3 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #1e2130;">
            <h6 class="mb-0 fw-semibold">Submissions</h6>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-secondary">{{ number_format($submissions->total()) }} total</span>
                @if($submissions->total() > 0)
                <form action="{{ route('lead-forms.export', $leadForm) }}" method="POST" class="mb-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-download me-1"></i> Export CSV
                    </button>
                </form>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        @foreach($leadForm->fields ?? [] as $field)
                            <th>{{ $field['label'] ?? $field['name'] }}</th>
                        @endforeach
                        <th>IP Address</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $submission)
                    <tr>
                        <td class="text-muted small">{{ $submission->id }}</td>
                        @foreach($leadForm->fields ?? [] as $field)
                            <td class="small">{{ $submission->data[$field['name']] ?? '—' }}</td>
                        @endforeach
                        <td class="text-muted small">{{ $submission->ip_address ?? '—' }}</td>
                        <td class="text-muted small">{{ $submission->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($leadForm->fields ?? []) + 3 }}" class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 2.5rem;"></i>
                            <p class="text-muted mt-3 mb-0">No submissions yet. Share your form to start collecting leads.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $submissions->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
function copyEmbedCode() {
    const code = document.getElementById('embedCodeBlock').textContent;
    navigator.clipboard.writeText(code.trim()).then(() => {
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
</script>
@endpush
