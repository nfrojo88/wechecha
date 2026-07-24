@extends('layouts.app')

@section('title', 'Edit Employee')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <h1 class="h3 mb-0">Edit: {{ $employee->full_name }}</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('employees.update', $employee) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12"><h6 class="text-muted text-uppercase small fw-bold border-bottom pb-2 mb-0">Personal Information</h6></div>
                <div class="col-md-3">
                    <label class="form-label">Employee Code <span class="text-danger">*</span></label>
                    <input type="text" name="employee_code" class="form-control @error('employee_code') is-invalid @enderror"
                           value="{{ old('employee_code', $employee->employee_code) }}" required>
                    @error('employee_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-5">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                           value="{{ old('full_name', $employee->full_name) }}" required>
                    @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fa-solid fa-fingerprint text-primary me-1"></i>ZKTeco Device User ID
                    </label>
                    <input type="text" name="device_user_id" class="form-control @error('device_user_id') is-invalid @enderror"
                           value="{{ old('device_user_id', $employee->device_user_id) }}" placeholder="e.g. 1, 2, 17, 50">
                    <small class="text-muted">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        Numeric ID assigned to this employee in the fingerprint machine's user list.
                        <a href="{{ route('attendance.deviceLogs') }}" target="_blank">View device logs</a>
                    </small>
                    @error('device_user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $employee->email) }}">
                </div>

                <div class="col-12 mt-2"><h6 class="text-muted text-uppercase small fw-bold border-bottom pb-2 mb-0">Employment Details</h6></div>
                <div class="col-md-4">
                    <label class="form-label">Role / Job Title</label>
                    <input type="text" name="role_title" class="form-control" value="{{ old('role_title', $employee->role_title) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Department</label>
                    <input type="text" name="department" class="form-control" value="{{ old('department', $employee->department) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Employment Type <span class="text-danger">*</span></label>
                    <select name="employment_type" class="form-select" required>
                        <option value="permanent" @selected(old('employment_type',$employee->employment_type)=='permanent')>Permanent</option>
                        <option value="contract"  @selected(old('employment_type',$employee->employment_type)=='contract')>Contract</option>
                        <option value="daily"     @selected(old('employment_type',$employee->employment_type)=='daily')>Daily Worker</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Assigned Project</label>
                    <select name="project_id" class="form-select">
                        <option value="">— HQ / Unassigned —</option>
                        @foreach($projects as $p)
                        <option value="{{ $p->id }}" @selected(old('project_id',$employee->project_id)==$p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date of Joining <span class="text-danger">*</span></label>
                    <input type="date" name="date_of_joining" class="form-control"
                           value="{{ old('date_of_joining', $employee->date_of_joining->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="active"     @selected(old('status',$employee->status)=='active')>Active</option>
                        <option value="suspended"  @selected(old('status',$employee->status)=='suspended')>Suspended</option>
                        <option value="terminated" @selected(old('status',$employee->status)=='terminated')>Terminated</option>
                    </select>
                </div>

                <div class="col-12 mt-2"><h6 class="text-muted text-uppercase small fw-bold border-bottom pb-2 mb-0">Salary</h6></div>
                <div class="col-md-4">
                    <label class="form-label">Basic Salary (ETB) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="basic_salary" class="form-control"
                           value="{{ old('basic_salary', $employee->basic_salary) }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="2" class="form-control">{{ old('notes', $employee->notes) }}</textarea>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('employees.show', $employee) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
