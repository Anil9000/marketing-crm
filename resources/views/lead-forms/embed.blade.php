<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $form->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }

        body {
            background: #f8fafc;
            min-height: 100vh;
            padding: 40px 16px;
        }

        .form-wrapper {
            max-width: 520px;
            margin: 0 auto;
        }

        .form-card {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 4px 32px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }

        .form-brand {
            text-align: center;
            margin-bottom: 1.75rem;
        }

        .form-brand .brand-logo {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.25rem;
            color: white;
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .form-subtitle {
            color: #64748b;
            font-size: 0.875rem;
        }

        .form-label {
            color: #374151;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .form-control {
            border-color: #d1d5db;
            color: #1e293b;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            background: #fff;
        }

        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
            outline: none;
        }

        .form-control::placeholder { color: #9ca3af; }

        .btn-submit {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            color: white;
            padding: 0.85rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.925rem;
            width: 100%;
            cursor: pointer;
            transition: opacity 0.15s, transform 0.15s;
        }

        .btn-submit:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
        }

        .success-card {
            text-align: center;
            padding: 2rem 0;
        }

        .success-icon {
            width: 64px;
            height: 64px;
            background: rgba(16, 185, 129, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            font-size: 1.75rem;
        }

        .required-note {
            color: #9ca3af;
            font-size: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #9ca3af;
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
    <div class="form-wrapper">
        <div class="form-card">
            <div class="form-brand">
                <div class="brand-logo">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="white">
                        <path d="M10 2L3 7v11h4v-6h6v6h4V7l-7-5z"/>
                    </svg>
                </div>
                <div class="form-title">{{ $form->name }}</div>
                <div class="form-subtitle">Fill out the form below to get started.</div>
            </div>

            @if(session('success'))
                <div class="success-card">
                    <div class="success-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <h5 style="color: #1e293b; font-weight: 700; margin-bottom: 0.5rem;">Thank You!</h5>
                    <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 1.5rem;">
                        {{ $form->settings['success_message'] ?? "We've received your submission and will be in touch soon." }}
                    </p>
                    <button onclick="location.reload()" style="background: transparent; border: 1px solid #d1d5db; color: #374151; padding: 0.5rem 1.25rem; border-radius: 8px; cursor: pointer; font-size: 0.875rem;">
                        Submit Another Response
                    </button>
                </div>
            @else
                @if($errors->any())
                    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 0.875rem 1rem; margin-bottom: 1.25rem;">
                        <ul style="color: #dc2626; font-size: 0.875rem; margin: 0; padding-left: 1.25rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(collect($form->fields ?? [])->contains(fn($f) => !empty($f['required'])))
                    <p class="required-note">Fields marked with <span style="color: #ef4444;">*</span> are required.</p>
                @endif

                <form action="{{ route('lead-forms.public-submit', $form->slug) }}" method="POST" id="leadForm">
                    @csrf

                    @foreach($form->fields ?? [] as $field)
                        <div class="mb-4">
                            <label class="form-label d-block mb-1">
                                {{ $field['label'] }}
                                @if(!empty($field['required']))
                                    <span style="color: #ef4444;">*</span>
                                @endif
                            </label>

                            @if($field['type'] === 'textarea')
                                <textarea
                                    name="{{ $field['name'] }}"
                                    class="form-control"
                                    rows="4"
                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                    {{ !empty($field['required']) ? 'required' : '' }}>{{ old($field['name']) }}</textarea>

                            @elseif($field['type'] === 'select' && !empty($field['options']))
                                <select name="{{ $field['name'] }}" class="form-control"
                                        {{ !empty($field['required']) ? 'required' : '' }}>
                                    <option value="">Choose an option...</option>
                                    @foreach(explode(',', $field['options']) as $opt)
                                        <option value="{{ trim($opt) }}" {{ old($field['name']) === trim($opt) ? 'selected' : '' }}>
                                            {{ trim($opt) }}
                                        </option>
                                    @endforeach
                                </select>

                            @elseif($field['type'] === 'checkbox')
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.25rem;">
                                    <input type="checkbox"
                                           name="{{ $field['name'] }}"
                                           id="field_{{ $field['name'] }}"
                                           value="1"
                                           style="width: 18px; height: 18px; accent-color: #6366f1; cursor: pointer;"
                                           {{ !empty($field['required']) ? 'required' : '' }}>
                                    <label for="field_{{ $field['name'] }}" style="color: #374151; font-size: 0.875rem; cursor: pointer; margin: 0;">
                                        {{ $field['checkbox_label'] ?? 'I agree' }}
                                    </label>
                                </div>

                            @else
                                <input
                                    type="{{ in_array($field['type'], ['text','email','phone','number','date']) ? $field['type'] : 'text' }}"
                                    name="{{ $field['name'] }}"
                                    class="form-control"
                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                    value="{{ old($field['name']) }}"
                                    {{ !empty($field['required']) ? 'required' : '' }}>
                            @endif
                        </div>
                    @endforeach

                    <button type="submit" class="btn-submit" id="submitBtn">
                        {{ $form->settings['button_label'] ?? 'Submit' }}
                    </button>
                </form>

                <div class="form-footer">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 4px;">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    Your information is secure and will never be shared.
                </div>
            @endif
        </div>
    </div>

    <script>
    document.getElementById('leadForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.textContent = 'Submitting...';
    });
    </script>
</body>
</html>
