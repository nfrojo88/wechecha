@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Employee Self-Service Dashboard</h2>
                <p class="text-muted">Welcome, {{ $employee->name }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('employee.profile.update') }}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-edit"></i> Edit Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary">
                <div class="card-body">
                    <div class="text-primary mb-2">
                        <i class="fa-solid fa-calendar-check fa-2x"></i>
                    </div>
                    <h5 class="card-title small text-uppercase mb-1">Present Days</h5>
                    <p class="card-text text-lg font-weight-bold">{{ $presentDays }}</p>
                    <small class="text-muted">This month</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-danger">
                <div class="card-body">
                    <div class="text-danger mb-2">
                        <i class="fa-solid fa-calendar-x fa-2x"></i>
                    </div>
                    <h5 class="card-title small text-uppercase mb-1">Absent Days</h5>
                    <p class="card-text text-lg font-weight-bold">{{ $absentDays }}</p>
                    <small class="text-muted">This month</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-warning">
                <div class="card-body">
                    <div class="text-warning mb-2">
                        <i class="fa-solid fa-calendar-days fa-2x"></i>
                    </div>
                    <h5 class="card-title small text-uppercase mb-1">Leave Days</h5>
                    <p class="card-text text-lg font-weight-bold">{{ $leaveDays }}</p>
                    <small class="text-muted">This month</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-success">
                <div class="card-body">
                    <div class="text-success mb-2">
                        <i class="fa-solid fa-paper-plane fa-2x"></i>
                    </div>
                    <h5 class="card-title small text-uppercase mb-1">Pending Leaves</h5>
                    <p class="card-text text-lg font-weight-bold">{{ $pendingLeaves }}</p>
                    <small class="text-muted">Awaiting approval</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quick Links -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fa-solid fa-link"></i> Quick Links</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('employee.attendance') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-fingerprint text-primary"></i> My Attendance</span>
                            <i class="fa-solid fa-chevron-right text-muted"></i>
                        </div>
                    </a>
                    <a href="{{ route('employee.payroll') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-money-bill text-success"></i> Payroll Slips</span>
                            <i class="fa-solid fa-chevron-right text-muted"></i>
                        </div>
                    </a>
                    <a href="{{ route('employee.contract') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-file-contract text-info"></i> My Contract</span>
                            <i class="fa-solid fa-chevron-right text-muted"></i>
                        </div>
                    </a>
                    <a href="{{ route('employee.leave-history') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-history text-warning"></i> Leave History</span>
                            <i class="fa-solid fa-chevron-right text-muted"></i>
                        </div>
                    </a>
                    <a href="{{ route('employee.leave-balance') }}" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-balance-scale text-secondary"></i> Leave Balance</span>
                            <i class="fa-solid fa-chevron-right text-muted"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Performance & Recognition -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fa-solid fa-star"></i> Performance & Recognition</h5>
                </div>
                <div class="card-body">
                    @if ($latestReview)
                        <div class="mb-3">
                            <h6 class="mb-2">Latest Performance Review</h6>
                            <p class="mb-1">
                                <strong>Period:</strong> {{ $latestReview->review_period }}
                            </p>
                            <p class="mb-1">
                                <strong>Rating:</strong>
                                <span class="badge badge-success">{{ $latestReview->overall_rating }}/5</span>
                            </p>
                            <a href="{{ route('employee.performance') }}" class="btn btn-sm btn-outline-primary">
                                View Reviews
                            </a>
                        </div>
                    @else
                        <p class="text-muted">No performance reviews yet</p>
                    @endif
                </div>
            </div>

            @if ($recentAchievements->count() > 0)
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fa-solid fa-trophy"></i> Recent Achievements</h5>
                </div>
                <div class="list-group list-group-flush">
                    @foreach ($recentAchievements as $achievement)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $achievement->title }}</h6>
                                <p class="text-muted small mb-0">{{ $achievement->description }}</p>
                            </div>
                            <small class="text-muted">{{ $achievement->achievement_date->format('M d, Y') }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="card-footer bg-light">
                    <a href="{{ route('employee.achievements') }}" class="text-sm">View all achievements →</a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Employee Information -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fa-solid fa-id-card"></i> Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Employee ID:</strong> {{ $employee->employee_id }}</p>
                            <p><strong>Department:</strong> {{ $employee->department->name ?? 'N/A' }}</p>
                            <p><strong>Designation:</strong> {{ $employee->designation->name ?? 'N/A' }}</p>
                            <p><strong>Email:</strong> {{ $employee->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Phone:</strong> {{ $employee->phone }}</p>
                            <p><strong>Address:</strong> {{ $employee->address }}</p>
                            <p><strong>Joining Date:</strong> {{ $employee->joining_date->format('M d, Y') }}</p>
                            @if ($currentContract)
                            <p><strong>Contract Status:</strong> <span class="badge badge-success">Active</span></p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
