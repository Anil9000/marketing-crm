@extends('layouts.app')
@section('title', 'Edit Campaign')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('campaigns.index') }}" class="text-decoration-none text-muted">Campaigns</a></li>
    <li class="breadcrumb-item"><a href="{{ route('campaigns.show', $campaign) }}" class="text-decoration-none text-muted">{{ Str::limit($campaign->name, 20) }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>Edit Campaign</h1>
        <p class="text-muted small mb-0">Update campaign settings, content, and scheduling.</p>
    </div>
    <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="page-content">
    <form action="{{ route('campaigns.update', $campaign) }}" method="POST"
          x-data="{ campaignType: '{{ old('type', $campaign->type->value) }}', abEnabled: {{ $campaign->ab_test_enabled ? 'true' : 'false' }} }">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <!-- Left Column -->
            <div class="col-lg-8">
                <div class="crm-card mb-3">
                    <h6 class="fw-semibold mb-3">Campaign Details</h6>

                    <div class="mb-3">
                        <label class="form-label">Campaign Name <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $campaign->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-2">
                        <div class="col-sm-6">
                            <label class="form-label">Campaign Type</label>
                            <select name="type" class="form-select" x-model="campaignType">
                                @foreach($types as $type)
                                    <option value="{{ $type->value }}"
                                            {{ old('type', $campaign->type->value) === $type->value ? 'selected' : '' }}>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->value }}"
                                            {{ old('status', $campaign->status->value) === $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Email subject (only for email type) -->
                    <div class="mt-3" x-show="campaignType === 'email'" x-transition>
                        <label class="form-label">Email Subject</label>
                        <input type="text" name="subject" class="form-control"
                               placeholder="Your email subject line"
                               value="{{ old('subject', $campaign->subject) }}">
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Content / Message</label>
                        <textarea name="content" class="form-control" rows="10"
                                  placeholder="Campaign content or HTML body...">{{ old('content', $campaign->content) }}</textarea>
                        <div class="small text-muted mt-1">HTML is supported for email campaigns.</div>
                    </div>
                </div>

                <!-- A/B Testing -->
                <div class="crm-card mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-semibold mb-0">A/B Testing</h6>
                            <small class="text-muted">Test two variants to identify the higher-performing version</small>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="ab_test_enabled"
                                   id="abToggle" value="1" x-model="abEnabled"
                                   {{ $campaign->ab_test_enabled ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div x-show="abEnabled" x-transition>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 rounded" style="background: rgba(99,102,241,0.08); border: 1px solid rgba(99,102,241,0.2);">
                                    <div class="fw-semibold small text-light mb-2">
                                        <span class="badge bg-primary me-1">A</span> Variant A
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Subject</label>
                                        <input type="text" name="variant_a_subject" class="form-control form-control-sm"
                                               placeholder="Subject line A"
                                               value="{{ old('variant_a_subject', $campaign->abTest->variant_a_subject ?? '') }}">
                                    </div>
                                    <div>
                                        <label class="form-label">Content</label>
                                        <textarea name="variant_a" class="form-control form-control-sm" rows="5"
                                                  placeholder="Email body for variant A">{{ old('variant_a', $campaign->abTest->variant_a ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded" style="background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2);">
                                    <div class="fw-semibold small text-light mb-2">
                                        <span class="badge bg-success me-1">B</span> Variant B
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Subject</label>
                                        <input type="text" name="variant_b_subject" class="form-control form-control-sm"
                                               placeholder="Subject line B"
                                               value="{{ old('variant_b_subject', $campaign->abTest->variant_b_subject ?? '') }}">
                                    </div>
                                    <div>
                                        <label class="form-label">Content</label>
                                        <textarea name="variant_b" class="form-control form-control-sm" rows="5"
                                                  placeholder="Email body for variant B">{{ old('variant_b', $campaign->abTest->variant_b ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <div class="crm-card mb-3">
                    <h6 class="fw-semibold mb-3">Scheduling</h6>

                    <div class="mb-3">
                        <label class="form-label">Frequency</label>
                        <select name="frequency" class="form-select">
                            @foreach(['one_time' => 'One-time send', 'daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $val => $label)
                                <option value="{{ $val }}"
                                        {{ old('frequency', $campaign->frequency) === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Schedule Date &amp; Time</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control"
                               value="{{ old('scheduled_at', $campaign->scheduled_at?->format('Y-m-d\TH:i')) }}">
                        <div class="small text-muted mt-1">Leave blank to keep as draft.</div>
                    </div>
                </div>

                <div class="crm-card mb-3">
                    <h6 class="fw-semibold mb-3">Audience</h6>
                    <label class="form-label">Target Segment</label>
                    <select name="segment_id" class="form-select">
                        <option value="">No segment (all contacts)</option>
                        @foreach($segments as $segment)
                            <option value="{{ $segment->id }}"
                                    {{ old('segment_id', $campaign->segment_id) == $segment->id ? 'selected' : '' }}>
                                {{ $segment->name }}
                                @if($segment->contact_count)
                                    ({{ number_format($segment->contact_count) }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="crm-card mb-3">
                    <h6 class="fw-semibold mb-3">Budget</h6>
                    <label class="form-label">Allocated Budget (USD)</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background: #1a1f2e; border-color: #1e2130; color: #94a3b8;">$</span>
                        <input type="number" name="budget" class="form-control"
                               value="{{ old('budget', $campaign->budget) }}"
                               step="0.01" min="0" placeholder="0.00">
                    </div>
                </div>

                <div class="crm-card">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-accent">
                            <i class="bi bi-floppy me-1"></i> Save Changes
                        </button>
                        <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
