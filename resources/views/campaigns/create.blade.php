@extends('layouts.app')
@section('title', 'New Campaign')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('campaigns.index') }}" class="text-decoration-none text-muted">Campaigns</a></li>
    <li class="breadcrumb-item active">New Campaign</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>Create Campaign</h1>
        <p class="text-muted small mb-0">Set up a new marketing campaign.</p>
    </div>
    <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="page-content">
    <form action="{{ route('campaigns.store') }}" method="POST" x-data="campaignForm()">
        @csrf

        <div class="row g-3">
            <!-- Left Column -->
            <div class="col-lg-8">
                <div class="crm-card mb-3">
                    <h6 class="fw-semibold mb-3">Campaign Details</h6>

                    <div class="mb-3">
                        <label class="form-label">Campaign Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Summer Sale 2025" value="{{ old('name') }}" required>
                    </div>

                    <div class="row g-2">
                        <div class="col-sm-6">
                            <label class="form-label">Campaign Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" x-model="campaignType" required>
                                <option value="">Select type...</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->value }}" {{ old('type') === $type->value ? 'selected' : '' }}>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Target Segment</label>
                            <select name="segment_id" class="form-select">
                                <option value="">No segment (all contacts)</option>
                                @foreach($segments as $segment)
                                    <option value="{{ $segment->id }}" {{ old('segment_id') == $segment->id ? 'selected' : '' }}>
                                        {{ $segment->name }} ({{ number_format($segment->contact_count) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Email-specific fields -->
                    <div x-show="campaignType === 'email'" class="mt-3">
                        <div class="mb-3">
                            <label class="form-label">Email Subject</label>
                            <input type="text" name="subject" class="form-control" placeholder="Your email subject line" value="{{ old('subject') }}">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Content / Message</label>
                        <textarea name="content" class="form-control" rows="8" placeholder="Write your campaign content here...">{{ old('content') }}</textarea>
                        <div class="small text-muted mt-1">HTML is supported for email campaigns.</div>
                    </div>
                </div>

                <!-- A/B Testing -->
                <div class="crm-card mb-3" x-data="{ enabled: {{ old('ab_test_enabled') ? 'true' : 'false' }} }">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-semibold mb-0">A/B Testing</h6>
                            <small class="text-muted">Test two variants to find the winner</small>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="ab_test_enabled" x-model="enabled" value="1">
                        </div>
                    </div>

                    <div x-show="enabled" x-transition>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Variant A Subject</label>
                                <input type="text" name="variant_a_subject" class="form-control" placeholder="Subject A">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Variant B Subject</label>
                                <input type="text" name="variant_b_subject" class="form-control" placeholder="Subject B">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Variant A Content</label>
                                <textarea name="variant_a" class="form-control" rows="5"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Variant B Content</label>
                                <textarea name="variant_b" class="form-control" rows="5"></textarea>
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
                            <option value="one_time">One-time send</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Schedule Date & Time</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at') }}">
                        <div class="small text-muted mt-1">Leave blank to save as draft.</div>
                    </div>
                </div>

                <div class="crm-card mb-3">
                    <h6 class="fw-semibold mb-3">Budget</h6>
                    <div>
                        <label class="form-label">Allocated Budget (USD)</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background: #1a1f2e; border-color: #1e2130; color: #94a3b8;">$</span>
                            <input type="number" name="budget" class="form-control" placeholder="0.00" step="0.01" min="0" value="{{ old('budget') }}">
                        </div>
                    </div>
                </div>

                <div class="crm-card">
                    <h6 class="fw-semibold mb-3">Publish</h6>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-accent">
                            <i class="bi bi-floppy me-1"></i> Save Campaign
                        </button>
                        <button type="submit" name="status" value="scheduled" class="btn btn-outline-info">
                            <i class="bi bi-clock me-1"></i> Schedule Campaign
                        </button>
                        <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function campaignForm() {
    return {
        campaignType: '{{ old('type', 'email') }}'
    };
}
</script>
@endpush
