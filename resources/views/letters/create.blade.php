@extends('layouts.app')

@section('title', 'New Letter Entry - Correspondence')

@section('content')
<div class="container-fluid py-3" style="max-width: 1000px;">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="fa-solid fa-file-pen text-primary me-2"></i>New Correspondence Letter
            </h3>
            <p class="text-muted small mb-0">Compose and register incoming or outgoing official letters with attachments and routing.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('letters.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Inbox
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-start border-4 border-danger" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fa-solid fa-triangle-exclamation me-2"></i>Please fix the errors below:</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="fw-bold mb-0"><i class="fa-solid fa-envelope-circle-check me-2"></i>Letter Details & Routing Information</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('letters.store') }}" method="POST" enctype="multipart/form-data" id="letterForm">
                @csrf

                {{-- Section 1: Letter Type & Numbers --}}
                <div class="row g-3 mb-4 pb-3 border-bottom">
                    <div class="col-12">
                        <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-sliders me-2"></i>1. Classification & Numbering</h6>
                    </div>

                    {{-- Type --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Letter Type <span class="text-danger">*</span></label>
                        <select name="type" id="letterTypeSelect" class="form-select fw-bold" required onchange="handleTypeChange()">
                            <option value="incoming" {{ old('type', $defaultType) === 'incoming' ? 'selected' : '' }}>📥 Incoming Letter</option>
                            <option value="outgoing" {{ old('type', $defaultType) === 'outgoing' ? 'selected' : '' }}>📤 Outgoing Letter</option>
                        </select>
                    </div>

                    {{-- Letter Number --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Letter Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="letter_number" id="letterNumberInput" 
                                   class="form-control fw-bold font-monospace text-primary" 
                                   value="{{ old('letter_number', $suggestedNumber) }}" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="refreshSuggestedNumber()" title="Regenerate Sequence Number">
                                <i class="fa-solid fa-arrows-rotate"></i>
                            </button>
                        </div>
                        <small class="text-muted">Auto-suggested sequence. You can manually edit.</small>
                    </div>

                    {{-- Date --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                    </div>

                    {{-- Priority --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Priority <span class="text-danger">*</span></label>
                        <select name="priority" class="form-select" required>
                            <option value="normal" {{ old('priority') === 'normal' ? 'selected' : '' }}>🟢 Normal</option>
                            <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>🔴 Urgent</option>
                        </select>
                    </div>

                    {{-- Dynamic Sender / Recipient --}}
                    <div class="col-md-4" id="senderGroup">
                        <label class="form-label fw-bold" id="senderLabel">Sender / Origin <span class="text-danger">*</span></label>
                        <input type="text" name="sender" id="senderInput" class="form-control" 
                               placeholder="e.g., City Municipality, Ministry of Works" value="{{ old('sender') }}">
                    </div>

                    <div class="col-md-4" id="senderDeptGroup">
                        <label class="form-label fw-bold">Sender Department <small class="text-muted">(Optional)</small></label>
                        <input type="text" name="sender_department" class="form-control" 
                               placeholder="e.g., Legal Department, Engineering Bureau" value="{{ old('sender_department') }}">
                    </div>

                    <div class="col-md-4 d-none" id="recipientOrgGroup">
                        <label class="form-label fw-bold">Recipient Organization <span class="text-danger">*</span></label>
                        <input type="text" name="recipient_organization" id="recipientOrgInput" class="form-control" 
                               placeholder="e.g., Commercial Bank of Ethiopia, Subcontractor ABC" value="{{ old('recipient_organization') }}">
                    </div>
                </div>

                {{-- Section 2: Subject & Content --}}
                <div class="row g-3 mb-4 pb-3 border-bottom">
                    <div class="col-12">
                        <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-align-left me-2"></i>2. Subject & Detailed Specification</h6>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Subject / Title <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control fw-bold" 
                               placeholder="e.g., Request for Site Inspection & Approval for Phase 2 Works" 
                               value="{{ old('subject') }}" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Specification / Detailed Description <span class="text-danger">*</span></label>
                        <textarea name="specification" class="form-control" rows="5" 
                                  placeholder="Provide complete details regarding the contents, purpose, background, and required actions for this letter..." required>{{ old('specification') }}</textarea>
                    </div>
                </div>

                {{-- Section 3: Attachments Upload --}}
                <div class="row g-3 mb-4 pb-3 border-bottom">
                    <div class="col-12">
                        <h6 class="fw-bold text-primary mb-2"><i class="fa-solid fa-paperclip me-2"></i>3. File Attachments (Multi-Upload Support)</h6>
                        <p class="text-muted small mb-3">Upload signed documents, scans, photos, or official letterheads in <strong>PDF, PNG, JPG, or JPEG</strong> format (Max 10MB per file).</p>
                    </div>

                    <div class="col-12">
                        <div class="border border-2 border-dashed rounded-3 p-4 text-center bg-light" id="dropZone">
                            <i class="fa-solid fa-cloud-arrow-up fa-2x text-primary mb-2"></i>
                            <div class="fw-bold">Select or Drag & Drop Documents</div>
                            <small class="text-muted d-block mb-3">Supported formats: PDF, PNG, JPG, JPEG (Max 10MB each)</small>
                            <input type="file" name="attachments[]" id="fileAttachmentInput" class="form-control w-auto mx-auto" 
                                   multiple accept=".pdf,.png,.jpg,.jpeg">
                            <div id="fileListPreview" class="mt-3 text-start d-flex flex-wrap gap-2"></div>
                        </div>
                    </div>
                </div>

                {{-- Section 4: Target Recipient & Dispatch --}}
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-paper-plane me-2"></i>4. Assign & Send To</h6>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Send Target Mode <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3 mt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="send_target_type" id="targetTypeUser" value="user" 
                                       {{ old('send_target_type', 'user') === 'user' ? 'checked' : '' }} onchange="handleTargetTypeToggle()">
                                <label class="form-check-label fw-semibold" for="targetTypeUser">
                                    <i class="fa-solid fa-user me-1 text-primary"></i> Specific Person
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="send_target_type" id="targetTypeRole" value="role" 
                                       {{ old('send_target_type') === 'role' ? 'checked' : '' }} onchange="handleTargetTypeToggle()">
                                <label class="form-check-label fw-semibold" for="targetTypeRole">
                                    <i class="fa-solid fa-users me-1 text-info"></i> Role / Department
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Specific User Select --}}
                    <div class="col-md-8" id="userTargetBox">
                        <label class="form-label fw-bold">Select Recipient Person <span class="text-danger">*</span></label>
                        <select name="to_user_id" id="toUserSelect" class="form-select">
                            <option value="">-- Choose User / Employee --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ old('to_user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Role Target Select --}}
                    <div class="col-md-8 d-none" id="roleTargetBox">
                        <label class="form-label fw-bold">Select Recipient Role / Department <span class="text-danger">*</span></label>
                        <select name="to_role_name" id="toRoleSelect" class="form-select">
                            <option value="">-- Choose Role / Department --</option>
                            @foreach($roles as $r)
                                <option value="{{ $r }}" {{ old('to_role_name') == $r ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace(['_', '-'], ' ', $r)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Initial Dispatch Notes / Instructions <small class="text-muted">(Optional)</small></label>
                        <textarea name="initial_notes" class="form-control" rows="2" 
                                  placeholder="e.g., Please review the attached contract proposal and provide your feedback by end of week.">{{ old('initial_notes') }}</textarea>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('letters.index') }}" class="btn btn-secondary px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                        <i class="fa-solid fa-paper-plane me-1"></i> Submit & Dispatch Letter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function handleTypeChange() {
    const type = document.getElementById('letterTypeSelect').value;
    const senderGroup = document.getElementById('senderGroup');
    const senderDeptGroup = document.getElementById('senderDeptGroup');
    const recipientOrgGroup = document.getElementById('recipientOrgGroup');
    const senderInput = document.getElementById('senderInput');
    const recipientOrgInput = document.getElementById('recipientOrgInput');

    if (type === 'outgoing') {
        senderGroup.classList.add('d-none');
        senderDeptGroup.classList.add('d-none');
        recipientOrgGroup.classList.remove('d-none');
        if (senderInput) senderInput.removeAttribute('required');
        if (recipientOrgInput) recipientOrgInput.setAttribute('required', 'required');
    } else {
        senderGroup.classList.remove('d-none');
        senderDeptGroup.classList.remove('d-none');
        recipientOrgGroup.classList.add('d-none');
        if (senderInput) senderInput.setAttribute('required', 'required');
        if (recipientOrgInput) recipientOrgInput.removeAttribute('required');
    }

    refreshSuggestedNumber();
}

function handleTargetTypeToggle() {
    const isRole = document.getElementById('targetTypeRole').checked;
    const userBox = document.getElementById('userTargetBox');
    const roleBox = document.getElementById('roleTargetBox');
    const userSelect = document.getElementById('toUserSelect');
    const roleSelect = document.getElementById('toRoleSelect');

    if (isRole) {
        userBox.classList.add('d-none');
        roleBox.classList.remove('d-none');
        userSelect.removeAttribute('required');
        roleSelect.setAttribute('required', 'required');
    } else {
        userBox.classList.remove('d-none');
        roleBox.classList.add('d-none');
        userSelect.setAttribute('required', 'required');
        roleSelect.removeAttribute('required');
    }
}

function refreshSuggestedNumber() {
    const type = document.getElementById('letterTypeSelect').value;
    fetch(`{{ route('letters.suggested-number') }}?type=${type}`)
        .then(res => res.json())
        .then(data => {
            if (data.suggested_number) {
                document.getElementById('letterNumberInput').value = data.suggested_number;
            }
        })
        .catch(err => console.error(err));
}

// Attachment preview
document.addEventListener('DOMContentLoaded', function() {
    handleTypeChange();
    handleTargetTypeToggle();

    const fileInput = document.getElementById('fileAttachmentInput');
    const previewContainer = document.getElementById('fileListPreview');

    if (fileInput && previewContainer) {
        fileInput.addEventListener('change', function() {
            previewContainer.innerHTML = '';
            Array.from(this.files).forEach((file, index) => {
                const badge = document.createElement('div');
                badge.className = 'badge bg-white text-dark border p-2 shadow-sm d-flex align-items-center gap-2';
                
                const icon = file.name.endsWith('.pdf') ? 'fa-file-pdf text-danger' : 'fa-file-image text-primary';
                const sizeMb = (file.size / (1024 * 1024)).toFixed(2);

                badge.innerHTML = `
                    <i class="fa-solid ${icon}"></i>
                    <span>${file.name} (${sizeMb} MB)</span>
                `;
                previewContainer.appendChild(badge);
            });
        });
    }
});
</script>
@endsection
