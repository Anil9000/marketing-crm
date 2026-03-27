@extends('layouts.app')
@section('title', $campaign->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('campaigns.index') }}" class="text-decoration-none text-muted">Campaigns</a></li>
    <li class="breadcrumb-item active">{{ Str::limit($campaign->name, 30) }}</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1>{{ $campaign->name }}</h1>
            <span class="badge {{ $campaign->status->badgeClass() }}">{{ $campaign->status->label() }}</span>
        </div>
        <div class="d-flex gap-3 text-muted small">
            <span><i class="{{ $campaign->type->icon() }} me-1"></i>{{ $campaign->type->label() }}</span>
            @if($campaign->sent_at)
                <span><i class="bi bi-send me-1"></i>Sent {{ $campaign->sent_at->format('M d, Y g:i A') }}</span>
            @endif
            @if($campaign->segment)
                <span><i class="bi bi-funnel me-1"></i>{{ $campaign->segment->name }}</span>
            @endif
        </div>
    </div>
    <div class="d-flex gap-2">
        @if(in_array($campaign->status->value, ['draft', 'scheduled']))
        <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger" onclick="return confirm('Delete campaign?')">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
        </form>
        @endif
        <a href="{{ route('campaigns.edit', $campaign) }}" class="btn btn-outline-secondary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        @if(in_array($campaign->status->value, ['draft', 'scheduled']))
        <form action="/api/v1/campaigns/{{ $campaign->id }}/send" method="POST" id="sendForm">
            <button type="button" class="btn btn-accent" onclick="sendCampaign()">
                <i class="bi bi-send me-1"></i> Send Now
            </button>
        </form>
        @endif
    </div>
</div>

<div class="page-content">
    <!-- Stats Strip -->
    <div class="crm-card mb-4 p-0">
        <div class="row g-0 text-center" style="border-radius:12px; overflow:hidden;">
            @php
                $metrics = [
                    ['value' => number_format($stats['sent_count']),   'label' => 'SENT',        'rate' => null,                      'color' => '#6366f1'],
                    ['value' => number_format($stats['opens']),        'label' => 'OPENS',       'rate' => $stats['open_rate'].'%',   'color' => '#3b82f6'],
                    ['value' => number_format($stats['clicks']),       'label' => 'CLICKS',      'rate' => $stats['click_rate'].'%',  'color' => '#10b981'],
                    ['value' => number_format($stats['conversions']),  'label' => 'CONVERSIONS', 'rate' => $stats['conversion_rate'].'%', 'color' => '#f59e0b'],
                    ['value' => number_format($stats['bounces']),      'label' => 'BOUNCES',     'rate' => null,                      'color' => '#f97316'],
                    ['value' => number_format($stats['unsubscribes']), 'label' => 'UNSUBS',      'rate' => null,                      'color' => '#ef4444'],
                ];
            @endphp
            @foreach($metrics as $i => $m)
            <div class="col-6 col-md-4 col-lg-2 py-4 px-3"
                 style="border-right: {{ $i < 5 ? '1px solid #1e2130' : 'none' }}; border-bottom: 1px solid #1e2130;">
                <div style="font-size:1.8rem; font-weight:700; color:{{ $m['color'] }}; line-height:1;">
                    {{ $m['value'] }}
                </div>
                <div style="font-size:0.7rem; font-weight:600; letter-spacing:0.08em; color:#64748b; margin-top:6px;">
                    {{ $m['label'] }}
                </div>
                @if($m['rate'])
                <div style="font-size:0.75rem; color:#94a3b8; margin-top:2px;">{{ $m['rate'] }}</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <div class="row g-3">
        <!-- Performance Chart -->
        <div class="col-lg-8">
            <div class="crm-card">
                <h6 class="fw-semibold mb-3">Performance Overview</h6>
                <canvas id="performanceChart" height="120"></canvas>
            </div>

            @if($campaign->content)
            <div class="crm-card mt-3">
                <h6 class="fw-semibold mb-3">Campaign Content</h6>
                @if($campaign->type->value === 'email')
                    <iframe srcdoc="{!! htmlspecialchars($campaign->content ?? '', ENT_QUOTES, 'UTF-8') !!}" style="width: 100%; height: 400px; border: 1px solid #1e2130; border-radius: 8px; background: white;"></iframe>
                @else
                    <div class="p-3 rounded" style="background: #1a1f2e; white-space: pre-wrap; font-size: 0.875rem;">{{ $campaign->content }}</div>
                @endif
            </div>
            @endif
        </div>

        <!-- Side Details -->
        <div class="col-lg-4">
            <div class="crm-card mb-3">
                <h6 class="fw-semibold mb-3">Campaign Info</h6>
                <dl class="row small mb-0">
                    <dt class="col-5 text-muted">Created</dt>
                    <dd class="col-7">{{ $campaign->created_at->format('M d, Y') }}</dd>

                    <dt class="col-5 text-muted">Scheduled</dt>
                    <dd class="col-7">{{ $campaign->scheduled_at?->format('M d, Y H:i') ?? '—' }}</dd>

                    <dt class="col-5 text-muted">Sent At</dt>
                    <dd class="col-7">{{ $campaign->sent_at?->format('M d, Y H:i') ?? '—' }}</dd>

                    <dt class="col-5 text-muted">Budget</dt>
                    <dd class="col-7">${{ number_format($campaign->budget, 2) }}</dd>

                    <dt class="col-5 text-muted">Spent</dt>
                    <dd class="col-7">${{ number_format($campaign->spent, 2) }}</dd>

                    <dt class="col-5 text-muted">A/B Test</dt>
                    <dd class="col-7">{{ $campaign->ab_test_enabled ? 'Enabled' : 'Disabled' }}</dd>

                    <dt class="col-5 text-muted">Frequency</dt>
                    <dd class="col-7">{{ ucwords(str_replace('_', ' ', $campaign->frequency)) }}</dd>

                    @if($campaign->segment)
                    <dt class="col-5 text-muted">Segment</dt>
                    <dd class="col-7">
                        <a href="{{ route('segments.show', $campaign->segment) }}" style="color: #6366f1;">
                            {{ $campaign->segment->name }}
                        </a>
                    </dd>
                    @endif
                </dl>
            </div>

            <!-- Budget Progress -->
            <div class="crm-card mb-3">
                <h6 class="fw-semibold mb-3">Budget Utilization</h6>
                @php $pct = $campaign->budget > 0 ? min(100, round(($campaign->spent / $campaign->budget) * 100)) : 0; @endphp
                <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>Spent: ${{ number_format($campaign->spent, 2) }}</span>
                    <span>Budget: ${{ number_format($campaign->budget, 2) }}</span>
                </div>
                <div class="progress" style="height: 8px; background: #1e2130;">
                    <div class="progress-bar {{ $pct > 90 ? 'bg-danger' : 'bg-success' }}" style="width: {{ $pct }}%;"></div>
                </div>
                <div class="small text-muted mt-1">{{ $pct }}% utilized</div>
            </div>

            @if($campaign->abTest)
            <div class="crm-card">
                <h6 class="fw-semibold mb-3">A/B Test Results</h6>
                <div class="row g-2">
                    <div class="col-6">
                        <div class="p-2 rounded text-center" style="background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.3);">
                            <div class="small text-muted">Variant A</div>
                            <div class="fw-bold">{{ $campaign->abTest->variant_a_opens }} opens</div>
                            <div class="small text-muted">{{ $campaign->abTest->variant_a_clicks }} clicks</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 rounded text-center" style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3);">
                            <div class="small text-muted">Variant B</div>
                            <div class="fw-bold">{{ $campaign->abTest->variant_b_opens }} opens</div>
                            <div class="small text-muted">{{ $campaign->abTest->variant_b_clicks }} clicks</div>
                        </div>
                    </div>
                </div>
                @if($campaign->abTest->winner)
                    <div class="mt-2 text-center">
                        <span class="badge bg-success">Winner: Variant {{ strtoupper($campaign->abTest->winner) }}</span>
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
Chart.defaults.color = '#8892a4';
Chart.defaults.borderColor = '#1e2130';

new Chart(document.getElementById('performanceChart'), {
    type: 'bar',
    data: {
        labels: ['Sent', 'Opens', 'Clicks', 'Conversions', 'Bounces', 'Unsubscribes'],
        datasets: [{
            label: 'Count',
            data: [
                {{ $stats['sent_count'] }},
                {{ $stats['opens'] }},
                {{ $stats['clicks'] }},
                {{ $stats['conversions'] }},
                {{ $stats['bounces'] }},
                {{ $stats['unsubscribes'] }}
            ],
            backgroundColor: ['#3b82f6', '#6366f1', '#10b981', '#f59e0b', '#f97316', '#ef4444'],
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

function sendCampaign() {
    if (!confirm('Send this campaign now? This will activate it and begin sending.')) return;

    const token = document.querySelector('meta[name="csrf-token"]').content;
    fetch('/api/v1/campaigns/{{ $campaign->id }}/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Authorization': 'Bearer ' + (localStorage.getItem('jwt_token') || ''),
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.message) {
            alert(data.message);
            window.location.reload();
        }
    })
    .catch(() => {
        // Fallback: submit form POST to web route
        window.location.href = '/campaigns/{{ $campaign->id }}/edit';
    });
}
</script>
@endpush
