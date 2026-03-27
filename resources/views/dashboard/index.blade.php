@extends('layouts.app')
@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>Dashboard</h1>
        <p class="text-muted small mb-0">Welcome back, {{ auth()->user()->name }}. Here's what's happening.</p>
    </div>
    <a href="{{ route('campaigns.create') }}" class="btn btn-accent">
        <i class="bi bi-plus-lg me-1"></i> New Campaign
    </a>
</div>

<div class="page-content">
    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Total Contacts</div>
                        <div class="stat-value">{{ number_format($stats['total_contacts']) }}</div>
                        <div class="small text-success mt-2"><i class="bi bi-arrow-up-right"></i> 12% this month</div>
                    </div>
                    <div class="stat-icon" style="background: rgba(99, 102, 241, 0.15); color: #6366f1;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Active Campaigns</div>
                        <div class="stat-value">{{ $stats['active_campaigns'] }}</div>
                        <div class="small text-muted mt-2">{{ $stats['total_campaigns'] }} total</div>
                    </div>
                    <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                        <i class="bi bi-megaphone-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Avg. Open Rate</div>
                        <div class="stat-value">{{ $stats['avg_open_rate'] }}%</div>
                        <div class="small text-muted mt-2">Industry avg: 21.3%</div>
                    </div>
                    <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                        <i class="bi bi-envelope-open-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-2">Emails Sent</div>
                        <div class="stat-value">{{ number_format($stats['total_sent']) }}</div>
                        <div class="small text-muted mt-2">{{ $stats['total_segments'] }} segments</div>
                    </div>
                    <div class="stat-icon" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">
                        <i class="bi bi-send-fill"></i>
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
                    <h6 class="mb-0 fw-semibold">Campaigns Over Time</h6>
                    <span class="badge bg-secondary">Last 6 months</span>
                </div>
                <canvas id="campaignChart" height="100"></canvas>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="crm-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-semibold">Campaign Types</h6>
                </div>
                <canvas id="typeChart" height="180"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Campaigns -->
    <div class="crm-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0 fw-semibold">Recent Campaigns</h6>
            <a href="{{ route('campaigns.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
        </div>

        @if($recentCampaigns->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-megaphone text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3">No campaigns yet. <a href="{{ route('campaigns.create') }}" style="color: #6366f1;">Create your first one</a></p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead>
                    <tr>
                        <th>Campaign</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Sent</th>
                        <th>Opens</th>
                        <th>Clicks</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentCampaigns as $campaign)
                    <tr>
                        <td>
                            <a href="{{ route('campaigns.show', $campaign) }}" class="text-decoration-none text-light fw-medium">
                                {{ $campaign->name }}
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-secondary">
                                <i class="{{ $campaign->type->icon() }} me-1"></i>{{ $campaign->type->label() }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $campaign->status->badgeClass() }}">
                                {{ $campaign->status->label() }}
                            </span>
                        </td>
                        <td>{{ number_format($campaign->stats->sent_count ?? 0) }}</td>
                        <td>{{ number_format($campaign->stats->opens ?? 0) }}</td>
                        <td>{{ number_format($campaign->stats->clicks ?? 0) }}</td>
                        <td class="text-muted small">{{ $campaign->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- Engagement Funnel -->
    <div class="row g-3 mt-0">
        <div class="col-lg-6">
            <div class="crm-card">
                <h6 class="mb-3 fw-semibold">Email Engagement Funnel</h6>
                <canvas id="funnelChart" height="140"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="crm-card">
                <h6 class="mb-3 fw-semibold">Quick Actions</h6>
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('campaigns.create') }}" class="d-block text-decoration-none p-3 rounded" style="background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.2);">
                            <i class="bi bi-megaphone text-indigo-400" style="color: #818cf8;"></i>
                            <div class="small mt-1 text-light">New Campaign</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('contacts.import-form') }}" class="d-block text-decoration-none p-3 rounded" style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2);">
                            <i class="bi bi-cloud-upload" style="color: #34d399;"></i>
                            <div class="small mt-1 text-light">Import Contacts</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('segments.create') }}" class="d-block text-decoration-none p-3 rounded" style="background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.2);">
                            <i class="bi bi-funnel" style="color: #fbbf24;"></i>
                            <div class="small mt-1 text-light">Build Segment</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('lead-forms.create') }}" class="d-block text-decoration-none p-3 rounded" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2);">
                            <i class="bi bi-ui-checks" style="color: #f87171;"></i>
                            <div class="small mt-1 text-light">Create Lead Form</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const chartDefaults = {
    color: '#8892a4',
    borderColor: '#1e2130',
};

Chart.defaults.color = '#8892a4';
Chart.defaults.borderColor = '#1e2130';

// Campaign line chart
new Chart(document.getElementById('campaignChart'), {
    type: 'line',
    data: {
        labels: @json($chartData['labels']),
        datasets: [{
            label: 'Campaigns Created',
            data: @json($chartData['data']),
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99, 102, 241, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#6366f1',
            pointRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});

// Doughnut chart for types
new Chart(document.getElementById('typeChart'), {
    type: 'doughnut',
    data: {
        labels: ['Email', 'SMS', 'Push', 'Social'],
        datasets: [{
            data: [{{ $recentCampaigns->where('type.value', 'email')->count() + 1 }}, 2, 1, 1],
            backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true } }
        }
    }
});

// Funnel bar chart
new Chart(document.getElementById('funnelChart'), {
    type: 'bar',
    data: {
        labels: ['Sent', 'Opened', 'Clicked', 'Converted'],
        datasets: [{
            label: 'Contacts',
            data: [
                {{ $stats['total_sent'] }},
                {{ round($stats['total_sent'] * ($stats['avg_open_rate'] / 100)) }},
                {{ round($stats['total_sent'] * 0.03) }},
                {{ round($stats['total_sent'] * 0.01) }}
            ],
            backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444'],
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true } }
    }
});
</script>
@endpush
