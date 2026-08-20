@extends('layouts.app')

@section('title', 'Add Employee')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <h1 class="h3 mb-0">Add New Employee</h1>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-3 shadow-sm mb-4" role="alert">
    <i class="fa-solid fa-circle-check fa-2x text-success"></i>
    <div>
        <strong class="d-block fs-6">Registration Successful!</strong>
        {{ session('success') }}
    </div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Multi-Step Progress Indicator -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-center flex-grow-1">
                <div class="step-indicator {{ request()->get('step', 1) == 1 ? 'active' : (request()->get('step', 1) > 1 ? 'completed' : '') }}">
                    <span class="step-number">1</span>
                </div>
                <small class="text-muted d-block mt-1">Basic Info</small>
            </div>
            <div class="flex-grow-1" style="height: 2px; background: #dee2e6; margin: 0 10px; margin-top: -15px;"></div>
            <div class="text-center flex-grow-1">
                <div class="step-indicator {{ request()->get('step', 1) == 2 ? 'active' : (request()->get('step', 1) > 2 ? 'completed' : '') }}">
                    <span class="step-number">2</span>
                </div>
                <small class="text-muted d-block mt-1">Employment</small>
            </div>
            <div class="flex-grow-1" style="height: 2px; background: #dee2e6; margin: 0 10px; margin-top: -15px;"></div>
            <div class="text-center flex-grow-1">
                <div class="step-indicator {{ request()->get('step', 1) == 3 ? 'active' : (request()->get('step', 1) > 3 ? 'completed' : '') }}">
                    <span class="step-number">3</span>
                </div>
                <small class="text-muted d-block mt-1">Salary</small>
            </div>
            <div class="flex-grow-1" style="height: 2px; background: #dee2e6; margin: 0 10px; margin-top: -15px;"></div>
            <div class="text-center flex-grow-1">
                <div class="step-indicator {{ request()->get('step', 1) == 4 ? 'active' : (request()->get('step', 1) > 4 ? 'completed' : '') }}">
                    <span class="step-number">4</span>
                </div>
                <small class="text-muted d-block mt-1">Assets</small>
            </div>
            <div class="flex-grow-1" style="height: 2px; background: #dee2e6; margin: 0 10px; margin-top: -15px;"></div>
            <div class="text-center flex-grow-1">
                <div class="step-indicator {{ request()->get('step', 1) == 5 ? 'active' : (request()->get('step', 1) > 5 ? 'completed' : '') }}">
                    <span class="step-number">5</span>
                </div>
                <small class="text-muted d-block mt-1">Education</small>
            </div>
            <div class="flex-grow-1" style="height: 2px; background: #dee2e6; margin: 0 10px; margin-top: -15px;"></div>
            <div class="text-center flex-grow-1">
                <div class="step-indicator {{ request()->get('step', 1) == 6 ? 'active' : (request()->get('step', 1) > 6 ? 'completed' : '') }}">
                    <span class="step-number">6</span>
                </div>
                <small class="text-muted d-block mt-1">Experience</small>
            </div>
        </div>
    </div>
</div>

<style>
.step-indicator {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: #6c757d;
    transition: all 0.3s ease;
}
.step-indicator.active {
    background: #0d6efd;
    color: white;
}
.step-indicator.completed {
    background: #28a745;
    color: white;
}
</style>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('employees.store') }}" id="employeeForm" enctype="multipart/form-data" novalidate>
            @csrf
            <input type="hidden" name="step" value="{{ request()->get('step', 1) }}">
            <input type="hidden" name="action" id="formAction" value="next">
            
            {{-- Display Validation Errors --}}
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
            
            <!-- Hidden fields to preserve data across steps (session-first fallback) -->
            <input type="hidden" name="employee_code"       value="{{ old('employee_code',       session('employee_data.employee_code')) }}">
            <input type="hidden" name="full_name"           value="{{ old('full_name',           session('employee_data.full_name')) }}">
            <input type="hidden" name="phone"               value="{{ old('phone',               session('employee_data.phone')) }}">
            <input type="hidden" name="email"               value="{{ old('email',               session('employee_data.email')) }}">
            <input type="hidden" name="department"          value="{{ old('department',          session('employee_data.department')) }}">
            <input type="hidden" name="role_title"          value="{{ old('role_title',          session('employee_data.role_title')) }}">
            <input type="hidden" name="employment_type"     value="{{ old('employment_type',     session('employee_data.employment_type', 'permanent')) }}">
            <input type="hidden" name="date_of_joining"     value="{{ old('date_of_joining',     session('employee_data.date_of_joining')) }}">
            <input type="hidden" name="status"              value="{{ old('status',              session('employee_data.status', 'active')) }}">
            <input type="hidden" name="project_id"          value="{{ old('project_id',          session('employee_data.project_id')) }}">
            <input type="hidden" name="site_assignment"     value="{{ old('site_assignment',     session('employee_data.site_assignment')) }}">
            <input type="hidden" name="basic_salary"        value="{{ old('basic_salary',        session('employee_data.basic_salary', 0)) }}">
            <input type="hidden" name="transport_allowance" value="{{ old('transport_allowance', session('employee_data.transport_allowance', 0)) }}">
            <input type="hidden" name="house_allowance"     value="{{ old('house_allowance',     session('employee_data.house_allowance', 0)) }}">
            <input type="hidden" name="position_allowance"  value="{{ old('position_allowance',  session('employee_data.position_allowance', 0)) }}">
            <input type="hidden" name="contract_type"       value="{{ old('contract_type',       session('employee_data.contract_type', 'Full-Time')) }}">
            <input type="hidden" name="bank_name"           value="{{ old('bank_name',           session('employee_data.bank_name')) }}">
            <input type="hidden" name="account_number"      value="{{ old('account_number',      session('employee_data.account_number')) }}">
            <input type="hidden" name="device_user_id"      value="{{ old('device_user_id',      session('employee_data.device_user_id')) }}">
            <input type="hidden" name="notes"               value="{{ old('notes',               session('employee_data.notes')) }}">

            {{-- STEP 1: Basic Information --}}
            @if(request()->get('step', 1) == 1)
            <div class="mb-3" data-step="1">
                <h5 class="mb-4"><i class="fa-solid fa-user-circle text-primary me-2"></i>Basic Information</h5>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Employee Code <span class="text-danger">*</span></label>
                        <input type="text" name="employee_code" class="form-control @error('employee_code') is-invalid @enderror"
                               value="{{ session('employee_data.employee_code') ?? old('employee_code', 'EMP-'.rand(10000,99999)) }}" required>
                        @error('employee_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                               value="{{ session('employee_data.full_name') ?? old('full_name') }}" placeholder="e.g. Abebe Bikila" required>
                        @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">
                            <i class="fa-solid fa-fingerprint text-primary me-1"></i>ZKTeco Device User ID
                        </label>
                        <input type="text" name="device_user_id" class="form-control @error('device_user_id') is-invalid @enderror"
                               value="{{ session('employee_data.device_user_id') ?? old('device_user_id') }}"
                               placeholder="e.g. 1, 2, 17, 50">
                        <small class="text-muted">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            The numeric ID assigned to this employee in the fingerprint machine's user list.
                        </small>
                        @error('device_user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Primary Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                               value="{{ session('employee_data.phone') ?? old('phone') }}" placeholder="+251 911 234 567" required>
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ session('employee_data.email') ?? old('email') }}" placeholder="employee@company.com">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Department <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="department" class="form-select @error('department') is-invalid @enderror" required>
                                <option value="">-- Select Department --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->name }}" {{ (session('employee_data.department') ?? old('department')) == $dept->name ? 'selected' : '' }}>
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
                </div>
            </div>
            @endif

            {{-- STEP 2: Employment Details --}}
            @if(request()->get('step', 1) == 2)
            <div class="mb-3" data-step="2">
                <h5 class="mb-4"><i class="fa-solid fa-briefcase text-success me-2"></i>Employment Details</h5>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Employment Type <span class="text-danger">*</span></label>
                        <select name="employment_type" class="form-select" required>
                            <option value="permanent" @selected((session('employee_data.employment_type') ?? old('employment_type','permanent'))=='permanent')>Permanent</option>
                            <option value="contract" @selected((session('employee_data.employment_type') ?? old('employment_type'))=='contract')>Contract</option>
                            <option value="daily" @selected((session('employee_data.employment_type') ?? old('employment_type'))=='daily')>Daily Worker</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Contract Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="date_of_joining" class="form-control @error('date_of_joining') is-invalid @enderror"
                               value="{{ session('employee_data.date_of_joining') ?? old('date_of_joining', date('Y-m-d')) }}" required>
                        @error('date_of_joining')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active" @selected((session('employee_data.status') ?? old('status','active'))=='active')>Active</option>
                            <option value="suspended" @selected((session('employee_data.status') ?? old('status'))=='suspended')>Suspended</option>
                            <option value="terminated" @selected((session('employee_data.status') ?? old('status'))=='terminated')>Terminated</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Assigned Project</label>
                        <select name="project_id" class="form-select">
                            <option value="">-- HQ / Unassigned --</option>
                            @foreach($projects as $p)
                            <option value="{{ $p->id }}" @selected((session('employee_data.project_id') ?? old('project_id'))==$p->id)>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Site Assignment</label>
                        <select name="site_assignment" class="form-select">
                            <option value="">-- No Specific Site --</option>
                            <option value="HQ" @selected((session('employee_data.site_assignment') ?? old('site_assignment'))=='HQ')>Headquarters</option>
                            <option value="Site_A" @selected((session('employee_data.site_assignment') ?? old('site_assignment'))=='Site_A')>Site A</option>
                            <option value="Site_B" @selected((session('employee_data.site_assignment') ?? old('site_assignment'))=='Site_B')>Site B</option>
                        </select>
                    </div>
                </div>
            </div>
            @endif

            {{-- STEP 3: Salary Information --}}
            @if(request()->get('step', 1) == 3)
            <div class="mb-3" data-step="3">
                <h5 class="mb-4"><i class="fa-solid fa-money-bill text-warning me-2"></i>Salary Information</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Monthly Base Salary (ETB) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="basic_salary"
                               class="form-control @error('basic_salary') is-invalid @enderror"
                               value="{{ session('employee_data.basic_salary') ?? old('basic_salary', 0) }}" required>
                        <small class="text-muted">Br 0.00</small>
                        @error('basic_salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contract Type</label>
                        <select name="contract_type" class="form-select">
                            <option value="Full-Time" @selected((session('employee_data.contract_type') ?? old('contract_type'))=='Full-Time')>Full-Time</option>
                            <option value="Part-Time" @selected((session('employee_data.contract_type') ?? old('contract_type'))=='Part-Time')>Part-Time</option>
                            <option value="Temporary" @selected((session('employee_data.contract_type') ?? old('contract_type'))=='Temporary')>Temporary</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="alert alert-light border">
                            <h6 class="mb-3"><i class="fa-solid fa-coins text-success me-2"></i>Allowances</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Transport Allowance (ETB)</label>
                                    <input type="number" step="0.01" min="0" name="transport_allowance" class="form-control" value="{{ session('employee_data.transport_allowance') ?? old('transport_allowance', 0) }}">
                                    <small class="text-muted"><i class="fa-solid fa-info-circle me-1"></i>&lt; 2200 is not taxable</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">House Allowance (ETB)</label>
                                    <input type="number" step="0.01" min="0" name="house_allowance" class="form-control" value="{{ session('employee_data.house_allowance') ?? old('house_allowance', 0) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Position Allowance (ETB)</label>
                                    <input type="number" step="0.01" min="0" name="position_allowance" class="form-control" value="{{ session('employee_data.position_allowance') ?? old('position_allowance', 0) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="alert alert-light border">
                            <h6 class="mb-3"><i class="fa-solid fa-info-circle text-info me-2"></i>Bank Information</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Bank Name (e.g. CBE)</label>
                                    <input type="text" name="bank_name" class="form-control" value="{{ session('employee_data.bank_name') ?? old('bank_name') }}" placeholder="Commercial Bank of Ethiopia">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Account Number</label>
                                    <input type="text" name="account_number" class="form-control" value="{{ session('employee_data.account_number') ?? old('account_number') }}" placeholder="1000123456789">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="alert alert-warning border-start border-4">
                            <h6 class="mb-3"><i class="fa-solid fa-shield-halved text-warning me-2"></i>Guarantee Letter (Optional)</h6>
                            <p class="small text-muted mb-3">
                                <i class="fa-solid fa-info-circle me-1"></i>
                                <strong>Important:</strong> If not uploaded now, employee must submit within 30 days of joining date.
                            </p>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Upload Guarantee Letter <small class="text-muted">(PDF or Image - Max 10MB)</small></label>
                                    <input type="file" name="guarantee_letter" class="form-control" 
                                           accept="application/pdf,image/jpeg,image/png,image/jpg">
                                    <small class="text-muted">
                                        <i class="fa-solid fa-exclamation-triangle text-warning me-1"></i>
                                        Warning will show after 20 days • Login blocked after 30 days if not submitted
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- STEP 4: Asset Assignment --}}
            @if(request()->get('step', 1) == 4)
            <div class="mb-3" data-step="4">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <div>
                        <h5 class="mb-1"><i class="fa-solid fa-truck-monster text-warning me-2"></i>Assign Fixed Assets & Equipment</h5>
                        <p class="text-muted small mb-0">Select available equipment from centralized Store inventory (computers, vehicles, tools, etc.) to assign to this employee.</p>
                    </div>
                    <div>
                        <span class="badge bg-success-subtle text-success border border-success px-3 py-2 fs-6">
                            <i class="fa-solid fa-warehouse me-1"></i>{{ $fixedAssetUnits->count() }} Units Available In Store
                        </span>
                    </div>
                </div>

                {{-- Global Category Filter Pills --}}
                @php
                    $availableCategories = $fixedAssetUnits->map(function($u) {
                        return $u->parentAsset->category ?? 'General';
                    })->unique()->values();
                @endphp
                <div class="card border-0 bg-light p-2 mb-3 shadow-sm">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <small class="text-muted fw-bold me-1"><i class="fa-solid fa-filter me-1"></i>Filter by Category:</small>
                        <button type="button" class="btn btn-xs btn-dark rounded-pill px-3 py-1 btn-category-filter active" data-category="ALL" onclick="filterByCategory('ALL', this)">
                            All ({{ $fixedAssetUnits->count() }})
                        </button>
                        @foreach($availableCategories as $cat)
                            @php
                                $catCount = $fixedAssetUnits->filter(fn($u) => ($u->parentAsset->category ?? 'General') === $cat)->count();
                            @endphp
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-3 py-1 btn-category-filter" data-category="{{ $cat }}" onclick="filterByCategory('{{ $cat }}', this)">
                                {{ $cat }} ({{ $catCount }})
                            </button>
                        @endforeach
                    </div>
                </div>

                <div id="assetsContainer">
                    @php
                        $savedUnits = session('employee_data.fixed_asset_units') ?? old('fixed_asset_units', ['']);
                    @endphp
                    @foreach($savedUnits as $index => $selectedUnitId)
                    <div class="asset-entry border rounded p-3 mb-3 bg-light" data-index="{{ $index }}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 fw-bold"><i class="fa-solid fa-barcode text-primary me-2"></i>Assigned Asset Unit #{{ $index + 1 }}</h6>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-asset {{ count($savedUnits) > 1 ? '' : 'd-none' }}" onclick="removeAsset({{ $index }})">
                                <i class="fa-solid fa-trash me-1"></i>Remove
                            </button>
                        </div>

                        {{-- Per-Row Live Search Input --}}
                        <div class="mb-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-primary"></i></span>
                                <input type="text" class="form-control form-control-sm border-start-0 asset-row-search" 
                                       placeholder="🔍 Type unit code (e.g. COMP-1), serial, plate, or brand to search..." 
                                       oninput="filterAssetOptions(this)">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearRowSearch(this)" title="Clear Search">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label small fw-bold mb-1">
                                    Select Available Fixed Asset Unit <span class="badge bg-success ms-1">In Store</span>
                                </label>
                                <select name="fixed_asset_units[]" class="form-select asset-select font-monospace" onchange="onAssetUnitSelected(this)">
                                    <option value="">-- Choose an Available Asset Unit --</option>
                                    @foreach($fixedAssetUnits as $unit)
                                        @php
                                            $detailStr = $unit->plate_number ? 'Plate: ' . $unit->plate_number : ($unit->serial_number ? 'SN: ' . $unit->serial_number : ($unit->brand ? $unit->brand . ' ' . $unit->model : 'In Store'));
                                            $pName = $unit->parentAsset->name ?? 'Asset';
                                            $pCat = $unit->parentAsset->category ?? 'General';
                                            $searchKeywords = strtolower("{$unit->unit_code} {$pName} {$pCat} {$detailStr} {$unit->brand} {$unit->model} {$unit->serial_number} {$unit->plate_number}");
                                        @endphp
                                        <option value="{{ $unit->id }}" 
                                                data-category="{{ $pCat }}" 
                                                data-search="{{ $searchKeywords }}"
                                                data-unit-code="{{ $unit->unit_code }}"
                                                data-asset-name="{{ $pName }}"
                                                data-specs="{{ $detailStr }}"
                                                data-condition="{{ $unit->condition }}"
                                                @selected($selectedUnitId == $unit->id)>
                                            {{ $unit->unit_code }} — {{ $pName }} ({{ $detailStr }}) • [{{ $pCat }}]
                                        </option>
                                    @endforeach
                                </select>
                                <div class="asset-match-count small text-muted mt-1" style="font-size: 0.75rem;"></div>
                            </div>
                        </div>

                        {{-- Selected Unit Live Details Badge --}}
                        <div class="selected-asset-details mt-2 p-2 bg-white rounded border d-none small">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="badge bg-dark font-monospace fs-6 me-2 selected-unit-code"></span>
                                    <strong class="text-dark selected-asset-name"></strong>
                                    <span class="text-muted ms-2 selected-asset-specs"></span>
                                </div>
                                <div>
                                    <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Ready for Assignment</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm fw-semibold" onclick="addAsset()">
                        <i class="fa-solid fa-plus me-1"></i> Assign Another Asset
                    </button>
                </div>

                <div class="alert alert-info mt-3 small">
                    <i class="fa-solid fa-info-circle me-1"></i>
                    <strong>Centralized Inventory Rule:</strong> Only assets with status <em>In Store (Available)</em> can be selected. When saved, the asset unit will automatically link to this employee and update its status to <em>Assigned</em>.
                </div>
            </div>
            @endif

            {{-- STEP 5: Educational Background --}}
            @if(request()->get('step', 1) == 5)
            <div class="mb-3" data-step="5">
                <h5 class="mb-4"><i class="fa-solid fa-graduation-cap text-primary me-2"></i>Educational Background</h5>
                
                <div class="alert alert-info">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    <strong>This step is optional.</strong> You can skip education history by clicking "Next Step" below.
                </div>
                
                <div id="educationContainer">
                    <div class="education-entry border rounded p-3 mb-3 bg-light" data-index="0">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><i class="fa-solid fa-book me-2"></i>Education Record #1</h6>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-education" onclick="removeEducation(0)" style="display: none;">
                                <i class="fa-solid fa-trash"></i> Remove
                            </button>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Degree Level</label>
                                <select name="education[0][degree_level]" class="form-select">
                                    <option value="">Select Degree</option>
                                    <option value="PhD">PhD / Doctorate</option>
                                    <option value="Master">Master's Degree</option>
                                    <option value="Bachelor">Bachelor's Degree</option>
                                    <option value="Diploma">Diploma</option>
                                    <option value="Certificate">Certificate</option>
                                    <option value="High School">High School</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Field of Study</label>
                                <input type="text" name="education[0][field_of_study]" class="form-control" 
                                       placeholder="e.g., Civil Engineering">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Institution Name</label>
                                <input type="text" name="education[0][institution_name]" class="form-control" 
                                       placeholder="e.g., Addis Ababa University">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Location</label>
                                <input type="text" name="education[0][location]" class="form-control" 
                                       placeholder="e.g., Addis Ababa, Ethiopia">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="education[0][start_date]" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">End Date / Expected</label>
                                <input type="date" name="education[0][end_date]" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Grade / GPA</label>
                                <input type="text" name="education[0][grade_gpa]" class="form-control" 
                                       placeholder="e.g., 3.8/4.0 or Distinction">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description / Achievements</label>
                                <textarea name="education[0][description]" class="form-control" rows="2" 
                                          placeholder="Optional: Thesis title, honors, relevant coursework, etc."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Certificate / Degree Photo <small class="text-muted">(Image: JPG, PNG - Max 5MB)</small></label>
                                <input type="file" name="education[0][certificate_photo]" class="form-control" 
                                       accept="image/jpeg,image/png,image/jpg">
                                <small class="text-muted">Upload a photo of your certificate or degree</small>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addEducation()">
                    <i class="fa-solid fa-plus me-2"></i>Add Another Education
                </button>

                <div class="alert alert-light mt-3 border-start border-4 border-primary">
                    <small>
                        <i class="fa-solid fa-lightbulb me-2"></i>
                        <strong>Optional:</strong> Leave all fields blank and click "Next Step" to skip education history.
                    </small>
                </div>
            </div>
            @endif

            {{-- STEP 6: Work Experience & Professional License --}}
            @if(request()->get('step', 1) == 6)
            <div class="mb-3" data-step="6">
                <h5 class="mb-4"><i class="fa-solid fa-briefcase text-success me-2"></i>Work Experience & Professional License</h5>
                
                <div class="alert alert-info">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    <strong>This step is completely optional.</strong> If this is the employee's first job or you want to skip work history, simply click "Complete Registration" below.
                </div>

                <div id="experienceContainer">
                    <div class="experience-entry border rounded p-3 mb-3 bg-light" data-index="0">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><i class="fa-solid fa-building me-2"></i>Experience Record #1</h6>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-experience" onclick="removeExperience(0)" style="display: none;">
                                <i class="fa-solid fa-trash"></i> Remove
                            </button>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Job Title</label>
                                <input type="text" name="experience[0][job_title]" class="form-control" 
                                       placeholder="e.g., Site Engineer">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="experience[0][company_name]" class="form-control" 
                                       placeholder="e.g., ABC Construction">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Location</label>
                                <input type="text" name="experience[0][location]" class="form-control" 
                                       placeholder="e.g., Addis Ababa, Ethiopia">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="experience[0][start_date]" class="form-control">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">End Date</label>
                                <input type="date" name="experience[0][end_date]" class="form-control" id="exp_end_date_0">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="form-check">
                                    <input type="checkbox" name="experience[0][is_current]" class="form-check-input" 
                                           id="is_current_0" value="1" onchange="toggleEndDate(0)">
                                    <label class="form-check-label" for="is_current_0">
                                        Current
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Key Responsibilities</label>
                                <textarea name="experience[0][responsibilities]" class="form-control" rows="3" 
                                          placeholder="Describe your main duties and achievements..."></textarea>
                            </div>
                            
                            <!-- Reference Section -->
                            <div class="col-12">
                                <hr>
                                <h6 class="text-muted"><i class="fa-solid fa-user-check me-2"></i>Reference (Optional)</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reference Name</label>
                                <input type="text" name="experience[0][reference_name]" class="form-control" 
                                       placeholder="e.g., John Doe">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reference Phone</label>
                                <input type="text" name="experience[0][reference_phone]" class="form-control" 
                                       placeholder="+251 911 234 567">
                            </div>

                            <!-- Professional License Section -->
                            <div class="col-12">
                                <hr>
                                <h6 class="text-muted"><i class="fa-solid fa-certificate me-2"></i>Professional License (Optional)</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">License Number</label>
                                <input type="text" name="experience[0][license_number]" class="form-control" 
                                       placeholder="e.g., PE-12345">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">License Expiry Date</label>
                                <input type="date" name="experience[0][license_expiry]" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label">License Document <small class="text-muted">(PDF or Image - Max 10MB)</small></label>
                                <input type="file" name="experience[0][license_document]" class="form-control" 
                                       accept="application/pdf,image/jpeg,image/png,image/jpg">
                                <small class="text-muted">Upload professional license, certificate, or qualification document (PDF or Image)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-outline-success btn-sm" onclick="addExperience()">
                    <i class="fa-solid fa-plus me-2"></i>Add Another Experience
                </button>

                <div class="alert alert-light mt-3 border-start border-4 border-success">
                    <small>
                        <i class="fa-solid fa-lightbulb me-2"></i>
                        <strong>No experience?</strong> Leave all fields blank and click "Complete Registration" to finish.
                    </small>
                </div>
            </div>
            @endif

            {{-- Navigation Buttons --}}
            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                <div>
                    @if(request()->get('step', 1) > 1)
                    <button type="submit" name="action" value="previous" class="btn btn-outline-secondary" formnovalidate>
                        <i class="fa-solid fa-arrow-left me-2"></i>Previous
                    </button>
                    @endif
                </div>
                <div>
                    @if(request()->get('step', 1) < 6)
                    <button type="submit" name="action" value="next" class="btn btn-primary fw-semibold px-4">
                        Next Step <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                    @else
                    <button type="submit" name="action" value="submit" class="btn btn-success fw-bold px-4">
                        <i class="fa-solid fa-check me-2"></i>Complete Registration
                    </button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<div id="employeeWizardConfig" class="d-none"
     data-education-count="{{ is_array(session('employee_data.education')) ? count(session('employee_data.education')) : 1 }}"
     data-experience-count="{{ is_array(session('employee_data.experience')) ? count(session('employee_data.experience')) : 1 }}"
     data-asset-count="{{ is_array(session('employee_data.fixed_asset_units')) ? count(session('employee_data.fixed_asset_units')) : 1 }}"
     data-fixed-assets="{{ json_encode($fixedAssetUnits ?? []) }}">
</div>

<script>
function nextStep() {
    const form = document.getElementById('employeeForm');
    if (form) {
        document.getElementById('formAction').value = 'next';
        form.submit();
    }
}

function previousStep() {
    const form = document.getElementById('employeeForm');
    if (form) {
        document.getElementById('formAction').value = 'previous';
        form.submit();
    }
}

const wizardCfg = document.getElementById('employeeWizardConfig')?.dataset || {};
let educationCount = parseInt(wizardCfg.educationCount || '1', 10);
let experienceCount = parseInt(wizardCfg.experienceCount || '1', 10);
let assetCount = parseInt(wizardCfg.assetCount || '1', 10);

// Provide the fixed asset units array to javascript for dynamic rows
const fixedAssetUnitsList = JSON.parse(wizardCfg.fixedAssets || '[]');
let currentActiveCategory = 'ALL';

// Live Search per row
function filterAssetOptions(input) {
    const entry = input.closest('.asset-entry');
    if (!entry) return;

    const query = (input.value || '').trim().toLowerCase();
    const select = entry.querySelector('.asset-select');
    const countBadge = entry.querySelector('.asset-match-count');
    if (!select) return;

    let matchCount = 0;
    const options = select.querySelectorAll('option');

    options.forEach(opt => {
        if (!opt.value) {
            opt.hidden = false;
            return;
        }

        const searchKeywords = opt.dataset.search || opt.textContent.toLowerCase();
        const optCat = opt.dataset.category || 'General';

        const matchesQuery = query === '' || searchKeywords.includes(query);
        const matchesCategory = currentActiveCategory === 'ALL' || optCat === currentActiveCategory;

        if (matchesQuery && matchesCategory) {
            opt.hidden = false;
            matchCount++;
        } else {
            opt.hidden = true;
        }
    });

    if (countBadge) {
        if (query || currentActiveCategory !== 'ALL') {
            countBadge.innerHTML = `<i class="fa-solid fa-filter me-1"></i>Found <strong>${matchCount}</strong> matching units`;
        } else {
            countBadge.innerHTML = '';
        }
    }
}

function clearRowSearch(btn) {
    const entry = btn.closest('.asset-entry');
    if (!entry) return;
    const input = entry.querySelector('.asset-row-search');
    if (input) {
        input.value = '';
        filterAssetOptions(input);
    }
}

// Global Category Filter
function filterByCategory(cat, btn) {
    currentActiveCategory = cat;

    // Update active button styles
    document.querySelectorAll('.btn-category-filter').forEach(b => {
        b.classList.remove('active', 'btn-dark');
        b.classList.add('btn-outline-secondary');
    });
    if (btn) {
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('active', 'btn-dark');
    }

    // Re-filter all asset entries
    document.querySelectorAll('.asset-row-search').forEach(input => {
        filterAssetOptions(input);
    });
}

// Show live details preview when a unit is picked
function onAssetUnitSelected(select) {
    const entry = select.closest('.asset-entry');
    if (!entry) return;

    const detailsBox = entry.querySelector('.selected-asset-details');
    const selectedOpt = select.options[select.selectedIndex];

    if (!select.value || !selectedOpt) {
        if (detailsBox) detailsBox.classList.add('d-none');
        return;
    }

    if (detailsBox) {
        const codeEl = detailsBox.querySelector('.selected-unit-code');
        const nameEl = detailsBox.querySelector('.selected-asset-name');
        const specsEl = detailsBox.querySelector('.selected-asset-specs');

        if (codeEl) codeEl.textContent = selectedOpt.dataset.unitCode || selectedOpt.textContent.split('—')[0].trim();
        if (nameEl) nameEl.textContent = selectedOpt.dataset.assetName || '';
        if (specsEl) specsEl.textContent = selectedOpt.dataset.specs ? `(${selectedOpt.dataset.specs})` : '';

        detailsBox.classList.remove('d-none');
    }
}

// Initialize on page load for any already-selected units
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.asset-select').forEach(sel => {
        if (sel.value) onAssetUnitSelected(sel);
    });
});

// Asset Functions
function addAsset() {
    const container = document.getElementById('assetsContainer');
    const index = assetCount;
    
    let optionsHtml = '<option value="">-- Choose an Available Asset Unit --</option>';
    fixedAssetUnitsList.forEach(u => {
        const pName = u.parent_asset ? u.parent_asset.name : 'Asset';
        const pCat = u.parent_asset ? u.parent_asset.category : 'General';
        const detailStr = u.plate_number ? `Plate: ${u.plate_number}` : (u.serial_number ? `SN: ${u.serial_number}` : (u.brand ? `${u.brand} ${u.model || ''}` : 'In Store'));
        const searchKeywords = `${u.unit_code} ${pName} ${pCat} ${detailStr} ${u.brand || ''} ${u.model || ''} ${u.serial_number || ''} ${u.plate_number || ''}`.toLowerCase();
        optionsHtml += `<option value="${u.id}" data-category="${pCat}" data-search="${searchKeywords}" data-unit-code="${u.unit_code}" data-asset-name="${pName}" data-specs="${detailStr}">${u.unit_code} — ${pName} (${detailStr}) • [${pCat}]</option>`;
    });

    const html = `
        <div class="asset-entry border rounded p-3 mb-3 bg-light" data-index="${index}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0 fw-bold"><i class="fa-solid fa-barcode text-primary me-2"></i>Assigned Asset Unit #${index + 1}</h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-asset" onclick="removeAsset(${index})">
                    <i class="fa-solid fa-trash me-1"></i>Remove
                </button>
            </div>

            {{-- Per-Row Live Search Input --}}
            <div class="mb-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-primary"></i></span>
                    <input type="text" class="form-control form-control-sm border-start-0 asset-row-search" 
                           placeholder="🔍 Type unit code (e.g. COMP-1), serial, plate, or brand to search..." 
                           oninput="filterAssetOptions(this)">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearRowSearch(this)" title="Clear Search">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <div class="row g-2">
                <div class="col-12">
                    <label class="form-label small fw-bold mb-1">
                        Select Available Fixed Asset Unit <span class="badge bg-success ms-1">In Store</span>
                    </label>
                    <select name="fixed_asset_units[]" class="form-select asset-select font-monospace" onchange="onAssetUnitSelected(this)">
                        ${optionsHtml}
                    </select>
                    <div class="asset-match-count small text-muted mt-1" style="font-size: 0.75rem;"></div>
                </div>
            </div>

            {{-- Selected Unit Live Details Badge --}}
            <div class="selected-asset-details mt-2 p-2 bg-white rounded border d-none small">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="badge bg-dark font-monospace fs-6 me-2 selected-unit-code"></span>
                        <strong class="text-dark selected-asset-name"></strong>
                        <span class="text-muted ms-2 selected-asset-specs"></span>
                    </div>
                    <div>
                        <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Ready for Assignment</span>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', html);
    assetCount++;
    updateRemoveButtons();
}

function removeAsset(index) {
    const entry = document.querySelector(`.asset-entry[data-index="${index}"]`);
    if (entry) {
        entry.remove();
    }
    updateRemoveButtons();
}

// Education Functions
function addEducation() {
    const container = document.getElementById('educationContainer');
    const index = educationCount;
    
    const html = `
        <div class="education-entry border rounded p-3 mb-3 bg-light" data-index="${index}">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0"><i class="fa-solid fa-book me-2"></i>Education Record #${index + 1}</h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-education" onclick="removeEducation(${index})">
                    <i class="fa-solid fa-trash"></i> Remove
                </button>
            </div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Degree Level</label>
                    <select name="education[${index}][degree_level]" class="form-select">
                        <option value="">Select Degree</option>
                        <option value="PhD">PhD / Doctorate</option>
                        <option value="Master">Master's Degree</option>
                        <option value="Bachelor">Bachelor's Degree</option>
                        <option value="Diploma">Diploma</option>
                        <option value="Certificate">Certificate</option>
                        <option value="High School">High School</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Field of Study</label>
                    <input type="text" name="education[${index}][field_of_study]" class="form-control" 
                           placeholder="e.g., Civil Engineering">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Institution Name</label>
                    <input type="text" name="education[${index}][institution_name]" class="form-control" 
                           placeholder="e.g., Addis Ababa University">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Location</label>
                    <input type="text" name="education[${index}][location]" class="form-control" 
                           placeholder="e.g., Addis Ababa, Ethiopia">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="education[${index}][start_date]" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">End Date / Expected</label>
                    <input type="date" name="education[${index}][end_date]" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Grade / GPA</label>
                    <input type="text" name="education[${index}][grade_gpa]" class="form-control" 
                           placeholder="e.g., 3.8/4.0 or Distinction">
                </div>
                <div class="col-12">
                    <label class="form-label">Description / Achievements</label>
                    <textarea name="education[${index}][description]" class="form-control" rows="2" 
                              placeholder="Optional: Thesis title, honors, relevant coursework, etc."></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Certificate / Degree Photo <small class="text-muted">(Image: JPG, PNG - Max 5MB)</small></label>
                    <input type="file" name="education[${index}][certificate_photo]" class="form-control" 
                           accept="image/jpeg,image/png,image/jpg">
                    <small class="text-muted">Upload a photo of your certificate or degree</small>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', html);
    educationCount++;
    updateRemoveButtons();
}

function removeEducation(index) {
    const entry = document.querySelector(`.education-entry[data-index="${index}"]`);
    if (entry) {
        entry.remove();
    }
    updateRemoveButtons();
}

// Experience Functions
function addExperience() {
    const container = document.getElementById('experienceContainer');
    const index = experienceCount;
    
    const html = `
        <div class="experience-entry border rounded p-3 mb-3 bg-light" data-index="${index}">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0"><i class="fa-solid fa-building me-2"></i>Experience Record #${index + 1}</h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-experience" onclick="removeExperience(${index})">
                    <i class="fa-solid fa-trash"></i> Remove
                </button>
            </div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Job Title</label>
                    <input type="text" name="experience[${index}][job_title]" class="form-control" 
                           placeholder="e.g., Site Engineer">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Company Name</label>
                    <input type="text" name="experience[${index}][company_name]" class="form-control" 
                           placeholder="e.g., ABC Construction">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Location</label>
                    <input type="text" name="experience[${index}][location]" class="form-control" 
                           placeholder="e.g., Addis Ababa, Ethiopia">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="experience[${index}][start_date]" class="form-control">
                </div>
                <div class="col-md-5">
                    <label class="form-label">End Date</label>
                    <input type="date" name="experience[${index}][end_date]" class="form-control" id="exp_end_date_${index}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="experience[${index}][is_current]" class="form-check-input" 
                               id="is_current_${index}" value="1" onchange="toggleEndDate(${index})">
                        <label class="form-check-label" for="is_current_${index}">
                            Current
                        </label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Key Responsibilities</label>
                    <textarea name="experience[${index}][responsibilities]" class="form-control" rows="3" 
                              placeholder="Describe your main duties and achievements..."></textarea>
                </div>
                
                <div class="col-12">
                    <hr>
                    <h6 class="text-muted"><i class="fa-solid fa-user-check me-2"></i>Reference (Optional)</h6>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Reference Name</label>
                    <input type="text" name="experience[${index}][reference_name]" class="form-control" 
                           placeholder="e.g., John Doe">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Reference Phone</label>
                    <input type="text" name="experience[${index}][reference_phone]" class="form-control" 
                           placeholder="+251 911 234 567">
                </div>

                <div class="col-12">
                    <hr>
                    <h6 class="text-muted"><i class="fa-solid fa-certificate me-2"></i>Professional License (Optional)</h6>
                </div>
                <div class="col-md-6">
                    <label class="form-label">License Number</label>
                    <input type="text" name="experience[${index}][license_number]" class="form-control" 
                           placeholder="e.g., PE-12345">
                </div>
                <div class="col-md-6">
                    <label class="form-label">License Expiry Date</label>
                    <input type="date" name="experience[${index}][license_expiry]" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">License Document <small class="text-muted">(PDF or Image - Max 10MB)</small></label>
                    <input type="file" name="experience[${index}][license_document]" class="form-control" 
                           accept="application/pdf,image/jpeg,image/png,image/jpg">
                    <small class="text-muted">Upload professional license, certificate, or qualification document (PDF or Image)</small>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', html);
    experienceCount++;
    updateRemoveButtons();
}

function removeExperience(index) {
    const entry = document.querySelector(`.experience-entry[data-index="${index}"]`);
    if (entry) {
        entry.remove();
    }
    updateRemoveButtons();
}

function toggleEndDate(index) {
    const checkbox = document.getElementById(`is_current_${index}`);
    const endDateField = document.getElementById(`exp_end_date_${index}`);
    
    if (checkbox && endDateField) {
        endDateField.disabled = checkbox.checked;
        if (checkbox.checked) {
            endDateField.value = '';
        }
    }
}

function updateRemoveButtons() {
    // Show remove buttons only if there's more than one entry
    const educationEntries = document.querySelectorAll('.education-entry');
    educationEntries.forEach((entry, idx) => {
        const removeBtn = entry.querySelector('.remove-education');
        if (removeBtn) {
            removeBtn.style.display = educationEntries.length > 1 ? 'inline-block' : 'none';
        }
    });
    
    const experienceEntries = document.querySelectorAll('.experience-entry');
    experienceEntries.forEach((entry, idx) => {
        const removeBtn = entry.querySelector('.remove-experience');
        if (removeBtn) {
            removeBtn.style.display = experienceEntries.length > 1 ? 'inline-block' : 'none';
        }
    });
    
    const assetEntries = document.querySelectorAll('.asset-entry');
    assetEntries.forEach((entry, idx) => {
        const removeBtn = entry.querySelector('.remove-asset');
        if (removeBtn) {
            removeBtn.style.display = assetEntries.length > 1 ? 'inline-block' : 'none';
        }
    });
}
</script>

@endsection
