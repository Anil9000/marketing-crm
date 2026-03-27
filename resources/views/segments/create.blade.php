@extends('layouts.app')
@section('title', 'New Segment')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('segments.index') }}" class="text-decoration-none text-muted">Segments</a></li>
    <li class="breadcrumb-item active">New Segment</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>Build Segment</h1>
        <p class="text-muted small mb-0">Define filter rules to group your contacts dynamically.</p>
    </div>
    <a href="{{ route('segments.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="page-content">
    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('segments.store') }}" method="POST" x-data="segmentBuilder()">
                @csrf

                <div class="crm-card mb-3">
                    <h6 class="fw-semibold mb-3">Segment Details</h6>

                    <div class="mb-3">
                        <label class="form-label">Segment Name <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="e.g. Active US Customers"
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"
                                  placeholder="Optional description of this segment...">{{ old('description') }}</textarea>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_dynamic" value="1"
                               id="isDynamic" checked x-model="isDynamic">
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
                        <p class="text-muted small mt-2 mb-0">No filters added. Click "Add Filter" to target specific contacts.</p>
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

                    <div x-show="filters.length > 0" class="mt-3 d-flex align-items-center gap-3">
                        <button type="button" class="btn btn-sm btn-outline-info" @click="previewCount()">
                            <i class="bi bi-eye me-1"></i> Preview Match Count
                        </button>
                        <span x-show="previewResult !== null"
                              x-text="`Estimated matching contacts: ${previewResult}`"
                              class="text-muted small"></span>
                        <span x-show="previewLoading" class="text-muted small">
                            <span class="spinner-border spinner-border-sm me-1"></span> Calculating...
                        </span>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-accent">
                        <i class="bi bi-floppy me-1"></i> Create Segment
                    </button>
                    <a href="{{ route('segments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="crm-card mb-3">
                <h6 class="fw-semibold mb-3">Filter Reference</h6>
                <dl class="small mb-0">
                    <dt class="text-muted">email</dt>
                    <dd class="mb-2">Contact's email address</dd>
                    <dt class="text-muted">first_name / last_name</dt>
                    <dd class="mb-2">Contact's name fields</dd>
                    <dt class="text-muted">location</dt>
                    <dd class="mb-2">City or country (e.g. "New York")</dd>
                    <dt class="text-muted">age</dt>
                    <dd class="mb-2">Numeric — use greater_than / less_than</dd>
                    <dt class="text-muted">gender</dt>
                    <dd class="mb-2">male, female, other</dd>
                    <dt class="text-muted">status</dt>
                    <dd class="mb-2">active, unsubscribed, bounced</dd>
                    <dt class="text-muted">last_activity_at</dt>
                    <dd class="mb-0">Date string (e.g. "2025-01-01")</dd>
                </dl>
            </div>

            <div class="crm-card">
                <h6 class="fw-semibold mb-3">Tips</h6>
                <ul class="small text-muted mb-0 ps-3">
                    <li class="mb-1">Dynamic segments update automatically as contacts change.</li>
                    <li class="mb-1">Static segments capture contacts at creation time only.</li>
                    <li class="mb-1">All filters are combined with AND logic.</li>
                    <li>Use "Preview Match Count" to estimate reach before saving.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function segmentBuilder() {
    return {
        filters: [],
        isDynamic: true,
        previewResult: null,
        previewLoading: false,

        addFilter() {
            this.filters.push({ field: '', operator: 'equals', value: '' });
        },

        removeFilter(index) {
            this.filters.splice(index, 1);
            if (this.filters.length === 0) this.previewResult = null;
        },

        previewCount() {
            const validFilters = this.filters.filter(f => f.field && f.value);
            if (!validFilters.length) return;

            this.previewLoading = true;
            this.previewResult = null;

            fetch('/api/v1/segments/preview-count', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ filters: validFilters }),
            })
            .then(r => r.json())
            .then(data => {
                this.previewResult = data.count ?? 0;
                this.previewLoading = false;
            })
            .catch(() => {
                this.previewLoading = false;
            });
        }
    };
}
</script>
@endpush
