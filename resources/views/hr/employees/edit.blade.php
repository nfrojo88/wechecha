@extends('layouts.app')

@section('title', 'Edit Employee — '.$employee->full_name)

@section('content')

<div class="d-flex align-items-center mb-4">
    <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="h3 mb-0">Edit: {{ $employee->full_name }}</h1>
        <small class="text-muted">{{ $employee->employee_code }}</small>
    </div>
</div>

{{-- Validation errors --}}
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <h6 class="alert-heading"><i class="fa-solid fa-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form method="POST" action="{{ route('employees.update', $employee) }}">
    @csrf @method('PUT')

    {{-- ═══════════════════════════════════════════
         SECTION 1 — Personal Information
    ════════════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0"><i class="fa-solid fa-user-circle text-primary me-2"></i>Personal Information</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Employee Code <span class="text-danger">*</span></label>
                    <input type="text" name="employee_code"
                           class="form-control @error('employee_code') is-invalid @enderror"
                           value="{{ old('employee_code', $employee->employee_code) }}" required>
                    @error('employee_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="full_name"
                           class="form-control @error('full_name') is-invalid @enderror"
                           value="{{ old('full_name', $employee->full_name) }}" required>
                    @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fa-solid fa-fingerprint text-primary me-1"></i>ZKTeco Device User ID
                    </label>
                    <input type="text" name="device_user_id"
                           class="form-control @error('device_user_id') is-invalid @enderror"
                           value="{{ old('device_user_id', $employee->device_user_id) }}"
                           placeholder="e.g. 1, 2, 17, 50">
                    <small class="text-muted">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        Numeric ID assigned to this employee in the fingerprint machine's user list.
                        <a href="{{ route('attendance.deviceLogs') }}" target="_blank">View device logs</a>
                    </small>
                    @error('device_user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Primary Phone</label>
                    <input type="text" name="phone" class="form-control"
                           value="{{ old('phone', $employee->phone) }}" placeholder="+251 911 234 567">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email', $employee->email) }}" placeholder="employee@company.com">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Department</label>
                    <div class="input-group">
                        <select name="department" class="form-select @error('department') is-invalid @enderror">
                            <option value="">-- Select Department --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->name }}"
                                    {{ old('department', $employee->department) == $dept->name ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        <a href="{{ route('departments.index') }}" target="_blank" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-cog me-1"></i>Manage
                        </a>
                    </div>
                    @error('department')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Role / Job Title</label>
                    <input type="text" name="role_title" class="form-control"
                           value="{{ old('role_title', $employee->role_title) }}" placeholder="e.g. Site Engineer">
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         SECTION 2 — Employment Details
    ════════════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0"><i class="fa-solid fa-briefcase text-success me-2"></i>Employment Details</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Employment Type <span class="text-danger">*</span></label>
                    <select name="employment_type" class="form-select @error('employment_type') is-invalid @enderror" required>
                        @php $empType = old('employment_type', $employee->employment_type); @endphp
                        <option value="permanent" {{ $empType == 'permanent' ? 'selected' : '' }}>Permanent</option>
                        <option value="contract"  {{ $empType == 'contract'  ? 'selected' : '' }}>Contract</option>
                        <option value="daily"     {{ $empType == 'daily'     ? 'selected' : '' }}>Daily Worker</option>
                    </select>
                    @error('employment_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Contract Start Date <span class="text-danger">*</span></label>
                    <input type="date" name="date_of_joining"
                           class="form-control @error('date_of_joining') is-invalid @enderror"
                           value="{{ old('date_of_joining', $employee->date_of_joining ? $employee->date_of_joining->format('Y-m-d') : '') }}" required>
                    @error('date_of_joining')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        @php $status = old('status', $employee->status); @endphp
                        <option value="active"     {{ $status == 'active'     ? 'selected' : '' }}>Active</option>
                        <option value="suspended"  {{ $status == 'suspended'  ? 'selected' : '' }}>Suspended</option>
                        <option value="terminated" {{ $status == 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Assigned Project</label>
                    <select name="project_id" class="form-select">
                        <option value="">— HQ / Unassigned —</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}"
                                {{ old('project_id', $employee->project_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Site Assignment</label>
                    <select name="site_assignment" class="form-select">
                        @php $site = old('site_assignment', $employee->site_assignment ?? ''); @endphp
                        <option value="">-- No Specific Site --</option>
                        <option value="HQ"     {{ $site == 'HQ'     ? 'selected' : '' }}>Headquarters</option>
                        <option value="Site_A" {{ $site == 'Site_A' ? 'selected' : '' }}>Site A</option>
                        <option value="Site_B" {{ $site == 'Site_B' ? 'selected' : '' }}>Site B</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         SECTION 3 — Salary Information
    ════════════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0"><i class="fa-solid fa-money-bill text-warning me-2"></i>Salary Information</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Monthly Base Salary (ETB) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="basic_salary"
                           class="form-control @error('basic_salary') is-invalid @enderror"
                           value="{{ old('basic_salary', $employee->basic_salary) }}" required>
                    @error('basic_salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Contract Type</label>
                    <select name="contract_type" class="form-select">
                        @php $ct = old('contract_type', $employee->contract_type ?? ''); @endphp
                        <option value="Full-Time"  {{ $ct == 'Full-Time'  ? 'selected' : '' }}>Full-Time</option>
                        <option value="Part-Time"  {{ $ct == 'Part-Time'  ? 'selected' : '' }}>Part-Time</option>
                        <option value="Temporary"  {{ $ct == 'Temporary'  ? 'selected' : '' }}>Temporary</option>
                    </select>
                </div>

                {{-- Allowances --}}
                <div class="col-12">
                    <div class="alert alert-light border mb-0">
                        <h6 class="mb-3"><i class="fa-solid fa-coins text-success me-2"></i>Allowances</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Transport Allowance (ETB)</label>
                                <input type="number" step="0.01" min="0" name="transport_allowance"
                                       class="form-control"
                                       value="{{ old('transport_allowance', $employee->transport_allowance ?? 0) }}">
                                <small class="text-muted"><i class="fa-solid fa-info-circle me-1"></i>&lt; 2200 is not taxable</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">House Allowance (ETB)</label>
                                <input type="number" step="0.01" min="0" name="house_allowance"
                                       class="form-control"
                                       value="{{ old('house_allowance', $employee->house_allowance ?? 0) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Position Allowance (ETB)</label>
                                <input type="number" step="0.01" min="0" name="position_allowance"
                                       class="form-control"
                                       value="{{ old('position_allowance', $employee->position_allowance ?? 0) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bank Information --}}
                <div class="col-12">
                    <div class="alert alert-light border mb-0">
                        <h6 class="mb-3"><i class="fa-solid fa-building-columns text-info me-2"></i>Bank Information</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Bank Name</label>
                                <input type="text" name="bank_name" class="form-control"
                                       value="{{ old('bank_name', $employee->bank_name ?? '') }}"
                                       placeholder="Commercial Bank of Ethiopia">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Account Number</label>
                                <input type="text" name="account_number" class="form-control"
                                       value="{{ old('account_number', $employee->account_number ?? '') }}"
                                       placeholder="1000123456789">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         SECTION 4 — Notes
    ════════════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0"><i class="fa-solid fa-note-sticky text-secondary me-2"></i>Notes</h5>
        </div>
        <div class="card-body">
            <textarea name="notes" rows="3" class="form-control"
                      placeholder="Any additional notes about this employee...">{{ old('notes', $employee->notes) }}</textarea>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('employees.show', $employee) }}" class="btn btn-outline-secondary px-4">
            <i class="fa-solid fa-xmark me-1"></i>Cancel
        </a>
        <button type="submit" class="btn btn-primary px-4">
            <i class="fa-solid fa-floppy-disk me-1"></i>Save Changes
        </button>
    </div>

</form>
@endsection
