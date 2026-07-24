@extends('layouts.app')

@section('title', 'Employee Profile - ' . $employee->full_name)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center">
        <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-secondary me-3">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-0">{{ $employee->full_name }}</h1>
            <small class="text-muted">{{ $employee->employee_code }} • {{ $employee->role_title }}</small>
        </div>
    </div>
    <div class="d-flex gap-2">
        @role('gm')
            @if(!$employee->is_approved_by_gm)
                <form action="{{ route('employees.approve', $employee) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="fa-solid fa-check me-2"></i>Approve Employee
                    </button>
                </form>
            @endif
        @endrole
        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-primary">
            <i class="fa-solid fa-edit me-2"></i>Edit
        </a>
    </div>
</div>

<div class="row g-3">
    {{-- Left Column: Employee Info --}}
    <div class="col-lg-8">
        {{-- Employment Info Card --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fa-solid fa-briefcase text-primary me-2"></i>Employment Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Department</small>
                        <h6 class="mb-0">{{ $employee->department ?? 'N/A' }}</h6>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Employment Type</small>
                        <h6 class="mb-0">
                            @php
                                $types = ['permanent' => 'Permanent', 'contract' => 'Contract', 'daily' => 'Daily Worker'];
                            @endphp
                            {{ $types[$employee->employment_type] ?? $employee->employment_type }}
                        </h6>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Date of Joining</small>
                        <h6 class="mb-0">{{ $employee->date_of_joining->format('d M Y') }}</h6>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Status</small>
                        <h6 class="mb-0">
                            @if($employee->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($employee->status === 'suspended')
                                <span class="badge bg-warning">Suspended</span>
                            @else
                                <span class="badge bg-danger">Terminated</span>
                            @endif

                            @if($employee->is_approved_by_gm)
                                <span class="badge bg-success ms-1"><i class="fa-solid fa-check-circle me-1"></i>Approved by GM</span>
                            @else
                                <span class="badge bg-warning ms-1 text-dark"><i class="fa-solid fa-clock me-1"></i>Pending GM Approval</span>
                            @endif
                        </h6>
                    </div>
                    @if($employee->project)
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Assigned Project</small>
                        <h6 class="mb-0">{{ $employee->project->name }}</h6>
                    </div>
                    @endif
                    @if($employee->site_assignment)
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Site Assignment</small>
                        <h6 class="mb-0">{{ $employee->site_assignment }}</h6>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Contact Information Card --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fa-solid fa-phone text-primary me-2"></i>Contact Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Phone</small>
                        <h6 class="mb-0">{{ $employee->phone ?? 'N/A' }}</h6>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Email</small>
                        <h6 class="mb-0">{{ $employee->email ?? 'N/A' }}</h6>
                    </div>
                </div>
            </div>
        </div>

        {{-- Salary Information Card --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fa-solid fa-money-bill text-success me-2"></i>Salary Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Monthly Base Salary</small>
                        <h6 class="mb-0">Br {{ number_format($employee->basic_salary, 2) }}</h6>
                    </div>
                    @if($employee->bank_name)
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Bank Account</small>
                        <h6 class="mb-0">{{ $employee->bank_name }} - {{ $employee->account_number }}</h6>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Guarantee Letter Status Card --}}
        @if($employee->guarantee_letter_required)
        <div class="card border-0 shadow-sm mb-3 @if($employee->is_guarantee_overdue) border-danger @elseif($employee->show_guarantee_warning) border-warning @endif">
            <div class="card-header @if($employee->is_guarantee_overdue) bg-danger text-white @elseif($employee->show_guarantee_warning) bg-warning text-dark @else bg-light @endif">
                <h5 class="mb-0">
                    <i class="fa-solid fa-shield-halved me-2"></i>Guarantee Letter Status
                </h5>
            </div>
            <div class="card-body">
                @if($employee->guarantee_letter)
                    {{-- Guarantee letter submitted --}}
                    <div class="alert alert-success">
                        <i class="fa-solid fa-check-circle me-2"></i>
                        <strong>Guarantee Letter Submitted</strong>
                        <br><small>Submitted on: {{ $employee->guarantee_letter_submitted_at ? $employee->guarantee_letter_submitted_at->format('d M Y') : 'Unknown Date' }}</small>
                    </div>
                    <a href="{{ $employee->guarantee_letter_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fa-solid fa-file-pdf me-1"></i>View Guarantee Letter
                    </a>
                @elseif($employee->is_guarantee_overdue)
                    {{-- Overdue - 30+ days --}}
                    <div class="alert alert-danger mb-3">
                        <i class="fa-solid fa-exclamation-circle me-2"></i>
                        <strong>OVERDUE!</strong> Guarantee letter was due {{ abs($employee->days_until_guarantee_deadline) }} days ago.
                        <br><small>Login access has been blocked until submission.</small>
                    </div>
                    <p class="text-muted mb-3">
                        <i class="fa-solid fa-calendar me-2"></i>
                        Joined: {{ $employee->date_of_joining->format('d M Y') }}
                        <br>
                        <i class="fa-solid fa-clock me-2"></i>
                        Deadline was: {{ $employee->date_of_joining->addDays(30)->format('d M Y') }}
                    </p>
                    <form action="{{ route('employees.upload-guarantee', $employee) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Upload Guarantee Letter <span class="text-danger">*</span></label>
                            <input type="file" name="guarantee_letter" class="form-control" required accept="application/pdf,image/jpeg,image/png,image/jpg">
                            <small class="text-muted">PDF or Image (Max 10MB)</small>
                        </div>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa-solid fa-upload me-2"></i>Submit Now to Restore Access
                        </button>
                    </form>
                @elseif($employee->show_guarantee_warning)
                    {{-- Warning - 20+ days --}}
                    <div class="alert alert-warning mb-3">
                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                        <strong>Warning!</strong> Guarantee letter must be submitted within {{ $employee->days_until_guarantee_deadline }} days.
                        <br><small>Login will be blocked after {{ $employee->date_of_joining->addDays(30)->format('d M Y') }}</small>
                    </div>
                    <p class="text-muted mb-3">
                        <i class="fa-solid fa-calendar me-2"></i>
                        Joined: {{ $employee->date_of_joining->format('d M Y') }}
                        <br>
                        <i class="fa-solid fa-clock me-2"></i>
                        Deadline: {{ $employee->date_of_joining->addDays(30)->format('d M Y') }}
                    </p>
                    <form action="{{ route('employees.upload-guarantee', $employee) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Upload Guarantee Letter</label>
                            <input type="file" name="guarantee_letter" class="form-control" accept="application/pdf,image/jpeg,image/png,image/jpg">
                            <small class="text-muted">PDF or Image (Max 10MB)</small>
                        </div>
                        <button type="submit" class="btn btn-warning">
                            <i class="fa-solid fa-upload me-2"></i>Submit Now
                        </button>
                    </form>
                @else
                    {{-- Not yet 20 days --}}
                    <div class="alert alert-info mb-3">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        Guarantee letter due in {{ $employee->days_until_guarantee_deadline }} days.
                    </div>
                    <p class="text-muted mb-3">
                        <i class="fa-solid fa-calendar me-2"></i>
                        Joined: {{ $employee->date_of_joining->format('d M Y') }}
                        <br>
                        <i class="fa-solid fa-clock me-2"></i>
                        Deadline: {{ $employee->date_of_joining->addDays(30)->format('d M Y') }}
                    </p>
                    <form action="{{ route('employees.upload-guarantee', $employee) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Upload Guarantee Letter (Optional - can submit anytime)</label>
                            <input type="file" name="guarantee_letter" class="form-control" accept="application/pdf,image/jpeg,image/png,image/jpg">
                            <small class="text-muted">PDF or Image (Max 10MB)</small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-upload me-2"></i>Submit Early
                        </button>
                    </form>
                @endif
            </div>
        </div>
        @endif

        {{-- Educational Background Card --}}
        @if($employee->education()->count() > 0)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa-solid fa-graduation-cap text-primary me-2"></i>Educational Background</h5>
                <span class="badge bg-primary">{{ $employee->education()->count() }} Record(s)</span>
            </div>
            <div class="card-body">
                @foreach($employee->education as $edu)
                <div class="border-start border-4 border-primary ps-3 mb-4 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="mb-1">
                                <i class="fa-solid fa-award text-warning me-2"></i>
                                <strong>{{ $edu->degree_level }}</strong> in {{ $edu->field_of_study }}
                                @if($edu->is_verified)
                                    <span class="badge bg-success ms-2"><i class="fa-solid fa-check"></i> Verified</span>
                                @endif
                            </h6>
                            <p class="text-muted mb-2">
                                <i class="fa-solid fa-building me-2"></i>{{ $edu->institution_name }}
                                @if($edu->location)
                                    <br><i class="fa-solid fa-map-marker-alt me-2"></i>{{ $edu->location }}
                                @endif
                            </p>
                            <small class="text-muted">
                                <i class="fa-solid fa-calendar me-2"></i>{{ $edu->duration }}
                            </small>
                            @if($edu->grade_gpa)
                                <br><small class="text-muted">
                                    <i class="fa-solid fa-star me-2"></i>Grade: <strong>{{ $edu->grade_gpa }}</strong>
                                </small>
                            @endif
                            @if($edu->description)
                                <p class="mt-2 mb-0 small text-secondary">{{ $edu->description }}</p>
                            @endif
                        </div>
                        <div class="col-md-4 text-end">
                            @if($edu->certificate_photo)
                                <a href="{{ $edu->certificate_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-image me-1"></i>View Certificate
                                </a>
                            @else
                                <small class="text-muted">No certificate uploaded</small>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Work Experience & Licenses Card --}}
        @if($employee->experience()->count() > 0)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa-solid fa-briefcase text-success me-2"></i>Work Experience & Professional Licenses</h5>
                <span class="badge bg-success">{{ $employee->experience()->count() }} Position(s)</span>
            </div>
            <div class="card-body">
                @foreach($employee->experience as $exp)
                <div class="border-start border-4 border-success ps-3 mb-4 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="mb-1">
                                <i class="fa-solid fa-user-tie text-info me-2"></i>
                                <strong>{{ $exp->job_title }}</strong>
                                @if($exp->is_current)
                                    <span class="badge bg-info ms-2">Current</span>
                                @endif
                            </h6>
                            <p class="text-muted mb-2">
                                <i class="fa-solid fa-building me-2"></i>{{ $exp->company_name }}
                                @if($exp->location)
                                    <br><i class="fa-solid fa-map-marker-alt me-2"></i>{{ $exp->location }}
                                @endif
                            </p>
                            <small class="text-muted">
                                <i class="fa-solid fa-calendar me-2"></i>{{ $exp->period }}
                                <span class="badge bg-secondary ms-2">{{ $exp->duration }}</span>
                            </small>
                            
                            @if($exp->responsibilities)
                                <p class="mt-2 mb-2 small text-secondary">{{ Str::limit($exp->responsibilities, 200) }}</p>
                            @endif

                            {{-- Professional License Info --}}
                            @if($exp->license_number || $exp->license_document)
                                <div class="alert alert-light mt-3 mb-0 border-start border-4 border-warning">
                                    <small class="text-muted d-block mb-1"><i class="fa-solid fa-certificate text-warning me-2"></i><strong>Professional License</strong></small>
                                    @if($exp->license_number)
                                        <small><strong>License #:</strong> {{ $exp->license_number }}</small><br>
                                    @endif
                                    @if($exp->license_expiry)
                                        <small>
                                            <strong>Expiry:</strong> {{ $exp->license_expiry->format('d M Y') }}
                                            @if($exp->is_license_expired)
                                                <span class="badge bg-danger ms-2">Expired</span>
                                            @else
                                                <span class="badge bg-success ms-2">Valid</span>
                                            @endif
                                        </small>
                                    @endif
                                </div>
                            @endif

                            {{-- Reference Info --}}
                            @if($exp->reference_name)
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fa-solid fa-user-check me-2"></i><strong>Reference:</strong> 
                                        {{ $exp->reference_name }}
                                        @if($exp->reference_phone)
                                            ({{ $exp->reference_phone }})
                                        @endif
                                    </small>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4 text-end">
                            @if($exp->license_document)
                                <a href="{{ $exp->license_url }}" target="_blank" class="btn btn-sm btn-outline-success">
                                    <i class="fa-solid fa-file-pdf me-1"></i>View License
                                </a>
                            @else
                                <small class="text-muted">No license document</small>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Assigned Assets Card --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa-solid fa-computer text-info me-2"></i>Assigned Assets & Equipment</h5>
                <span class="badge bg-info">{{ $employee->activeAssets()->count() }} Active</span>
            </div>
            <div class="card-body">
                @if($employee->activeAssets()->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Asset Name</th>
                                <th>Type</th>
                                <th>Assigned Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employee->activeAssets() as $asset)
                            <tr>
                                <td>
                                    <strong>{{ $asset->product->name ?? 'Unknown' }}</strong>
                                    @if($asset->notes)
                                    <br><small class="text-muted">{{ $asset->notes }}</small>
                                    @endif
                                </td>
                                <td>{{ $asset->product->type ?? 'General' }}</td>
                                <td>{{ $asset->assigned_date->format('d M Y') }}</td>
                                <td>
                                    @if($asset->status === 'assigned')
                                        <span class="badge bg-primary">Assigned</span>
                                    @elseif($asset->status === 'in_use')
                                        <span class="badge bg-success">In Use</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('employee-assets.return', $asset) }}" class="btn btn-outline-warning" title="Return Asset">
                                            <i class="fa-solid fa-arrow-rotate-left"></i>
                                        </a>
                                        <a href="{{ route('employee-assets.damage', $asset) }}" class="btn btn-outline-danger" title="Report Damage">
                                            <i class="fa-solid fa-exclamation-triangle"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="fa-solid fa-inbox fa-2x mb-3 text-muted opacity-50"></i>
                    <p class="mb-0">No assets currently assigned to this employee</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Recently Returned Assets --}}
        @php
            $returnedAssets = $employee->assets()->where('status', 'returned')->latest('returned_date')->limit(5)->get();
            $damagedAssets = $employee->assets()->where('status', 'damaged')->latest('updated_at')->limit(5)->get();
        @endphp
        
        @if($returnedAssets->count() > 0 || $damagedAssets->count() > 0)
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fa-solid fa-history text-secondary me-2"></i>Asset History</h5>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="returned-tab" data-bs-toggle="tab" data-bs-target="#returned" type="button" role="tab">
                            Returned <span class="badge bg-secondary ms-2">{{ $returnedAssets->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="damaged-tab" data-bs-toggle="tab" data-bs-target="#damaged" type="button" role="tab">
                            Damaged <span class="badge bg-danger ms-2">{{ $damagedAssets->count() }}</span>
                        </button>
                    </li>
                </ul>
                <div class="tab-content mt-3">
                    <div class="tab-pane fade show active" id="returned" role="tabpanel">
                        @if($returnedAssets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Asset</th>
                                        <th>Assigned</th>
                                        <th>Returned</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($returnedAssets as $asset)
                                    <tr>
                                        <td>{{ $asset->product->name ?? 'Unknown' }}</td>
                                        <td>{{ $asset->assigned_date->format('d M Y') }}</td>
                                        <td>{{ $asset->returned_date->format('d M Y') }}</td>
                                        <td>{{ $asset->notes ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                    <div class="tab-pane fade" id="damaged" role="tabpanel">
                        @if($damagedAssets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Asset</th>
                                        <th>Assigned Date</th>
                                        <th>Damage Reported</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($damagedAssets as $asset)
                                    <tr>
                                        <td>{{ $asset->product->name ?? 'Unknown' }}</td>
                                        <td>{{ $asset->assigned_date->format('d M Y') }}</td>
                                        <td>{{ $asset->updated_at->format('d M Y') }}</td>
                                        <td>{{ $asset->notes ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Right Column: Quick Stats --}}
    <div class="col-lg-4">
        {{-- Assets Summary --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fa-solid fa-computer fa-3x text-info opacity-50"></i>
                </div>
                <h6 class="text-muted mb-1">Active Assets</h6>
                <h2 class="mb-0">{{ $employee->activeAssets()->count() }}</h2>
            </div>
        </div>

        {{-- Education & Experience Summary --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fa-solid fa-chart-simple me-2"></i>Qualifications</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <i class="fa-solid fa-graduation-cap text-primary fa-2x"></i>
                    </div>
                    <div class="text-end">
                        <h6 class="mb-0">{{ $employee->education()->count() }}</h6>
                        <small class="text-muted">Education Records</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <i class="fa-solid fa-briefcase text-success fa-2x"></i>
                    </div>
                    <div class="text-end">
                        <h6 class="mb-0">{{ $employee->experience()->count() }}</h6>
                        <small class="text-muted">Work Experience</small>
                    </div>
                </div>
                @php
                    $totalExperienceMonths = 0;
                    foreach($employee->experience as $exp) {
                        if ($exp->is_current) {
                            $totalExperienceMonths += $exp->start_date->diffInMonths(now());
                        } elseif ($exp->end_date) {
                            $totalExperienceMonths += $exp->start_date->diffInMonths($exp->end_date);
                        }
                    }
                    $totalYears = floor($totalExperienceMonths / 12);
                    $totalMonths = $totalExperienceMonths % 12;
                @endphp
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fa-solid fa-clock text-warning fa-2x"></i>
                    </div>
                    <div class="text-end">
                        <h6 class="mb-0">
                            {{ $totalYears }}y {{ $totalMonths }}m
                        </h6>
                        <small class="text-muted">Total Experience</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payroll Info --}}
        @if($employee->payrolls()->count() > 0)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <small class="text-muted d-block mb-3">Latest Payroll</small>
                @php
                    $latestPayroll = $employee->payrolls()->latest()->first();
                @endphp
                <h6 class="mb-2">{{ $latestPayroll->payroll_month ?? 'N/A' }}</h6>
                <div class="row g-2 text-center">
                    <div class="col-6">
                        <small class="text-muted d-block mb-1">Gross</small>
                        <strong>Br {{ number_format($latestPayroll->gross_salary ?? 0, 2) }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block mb-1">Deduction</small>
                        <strong>Br {{ number_format($latestPayroll->total_deduction ?? 0, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Leave Balance --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0">Leave Balance</h6>
            </div>
            <div class="card-body">
                @php
                    $leaveBalance = $employee->leaveBalances()
                        ->where('year', date('Y'))
                        ->latest()
                        ->first();
                @endphp
                
                @if($leaveBalance)
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Casual Leave</small>
                    <div class="progress" style="height: 20px;">
                        @php
                            $used = $leaveBalance->casual_used ?? 0;
                            $total = $leaveBalance->casual_allotted ?? 0;
                            $percentage = $total > 0 ? ($used / $total) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-success" style="width: {{ $percentage }}%">
                            {{ $used }}/{{ $total }}
                        </div>
                    </div>
                </div>
                <div>
                    <small class="text-muted d-block mb-1">Annual Leave</small>
                    <div class="progress" style="height: 20px;">
                        @php
                            $usedAnnual = $leaveBalance->annual_used ?? 0;
                            $totalAnnual = $leaveBalance->annual_allotted ?? 0;
                            $percentageAnnual = $totalAnnual > 0 ? ($usedAnnual / $totalAnnual) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-info" style="width: {{ $percentageAnnual }}%">
                            {{ $usedAnnual }}/{{ $totalAnnual }}
                        </div>
                    </div>
                </div>
                @else
                <p class="text-muted mb-0">No leave balance data available</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ===================== ATTENDANCE HISTORY ===================== --}}
<div class="row g-3 mt-1">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="fa-solid fa-clock text-primary me-2"></i>Attendance History</h5>
                @if($employee->device_user_id)
                    <span class="badge bg-success"><i class="fa-solid fa-link me-1"></i>Device ID: {{ $employee->device_user_id }}</span>
                @else
                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-unlink me-1"></i>No Device Linked — Set Device User ID in Edit</span>
                @endif
            </div>
            <div class="card-body p-0">
                {{-- Summary Stats --}}
                @php
                    $thisMonth = $employee->attendances()->whereMonth('attendance_date', now()->month)->whereYear('attendance_date', now()->year)->get();
                    $presentCount = $thisMonth->where('status', 'present')->count();
                    $absentCount  = $thisMonth->where('status', 'absent')->count();
                    $lateCount    = $thisMonth->where('status', 'half_day')->count();
                    $totalHours   = $thisMonth->sum('hours_worked');
                @endphp
                <div class="row g-0 border-bottom">
                    <div class="col-3 text-center py-3 border-end">
                        <div class="h4 mb-0 text-success fw-bold">{{ $presentCount }}</div>
                        <small class="text-muted">Present (This Month)</small>
                    </div>
                    <div class="col-3 text-center py-3 border-end">
                        <div class="h4 mb-0 text-danger fw-bold">{{ $absentCount }}</div>
                        <small class="text-muted">Absent (This Month)</small>
                    </div>
                    <div class="col-3 text-center py-3 border-end">
                        <div class="h4 mb-0 text-warning fw-bold">{{ $lateCount }}</div>
                        <small class="text-muted">Half Day (This Month)</small>
                    </div>
                    <div class="col-3 text-center py-3">
                        <div class="h4 mb-0 text-info fw-bold">{{ number_format($totalHours, 1) }}</div>
                        <small class="text-muted">Hours Worked (This Month)</small>
                    </div>
                </div>

                {{-- Filter Form --}}
                <div class="p-3 border-bottom bg-light d-flex gap-2 flex-wrap align-items-end">
                    <form method="GET" action="{{ route('employees.show', $employee) }}" class="d-flex gap-2 flex-wrap align-items-end mb-0">
                        <div>
                            <label class="form-label form-label-sm mb-1">From</label>
                            <input type="date" name="att_from" value="{{ request('att_from', now()->startOfMonth()->format('Y-m-d')) }}" class="form-control form-control-sm">
                        </div>
                        <div>
                            <label class="form-label form-label-sm mb-1">To</label>
                            <input type="date" name="att_to" value="{{ request('att_to', now()->format('Y-m-d')) }}" class="form-control form-control-sm">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    </form>
                </div>

                {{-- Attendance Table --}}
                @php
                    $attFrom = request('att_from', now()->startOfMonth()->format('Y-m-d'));
                    $attTo   = request('att_to', now()->format('Y-m-d'));
                    $attendances = $employee->attendances()
                        ->whereBetween('attendance_date', [$attFrom, $attTo])
                        ->orderBy('attendance_date', 'desc')
                        ->paginate(20, ['*'], 'att_page');
                @endphp
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Date</th>
                                <th>Status</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Hours Worked</th>
                                <th>Source</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $att)
                            <tr>
                                <td class="ps-3 fw-semibold">{{ $att->attendance_date->format('D, d M Y') }}</td>
                                <td>
                                    @php
                                        $statusMap = [
                                            'present'  => ['bg-success',  'Present'],
                                            'absent'   => ['bg-danger',   'Absent'],
                                            'half_day' => ['bg-warning text-dark', 'Half Day'],
                                            'leave'    => ['bg-info',     'On Leave'],
                                            'holiday'  => ['bg-secondary','Holiday'],
                                            'weekend'  => ['bg-light text-dark border','Weekend'],
                                        ];
                                        [$cls, $lbl] = $statusMap[$att->status] ?? ['bg-secondary', $att->status];
                                    @endphp
                                    <span class="badge {{ $cls }}">{{ $lbl }}</span>
                                </td>
                                <td>{{ $att->check_in  ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '—' }}</td>
                                <td>{{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '—' }}</td>
                                <td>
                                    @if($att->hours_worked)
                                        <span class="text-{{ $att->hours_worked >= 8 ? 'success' : 'warning' }}">
                                            {{ number_format($att->hours_worked, 1) }} hrs
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <i class="fa-solid fa-{{ $att->source === 'device' ? 'fingerprint' : 'keyboard' }} me-1"></i>
                                        {{ ucfirst(str_replace('_', ' ', $att->source ?? 'manual')) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fa-solid fa-calendar-xmark fa-2x mb-2 d-block opacity-25"></i>
                                    No attendance records found for this period.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($attendances->hasPages())
                <div class="p-3">{{ $attendances->appends(request()->except('att_page'))->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
