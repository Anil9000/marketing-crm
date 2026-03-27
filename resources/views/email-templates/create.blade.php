@extends('layouts.app')
@section('title', 'New Email Template')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('email-templates.index') }}" class="text-decoration-none text-muted">Email Templates</a></li>
    <li class="breadcrumb-item active">New Template</li>
@endsection

@push('styles')
<style>
.html-editor { font-family: 'Courier New', monospace; font-size: 0.8rem; line-height: 1.5; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1>Create Email Template</h1>
        <p class="text-muted small mb-0">Build a reusable HTML email template.</p>
    </div>
    <a href="{{ route('email-templates.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="page-content">
    <form action="{{ route('email-templates.store') }}" method="POST" x-data="templateEditor()">
        @csrf

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
                                   placeholder="e.g. Welcome Email"
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-5">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select @error('category') is-invalid @enderror">
                                <option value="">Select category...</option>
                                <option value="welcome"        {{ old('category') === 'welcome'        ? 'selected' : '' }}>Welcome</option>
                                <option value="newsletter"     {{ old('category') === 'newsletter'     ? 'selected' : '' }}>Newsletter</option>
                                <option value="promotional"    {{ old('category') === 'promotional'    ? 'selected' : '' }}>Promotional</option>
                                <option value="re-engagement"  {{ old('category') === 're-engagement'  ? 'selected' : '' }}>Re-engagement</option>
                                <option value="event"          {{ old('category') === 'event'          ? 'selected' : '' }}>Event</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject"
                                   class="form-control @error('subject') is-invalid @enderror"
                                   placeholder="Subject line for emails using this template"
                                   value="{{ old('subject') }}" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- HTML Editor / Preview tabs -->
                <div class="crm-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-semibold mb-0">HTML Content</h6>
                            <small class="text-muted">Full HTML is supported — tables, inline styles, media queries</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm"
                                    :class="activeTab === 'editor' ? 'btn-accent' : 'btn-outline-secondary'"
                                    @click="activeTab = 'editor'">
                                <i class="bi bi-code-slash me-1"></i> Editor
                            </button>
                            <button type="button" class="btn btn-sm"
                                    :class="activeTab === 'preview' ? 'btn-accent' : 'btn-outline-secondary'"
                                    @click="updatePreview(); activeTab = 'preview'">
                                <i class="bi bi-eye me-1"></i> Preview
                            </button>
                        </div>
                    </div>

                    <!-- Quick insert block buttons -->
                    <div class="d-flex gap-2 mb-3 flex-wrap" x-show="activeTab === 'editor'">
                        <span class="small text-muted align-self-center me-1">Insert block:</span>
                        @foreach(['header', 'button', 'text', 'divider', 'footer'] as $block)
                            <button type="button" class="btn btn-sm btn-outline-secondary" @click="insertBlock('{{ $block }}')">
                                <i class="bi bi-plus-lg me-1"></i>{{ ucfirst($block) }}
                            </button>
                        @endforeach
                    </div>

                    <div x-show="activeTab === 'editor'">
                        <textarea name="html_content" id="htmlContent"
                                  class="form-control html-editor @error('html_content') is-invalid @enderror"
                                  rows="22" x-model="htmlContent"
                                  placeholder="Paste or type your HTML email here..."></textarea>
                        @error('html_content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div x-show="activeTab === 'preview'">
                        <iframe id="previewFrame"
                                style="width: 100%; height: 540px; border: 1px solid #1e2130; border-radius: 8px; background: white;"></iframe>
                    </div>
                </div>
            </div>

            <!-- Right: Settings & Actions -->
            <div class="col-lg-4">
                <div class="crm-card mb-3">
                    <h6 class="fw-semibold mb-3">Visibility</h6>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_public" value="1" id="isPublic">
                        <label class="form-check-label text-muted" for="isPublic">
                            Make template public (visible to all workspace members)
                        </label>
                    </div>
                </div>

                <div class="crm-card mb-3">
                    <h6 class="fw-semibold mb-3">Quick Block Reference</h6>
                    <ul class="small text-muted list-unstyled mb-0">
                        <li class="mb-1"><i class="bi bi-layout-text-window me-1 text-indigo-400" style="color: #818cf8;"></i> <strong>Header</strong> — branded logo bar</li>
                        <li class="mb-1"><i class="bi bi-cursor-fill me-1" style="color: #34d399;"></i> <strong>Button</strong> — CTA action button</li>
                        <li class="mb-1"><i class="bi bi-text-paragraph me-1" style="color: #fbbf24;"></i> <strong>Text</strong> — body paragraph</li>
                        <li class="mb-1"><i class="bi bi-dash-lg me-1" style="color: #94a3b8;"></i> <strong>Divider</strong> — horizontal rule</li>
                        <li><i class="bi bi-envelope me-1" style="color: #f87171;"></i> <strong>Footer</strong> — unsubscribe link</li>
                    </ul>
                </div>

                <div class="crm-card">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-accent">
                            <i class="bi bi-floppy me-1"></i> Save Template
                        </button>
                        <a href="{{ route('email-templates.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function templateEditor() {
    const blocks = {
        header: `\n<div style="background:#6366f1;padding:24px 20px;text-align:center;">
  <h1 style="color:white;margin:0;font-family:Arial,sans-serif;font-size:24px;">Your Brand</h1>
</div>`,
        button: `\n<div style="text-align:center;padding:24px 20px;">
  <a href="#" style="display:inline-block;background:#6366f1;color:white;padding:14px 28px;text-decoration:none;border-radius:6px;font-family:Arial,sans-serif;font-weight:600;font-size:15px;">Click Here</a>
</div>`,
        text: `\n<div style="padding:20px;font-family:Arial,sans-serif;color:#1e293b;line-height:1.7;font-size:15px;">
  <p style="margin:0 0 12px;">Your email content goes here. Write your message clearly and concisely to engage your audience.</p>
</div>`,
        divider: `\n<div style="padding:0 20px;"><hr style="border:none;border-top:1px solid #e2e8f0;margin:0;"></div>`,
        footer: `\n<div style="background:#f8fafc;padding:20px;text-align:center;font-family:Arial,sans-serif;color:#64748b;font-size:12px;line-height:1.5;">
  <p style="margin:0 0 6px;">You received this email because you subscribed to our newsletter.</p>
  <p style="margin:0;"><a href="{{ config('app.url') }}/unsubscribe/@{{token}}" style="color:#6366f1;">Unsubscribe</a> &bull; <a href="{{ config('app.url') }}/privacy" style="color:#6366f1;">Privacy Policy</a></p>
</div>`,
    };

    return {
        activeTab: 'editor',
        htmlContent: `<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Email</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;">
    <tr>
      <td align="center" style="padding:40px 16px;">
        <table width="600" cellpadding="0" cellspacing="0" style="background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 16px rgba(0,0,0,0.06);">
          <tr><td><!-- Insert blocks here --></td></tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>`,

        insertBlock(type) {
            this.htmlContent += blocks[type] || '';
        },

        updatePreview() {
            const frame = document.getElementById('previewFrame');
            if (frame) frame.srcdoc = this.htmlContent;
        }
    };
}
</script>
@endpush
