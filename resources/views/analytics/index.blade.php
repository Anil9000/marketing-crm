@extends('layouts.app')
@section('title', 'Analytics')

@section('breadcrumb')
    <li class="breadcrumb-item active">Analytics</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>Analytics</h1>
        <p class="text-muted small mb-0">Track campaign performance and email engagement metrics.</p>
    </div>
    <a href="{{ route('analytics.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-clockwise me-1"></i> Refresh
    </a>
</div>

<div class="page-content">
    <!-- Date Range Filter -->
    <div class="crm-card mb-4">
        <form action="{{ route('analytics.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-sm-6 col-md-3">
                <label class="form-label">Date From</label>
                <input type="date" name="from" class="form-control" value="{{ request('from', now()->subDays(30)->format('Y-m-d')) }}">
            </div>
            <div class="col-sm-6 col-md-3">
                <label class="form-label">Date To</label>
                <input type="date" name="to" class="form-control" value="{{ request('to', now()->format('Y-m-d')) }}">
            </div>
            <div class="col-sm-6 col-md-2">
                <label class="form-label">Campaign Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="email"  {{ request('type') === 'email'  ? 'selected' : '' }}>Email</option>
                    <option value="sms"    {{ request('type') === 'sms'    ? 'selected' : '' }}>SMS</option>
                    <option value="push"   {{ request('type') === 'push'   ? 'selected' : '' }}>Push</option>
                    <option value="social" {{ request('type') === 'social' ? 'selected' : '' }}>Social</option>
                </select>
            </div>
            <div class="col-sm-6 col-md-2">
                <button type="submit" class="btn btn-accent w-100">
                    <i class="bi bi-funnel me-1"></i> Apply Filter
                </button>
            </div>
            <div class="col-sm-6 col-md-2">
                <a href="{{ route('analytics.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-lg me-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Overview Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Emails Sent</div>
                        <div class="stat-value">{{ number_format($overview['total_sent']) }}</div>
                        <div class="small text-muted mt-1">In selected period</div>
                    </div>
                    <div class="stat-icon" style="background: rgba(99,102,241,0.15); color: #6366f1;">
                        <i class="bi bi-send-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Open Rate</div>
                        <div class="stat-value">{{ $overview['avg_open_rate'] }}%</div>
                        <div class="small text-muted mt-1">{{ number_format($overview['total_opens']) }} total opens</div>
                    </div>
                    <div class="stat-icon" style="background: rgba(16,185,129,0.15); color: #10b981;">
                        <i class="bi bi-envelope-open-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Click Rate</div>
                        <div class="stat-value">{{ $overview['avg_click_rate'] }}%</div>
                        <div class="small text-muted mt-1">{{ number_format($overview['total_clicks']) }} total clicks</div>
                    </div>
                    <div class="stat-icon" style="background: rgba(59,130,246,0.15); color: #3b82f6;">
                        <i class="bi bi-cursor-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Bounce Rate</div>
                        <div class="stat-value">{{ $overview['avg_bounce_rate'] ?? '0' }}%</div>
                        <div class="small text-muted mt-1">{{ number_format($overview['total_bounces'] ?? 0) }} bounces</div>
                    </div>
                    <div class="stat-icon" style="background: rgba(239,68,68,0.15); color: #ef4444;">
                        <i class="bi bi-envelope-x-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="crm-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0">Email Events Over Time</h6>
                    <span class="badge bg-secondary">
                        {{ request('from', now()->subDays(30)->format('M d')) }} — {{ request('to', now()->format('M d, Y')) }}
                    </span>
                </div>
                <canvas id="timelineChart" height="120"></canvas>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="crm-card">
                <h6 class="fw-semibold mb-3">Campaign Type Distribution</h6>
                <canvas id="typeChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Opens vs Clicks Bar Chart -->
    <div class="row g-3 mb-4">
        <div class="col-lg-5">
            <div class="crm-card">
                <h6 class="fw-semibold mb-3">Engagement Funnel</h6>
                <canvas id="funnelChart" height="180"></canvas>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="crm-card">
                <h6 class="fw-semibold mb-3">Opens vs Clicks per Campaign</h6>
                <canvas id="opensClicksChart" height="180"></canvas>
            </div>
        </div>
    </div>

    <!-- Campaign Stats Table -->
    <div class="crm-table">
        <div class="px-3 py-3 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #1e2130;">
            <h6 class="mb-0 fw-semibold">Campaign Performance</h6>
            <span class="badge bg-secondary">{{ count($campaignStats) }} campaigns</span>
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead>
                    <tr>
                        <th>Campaign Name</th>
                        <th>Type</th>
                        <th class="text-end">Sent</th>
                        <th class="text-end">Opens</th>
                        <th class="text-end">Clicks</th>
                        <th class="text-end">Conversions</th>
                        <th class="text-end">Open Rate</th>
                        <th class="text-end">Click Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaignStats as $row)
                    <tr>
                        <td>
                            <a href="{{ route('campaigns.show', $row['campaign']) }}" class="text-decoration-none text-light fw-medium">
                                {{ Str::limit($row['campaign']->name, 35) }}
                            </a>
                        </td>
                        <td>
                            @php
                                $typeBadge = match($row['campaign']->type->value ?? 'email') {
                                    'email'             => 'bg-primary',
                                    'sms'               => 'bg-warning text-dark',
                                    'push_notification' => 'bg-info text-dark',
                                    'social_media'      => 'bg-success',
                                    default             => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $typeBadge }}">{{ $row['campaign']->type->label() }}</span>
                        </td>
                        <td class="text-end">{{ number_format($row['sent']) }}</td>
                        <td class="text-end">{{ number_format($row['opens']) }}</td>
                        <td class="text-end">{{ number_format($row['clicks']) }}</td>
                        <td class="text-end">{{ number_format($row['conversions']) }}</td>
                        <td class="text-end">
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                <div class="progress" style="width: 48px; height: 4px; background: #1e2130;">
                                    <div class="progress-bar" style="width: {{ min($row['open_rate'], 100) }}%; background: #6366f1;"></div>
                                </div>
                                <span class="small">{{ $row['open_rate'] }}%</span>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                <div class="progress" style="width: 48px; height: 4px; background: #1e2130;">
                                    <div class="progress-bar" style="width: {{ min($row['click_rate'], 100) }}%; background: #10b981;"></div>
                                </div>
                                <span class="small">{{ $row['click_rate'] }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-bar-chart-line text-muted" style="font-size: 2.5rem;"></i>
                            <p class="text-muted mt-3 mb-0">No campaign data found for the selected period.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
Chart.defaults.color = '#8892a4';
Chart.defaults.borderColor = '#1e2130';

@php
$timelineCollection = collect($timeline ?? []);
$dates = $timelineCollection->pluck('date')->unique()->sort()->values();
@endphp

// Timeline line chart
new Chart(document.getElementById('timelineChart'), {
    type: 'line',
    data: {
        labels: @json($dates->toArray()),
        datasets: [
            {
                label: 'Opens',
                data: @json($dates->map(fn($d) => $timelineCollection->where('date', $d)->where('event_type', 'open')->sum('count'))->values()),
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99,102,241,0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 3,
            },
            {
                label: 'Clicks',
                data: @json($dates->map(fn($d) => $timelineCollection->where('date', $d)->where('event_type', 'click')->sum('count'))->values()),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16,185,129,0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 3,
            },
            {
                label: 'Bounces',
                data: @json($dates->map(fn($d) => $timelineCollection->where('date', $d)->where('event_type', 'bounce')->sum('count'))->values()),
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239,68,68,0.05)',
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 3,
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        },
        plugins: {
            legend: { position: 'top', labels: { usePointStyle: true } }
        }
    }
});

// Type distribution doughnut
new Chart(document.getElementById('typeChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(collect($typeDistribution ?? [])->keys()->map(fn($k) => ucfirst(str_replace('_', ' ', $k)))->values()) !!},
        datasets: [{
            data: @json(collect($typeDistribution ?? [])->values()),
            backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444'],
            borderWidth: 0,
        }]
    },
    options: {
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true } }
        }
    }
});

// Engagement funnel
new Chart(document.getElementById('funnelChart'), {
    type: 'bar',
    data: {
        labels: ['Sent', 'Opened', 'Clicked', 'Converted'],
        datasets: [{
            data: [
                {{ $overview['total_sent'] }},
                {{ $overview['total_opens'] }},
                {{ $overview['total_clicks'] }},
                {{ $overview['total_conversions'] }}
            ],
            backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444'],
            borderRadius: 6,
        }]
    },
    options: {
        indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true } }
    }
});

// Opens vs Clicks bar chart per campaign
@php
$chartSlice   = collect($campaignStats)->take(10);
$chartLabels  = $chartSlice->map(fn($row) => Str::limit($row['campaign']->name, 20))->values();
$opensData    = $chartSlice->map(fn($row) => $row['opens'])->values();
$clicksData   = $chartSlice->map(fn($row) => $row['clicks'])->values();
@endphp

new Chart(document.getElementById('opensClicksChart'), {
    type: 'bar',
    data: {
        labels: @json($chartLabels->toArray()),
        datasets: [
            {
                label: 'Opens',
                data: @json($opensData->toArray()),
                backgroundColor: 'rgba(99,102,241,0.7)',
                borderRadius: 4,
            },
            {
                label: 'Clicks',
                data: @json($clicksData->toArray()),
                backgroundColor: 'rgba(16,185,129,0.7)',
                borderRadius: 4,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top', labels: { usePointStyle: true } }
        },
        scales: {
            y: { beginAtZero: true },
            x: { ticks: { maxRotation: 30 } }
        }
    }
});
</script>
@endpush
