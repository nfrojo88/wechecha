@extends('layouts.app')

@section('title', 'Add Employee')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <h1 class="h3 mb-0">Add New Employee</h1>
</div>

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
            
            <!-- Hidden fields to preserve data across steps -->
            <input type="hidden" name="employee_code" value="{{ old('employee_code') }}">
            <input type="hidden" name="full_name" value="{{ old('full_name') }}">
            <input type="hidden" name="phone" value="{{ old('phone') }}">
            <input type="hidden" name="email" value="{{ old('email') }}">
            <input type="hidden" name="department" value="{{ old('department') }}">
            <input type="hidden" name="role_title" value="{{ old('role_title') }}">
            <input type="hidden" name="employment_type" value="{{ old('employment_type') }}">
            <input type="hidden" name="date_of_joining" value="{{ old('date_of_joining') }}">
            <input type="hidden" name="status" value="{{ old('status') }}">
            <input type="hidden" name="project_id" value="{{ old('project_id') }}">
            <input type="hidden" name="site_assignment" value="{{ old('site_assignment') }}">
            <input type="hidden" name="basic_salary" value="{{ old('basic_salary') }}">
            <input type="hidden" name="transport_allowance" value="{{ old('transport_allowance') }}">
            <input type="hidden" name="house_allowance" value="{{ old('house_allowance') }}">
            <input type="hidden" name="position_allowance" value="{{ old('position_allowance') }}">
            <input type="hidden" name="contract_type" value="{{ old('contract_type') }}">
            <input type="hidden" name="bank_name" value="{{ old('bank_name') }}">
            <input type="hidden" name="account_number" value="{{ old('account_number') }}">

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
                                    <option value="{{ $dept->name }}" @selected((session('employee_data.department') ?? old('department')) == $dept->name)>
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
                <h5 class="mb-4"><i class="fa-solid fa-computer text-info me-2"></i>Assign Assets & Equipment</h5>
                
                <p class="text-muted mb-4">Link materials and equipment (computers, tools, etc.) to this employee</p>

                <div id="assetsContainer">
                    @php
                        $savedAssets = session('employee_data.assets') ?? old('assets', [['product_id' => '', 'quantity' => 1]]);
                    @endphp
                    @foreach($savedAssets as $index => $asset)
                    <div class="asset-entry border rounded p-3 mb-3 bg-light" data-index="{{ $index }}">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><i class="fa-solid fa-box me-2"></i>Asset #{{ $index + 1 }}</h6>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-asset" onclick="removeAsset({{ $index }})" style="display: {{ count($savedAssets) > 1 ? 'inline-block' : 'none' }};">
                                <i class="fa-solid fa-trash"></i> Remove
                            </button>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Select Asset</label>
                                <select name="assets[{{ $index }}][product_id]" class="form-select asset-select">
                                    <option value="">-- Choose an Asset --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" @selected(($asset['product_id'] ?? '') == $product->id)>
                                            {{ $product->name }} ({{ $product->category }}) - Br {{ number_format($product->unit_cost ?? 0, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Quantity</label>
                                <input type="number" name="assets[{{ $index }}][quantity]" class="form-control" value="{{ $asset['quantity'] ?? 1 }}" min="1">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <button type="button" class="btn btn-outline-info btn-sm" onclick="addAsset()">
                    <i class="fa-solid fa-plus me-2"></i>Add Another Asset
                </button>

                <div class="alert alert-info mt-3">
                    <small>
                        <i class="fa-solid fa-lightbulb me-2"></i>
                        Select items to assign to this employee. You can search within the dropdown. Skip or leave blank if no assets are assigned.
                    </small>
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
                    <button type="button" class="btn btn-outline-secondary" onclick="previousStep()">
                        <i class="fa-solid fa-arrow-left me-2"></i>Previous
                    </button>
                    @endif
                </div>
                <div>
                    @if(request()->get('step', 1) < 6)
                    <button type="button" class="btn btn-primary" onclick="nextStep(); return false;">
                        Next Step<i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                    @else
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-check me-2"></i>Complete Registration
                    </button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let educationCount = {{ is_array(session('employee_data.education')) ? count(session('employee_data.education')) : 1 }};
let experienceCount = {{ is_array(session('employee_data.experience')) ? count(session('employee_data.experience')) : 1 }};
let assetCount = {{ is_array(session('employee_data.assets')) ? count(session('employee_data.assets')) : 1 }};

// Provide the products array to javascript for dynamic rows
const productsList = @json($products);

function nextStep() {
    const form = document.getElementById('employeeForm');
    const currentStepInput = document.querySelector('input[name="step"]');
    const currentStep = parseInt(currentStepInput.value);
    
    // For step 5 and 6, check if any data was entered
    if (currentStep === 5) {
        // Education step - check if any fields have values
        const educationInputs = document.querySelectorAll('[name^="education[0]"]');
        let hasAnyValue = false;
        
        educationInputs.forEach(input => {
            if (input.type !== 'file' && input.value && input.value.trim() !== '') {
                hasAnyValue = true;
            }
        });
    }
    
    // Set action to next
    document.getElementById('formAction').value = 'next';
    
    // Show loading indicator
    const btn = event.target;
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Loading...';
    }
    
    form.submit();
}

function previousStep() {
    const form = document.getElementById('employeeForm');
    const currentStepInput = document.querySelector('input[name="step"]');
    const currentStep = parseInt(currentStepInput.value);
    
    if(currentStep > 1) {
        // Set action to previous
        document.getElementById('formAction').value = 'previous';
        form.submit();
    }
}

// Asset Functions
function addAsset() {
    const container = document.getElementById('assetsContainer');
    const index = assetCount;
    
    let optionsHtml = '<option value="">-- Choose an Asset --</option>';
    productsList.forEach(p => {
        const cost = p.unit_cost ? parseFloat(p.unit_cost).toFixed(2) : '0.00';
        optionsHtml += `<option value="${p.id}">${p.name} (${p.category || 'N/A'}) - Br ${cost}</option>`;
    });

    const html = `
        <div class="asset-entry border rounded p-3 mb-3 bg-light" data-index="${index}">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0"><i class="fa-solid fa-box me-2"></i>Asset #${index + 1}</h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-asset" onclick="removeAsset(${index})">
                    <i class="fa-solid fa-trash"></i> Remove
                </button>
            </div>
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Select Asset</label>
                    <select name="assets[${index}][product_id]" class="form-select asset-select">
                        ${optionsHtml}
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="assets[${index}][quantity]" class="form-control" value="1" min="1">
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
