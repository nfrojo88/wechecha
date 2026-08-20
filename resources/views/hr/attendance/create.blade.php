@extends('layouts.app')

@section('title', 'Record & Manage Attendance - Construct-Pro ERP')

@section('content')
<div class="container-fluid py-4">
    <!-- Header & Navigation -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Dashboard</a></li>
                    @if(!auth()->user() || !auth()->user()->hasRole('hr_officer'))
                    <li class="breadcrumb-item"><a href="{{ route('attendance.index') }}" class="text-decoration-none">Attendance</a></li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">Record Attendance</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold mb-0 text-gray-800">
                <i class="fa-solid fa-calendar-check text-primary me-2"></i>Record & Manage Employee Attendance
            </h1>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Date Navigator -->
            <form method="GET" action="{{ route('attendance.create') }}" class="d-flex align-items-center gap-2 bg-white p-2 rounded-3 shadow-sm border">
                <span class="text-muted small fw-semibold px-2"><i class="fa-regular fa-calendar me-1"></i>Date:</span>
                <input type="date" name="date" class="form-control form-control-sm border-0 shadow-none fw-bold text-primary" 
                       value="{{ $selectedDate }}" onchange="this.form.submit()" style="width: 140px;">
                <a href="{{ route('attendance.create', ['date' => today()->toDateString()]) }}" 
                   class="btn btn-xs btn-outline-primary fw-semibold px-2 {{ $selectedDate === today()->toDateString() ? 'active' : '' }}">Today</a>
                <a href="{{ route('attendance.create', ['date' => today()->subDay()->toDateString()]) }}" 
                   class="btn btn-xs btn-outline-secondary fw-semibold px-2">Yesterday</a>
            </form>

            @if(!auth()->user() || !auth()->user()->hasRole('hr_officer'))
            <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i>Attendance Log
            </a>
            @endif
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-check fa-lg me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-exclamation fa-lg me-2"></i>
                <div>
                    <strong>Please check form inputs:</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Attendance Summary Statistics for Selected Date -->
    @php
        $totalActive = $employees->count();
        $recordedCount = $attendances->count();
        $presentCount = $attendances->where('status', 'present')->count();
        $absentCount = $attendances->where('status', 'absent')->count();
        $leaveCount = $attendances->whereIn('status', ['leave', 'half_day'])->count();
        $unrecordedCount = max(0, $totalActive - $recordedCount);
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="border-left: 5px solid var(--brand-600) !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-uppercase text-muted fw-bold small" style="font-size: 0.7rem; letter-spacing: 0.5px;">Active Employees</span>
                            <h3 class="fw-bold mb-0 text-gray-900 mt-1">{{ $totalActive }}</h3>
                        </div>
                        <div class="rounded-3 p-3 bg-light text-primary">
                            <i class="fa-solid fa-users fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="border-left: 5px solid var(--success) !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-uppercase text-muted fw-bold small" style="font-size: 0.7rem; letter-spacing: 0.5px;">Present Today</span>
                            <h3 class="fw-bold mb-0 text-success mt-1">{{ $presentCount }}</h3>
                        </div>
                        <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                            <i class="fa-solid fa-user-check fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="border-left: 5px solid var(--danger) !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-uppercase text-muted fw-bold small" style="font-size: 0.7rem; letter-spacing: 0.5px;">Absent</span>
                            <h3 class="fw-bold mb-0 text-danger mt-1">{{ $absentCount }}</h3>
                        </div>
                        <div class="rounded-3 p-3 bg-danger bg-opacity-10 text-danger">
                            <i class="fa-solid fa-user-xmark fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="border-left: 5px solid var(--warning) !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-uppercase text-uppercase text-muted fw-bold small" style="font-size: 0.7rem; letter-spacing: 0.5px;">Pending Record</span>
                            <h3 class="fw-bold mb-0 text-warning mt-1">{{ $unrecordedCount }}</h3>
                        </div>
                        <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning">
                            <i class="fa-solid fa-user-clock fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content: Form (Left) & All Employees List (Right) -->
    <div class="row g-4">
        <!-- Attendance Form Panel (Left Column) -->
        <div class="col-lg-5 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 80px; z-index: 10;">
                <div class="card-header bg-white py-3 border-0 rounded-top-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="rounded-3 p-2 bg-primary bg-opacity-10 text-primary me-2">
                                <i class="fa-solid fa-clock fa-lg"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-0" id="formTitle">2-Session Clock Record</h5>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-2 py-1" id="selectedDateBadge">
                            {{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-4 pt-1">
                    <form action="{{ route('attendance.store') }}" method="POST" id="attendanceForm">
                        @csrf
                        <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">

                        <!-- Employee Selection -->
                        <div class="mb-3">
                            <label for="employee_id" class="form-label fw-bold small text-uppercase text-muted">
                                Employee <span class="text-danger">*</span>
                            </label>
                            <select name="employee_id" id="employee_id" class="form-select select2-employee" required onchange="onEmployeeSelect(this.value)">
                                <option value="">-- Select Employee --</option>
                                @foreach($employees as $e)
                                    @php
                                        $att = $attendances->get($e->id);
                                        $displayName = ($e->employee_code ? '['.$e->employee_code.'] ' : '') . $e->full_name . ($e->department ? ' ('.$e->department.')' : '');
                                    @endphp
                                    <option value="{{ $e->id }}" 
                                            data-name="{{ $e->full_name }}"
                                            data-code="{{ $e->employee_code }}"
                                            data-dept="{{ $e->department }}"
                                            data-salary="{{ $e->basic_salary ?? 0 }}"
                                            data-status="{{ $att->status ?? '' }}"
                                            data-morning-in="{{ $att->morning_in ?? '' }}"
                                            data-morning-out="{{ $att->morning_out ?? '' }}"
                                            data-afternoon-in="{{ $att->afternoon_in ?? '' }}"
                                            data-afternoon-out="{{ $att->afternoon_out ?? '' }}"
                                            data-checkin="{{ $att->check_in ?? '' }}"
                                            data-checkout="{{ $att->check_out ?? '' }}"
                                            data-othours="{{ $att->overtime_hours ?? 0 }}"
                                            data-ottype="{{ $att->overtime_type ?? 'none' }}"
                                            data-notes="{{ $att->notes ?? '' }}"
                                            {{ (string)$selectedEmployeeId === (string)$e->id ? 'selected' : '' }}>
                                        {{ $displayName }} {{ $att ? '✓' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold small text-uppercase text-muted">
                                Attendance Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" id="status" class="form-select fw-semibold" required onchange="toggleTimeInputs(this.value)">
                                <option value="present">🟢 Present</option>
                                <option value="absent">🔴 Absent</option>
                                <option value="half_day">🟡 Half Day</option>
                                <option value="leave">🔵 Leave</option>
                                <option value="holiday">🟣 Holiday</option>
                                <option value="weekend">⚪ Weekend</option>
                            </select>
                        </div>

                        <!-- Morning Session (Session 1) -->
                        <div class="card bg-light border-0 rounded-3 p-3 mb-3" id="morningSessionCard">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-primary small text-uppercase">
                                    <i class="fa-solid fa-sun text-warning me-1"></i>Session 1: Morning
                                </span>
                                <small class="text-muted" style="font-size: 0.75rem;">(e.g., 08:00 AM - 12:00 PM)</small>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label for="morning_in" class="form-label small fw-semibold text-muted mb-1">Morning In</label>
                                    <div class="input-group input-group-sm">
                                        <input type="time" name="morning_in" id="morning_in" class="form-control pe-1">
                                        <button class="btn btn-outline-secondary px-2" type="button" onclick="setCurrentTime('morning_in')">Now</button>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="morning_out" class="form-label small fw-semibold text-muted mb-1">Morning Out</label>
                                    <div class="input-group input-group-sm">
                                        <input type="time" name="morning_out" id="morning_out" class="form-control pe-1">
                                        <button class="btn btn-outline-secondary px-2" type="button" onclick="setCurrentTime('morning_out')">Now</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Afternoon Session (Session 2) -->
                        <div class="card bg-light border-0 rounded-3 p-3 mb-3" id="afternoonSessionCard">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-primary small text-uppercase">
                                    <i class="fa-solid fa-cloud-sun text-warning me-1"></i>Session 2: Afternoon
                                </span>
                                <small class="text-muted" style="font-size: 0.75rem;">(e.g., 01:00 PM - 05:00 PM)</small>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label for="afternoon_in" class="form-label small fw-semibold text-muted mb-1">Afternoon In</label>
                                    <div class="input-group input-group-sm">
                                        <input type="time" name="afternoon_in" id="afternoon_in" class="form-control pe-1">
                                        <button class="btn btn-outline-secondary px-2" type="button" onclick="setCurrentTime('afternoon_in')">Now</button>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="afternoon_out" class="form-label small fw-semibold text-muted mb-1">Afternoon Out</label>
                                    <div class="input-group input-group-sm">
                                        <input type="time" name="afternoon_out" id="afternoon_out" class="form-control pe-1">
                                        <button class="btn btn-outline-secondary px-2" type="button" onclick="setCurrentTime('afternoon_out')">Now</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Overtime Section -->
                        <div class="card bg-light border-0 rounded-3 p-3 mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="fw-bold mb-0 text-warning-emphasis" style="color: #b45309;">
                                    <i class="fa-solid fa-bolt me-1"></i>Overtime (OT)
                                </h6>
                                <span class="badge bg-warning bg-opacity-20 text-dark small">Optional</span>
                            </div>

                            <div class="row g-2">
                                <div class="col-5">
                                    <label for="overtime_hours" class="form-label small fw-semibold text-muted mb-1">OT Hours</label>
                                    <input type="number" name="overtime_hours" id="overtime_hours" 
                                           class="form-control form-control-sm" step="0.5" min="0" max="24" 
                                           value="0" placeholder="0">
                                </div>
                                <div class="col-7">
                                    <label for="overtime_type" class="form-label small fw-semibold text-muted mb-1">OT Multiplier</label>
                                    <select name="overtime_type" id="overtime_type" class="form-select form-select-sm">
                                        <option value="none">Auto / Standard</option>
                                        <option value="holiday">Holiday (×2.5)</option>
                                        <option value="rest_day">Rest Day (×2.0)</option>
                                        <option value="night_12_4">Night 12AM-4AM (×1.5)</option>
                                        <option value="night_4_12">Night 4PM-12AM (×1.75)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label fw-bold small text-uppercase text-muted">Notes / Remarks</label>
                            <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Reason for leave, late entry, or shift note..."></textarea>
                        </div>

                        <!-- Actions -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2.5 rounded-3 fw-bold shadow-sm">
                                <i class="fa-solid fa-floppy-disk me-2"></i>Save Attendance Record
                            </button>
                            <button type="button" class="btn btn-light py-2 rounded-3 text-muted fw-semibold" onclick="resetForm()">
                                Clear Form
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- All Employees List Table (Right Column) -->
        <div class="col-lg-7 col-xl-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-0 rounded-top-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h5 class="card-title fw-bold mb-0">
                                <i class="fa-solid fa-users me-2 text-primary"></i>All Employees Attendance (Morning & Afternoon)
                            </h5>
                            <small class="text-muted">Track two sessions (Morning & Afternoon) and quick-clock for any employee</small>
                        </div>

                        <!-- Search & Filter Bar -->
                        <div class="d-flex align-items-center gap-2" style="max-width: 320px;">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                                <input type="text" id="employeeSearchInput" class="form-control border-start-0 ps-0" 
                                       placeholder="Search name, code, dept..." onkeyup="filterEmployeeList()">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="employeeAttendanceTable">
                            <thead class="table-light text-uppercase small text-muted">
                                <tr>
                                    <th class="ps-4">Employee</th>
                                    <th>Status</th>
                                    <th>☀️ Morning Session</th>
                                    <th>🌤️ Afternoon Session</th>
                                    <th>Hours</th>
                                    <th class="text-end pe-4">HR Quick Clock Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $emp)
                                    @php
                                        $att = $attendances->get($emp->id);
                                        $status = $att->status ?? 'not_recorded';
                                        $morningIn = $att->morning_in ?? null;
                                        $morningOut = $att->morning_out ?? null;
                                        $afternoonIn = $att->afternoon_in ?? null;
                                        $afternoonOut = $att->afternoon_out ?? null;
                                        $hours = $att->hours_worked ?? null;
                                    @endphp
                                    <tr class="employee-row" 
                                        data-id="{{ $emp->id }}"
                                        data-search="{{ strtolower($emp->full_name . ' ' . $emp->employee_code . ' ' . $emp->department) }}">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold shadow-sm" 
                                                     style="width: 38px; height: 38px; font-size: 0.9rem;">
                                                    {{ strtoupper(substr($emp->full_name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-gray-900 mb-0">{{ $emp->full_name }}</div>
                                                    <small class="text-muted font-monospace">{{ $emp->employee_code ?: 'EMP-'.$emp->id }}</small>
                                                    <span class="badge bg-light text-dark fw-normal border ms-1">{{ $emp->department ?: 'General' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($status === 'present')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-semibold">
                                                    <i class="fa-solid fa-circle-check me-1"></i>Present
                                                </span>
                                            @elseif($status === 'absent')
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-semibold">
                                                    <i class="fa-solid fa-circle-xmark me-1"></i>Absent
                                                </span>
                                            @elseif($status === 'half_day')
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 fw-semibold">
                                                    <i class="fa-solid fa-circle-half-stroke me-1"></i>Half Day
                                                </span>
                                            @elseif($status === 'leave')
                                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 fw-semibold">
                                                    <i class="fa-solid fa-plane-departure me-1"></i>Leave
                                                </span>
                                            @elseif($status === 'holiday')
                                                <span class="badge bg-purple-subtle text-purple border border-purple-subtle px-2 py-1 fw-semibold" style="background: #f3e8ff; color: #7e22ce;">
                                                    <i class="fa-solid fa-umbrella-beach me-1"></i>Holiday
                                                </span>
                                            @elseif($status === 'weekend')
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 fw-semibold">
                                                    Weekend
                                                </span>
                                            @else
                                                <span class="badge bg-light text-muted border px-2 py-1">
                                                    <i class="fa-regular fa-clock me-1"></i>Not Recorded
                                                </span>
                                            @endif
                                        </td>
                                        <!-- Morning Session Column -->
                                        <td>
                                            <div class="small font-monospace">
                                                <span class="{{ $morningIn ? 'text-success fw-bold' : 'text-muted' }}">
                                                    In: {{ $morningIn ? \Carbon\Carbon::parse($morningIn)->format('h:i A') : '--:--' }}
                                                </span>
                                                <br>
                                                <span class="{{ $morningOut ? 'text-danger fw-bold' : 'text-muted' }}">
                                                    Out: {{ $morningOut ? \Carbon\Carbon::parse($morningOut)->format('h:i A') : '--:--' }}
                                                </span>
                                            </div>
                                        </td>
                                        <!-- Afternoon Session Column -->
                                        <td>
                                            <div class="small font-monospace">
                                                <span class="{{ $afternoonIn ? 'text-success fw-bold' : 'text-muted' }}">
                                                    In: {{ $afternoonIn ? \Carbon\Carbon::parse($afternoonIn)->format('h:i A') : '--:--' }}
                                                </span>
                                                <br>
                                                <span class="{{ $afternoonOut ? 'text-danger fw-bold' : 'text-muted' }}">
                                                    Out: {{ $afternoonOut ? \Carbon\Carbon::parse($afternoonOut)->format('h:i A') : '--:--' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($hours)
                                                <span class="fw-bold text-dark">{{ $hours }} hrs</span>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-xs btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fa-solid fa-clock"></i> Quick Punch
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    <li>
                                                        <form action="{{ route('attendance.quickClock') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="employee_id" value="{{ $emp->id }}">
                                                            <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">
                                                            <input type="hidden" name="action" value="morning_in">
                                                            <button type="submit" class="dropdown-item small text-success"><i class="fa-solid fa-sun me-1"></i> Morning In Now</button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('attendance.quickClock') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="employee_id" value="{{ $emp->id }}">
                                                            <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">
                                                            <input type="hidden" name="action" value="morning_out">
                                                            <button type="submit" class="dropdown-item small text-danger"><i class="fa-solid fa-sun me-1"></i> Morning Out Now</button>
                                                        </form>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('attendance.quickClock') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="employee_id" value="{{ $emp->id }}">
                                                            <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">
                                                            <input type="hidden" name="action" value="afternoon_in">
                                                            <button type="submit" class="dropdown-item small text-success"><i class="fa-solid fa-cloud-sun me-1"></i> Afternoon In Now</button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('attendance.quickClock') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="employee_id" value="{{ $emp->id }}">
                                                            <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">
                                                            <input type="hidden" name="action" value="afternoon_out">
                                                            <button type="submit" class="dropdown-item small text-danger"><i class="fa-solid fa-cloud-sun me-1"></i> Afternoon Out Now</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>

                                            <button type="button" class="btn btn-xs btn-primary ms-1" 
                                                    onclick="selectEmployeeForForm({{ $emp->id }})" title="Fill Form to Edit All Times">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-user-slash fa-2x mb-2 text-gray-300"></i>
                                            <p class="mb-0">No active employees found.</p>
                                        </td>
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

<script>
function onEmployeeSelect(empId) {
    if (!empId) {
        resetForm();
        return;
    }

    const select = document.getElementById('employee_id');
    const option = select.options[select.selectedIndex];
    if (!option) return;

    const name = option.getAttribute('data-name');
    const status = option.getAttribute('data-status') || 'present';
    const morningIn = option.getAttribute('data-morning-in') || option.getAttribute('data-checkin') || '';
    const morningOut = option.getAttribute('data-morning-out') || '';
    const afternoonIn = option.getAttribute('data-afternoon-in') || '';
    const afternoonOut = option.getAttribute('data-afternoon-out') || option.getAttribute('data-checkout') || '';
    const othours = option.getAttribute('data-othours') || '0';
    const ottype = option.getAttribute('data-ottype') || 'none';
    const notes = option.getAttribute('data-notes') || '';

    document.getElementById('formTitle').innerText = 'Edit Attendance: ' + name;
    document.getElementById('status').value = status;
    document.getElementById('morning_in').value = formatTimeForInput(morningIn);
    document.getElementById('morning_out').value = formatTimeForInput(morningOut);
    document.getElementById('afternoon_in').value = formatTimeForInput(afternoonIn);
    document.getElementById('afternoon_out').value = formatTimeForInput(afternoonOut);
    document.getElementById('overtime_hours').value = othours;
    document.getElementById('overtime_type').value = ottype;
    document.getElementById('notes').value = notes;

    toggleTimeInputs(status);
}

function selectEmployeeForForm(empId) {
    const select = document.getElementById('employee_id');
    select.value = empId;
    onEmployeeSelect(empId);
    
    document.getElementById('attendanceForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function formatTimeForInput(timeStr) {
    if (!timeStr) return '';
    if (timeStr.length >= 5) {
        return timeStr.substring(0, 5);
    }
    return timeStr;
}

function setCurrentTime(inputId) {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    document.getElementById(inputId).value = `${hours}:${minutes}`;
}

function toggleTimeInputs(status) {
    if (status === 'absent' || status === 'leave') {
        document.getElementById('morning_in').value = '';
        document.getElementById('morning_out').value = '';
        document.getElementById('afternoon_in').value = '';
        document.getElementById('afternoon_out').value = '';
    }
}

function resetForm() {
    document.getElementById('formTitle').innerText = '2-Session Clock Record';
    document.getElementById('employee_id').value = '';
    document.getElementById('status').value = 'present';
    document.getElementById('morning_in').value = '';
    document.getElementById('morning_out').value = '';
    document.getElementById('afternoon_in').value = '';
    document.getElementById('afternoon_out').value = '';
    document.getElementById('overtime_hours').value = '0';
    document.getElementById('overtime_type').value = 'none';
    document.getElementById('notes').value = '';
}

function filterEmployeeList() {
    const query = document.getElementById('employeeSearchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#employeeAttendanceTable .employee-row');

    rows.forEach(row => {
        const searchText = row.getAttribute('data-search');
        if (searchText.includes(query)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const empSelect = document.getElementById('employee_id');
    if (empSelect.value) {
        onEmployeeSelect(empSelect.value);
    }
});
</script>
@endsection
