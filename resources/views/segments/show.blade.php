@extends('layouts.app')
@section('title', $segment->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('segments.index') }}" class="text-decoration-none text-muted">Segments</a></li>
    <li class="breadcrumb-item active">{{ $segment->name }}</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1>{{ $segment->name }}</h1>
            <span class="badge {{ $segment->is_dynamic ? 'bg-info text-dark' : 'bg-secondary' }}">
                {{ $segment->is_dynamic ? 'Dynamic' : 'Static' }}
            </span>
        </div>
        @if($segment->description)
            <p class="text-muted small mb-0">{{ $segment->description }}</p>
        @endif
    </div>
    <div class="d-flex gap-2">
        <form action="{{ route('segments.export', $segment) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">
                <i class="bi bi-download me-1"></i> Export CSV
            </button>
        </form>
        <a href="{{ route('segments.edit', $segment) }}" class="btn btn-accent">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
    </div>
</div>

<div class="page-content">
    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="stat-card text-center">
                <div class="stat-value" style="color: #6366f1;">{{ number_format($segment->contact_count) }}</div>
                <div class="stat-label mt-1">Total Contacts</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card text-center">
                <div class="stat-value">{{ count($segment->filters ?? []) }}</div>
                <div class="stat-label mt-1">Active Filters</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card text-center">
                <div class="stat-value" style="font-size: 1.25rem;">{{ $segment->created_at->format('M d, Y') }}</div>
                <div class="stat-label mt-1">Created</div>
            </div>
        </div>
    </div>

    <!-- Applied Filters -->
    @if(!empty($segment->filters))
    <div class="crm-card mb-4">
        <h6 class="fw-semibold mb-3">Applied Filters</h6>
        <div class="d-flex flex-wrap gap-2">
            @foreach($segment->filters as $filter)
                <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded"
                     style="background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.25);">
                    <span class="badge bg-secondary" style="font-size: 0.7rem;">{{ ucfirst(str_replace('_', ' ', $filter['field'])) }}</span>
                    <span class="text-muted small">{{ str_replace('_', ' ', $filter['operator']) }}</span>
                    <span class="badge" style="background: rgba(99,102,241,0.3); color: #a5b4fc; font-size: 0.75rem;">
                        {{ $filter['value'] }}
                    </span>
                </div>
            @endforeach
        </div>
        <p class="text-muted small mt-2 mb-0">
            <i class="bi bi-info-circle me-1"></i>
            All filters are combined with AND logic — contacts must match every rule.
        </p>
    </div>
    @else
    <div class="crm-card mb-4">
        <h6 class="fw-semibold mb-2">Applied Filters</h6>
        <p class="text-muted small mb-0">This segment has no filters — it will match all contacts.</p>
    </div>
    @endif

    <!-- Contacts Table -->
    <div class="crm-table">
        <div class="d-flex justify-content-between align-items-center px-3 py-3" style="border-bottom: 1px solid #1e2130;">
            <h6 class="mb-0 fw-semibold">Contacts in this Segment</h6>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-secondary">{{ $contacts->total() }} contacts</span>
                <form action="{{ route('segments.export', $segment) }}" method="POST" class="mb-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-download me-1"></i> Export CSV
                    </button>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name / Email</th>
                        <th>Phone</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Last Activity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $contact)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                     style="width:32px; height:32px; background: #6366f1; font-size: 0.75rem; flex-shrink: 0;">
                                    {{ strtoupper(substr($contact->first_name ?? $contact->email, 0, 1)) }}
                                </div>
                                <div>
                                    <a href="{{ route('contacts.show', $contact) }}" class="text-decoration-none text-light fw-medium d-block">
                                        {{ $contact->full_name ?: 'No name' }}
                                    </a>
                                    <div class="small text-muted">{{ $contact->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted small">{{ $contact->phone ?? '—' }}</td>
                        <td class="text-muted small">{{ $contact->location ?? '—' }}</td>
                        <td>
                            <span class="badge {{ match($contact->status) {
                                'active'       => 'bg-success',
                                'unsubscribed' => 'bg-warning text-dark',
                                'bounced'      => 'bg-danger',
                                default        => 'bg-secondary',
                            } }}">
                                {{ ucfirst($contact->status) }}
                            </span>
                        </td>
                        <td class="text-muted small">
                            {{ $contact->last_activity_at ? $contact->last_activity_at->diffForHumans() : '—' }}
                        </td>
                        <td>
                            <a href="{{ route('contacts.show', $contact) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-people text-muted" style="font-size: 2.5rem;"></i>
                            <p class="text-muted mt-3 mb-0">No contacts match this segment's filters.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $contacts->links() }}</div>
</div>
@endsection
