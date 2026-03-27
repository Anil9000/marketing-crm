@extends('layouts.app')
@section('title', $contact->full_name ?: $contact->email)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('contacts.index') }}" class="text-decoration-none text-muted">Contacts</a></li>
    <li class="breadcrumb-item active">{{ $contact->full_name ?: $contact->email }}</li>
@endsection

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
             style="width:56px; height:56px; background: linear-gradient(135deg, #6366f1, #8b5cf6); font-size: 1.3rem; flex-shrink: 0;">
            {{ strtoupper(substr($contact->first_name ?? $contact->email, 0, 1)) }}
        </div>
        <div>
            <h1 class="mb-0">{{ $contact->full_name ?: 'Unnamed Contact' }}</h1>
            <div class="d-flex align-items-center gap-2 mt-1">
                <span class="text-muted small">{{ $contact->email }}</span>
                <span class="badge {{ match($contact->status) {
                    'active'       => 'bg-success',
                    'unsubscribed' => 'bg-warning text-dark',
                    'bounced'      => 'bg-danger',
                    default        => 'bg-secondary',
                } }}">
                    {{ ucfirst($contact->status) }}
                </span>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-outline-secondary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <form action="{{ route('contacts.destroy', $contact) }}" method="POST"
              onsubmit="return confirm('Permanently delete this contact? This cannot be undone.')">
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
        <!-- Left: Contact Details -->
        <div class="col-lg-4">
            <div class="crm-card mb-3">
                <h6 class="fw-semibold mb-3">Contact Details</h6>
                <dl class="row small mb-0">
                    <dt class="col-5 text-muted">Email</dt>
                    <dd class="col-7">
                        <a href="mailto:{{ $contact->email }}" class="text-decoration-none"
                           style="color: #6366f1;">{{ $contact->email }}</a>
                    </dd>

                    <dt class="col-5 text-muted">Phone</dt>
                    <dd class="col-7">
                        @if($contact->phone)
                            <a href="tel:{{ $contact->phone }}" class="text-decoration-none text-light">{{ $contact->phone }}</a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </dd>

                    <dt class="col-5 text-muted">Location</dt>
                    <dd class="col-7">{{ $contact->location ?? '—' }}</dd>

                    <dt class="col-5 text-muted">Age</dt>
                    <dd class="col-7">{{ $contact->age ?? '—' }}</dd>

                    <dt class="col-5 text-muted">Gender</dt>
                    <dd class="col-7">{{ $contact->gender ? ucwords(str_replace('_', ' ', $contact->gender)) : '—' }}</dd>

                    <dt class="col-5 text-muted">Status</dt>
                    <dd class="col-7">
                        <span class="badge {{ match($contact->status) {
                            'active'       => 'bg-success',
                            'unsubscribed' => 'bg-warning text-dark',
                            'bounced'      => 'bg-danger',
                            default        => 'bg-secondary',
                        } }}">
                            {{ ucfirst($contact->status) }}
                        </span>
                    </dd>

                    <dt class="col-5 text-muted">Last Activity</dt>
                    <dd class="col-7">
                        {{ $contact->last_activity_at ? $contact->last_activity_at->diffForHumans() : '—' }}
                    </dd>

                    <dt class="col-5 text-muted">Added</dt>
                    <dd class="col-7">{{ $contact->created_at->format('M d, Y') }}</dd>

                    <dt class="col-5 text-muted">Updated</dt>
                    <dd class="col-7 mb-0">{{ $contact->updated_at->diffForHumans() }}</dd>
                </dl>
            </div>

            @if($contact->segments && $contact->segments->isNotEmpty())
            <div class="crm-card">
                <h6 class="fw-semibold mb-3">Segment Memberships</h6>
                <div class="d-flex flex-wrap gap-1">
                    @foreach($contact->segments as $segment)
                        <a href="{{ route('segments.show', $segment) }}"
                           class="badge text-decoration-none"
                           style="background: rgba(99,102,241,0.2); color: #a5b4fc; border: 1px solid rgba(99,102,241,0.3); padding: 0.4rem 0.6rem;">
                            <i class="bi bi-funnel me-1"></i>{{ $segment->name }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Right: Email Activity -->
        <div class="col-lg-8">
            <div class="crm-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0">Email Activity</h6>
                    @if($contact->emailEvents && $contact->emailEvents->isNotEmpty())
                        <span class="badge bg-secondary">{{ $contact->emailEvents->count() }} events</span>
                    @endif
                </div>

                @if(!$contact->emailEvents || $contact->emailEvents->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-envelope text-muted" style="font-size: 2.5rem;"></i>
                        <p class="text-muted mt-3 mb-1">No email activity recorded yet.</p>
                        <p class="text-muted small mb-0">Activity appears here after campaigns are sent to this contact.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-dark table-hover small mb-0">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Campaign</th>
                                    <th>IP Address</th>
                                    <th>User Agent</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contact->emailEvents as $event)
                                <tr>
                                    <td>
                                        <span class="badge {{ match($event->event_type) {
                                            'open'        => 'bg-info text-dark',
                                            'click'       => 'bg-success',
                                            'bounce'      => 'bg-warning text-dark',
                                            'unsubscribe' => 'bg-danger',
                                            'send'        => 'bg-primary',
                                            default       => 'bg-secondary',
                                        } }}">
                                            <i class="bi bi-{{ match($event->event_type) {
                                                'open'        => 'envelope-open',
                                                'click'       => 'cursor',
                                                'bounce'      => 'envelope-x',
                                                'unsubscribe' => 'person-x',
                                                'send'        => 'send',
                                                default       => 'circle',
                                            } }} me-1"></i>
                                            {{ ucfirst($event->event_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($event->campaign)
                                            <a href="{{ route('campaigns.show', $event->campaign) }}"
                                               class="text-decoration-none" style="color: #6366f1;">
                                                {{ Str::limit($event->campaign->name, 30) }}
                                            </a>
                                        @else
                                            <span class="text-muted">Unknown</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ $event->ip_address ?? '—' }}</td>
                                    <td class="text-muted" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $event->user_agent ? Str::limit($event->user_agent, 25) : '—' }}
                                    </td>
                                    <td class="text-muted">{{ $event->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
