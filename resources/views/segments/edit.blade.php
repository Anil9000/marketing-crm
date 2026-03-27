@extends('layouts.app')
@section('title', 'Edit Segment')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('segments.index') }}" class="text-decoration-none text-muted">Segments</a></li>
    <li class="breadcrumb-item"><a href="{{ route('segments.show', $segment) }}" class="text-decoration-none text-muted">{{ $segment->name }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>Edit Segment</h1>
        <p class="text-muted small mb-0">Update segment name, description, and filter rules.</p>
    </div>
    <a href="{{ route('segments.show', $segment) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="page-content">
    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('segments.update', $segment) }}" method="POST"
                  x-data="segmentEditor({{ json_encode($segment->filters ?? []) }})">
                @csrf
                @method('PUT')

                <div class="crm-card mb-3">
                    <h6 class="fw-semibold mb-3">Segment Details</h6>

                    <div class="mb-3">
                        <label class="form-label">Segment Name <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $segment->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"
                                  placeholder="Optional description...">{{ old('description', $segment->description) }}</textarea>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_dynamic" value="1"
                               id="isDynamic" {{ $segment->is_dynamic ? 'checked' : '' }}>
                        <label class="form-check-label text-muted" for="isDynamic">
                            Dynamic segment — auto-updates when filters match new contacts
                        </label>
                    </div>
                </div>

                <div class="crm-card mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-semibold mb-0">Filter Rules</h6>
                            <small class="text-muted">All rules must match (AND logic)</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="addFilter()">
                            <i class="bi bi-plus-lg me-1"></i> Add Filter
                        </button>
                    </div>

                    <div x-show="filters.length === 0" class="text-center py-4 rounded" style="background: rgba(99,102,241,0.05); border: 1px dashed #2d3748;">
                        <i class="bi bi-funnel text-muted" style="font-size: 1.5rem;"></i>
                        <p class="text-muted small mt-2 mb-0">No filters. Click "Add Filter" to add rules.</p>
                    </div>

                    <template x-for="(filter, index) in filters" :key="index">
                        <div class="row g-2 mb-2 align-items-end p-2 rounded" style="background: rgba(99,102,241,0.05); border: 1px solid #1e2130;">
                            <div class="col-sm-4">
                                <label class="form-label small mb-1">Field</label>
                                <select class="form-select form-select-sm" x-model="filter.field" :name="`filters[${index}][field]`">
                                    <option value="">Select field...</option>
                                    <option value="email">Email</option>
                                    <option value="first_name">First Name</option>
                                    <option value="last_name">Last Name</option>
                                    <option value="location">Location</option>
                                    <option value="age">Age</option>
                                    <option value="gender">Gender</option>
                                    <option value="status">Status</option>
                                    <option value="last_activity_at">Last Activity</option>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label small mb-1">Operator</label>
                                <select class="form-select form-select-sm" x-model="filter.operator" :name="`filters[${index}][operator]`">
                                    <option value="equals">Equals</option>
                                    <option value="not_equals">Not equals</option>
                                    <option value="contains">Contains</option>
                                    <option value="greater_than">Greater than</option>
                                    <option value="less_than">Less than</option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label small mb-1">Value</label>
                                <input type="text" class="form-control form-control-sm"
                                       x-model="filter.value"
                                       :name="`filters[${index}][value]`"
                                       placeholder="Enter value...">
                            </div>
                            <div class="col-sm-1 text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        @click="removeFilter(index)" title="Remove filter">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-accent">
                        <i class="bi bi-floppy me-1"></i> Save Changes
                    </button>
                    <a href="{{ route('segments.show', $segment) }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="crm-card mb-3">
                <h6 class="fw-semibold mb-3">Current Stats</h6>
                <div class="d-flex align-items-center gap-3 p-3 rounded" style="background: rgba(99,102,241,0.1);">
                    <i class="bi bi-people" style="color: #6366f1; font-size: 1.5rem;"></i>
                    <div>
                        <div class="stat-value" style="font-size: 1.5rem; color: #f1f5f9;">{{ number_format($segment->contact_count) }}</div>
                        <div class="small text-muted">Matching contacts</div>
                    </div>
                </div>
                <div class="small text-muted mt-2">
                    Last updated {{ $segment->updated_at->diffForHumans() }}
                </div>
            </div>

            <div class="crm-card">
                <h6 class="fw-semibold mb-3">Filter Reference</h6>
                <dl class="small mb-0">
                    <dt class="text-muted">email</dt>
                    <dd class="mb-1">Contact's email address</dd>
                    <dt class="text-muted">first_name / last_name</dt>
                    <dd class="mb-1">Contact name fields</dd>
                    <dt class="text-muted">location</dt>
                    <dd class="mb-1">City or country name</dd>
                    <dt class="text-muted">age</dt>
                    <dd class="mb-1">Numeric — use greater_than/less_than</dd>
                    <dt class="text-muted">gender</dt>
                    <dd class="mb-1">male, female, other</dd>
                    <dt class="text-muted">status</dt>
                    <dd class="mb-0">active, unsubscribed, bounced</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function segmentEditor(existingFilters) {
    return {
        filters: Array.isArray(existingFilters) && existingFilters.length ? existingFilters : [],
        addFilter() {
            this.filters.push({ field: '', operator: 'equals', value: '' });
        },
        removeFilter(i) {
            this.filters.splice(i, 1);
        },
    };
}
</script>
@endpush
