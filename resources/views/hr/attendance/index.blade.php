@extends('layouts.app')
@section('title', 'Attendance Management')
@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-calendar-check me-2 text-primary"></i>Attendance Management</h1>
            <p class="text-muted mt-1">Track and manage employee attendance records</p>
        </div>
        <div class="btn-group" role="group">
            <a href="{{ route('attendance.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Record Attendance
            </a>
            <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#bulkAttendanceModal">
                <i class="fas fa-users me-1"></i>Bulk Upload
            </button>
            <a href="{{ route('attendance.deviceLogs') }}" class="btn btn-outline-info">
                <i class="fa-solid fa-fingerprint me-1"></i>Device Logs
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('attendance.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Employee</label>
                    <input type="text" name="employee" class="form-control" placeholder="Search employee..." value="{{ request('employee') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="present" @selected(request('status')=='present')>Present</option>
                        <option value="absent" @selected(request('status')=='absent')>Absent</option>
                        <option value="half_day" @selected(request('status')=='half_day')>Half Day</option>
                        <option value="leave" @selected(request('status')=='leave')>Leave</option>
                        <option value="holiday" @selected(request('status')=='holiday')>Holiday</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Present Today</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ \App\Models\Attendance::whereDate('attendance_date', now())->where('status', 'present')->count() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Absent Today</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ \App\Models\Attendance::whereDate('attendance_date', now())->where('status', 'absent')->count() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">On Leave Today</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ \App\Models\Attendance::whereDate('attendance_date', now())->where('status', 'leave')->count() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Half Day Today</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ \App\Models\Attendance::whereDate('attendance_date', now())->where('status', 'half_day')->count() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Records Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h6 class="mb-0 font-weight-bold">
                <i class="fas fa-table me-2"></i>Attendance Records
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th class="text-center">Hours</th>
                            <th>Status</th>
                            <th class="text-center">OT Hrs</th>
                            <th class="text-end">OT Pay</th>
                            <th>Source</th>
                            <th class="text-center">Approved</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $a)
                        <tr>
                            <td>
                                <strong>{{ $a->employee->full_name ?? $a->employee->first_name . ' ' . $a->employee->last_name }}</strong>
                                <br><small class="text-muted">{{ $a->employee->employee_code ?? 'N/A' }}</small>
                            </td>
                            <td>{{ $a->attendance_date->format('M d, Y') }}</td>
                            <td>
                                @if($a->check_in)
                                    <span class="badge bg-info">{{ $a->check_in }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($a->check_out)
                                    <span class="badge bg-info">{{ $a->check_out }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($a->hours_worked)
                                    <strong>{{ $a->hours_worked }}h</strong>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @php 
                                    $statusColors = [
                                        'present' => 'success',
                                        'absent' => 'danger',
                                        'half_day' => 'warning',
                                        'leave' => 'info',
                                        'holiday' => 'secondary',
                                        'weekend' => 'light'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$a->status] ?? 'secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $a->status)) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if(($a->overtime_hours ?? 0) > 0)
                                    <span class="badge bg-warning text-dark">{{ $a->overtime_hours }}h</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if(($a->overtime_pay ?? 0) > 0)
                                    @php
                                        $otLabels = ['holiday'=>'Holiday×2.5','rest_day'=>'Rest×2.0','night_12_4'=>'Night×1.5','night_4_12'=>'Night×1.75'];
                                    @endphp
                                    <span class="fw-bold text-warning" title="{{ $otLabels[$a->overtime_type] ?? '' }}">
                                        {{ number_format($a->overtime_pay, 2) }}
                                    </span>
                                    <br><small class="text-muted">{{ $otLabels[$a->overtime_type] ?? '' }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ ucfirst($a->source) }}</small>
                            </td>
                            <td class="text-center">
                                @if($a->is_approved)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Approved
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-hourglass-half me-1"></i>Pending
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                <p class="mb-0">No attendance records found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($attendances->hasPages())
        <div class="card-footer bg-light">
            {{ $attendances->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Bulk Attendance Modal -->
<div class="modal fade" id="bulkAttendanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Upload Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('attendance.bulkStore') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Attendance Date <span class="text-danger">*</span></label>
                        <input type="date" name="attendance_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Excel/CSV File <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" accept=".csv,.xlsx,.xls" required>
                        <small class="text-muted">
                            Format: Employee ID | Status (present/absent/leave/half_day)<br>
                            <a href="#" onclick="downloadTemplate()">Download template</a>
                        </small>
                    </div>

                    <div class="alert alert-info">
                        <strong>Instructions:</strong>
                        <ul class="mb-0">
                            <li>First column: Employee ID or Code</li>
                            <li>Second column: Status (present, absent, leave, half_day)</li>
                            <li>Skip header row if present</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i>Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function downloadTemplate() {
    const csv = "Employee ID,Status\nEMP001,present\nEMP002,absent\nEMP003,leave";
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'attendance-template.csv';
    a.click();
}
</script>
@endpush

@endsection
