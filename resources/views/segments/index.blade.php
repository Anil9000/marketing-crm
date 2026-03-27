@extends('layouts.app')
@section('title', 'Segments')

@section('breadcrumb')
    <li class="breadcrumb-item active">Segments</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>Audience Segments</h1>
        <p class="text-muted small mb-0">Group your contacts with custom filters.</p>
    </div>
    <a href="{{ route('segments.create') }}" class="btn btn-accent">
        <i class="bi bi-plus-lg me-1"></i> New Segment
    </a>
</div>

<div class="page-content">
    <div class="row g-3">
        @forelse($segments as $segment)
        <div class="col-md-6 col-xl-4">
            <div class="crm-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="fw-semibold mb-0">
                            <a href="{{ route('segments.show', $segment) }}" class="text-decoration-none text-light">
                                {{ $segment->name }}
                            </a>
                        </h6>
                        <div class="small text-muted">{{ $segment->description ?? 'No description' }}</div>
                    </div>
                    <span class="badge {{ $segment->is_dynamic ? 'bg-info' : 'bg-secondary' }}">
                        {{ $segment->is_dynamic ? 'Dynamic' : 'Static' }}
                    </span>
                </div>

                <div class="d-flex align-items-center gap-2 mt-3 py-2 px-3 rounded" style="background: rgba(99,102,241,0.1);">
                    <i class="bi bi-people" style="color: #6366f1;"></i>
                    <div>
                        <div class="fw-bold text-light">{{ number_format($segment->contact_count) }}</div>
                        <div class="small text-muted">Contacts</div>
                    </div>
                </div>

                @if(!empty($segment->filters))
                <div class="mt-3">
                    <div class="small text-muted mb-1">Filters ({{ count($segment->filters) }}):</div>
                    @foreach(array_slice($segment->filters, 0, 2) as $filter)
                        <span class="badge bg-secondary me-1 mb-1">
                            {{ $filter['field'] }} {{ $filter['operator'] }} {{ $filter['value'] }}
                        </span>
                    @endforeach
                    @if(count($segment->filters) > 2)
                        <span class="badge bg-secondary">+{{ count($segment->filters) - 2 }} more</span>
                    @endif
                </div>
                @endif

                <div class="d-flex gap-2 mt-3 pt-3" style="border-top: 1px solid #1e2130;">
                    <a href="{{ route('segments.show', $segment) }}" class="btn btn-sm btn-outline-secondary flex-grow-1">
                        <i class="bi bi-eye me-1"></i> View
                    </a>
                    <a href="{{ route('segments.edit', $segment) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('segments.destroy', $segment) }}" method="POST" onsubmit="return confirm('Delete segment?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="crm-card text-center py-5">
                <i class="bi bi-funnel text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3 mb-0">No segments yet. <a href="{{ route('segments.create') }}" style="color: #6366f1;">Build your first segment</a>.</p>
            </div>
        </div>
        @endforelse
    </div>

    <div class="mt-3">{{ $segments->links() }}</div>
</div>
@endsection
