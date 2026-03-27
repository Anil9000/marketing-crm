@extends('layouts.app')
@section('title', 'Contacts')

@section('breadcrumb')
    <li class="breadcrumb-item active">Contacts</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>Contacts</h1>
        <p class="text-muted small mb-0">Manage your contact database.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('contacts.import-form') }}" class="btn btn-outline-secondary">
            <i class="bi bi-upload me-1"></i> Import CSV
        </a>
        <a href="{{ route('contacts.create') }}" class="btn btn-accent">
            <i class="bi bi-plus-lg me-1"></i> Add Contact
        </a>
    </div>
</div>

<div class="page-content">
    <!-- Filters -->
    <div class="crm-card mb-3">
        <form action="{{ route('contacts.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-sm-6 col-md-4">
                <div class="input-group">
                    <span class="input-group-text" style="background: #1a1f2e; border-color: #1e2130;">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control" placeholder="Search by name, email, phone..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-sm-6 col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="unsubscribed" {{ request('status') === 'unsubscribed' ? 'selected' : '' }}>Unsubscribed</option>
                    <option value="bounced" {{ request('status') === 'bounced' ? 'selected' : '' }}>Bounced</option>
                </select>
            </div>
            <div class="col-sm-6 col-md-2">
                <select name="gender" class="form-select">
                    <option value="">All Genders</option>
                    <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ request('gender') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="col-sm-6 col-md-2">
                <input type="text" name="location" class="form-control" placeholder="Location..." value="{{ request('location') }}">
            </div>
            <div class="col-sm-6 col-md-2">
                <button type="submit" class="btn btn-accent w-100">Filter</button>
            </div>
        </form>
    </div>

    <div class="crm-table">
        <table class="table table-dark table-hover mb-0">
            <thead>
                <tr>
                    <th>Contact</th>
                    <th>Phone</th>
                    <th>Location</th>
                    <th>Gender</th>
                    <th>Status</th>
                    <th>Last Activity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contacts as $contact)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                 style="width:36px; height:36px; background: #6366f1; font-size: 0.8rem; flex-shrink: 0;">
                                {{ strtoupper(substr($contact->first_name ?? $contact->email, 0, 1)) }}
                            </div>
                            <div>
                                <a href="{{ route('contacts.show', $contact) }}" class="text-decoration-none text-light fw-medium d-block">
                                    {{ $contact->full_name ?: 'No name' }}
                                </a>
                                <div class="small text-muted">{{ $contact->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-muted small">{{ $contact->phone ?? '—' }}</td>
                    <td class="text-muted small">{{ $contact->location ?? '—' }}</td>
                    <td class="text-muted small">{{ ucfirst($contact->gender ?? '—') }}</td>
                    <td>
                        <span class="badge {{ $contact->status === 'active' ? 'bg-success' : ($contact->status === 'unsubscribed' ? 'bg-warning text-dark' : 'bg-danger') }}">
                            {{ ucfirst($contact->status) }}
                        </span>
                    </td>
                    <td class="text-muted small">
                        {{ $contact->last_activity_at ? $contact->last_activity_at->diffForHumans() : '—' }}
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('contacts.show', $contact) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Delete contact?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <i class="bi bi-people text-muted" style="font-size: 2.5rem;"></i>
                        <p class="text-muted mt-3 mb-0">No contacts found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $contacts->withQueryString()->links() }}
    </div>
</div>
@endsection
