@extends('layouts.app')
@section('title', 'Edit Template')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('email-templates.index') }}" class="text-decoration-none text-muted">Email Templates</a></li>
    <li class="breadcrumb-item"><a href="{{ route('email-templates.show', $template) }}" class="text-decoration-none text-muted">{{ Str::limit($template->name, 25) }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@push('styles')
<style>
.html-editor { font-family: 'Courier New', monospace; font-size: 0.8rem; line-height: 1.5; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1>Edit Template</h1>
        <p class="text-muted small mb-0">Update template content and settings.</p>
    </div>
    <a href="{{ route('email-templates.show', $template) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="page-content">
    <form action="{{ route('email-templates.update', $template) }}" method="POST"
          x-data="{ activeTab: 'editor', content: @js($template->html_content) }">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <!-- Left: Editor -->
            <div class="col-lg-8">
                <div class="crm-card mb-3">
                    <h6 class="fw-semibold mb-3">Template Details</h6>
                    <div class="row g-3">
                        <div class="col-sm-7">
                            <label class="form-label">Template Name <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $template->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-5">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select @error('category') is-invalid @enderror">
                                <option value="">Select category...</option>
                                @foreach(['welcome', 'newsletter', 'promotional', 're-engagement', 'event'] as $cat)
                                    <option value="{{ $cat }}" {{ old('category', $template->category) === $cat ? 'selected' : '' }}>
                                        {{ ucfirst($cat) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject"
                                   class="form-control @error('subject') is-invalid @enderror"
                                   value="{{ old('subject', $template->subject) }}" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Editor / Preview tabs -->
                <div class="crm-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-semibold mb-0">HTML Content</h6>
                            <small class="text-muted">Full HTML email content with inline styles</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm"
                                    :class="activeTab === 'editor' ? 'btn-accent' : 'btn-outline-secondary'"
                                    @click="activeTab = 'editor'">
                                <i class="bi bi-code-slash me-1"></i> Editor
                            </button>
                            <button type="button" class="btn btn-sm"
                                    :class="activeTab === 'preview' ? 'btn-accent' : 'btn-outline-secondary'"
                                    @click="activeTab = 'preview'; $nextTick(() => { const f = document.getElementById('editPreview'); if(f) f.srcdoc = content; })">
                                <i class="bi bi-eye me-1"></i> Preview
                            </button>
                        </div>
                    </div>

                    <div x-show="activeTab === 'editor'">
                        <textarea name="html_content" class="form-control html-editor @error('html_content') is-invalid @enderror"
                                  rows="22" x-model="content"></textarea>
                        @error('html_content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div x-show="activeTab === 'preview'">
                        <iframe id="editPreview"
                                style="width: 100%; height: 540px; border: 1px solid #1e2130; border-radius: 8px; background: white;"></iframe>
                    </div>
                </div>
            </div>

            <!-- Right: Settings & Actions -->
            <div class="col-lg-4">
                <div class="crm-card mb-3">
                    <h6 class="fw-semibold mb-3">Template Info</h6>
                    <dl class="row small mb-0">
                        <dt class="col-5 text-muted">Created</dt>
                        <dd class="col-7">{{ $template->created_at->format('M d, Y') }}</dd>
                        <dt class="col-5 text-muted">Last updated</dt>
                        <dd class="col-7">{{ $template->updated_at->diffForHumans() }}</dd>
                    </dl>
                </div>

                <div class="crm-card mb-3">
                    <h6 class="fw-semibold mb-3">Visibility</h6>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_public" value="1"
                               id="isPublic" {{ $template->is_public ? 'checked' : '' }}>
                        <label class="form-check-label text-muted" for="isPublic">
                            Public template (visible to all workspace members)
                        </label>
                    </div>
                </div>

                <div class="crm-card mb-3">
                    <h6 class="fw-semibold mb-3">Danger Zone</h6>
                    <form action="{{ route('email-templates.destroy', $template) }}" method="POST"
                          onsubmit="return confirm('Permanently delete this template? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash me-1"></i> Delete Template
                        </button>
                    </form>
                </div>

                <div class="crm-card">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-accent">
                            <i class="bi bi-floppy me-1"></i> Save Changes
                        </button>
                        <a href="{{ route('email-templates.show', $template) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
