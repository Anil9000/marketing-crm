@extends('layouts.app')
@section('title', 'Campaigns')

@section('breadcrumb')
    <li class="breadcrumb-item active">Campaigns</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>Campaigns</h1>
        <p class="text-muted small mb-0">Manage and monitor all your marketing campaigns.</p>
    </div>
    <a href="{{ route('campaigns.create') }}" class="btn btn-accent">
        <i class="bi bi-plus-lg me-1"></i> New Campaign
    </a>
</div>

<div class="page-content">
    <!-- Filters -->
    <div class="crm-card mb-3">
        <form action="{{ route('campaigns.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-sm-6 col-md-3">
                <label class="form-label">Search</label>
                <div class="input-group">
                    <span class="input-group-text" style="background: #1a1f2e; border-color: #1e2130;">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control" placeholder="Campaign name..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-sm-6 col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-md-2">
                <label class="form-label">Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    @foreach($types as $type)
                        <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-md-2">
                <label class="form-label">From</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-sm-6 col-md-2">
                <label class="form-label">To</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-sm-6 col-md-1">
                <button type="submit" class="btn btn-accent w-100">Filter</button>
            </div>
        </form>
    </div>

    <!-- Campaign Table -->
    <div class="crm-table">
        <table class="table table-dark table-hover mb-0">
            <thead>
                <tr>
                    <th>Campaign</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Sent</th>
                    <th>Open Rate</th>
                    <th>Click Rate</th>
                    <th>Scheduled</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($campaigns as $campaign)
                <tr>
                    <td>
                        <div class="fw-medium">
                            <a href="{{ route('campaigns.show', $campaign) }}" class="text-decoration-none text-light">
                                {{ $campaign->name }}
                            </a>
                        </div>
                        @if($campaign->subject)
                            <div class="small text-muted">{{ Str::limit($campaign->subject, 50) }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-secondary">
                            <i class="{{ $campaign->type->icon() }} me-1"></i>{{ $campaign->type->label() }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $campaign->status->badgeClass() }}">{{ $campaign->status->label() }}</span>
                    </td>
                    <td>{{ number_format($campaign->stats->sent_count ?? 0) }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 4px; background: #1e2130; max-width: 60px;">
                                <div class="progress-bar" style="width: {{ min($campaign->open_rate, 100) }}%; background: #6366f1;"></div>
                            </div>
                            <span class="small">{{ $campaign->open_rate }}%</span>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 4px; background: #1e2130; max-width: 60px;">
                                <div class="progress-bar" style="width: {{ min($campaign->click_rate, 100) }}%; background: #10b981;"></div>
                            </div>
                            <span class="small">{{ $campaign->click_rate }}%</span>
                        </div>
                    </td>
                    <td class="text-muted small">
                        {{ $campaign->scheduled_at ? $campaign->scheduled_at->format('M d, Y') : '—' }}
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-sm btn-outline-secondary" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('campaigns.edit', $campaign) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this campaign?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <i class="bi bi-megaphone text-muted" style="font-size: 2.5rem;"></i>
                        <p class="text-muted mt-3 mb-0">No campaigns found. <a href="{{ route('campaigns.create') }}" style="color: #6366f1;">Create one now</a>.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $campaigns->withQueryString()->links() }}
    </div>
</div>
@endsection
