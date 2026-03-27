@extends('layouts.app')
@section('title', 'Campaign Calendar')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('campaigns.index') }}" class="text-decoration-none text-muted">Campaigns</a></li>
    <li class="breadcrumb-item active">Calendar</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>Campaign Calendar</h1>
        <p class="text-muted small mb-0">View campaigns scheduled by date.</p>
    </div>
    <a href="{{ route('campaigns.create') }}" class="btn btn-accent">
        <i class="bi bi-plus-lg me-1"></i> New Campaign
    </a>
</div>

<div class="page-content">
    <!-- Month navigation -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ route('campaigns.calendar', ['month' => $prevMonth, 'year' => $prevYear]) }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-chevron-left"></i> {{ \Carbon\Carbon::create($prevYear, $prevMonth)->format('M Y') }}
        </a>
        <h5 class="mb-0 fw-semibold text-light">{{ $currentMonth }}</h5>
        <a href="{{ route('campaigns.calendar', ['month' => $nextMonth, 'year' => $nextYear]) }}"
           class="btn btn-outline-secondary">
            {{ \Carbon\Carbon::create($nextYear, $nextMonth)->format('M Y') }} <i class="bi bi-chevron-right"></i>
        </a>
    </div>

    <!-- Calendar Grid -->
    <div class="crm-card p-0 overflow-hidden">
        <table class="table table-bordered mb-0" style="border-color: #1e2130;">
            <thead>
                <tr>
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                        <th class="text-center py-2" style="background: #1a1f2e; color: #8892a4; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; border-color: #1e2130; width: 14.28%;">
                            {{ $day }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    $today = \Carbon\Carbon::today();
                    $firstDay = \Carbon\Carbon::create($calendarYear, $calendarMonthNum, 1);
                    $lastDay  = $firstDay->copy()->endOfMonth();
                    $startPad = $firstDay->dayOfWeek; // 0=Sun
                    $totalCells = $startPad + $lastDay->day;
                    $rows = (int) ceil($totalCells / 7);
                    $dayCounter = 1 - $startPad;
                @endphp

                @for($row = 0; $row < $rows; $row++)
                <tr>
                    @for($col = 0; $col < 7; $col++)
                        @php
                            $isCurrentMonth = $dayCounter >= 1 && $dayCounter <= $lastDay->day;
                            $date = $isCurrentMonth ? $firstDay->copy()->day($dayCounter)->format('Y-m-d') : null;
                            $isToday = $date === $today->format('Y-m-d');
                            $dayCampaigns = $date && isset($campaignsByDate[$date]) ? $campaignsByDate[$date] : collect();
                        @endphp
                        <td style="vertical-align: top; min-height: 100px; height: 110px; padding: 6px; border-color: #1e2130; background: {{ $isToday ? 'rgba(99,102,241,0.1)' : 'transparent' }};">
                            @if($isCurrentMonth)
                                <div class="d-flex justify-content-end mb-1">
                                    <span class="fw-semibold" style="font-size: 0.8rem; {{ $isToday ? 'color: #6366f1;' : 'color: #94a3b8;' }}">
                                        {{ $dayCounter }}
                                    </span>
                                </div>
                                @foreach($dayCampaigns->take(3) as $campaign)
                                    @php
                                        $badgeColor = match($campaign->type->value ?? 'email') {
                                            'email'             => '#6366f1',
                                            'sms'               => '#f59e0b',
                                            'push_notification' => '#3b82f6',
                                            'social_media'      => '#10b981',
                                            default             => '#6b7280',
                                        };
                                    @endphp
                                    <a href="{{ route('campaigns.show', $campaign) }}"
                                       class="d-block text-decoration-none mb-1 px-2 py-1 rounded"
                                       style="background: {{ $badgeColor }}22; border-left: 3px solid {{ $badgeColor }}; font-size: 0.7rem; color: #e2e8f0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                       title="{{ $campaign->name }}">
                                        {{ Str::limit($campaign->name, 18) }}
                                    </a>
                                @endforeach
                                @if($dayCampaigns->count() > 3)
                                    <div class="small text-muted" style="font-size: 0.65rem;">
                                        +{{ $dayCampaigns->count() - 3 }} more
                                    </div>
                                @endif
                            @else
                                <span style="color: #2d3748; font-size: 0.75rem;">
                                    {{ $dayCounter > 0 ? $dayCounter : '' }}
                                </span>
                            @endif
                        </td>
                        @php $dayCounter++; @endphp
                    @endfor
                </tr>
                @endfor
            </tbody>
        </table>
    </div>

    <!-- Upcoming campaigns list -->
    @if($upcomingCampaigns->isNotEmpty())
    <div class="crm-card mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-semibold mb-0">Upcoming Campaigns This Month</h6>
            <span class="badge bg-secondary">{{ $upcomingCampaigns->count() }} scheduled</span>
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead>
                    <tr>
                        <th>Campaign</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Scheduled Date</th>
                        <th>Segment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($upcomingCampaigns as $campaign)
                    <tr>
                        <td>
                            <a href="{{ route('campaigns.show', $campaign) }}" class="text-decoration-none text-light fw-medium">
                                {{ $campaign->name }}
                            </a>
                        </td>
                        <td>
                            @php
                                $typeBadge = match($campaign->type->value ?? 'email') {
                                    'email'             => 'bg-primary',
                                    'sms'               => 'bg-warning text-dark',
                                    'push_notification' => 'bg-info text-dark',
                                    'social_media'      => 'bg-success',
                                    default             => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $typeBadge }}">{{ $campaign->type->label() }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $campaign->status->badgeClass() }}">{{ $campaign->status->label() }}</span>
                        </td>
                        <td class="text-muted small">{{ $campaign->scheduled_at?->format('M d, Y H:i') ?? '—' }}</td>
                        <td class="text-muted small">{{ $campaign->segment?->name ?? 'All contacts' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('campaigns.edit', $campaign) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
