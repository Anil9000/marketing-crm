@extends('layouts.app')
@section('title', 'Edit Contact')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('contacts.index') }}" class="text-decoration-none text-muted">Contacts</a></li>
    <li class="breadcrumb-item"><a href="{{ route('contacts.show', $contact) }}" class="text-decoration-none text-muted">{{ $contact->full_name ?: $contact->email }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>Edit Contact</h1>
        <p class="text-muted small mb-0">Update contact information.</p>
    </div>
    <a href="{{ route('contacts.show', $contact) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="page-content">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form action="{{ route('contacts.update', $contact) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="crm-card">
                    <h6 class="fw-semibold mb-3">Contact Information</h6>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name"
                                   class="form-control @error('first_name') is-invalid @enderror"
                                   value="{{ old('first_name', $contact->first_name) }}" placeholder="John">
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name"
                                   class="form-control @error('last_name') is-invalid @enderror"
                                   value="{{ old('last_name', $contact->last_name) }}" placeholder="Smith">
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $contact->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $contact->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">Location</label>
                            <input type="text" name="location"
                                   class="form-control @error('location') is-invalid @enderror"
                                   value="{{ old('location', $contact->location) }}" placeholder="City, Country">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Age</label>
                            <input type="number" name="age"
                                   class="form-control @error('age') is-invalid @enderror"
                                   value="{{ old('age', $contact->age) }}" min="1" max="120">
                            @error('age')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">Not specified</option>
                                @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other', 'prefer_not_to_say' => 'Prefer not to say'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('gender', $contact->gender) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="active"       {{ old('status', $contact->status) === 'active'       ? 'selected' : '' }}>Active</option>
                                <option value="unsubscribed" {{ old('status', $contact->status) === 'unsubscribed' ? 'selected' : '' }}>Unsubscribed</option>
                                <option value="bounced"      {{ old('status', $contact->status) === 'bounced'      ? 'selected' : '' }}>Bounced</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-accent">
                            <i class="bi bi-floppy me-1"></i> Save Changes
                        </button>
                        <a href="{{ route('contacts.show', $contact) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
