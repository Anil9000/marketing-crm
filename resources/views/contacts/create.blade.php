@extends('layouts.app')
@section('title', 'Add Contact')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('contacts.index') }}" class="text-decoration-none text-muted">Contacts</a></li>
    <li class="breadcrumb-item active">Add Contact</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>Add Contact</h1>
        <p class="text-muted small mb-0">Create a new contact in your database.</p>
    </div>
    <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="page-content">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form action="{{ route('contacts.store') }}" method="POST">
                @csrf
                <div class="crm-card">
                    <h6 class="fw-semibold mb-3">Contact Information</h6>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name"
                                   class="form-control @error('first_name') is-invalid @enderror"
                                   value="{{ old('first_name') }}" placeholder="John">
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name"
                                   class="form-control @error('last_name') is-invalid @enderror"
                                   value="{{ old('last_name') }}" placeholder="Smith">
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" placeholder="john@example.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone') }}" placeholder="+1 (555) 000-0000">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">Location</label>
                            <input type="text" name="location"
                                   class="form-control @error('location') is-invalid @enderror"
                                   value="{{ old('location') }}" placeholder="New York, US">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Age</label>
                            <input type="number" name="age"
                                   class="form-control @error('age') is-invalid @enderror"
                                   value="{{ old('age') }}" min="1" max="120" placeholder="—">
                            @error('age')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">Not specified</option>
                                <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other"  {{ old('gender') === 'other'  ? 'selected' : '' }}>Other</option>
                                <option value="prefer_not_to_say" {{ old('gender') === 'prefer_not_to_say' ? 'selected' : '' }}>Prefer not to say</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="active"        {{ old('status', 'active') === 'active'        ? 'selected' : '' }}>Active</option>
                                <option value="unsubscribed"  {{ old('status') === 'unsubscribed'            ? 'selected' : '' }}>Unsubscribed</option>
                                <option value="bounced"       {{ old('status') === 'bounced'                 ? 'selected' : '' }}>Bounced</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-accent">
                            <i class="bi bi-person-plus me-1"></i> Add Contact
                        </button>
                        <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
