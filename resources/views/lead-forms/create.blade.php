@extends('layouts.app')
@section('title', 'New Lead Form')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('lead-forms.index') }}" class="text-decoration-none text-muted">Lead Forms</a></li>
    <li class="breadcrumb-item active">New Form</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>Form Builder</h1>
        <p class="text-muted small mb-0">Build an embeddable lead capture form.</p>
    </div>
    <a href="{{ route('lead-forms.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="page-content">
    <form action="{{ route('lead-forms.store') }}" method="POST" x-data="formBuilder()">
        @csrf

        <div class="row g-3">
            <!-- Left: Builder -->
            <div class="col-lg-8">
                <div class="crm-card mb-3">
                    <h6 class="fw-semibold mb-3">Form Details</h6>
                    <div class="mb-3">
                        <label class="form-label">Form Name <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="e.g. Newsletter Signup" required
                               value="{{ old('name') }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="crm-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-semibold mb-0">Form Fields</h6>
                            <small class="text-muted">Drag to reorder (coming soon)</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="addField()">
                            <i class="bi bi-plus-lg me-1"></i> Add Field
                        </button>
                    </div>

                    <!-- Quick add buttons -->
                    <div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
                        <span class="small text-muted">Quick add:</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="addQuickField('email')">
                            <i class="bi bi-envelope me-1"></i> Email
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="addQuickField('text')">
                            <i class="bi bi-person me-1"></i> Name
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="addQuickField('phone')">
                            <i class="bi bi-phone me-1"></i> Phone
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="addQuickField('textarea')">
                            <i class="bi bi-text-paragraph me-1"></i> Message
                        </button>
                    </div>

                    <div x-show="fields.length === 0" class="text-center py-5 rounded"
                         style="background: rgba(99,102,241,0.04); border: 1px dashed #2d3748;">
                        <i class="bi bi-layout-text-window text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2 mb-0 small">Click "Add Field" or use quick-add buttons to build your form.</p>
                    </div>

                    <template x-for="(field, index) in fields" :key="index">
                        <div class="p-3 mb-2 rounded" style="background: #1a1f2e; border: 1px solid #2d3748;">
                            <div class="row g-2">
                                <div class="col-sm-4">
                                    <label class="form-label small mb-1">Label</label>
                                    <input type="text" :name="`fields[${index}][label]`"
                                           class="form-control form-control-sm"
                                           x-model="field.label" placeholder="Field Label">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label small mb-1">Field Key</label>
                                    <input type="text" :name="`fields[${index}][name]`"
                                           class="form-control form-control-sm"
                                           x-model="field.name" placeholder="field_key">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label small mb-1">Type</label>
                                    <select :name="`fields[${index}][type]`"
                                            class="form-select form-select-sm" x-model="field.type">
                                        <option value="text">Text</option>
                                        <option value="email">Email</option>
                                        <option value="phone">Phone</option>
                                        <option value="number">Number</option>
                                        <option value="textarea">Textarea</option>
                                        <option value="select">Select (dropdown)</option>
                                        <option value="checkbox">Checkbox</option>
                                        <option value="date">Date</option>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label small mb-1">Required</label>
                                    <div class="form-check form-switch mt-1">
                                        <input class="form-check-input" type="checkbox"
                                               :name="`fields[${index}][required]`"
                                               x-model="field.required" value="1">
                                    </div>
                                </div>

                                <!-- Select options (only show for select type) -->
                                <div class="col-12" x-show="field.type === 'select'" x-transition>
                                    <label class="form-label small mb-1">Options (comma-separated)</label>
                                    <input type="text" :name="`fields[${index}][options]`"
                                           class="form-control form-control-sm"
                                           x-model="field.options" placeholder="Option 1, Option 2, Option 3">
                                </div>

                                <div class="col-12 d-flex justify-content-between align-items-center">
                                    <span class="small text-muted" x-text="`Key: ${field.name}`"></span>
                                    <button type="button" class="btn btn-sm btn-outline-danger" @click="removeField(index)">
                                        <i class="bi bi-trash me-1"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Right: Preview + Settings -->
            <div class="col-lg-4">
                <!-- Live Preview -->
                <div class="crm-card mb-3">
                    <h6 class="fw-semibold mb-3">Live Preview</h6>
                    <div class="p-4 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0; min-height: 120px;">
                        <template x-for="field in fields" :key="field.name">
                            <div class="mb-3">
                                <label style="color: #374151; font-size: 0.825rem; font-weight: 500; display: block; margin-bottom: 4px;"
                                       x-text="field.label + (field.required ? ' *' : '')"></label>
                                <input x-show="!['textarea','checkbox','select'].includes(field.type)"
                                       :type="['phone'].includes(field.type) ? 'tel' : (field.type || 'text')"
                                       disabled
                                       style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;background:#fff;color:#374151;font-size:0.85rem;">
                                <textarea x-show="field.type === 'textarea'" rows="2" disabled
                                          style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;background:#fff;color:#374151;font-size:0.85rem;resize:none;"></textarea>
                                <select x-show="field.type === 'select'" disabled
                                        style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;background:#fff;color:#374151;font-size:0.85rem;">
                                    <option>Choose an option...</option>
                                </select>
                                <div x-show="field.type === 'checkbox'" style="display:flex;align-items:center;gap:8px;margin-top:4px;">
                                    <input type="checkbox" disabled style="width:16px;height:16px;accent-color:#6366f1;">
                                    <span style="color:#374151;font-size:0.85rem;" x-text="field.label"></span>
                                </div>
                            </div>
                        </template>
                        <div x-show="fields.length > 0">
                            <button type="button" disabled
                                    style="width:100%;background:#6366f1;border:none;color:white;padding:10px;border-radius:8px;font-weight:600;font-size:0.875rem;opacity:0.85;">
                                Submit
                            </button>
                        </div>
                        <div x-show="fields.length === 0" style="text-align:center;color:#9ca3af;font-size:0.8rem;padding:1rem;">
                            Add fields to see preview
                        </div>
                    </div>
                </div>

                <div class="crm-card mb-3">
                    <h6 class="fw-semibold mb-3">Form Settings</h6>
                    <div class="mb-3">
                        <label class="form-label">Success Message</label>
                        <input type="text" name="settings[success_message]" class="form-control"
                               value="{{ old('settings.success_message', 'Thank you for your submission!') }}"
                               placeholder="Thank you for your submission!">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Submit Button Label</label>
                        <input type="text" name="settings[button_label]" class="form-control"
                               value="{{ old('settings.button_label', 'Submit') }}" placeholder="Submit">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                               id="isActive" checked>
                        <label class="form-check-label text-muted" for="isActive">Form is active</label>
                    </div>
                </div>

                <div class="crm-card">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-accent">
                            <i class="bi bi-floppy me-1"></i> Create Form
                        </button>
                        <a href="{{ route('lead-forms.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function formBuilder() {
    return {
        fields: [],

        addField() {
            this.fields.push({
                label: 'New Field',
                name: 'field_' + Date.now(),
                type: 'text',
                required: false,
                options: '',
            });
        },

        addQuickField(type) {
            const presets = {
                email:    { label: 'Email Address', name: 'email',   type: 'email',    required: true },
                text:     { label: 'Full Name',      name: 'name',    type: 'text',     required: true },
                phone:    { label: 'Phone Number',   name: 'phone',   type: 'phone',    required: false },
                textarea: { label: 'Message',        name: 'message', type: 'textarea', required: false },
            };
            const preset = presets[type] || { label: ucfirst(type), name: type, type: type, required: false };
            this.fields.push({ ...preset, options: '' });
        },

        removeField(index) {
            this.fields.splice(index, 1);
        }
    };
}
</script>
@endpush
