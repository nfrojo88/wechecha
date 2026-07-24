@extends('layouts.app')
@section('title', 'HR Manager Dashboard')
@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-briefcase me-2 text-primary"></i>HR Manager Dashboard
            </h1>
            <p class="text-muted mt-1">{{ now()->format('l, F j Y') }}</p>
        </div>
        <div>
            <a href="{{ route('employees.index') }}" class="btn btn-primary btn-sm me-2">
                <i class="fas fa-users me-1"></i>Manage Employees
            </a>
            <a href="{{ route('attendance.index') }}" class="btn btn-info btn-sm">
                <i class="fas fa-clipboard-check me-1"></i>Attendance
            </a>
        </div>
    </div>

    <!-- KPI Cards Row 1 -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                <i class="fas fa-users me-1"></i>Active Employees
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['total_active_employees'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                <i class="fas fa-check-circle me-1"></i>Present Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['present_today'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                <i class="fas fa-times-circle me-1"></i>Absent Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['absent_today'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                <i class="fas fa-users-slash me-1"></i>On Leave Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['on_leave_today'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Cards Row 2 -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                <i class="fas fa-clipboard-list me-1"></i>Pending Daily Reports
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['pending_daily_reports'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                <i class="fas fa-hourglass-half me-1"></i>Pending Attendance
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['pending_attendance'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                <i class="fas fa-person-dots-from-line me-1"></i>Manpower Requests
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['pending_manpower_requests'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-person-circle-plus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                <i class="fas fa-handshake me-1"></i>Active Subcon
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['active_subcon_agreements'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Monthly Attendance Rate -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Monthly Attendance Rate
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="h1 font-weight-bold text-success">{{ $statistics['attendance_rate_this_month'] ?? 0 }}%</div>
                        <p class="text-muted mb-0">Out of {{ $statistics['daily_reports_this_month'] ?? 0 }} working days this month</p>
                    </div>
                    <div class="progress mt-3" style="height: 10px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ $statistics['attendance_rate_this_month'] ?? 0 }}%;" 
                             aria-valuenow="{{ $statistics['attendance_rate_this_month'] ?? 0 }}" 
                             aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manpower Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>This Week's Manpower
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $weeklyManpower = $currentWeekManpower;
                        $total = $weeklyManpower['total_employees'] ?? 1;
                        $present = $weeklyManpower['present_total'] ?? 0;
                        $absent = $weeklyManpower['absent_total'] ?? 0;
                        $leave = $weeklyManpower['leave_total'] ?? 0;
                        
                        // Prevent division by zero
                        if ($total <= 0) {
                            $total = 1;
                        }
                    @endphp
                    <div class="row text-center">
                        <div class="col-md-4">
                            <p class="text-muted mb-1">Present</p>
                            <h5 class="font-weight-bold text-success">{{ $present }}</h5>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted mb-1">Absent</p>
                            <h5 class="font-weight-bold text-danger">{{ $absent }}</h5>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted mb-1">On Leave</p>
                            <h5 class="font-weight-bold text-warning">{{ $leave }}</h5>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($present/$total)*100 }}%"></div>
                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ ($absent/$total)*100 }}%"></div>
                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ ($leave/$total)*100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">
        <!-- Pending Daily Reports -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-invoice me-2"></i>Pending Daily Reports
                    </h6>
                    <a href="#" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#dailyReportsModal">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Project</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingDailyReports as $report)
                                <tr>
                                    <td>
                                        <small>{{ $report->project->project_name ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $report->report_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-warning">{{ ucfirst($report->status) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('daily-reports.show', $report) }}" class="btn btn-xs btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No pending reports</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Attendance Records -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clipboard-check me-2"></i>Pending Attendance
                    </h6>
                    <a href="{{ route('attendance.index') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Employee</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingAttendance as $attendance)
                                <tr>
                                    <td>
                                        <small>{{ $attendance->employee->full_name ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $attendance->attendance_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($attendance->status) }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-xs btn-outline-success" onclick="approveAttendance({{ $attendance->id }})">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No pending records</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subcon Agreements & Manpower Requests -->
    <div class="row">
        <!-- Active Subcon Agreements -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-handshake me-2"></i>Active Subcontractor Agreements
                    </h6>
                    <a href="{{ route('subcon-agreements.index') }}" class="btn btn-sm btn-outline-primary">
                        All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Subcontractor</th>
                                    <th>Project</th>
                                    <th>Status</th>
                                    <th>End Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subconAgreements as $agreement)
                                <tr>
                                    <td>
                                        <small>{{ $agreement->project->project_name ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $agreement->project->project_name ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $agreement->status === 'active' ? 'success' : 'info' }}">
                                            {{ ucfirst($agreement->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $agreement->end_date->format('M d, Y') }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No active agreements</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Manpower Requests -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-person-circle-plus me-2"></i>Pending Manpower Requests
                    </h6>
                    <a href="{{ route('manpower-requests.index') }}" class="btn btn-sm btn-outline-primary">
                        All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Project</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingManpowerRequests as $request)
                                <tr>
                                    <td>
                                        <small>{{ $request->project->project_name ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $request->required_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-warning">{{ ucfirst($request->status) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('manpower-requests.show', $request) }}" class="btn btn-xs btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No pending requests</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Recent Activities
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Activity</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivities as $activity)
                                <tr>
                                    <td>{{ $activity->user->name ?? 'System' }}</td>
                                    <td>{{ $activity->activity ?? 'Activity' }}</td>
                                    <td>
                                        <small class="text-muted">{{ $activity->entered_at->diffForHumans() ?? 'N/A' }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">No recent activities</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Action Buttons (Floating) -->
<div class="position-fixed bottom-0 end-0 p-3">
    <div class="d-flex flex-column gap-2">
        <a href="{{ route('employees.create') }}" class="btn btn-primary btn-lg rounded-circle" title="Add Employee">
            <i class="fas fa-user-plus"></i>
        </a>
        <a href="{{ route('attendance.create') }}" class="btn btn-info btn-lg rounded-circle" title="Mark Attendance">
            <i class="fas fa-clipboard-list"></i>
        </a>
    </div>
</div>

@endsection
