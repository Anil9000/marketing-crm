@extends('layouts.app')
@section('title', 'Import Contacts')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('contacts.index') }}" class="text-decoration-none text-muted">Contacts</a></li>
    <li class="breadcrumb-item active">Import CSV</li>
@endsection

@push('styles')
<style>
.drop-zone {
    border: 2px dashed #2d3748;
    border-radius: 12px;
    padding: 3rem 2rem;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
    background: rgba(99,102,241,0.03);
}

.drop-zone:hover,
.drop-zone.dragover {
    border-color: #6366f1;
    background: rgba(99,102,241,0.08);
}

.drop-zone.has-file {
    border-color: #10b981;
    background: rgba(16,185,129,0.08);
}

.drop-zone .drop-zone-icon {
    font-size: 2.5rem;
    color: #4a5568;
    margin-bottom: 0.75rem;
    transition: color 0.2s;
}

.drop-zone:hover .drop-zone-icon,
.drop-zone.dragover .drop-zone-icon {
    color: #6366f1;
}

.drop-zone.has-file .drop-zone-icon {
    color: #10b981;
}
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1>Import Contacts</h1>
        <p class="text-muted small mb-0">Bulk-import contacts from a CSV file.</p>
    </div>
    <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Contacts
    </a>
</div>

<div class="page-content">
    <div class="row g-4">
        <!-- Upload Form -->
        <div class="col-lg-7">
            <div class="crm-card">
                <h6 class="fw-semibold mb-3">Upload CSV File</h6>

                <form action="{{ route('contacts.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf

                    <div class="mb-4">
                        <div class="drop-zone" id="dropZone" onclick="document.getElementById('csvFile').click()">
                            <div class="drop-zone-icon" id="dropIcon">
                                <i class="bi bi-cloud-upload"></i>
                            </div>
                            <div id="dropText">
                                <p class="mb-1 fw-medium text-light">Click to browse or drag &amp; drop your CSV</p>
                                <p class="small text-muted mb-0">Supports .csv and .txt — max file size: 10 MB</p>
                            </div>
                            <div id="fileInfo" class="d-none mt-3">
                                <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded"
                                     style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3);">
                                    <i class="bi bi-file-earmark-spreadsheet text-success"></i>
                                    <span id="fileName" class="small text-light fw-medium"></span>
                                    <span id="fileSize" class="small text-muted"></span>
                                </div>
                            </div>
                        </div>
                        <input type="file" id="csvFile" name="file" accept=".csv,.txt" class="d-none" required>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="form-label">Update existing contacts?</label>
                            <select name="update_existing" class="form-select">
                                <option value="0">Skip duplicates</option>
                                <option value="1">Update existing by email</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Default status</label>
                            <select name="default_status" class="form-select">
                                <option value="active">Active</option>
                                <option value="unsubscribed">Unsubscribed</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-accent">
                            <i class="bi bi-cloud-upload me-1"></i> Import Contacts
                        </button>
                        <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Format Guide -->
        <div class="col-lg-5">
            <div class="crm-card mb-3">
                <h6 class="fw-semibold mb-3">
                    <i class="bi bi-info-circle me-1 text-info"></i> CSV Format Guide
                </h6>
                <p class="text-muted small mb-3">
                    Your CSV file must have a header row. The <code>email</code> column is the only required field.
                </p>

                <div class="table-responsive">
                    <table class="table table-dark small mb-0">
                        <thead>
                            <tr>
                                <th>Column</th>
                                <th>Required</th>
                                <th>Example</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>email</code></td>
                                <td><span class="badge bg-danger">Required</span></td>
                                <td class="text-muted">john@example.com</td>
                            </tr>
                            <tr>
                                <td><code>first_name</code></td>
                                <td><span class="badge bg-secondary">Optional</span></td>
                                <td class="text-muted">John</td>
                            </tr>
                            <tr>
                                <td><code>last_name</code></td>
                                <td><span class="badge bg-secondary">Optional</span></td>
                                <td class="text-muted">Smith</td>
                            </tr>
                            <tr>
                                <td><code>phone</code></td>
                                <td><span class="badge bg-secondary">Optional</span></td>
                                <td class="text-muted">+1234567890</td>
                            </tr>
                            <tr>
                                <td><code>location</code></td>
                                <td><span class="badge bg-secondary">Optional</span></td>
                                <td class="text-muted">New York, US</td>
                            </tr>
                            <tr>
                                <td><code>age</code></td>
                                <td><span class="badge bg-secondary">Optional</span></td>
                                <td class="text-muted">32</td>
                            </tr>
                            <tr>
                                <td><code>gender</code></td>
                                <td><span class="badge bg-secondary">Optional</span></td>
                                <td class="text-muted">male / female</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="crm-card">
                <h6 class="fw-semibold mb-2">Sample CSV</h6>
                <div class="p-3 rounded" style="background: #0a0c12; border: 1px solid #1e2130;">
                    <code style="color: #a5b4fc; font-size: 0.75rem; line-height: 1.8;">
                        first_name,last_name,email,phone,location,age,gender<br>
                        John,Smith,john@example.com,+1234567890,"New York, US",32,male<br>
                        Jane,Doe,jane@example.com,+0987654321,"London, UK",28,female<br>
                        Alex,Johnson,alex@example.com,,,25,other
                    </code>
                </div>

                <div class="mt-3">
                    <a href="/sample-contacts.csv" download class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-download me-1"></i> Download Sample CSV
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const dropZone  = document.getElementById('dropZone');
const fileInput = document.getElementById('csvFile');
const fileInfo  = document.getElementById('fileInfo');
const fileName  = document.getElementById('fileName');
const fileSize  = document.getElementById('fileSize');
const dropIcon  = document.getElementById('dropIcon');

function formatBytes(bytes) {
    if (bytes < 1024)       return bytes + ' B';
    if (bytes < 1048576)    return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

function showFile(file) {
    fileName.textContent = file.name;
    fileSize.textContent = '(' + formatBytes(file.size) + ')';
    fileInfo.classList.remove('d-none');
    dropZone.classList.add('has-file');
    dropIcon.innerHTML = '<i class="bi bi-file-earmark-check-fill text-success"></i>';
    document.querySelector('#dropText p:first-child').textContent = 'File selected — click to change';
}

fileInput.addEventListener('change', function () {
    if (this.files && this.files[0]) showFile(this.files[0]);
});

dropZone.addEventListener('dragover', function (e) {
    e.preventDefault();
    this.classList.add('dragover');
});

dropZone.addEventListener('dragleave', function () {
    this.classList.remove('dragover');
});

dropZone.addEventListener('drop', function (e) {
    e.preventDefault();
    this.classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (file && (file.name.endsWith('.csv') || file.name.endsWith('.txt'))) {
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
        showFile(file);
    } else {
        alert('Please drop a valid .csv or .txt file.');
    }
});
</script>
@endpush
