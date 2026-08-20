<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Employee::class);

        $statusFilter = $request->get('approval_status', 'all');
        $query = Employee::with(['project', 'gmApprovedBy', 'gmRejectedBy'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('role_title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($statusFilter === 'approved') {
            $query->where('is_approved_by_gm', true);
        } elseif ($statusFilter === 'pending') {
            $query->where('is_approved_by_gm', false)
                  ->where(function($q) {
                      $q->whereNull('gm_approval_status')->orWhere('gm_approval_status', '!=', 'rejected');
                  });
        } elseif ($statusFilter === 'rejected') {
            $query->where('gm_approval_status', 'rejected');
        }

        $employees = $query->paginate(20)->withQueryString();

        // Counts for tabs/badges
        $counts = [
            'all'      => Employee::count(),
            'approved' => Employee::where('is_approved_by_gm', true)->count(),
            'pending'  => Employee::where('is_approved_by_gm', false)->where(function($q) {
                            $q->whereNull('gm_approval_status')->orWhere('gm_approval_status', '!=', 'rejected');
                          })->count(),
            'rejected' => Employee::where('gm_approval_status', 'rejected')->count(),
        ];

        $departments = \App\Models\Department::where('is_active', true)->pluck('name');

        return view('hr.employees.index', compact('employees', 'counts', 'departments'));
    }

    public function pendingApproval(Request $request)
    {
        $query = Employee::where('is_approved_by_gm', false)
            ->where(function($q) {
                $q->whereNull('gm_approval_status')->orWhere('gm_approval_status', '!=', 'rejected');
            })
            ->with(['project']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%")
                  ->orWhere('role_title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        $pendingEmployees = $query->latest()->paginate(25)->withQueryString();
        $departments = Employee::whereNotNull('department')->distinct()->pluck('department');

        return view('hr.employees.pending_approval', compact('pendingEmployees', 'departments'));
    }

    public function approve(Request $request, Employee $employee)
    {
        $employee->update([
            'is_approved_by_gm'   => true,
            'gm_approval_status'  => 'approved',
            'gm_approved_at'      => now(),
            'gm_approved_by'      => auth()->id(),
            'gm_rejection_reason' => null,
        ]);

        return back()->with('success', "Employee {$employee->full_name} ({$employee->employee_code}) has been approved by GM successfully!");
    }

    public function reject(Request $request, Employee $employee)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:3|max:1000',
        ]);

        $employee->update([
            'is_approved_by_gm'   => false,
            'gm_approval_status'  => 'rejected',
            'gm_rejection_reason' => $request->rejection_reason,
            'gm_rejected_at'      => now(),
            'gm_rejected_by'      => auth()->id(),
        ]);

        return back()->with('success', "Employee {$employee->full_name} ({$employee->employee_code}) was rejected and sent back to HR Officer for correction.");
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        $count = Employee::whereIn('id', $request->employee_ids)->update([
            'is_approved_by_gm'   => true,
            'gm_approval_status'  => 'approved',
            'gm_approved_at'      => now(),
            'gm_approved_by'      => auth()->id(),
            'gm_rejection_reason' => null,
        ]);

        return back()->with('success', "{$count} employee(s) approved by GM successfully!");
    }

    public function bulkReject(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
            'rejection_reason' => 'required|string|min:3|max:1000',
        ]);

        $count = Employee::whereIn('id', $request->employee_ids)->update([
            'is_approved_by_gm'   => false,
            'gm_approval_status'  => 'rejected',
            'gm_rejection_reason' => $request->rejection_reason,
            'gm_rejected_at'      => now(),
            'gm_rejected_by'      => auth()->id(),
        ]);

        return back()->with('success', "{$count} employee(s) rejected and returned to HR Officer with correction reason.");
    }

    public function create()
    {
        Gate::authorize('create', Employee::class);
        $projects = Project::where('status', '!=', 'cancelled')->get();
        $departments = \App\Models\Department::where('is_active', true)->get();
        $products = \App\Models\Product::where('category', 'Fixed Asset')
            ->where('current_location', 'Main Store')
            ->where('asset_status', 'Available')
            ->get();
        
        // Available Centralized Fixed Asset Units (In Store)
        $fixedAssetUnits = \App\Models\FixedAssetUnit::with('parentAsset')
            ->where('status', \App\Models\FixedAssetUnit::STATUS_IN_STORE)
            ->orderBy('unit_code')
            ->get();

        return view('hr.employees.create', compact('projects', 'products', 'departments', 'fixedAssetUnits'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Employee::class);

        $currentStep = $request->input('step', 1);
        $action = $request->input('action', 'next');

        // If navigating steps (not final submission)
        if ($currentStep < 6 || $action === 'previous') {
            // Validate current step only if going forward
            if ($action === 'next') {
                $this->validateStep($request, $currentStep);
            }
            
            // Store data in session
            session([
                'employee_data.employee_code' => $request->employee_code ?? session('employee_data.employee_code'),
                'employee_data.full_name' => $request->full_name ?? session('employee_data.full_name'),
                'employee_data.phone' => $request->phone ?? session('employee_data.phone'),
                'employee_data.email' => $request->email ?? session('employee_data.email'),
                'employee_data.department' => $request->department ?? session('employee_data.department'),
                'employee_data.role_title' => $request->role_title ?? session('employee_data.role_title'),
                'employee_data.employment_type' => $request->employment_type ?? session('employee_data.employment_type'),
                'employee_data.date_of_joining' => $request->date_of_joining ?? session('employee_data.date_of_joining'),
                'employee_data.status' => $request->status ?? session('employee_data.status'),
                'employee_data.project_id' => $request->project_id ?? session('employee_data.project_id'),
                'employee_data.site_assignment' => $request->site_assignment ?? session('employee_data.site_assignment'),
                'employee_data.basic_salary' => $request->basic_salary ?? session('employee_data.basic_salary'),
                'employee_data.transport_allowance' => $request->transport_allowance ?? session('employee_data.transport_allowance'),
                'employee_data.house_allowance' => $request->house_allowance ?? session('employee_data.house_allowance'),
                'employee_data.position_allowance' => $request->position_allowance ?? session('employee_data.position_allowance'),
                'employee_data.contract_type' => $request->contract_type ?? session('employee_data.contract_type'),
                'employee_data.bank_name' => $request->bank_name ?? session('employee_data.bank_name'),
                'employee_data.account_number' => $request->account_number ?? session('employee_data.account_number'),
                'employee_data.device_user_id' => $request->device_user_id ?? session('employee_data.device_user_id'),
                'employee_data.assets' => $request->assets ?? session('employee_data.assets'),
                'employee_data.fixed_asset_units' => $request->fixed_asset_units ?? session('employee_data.fixed_asset_units'),
                'employee_data.education' => $request->education ?? session('employee_data.education'),
                'employee_data.experience' => $request->experience ?? session('employee_data.experience'),
            ]);

            // Handle file uploads in session for steps 5 & 6
            if ($currentStep == 5) {
                $this->storeEducationFilesInSession($request);
            }
            
            if ($currentStep == 6) {
                $this->storeExperienceFilesInSession($request);
            }

            $nextStep = $action === 'previous' ? max(1, $currentStep - 1) : $currentStep + 1;

            return redirect()->route('employees.create', ['step' => $nextStep])
                ->withInput($request->all());
        }

        if ($request->has('education') && is_array($request->education)) {
            $filteredEdu = array_filter($request->education, function($edu) {
                return !empty($edu['degree_level']) || !empty($edu['field_of_study']) || !empty($edu['institution_name']);
            });
            $request->merge(['education' => empty($filteredEdu) ? null : array_values($filteredEdu)]);
        }

        if ($request->has('experience') && is_array($request->experience)) {
            $filteredExp = array_filter($request->experience, function($exp) {
                return !empty($exp['job_title']) || !empty($exp['company_name']) || !empty($exp['start_date']);
            });
            $request->merge(['experience' => empty($filteredExp) ? null : array_values($filteredExp)]);
        }

        // Final submission - validate all fields
        $validated = $request->validate([
            'employee_code'   => 'required|string|unique:employees',
            'full_name'       => 'required|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:255',
            'role_title'      => 'nullable|string|max:255',
            'department'      => 'nullable|string|max:100',
            'project_id'      => 'nullable|exists:projects,id',
            'site_assignment' => 'nullable|string|max:100',
            'employment_type' => 'nullable|in:permanent,contract,daily',
            'date_of_joining' => 'nullable|date',
            'basic_salary'    => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'house_allowance' => 'nullable|numeric|min:0',
            'position_allowance' => 'nullable|numeric|min:0',
            'contract_type' => 'nullable|string',
            'status'          => 'nullable|in:active,suspended,terminated',
            'bank_name'       => 'nullable|string|max:255',
            'account_number'  => 'nullable|string|max:50',
            'notes'           => 'nullable|string',
            'guarantee_letter' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:10240',
            'assets'          => 'nullable|array',
            'assets.*.product_id' => 'required_with:assets|exists:products,id',
            'assets.*.quantity' => 'nullable|integer|min:1',
            'education'       => 'nullable|array',
            'education.*.degree_level' => 'nullable|string',
            'education.*.field_of_study' => 'nullable|string',
            'education.*.institution_name' => 'nullable|string',
            'education.*.certificate_photo' => 'nullable|file|mimes:pdf,jpeg,png,jpg,webp|max:10240',
            'experience'      => 'nullable|array',
            'experience.*.job_title' => 'nullable|string',
            'experience.*.company_name' => 'nullable|string',
            'experience.*.start_date' => 'nullable|date',
            'experience.*.license_document' => 'nullable|file|mimes:pdf,jpeg,png,jpg,webp|max:10240',
            'device_user_id' => 'nullable|string|max:100',
        ]);

        // Apply defaults for optional fields
        $validated['employment_type']    = $validated['employment_type'] ?? 'permanent';
        $validated['status']             = $validated['status'] ?? 'active';
        $validated['basic_salary']       = $validated['basic_salary'] ?? 0;
        $validated['transport_allowance'] = $validated['transport_allowance'] ?? 0;
        $validated['house_allowance'] = $validated['house_allowance'] ?? 0;
        $validated['position_allowance'] = $validated['position_allowance'] ?? 0;

        // Handle guarantee letter upload via Cloudinary
        $guaranteeLetterPath = null;
        if ($request->hasFile('guarantee_letter')) {
            $cloudinary = app(\App\Services\CloudinaryService::class);
            $guaranteeLetterPath = $cloudinary->upload($request->file('guarantee_letter'), 'guarantee_letters');
            $validated['guarantee_letter'] = $guaranteeLetterPath;
            $validated['guarantee_letter_submitted_at'] = now();
        }

        // Create User account for the new employee (using firstOrCreate to prevent duplicate key errors)
        $userEmail = $validated['email'] ?? strtolower($validated['employee_code']) . '@construct-pro.com';
        
        try {
            $user = \App\Models\User::firstOrCreate(
                ['email' => $userEmail],
                [
                    'name' => $validated['full_name'],
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'is_active' => true,
                ]
            );
            
            $validated['user_id'] = $user->id;

            // Strip nested arrays before Eloquent creation
            $employeeData = \Illuminate\Support\Arr::except($validated, ['assets', 'education', 'experience']);
            $employee = Employee::create($employeeData);

            // Attach Centralized Fixed Asset Units if selected
            $fixedAssetUnitIds = $request->input('fixed_asset_units', []) ?: (session('employee_data.fixed_asset_units') ?? []);
            if (!empty($fixedAssetUnitIds)) {
                foreach ($fixedAssetUnitIds as $unitId) {
                    if (!empty($unitId)) {
                        $unit = \App\Models\FixedAssetUnit::find($unitId);
                        if ($unit && $unit->isAvailable()) {
                            $unit->assignToEmployee($employee->id, auth()->id(), 'Assigned during employee registration');
                        }
                    }
                }
            }

            // Also support assets array from Step 4
            if (!empty($request->assets)) {
                foreach ($request->assets as $assetInfo) {
                    // Check if fixed_asset_unit_id was passed
                    if (!empty($assetInfo['fixed_asset_unit_id'])) {
                        $unit = \App\Models\FixedAssetUnit::find($assetInfo['fixed_asset_unit_id']);
                        if ($unit && $unit->isAvailable()) {
                            $unit->assignToEmployee($employee->id, auth()->id(), 'Assigned during employee registration');
                        }
                    } elseif (!empty($assetInfo['product_id'])) {
                        \DB::table('employee_assets')->insert([
                            'employee_id' => $employee->id,
                            'product_id' => $assetInfo['product_id'],
                            'quantity' => $assetInfo['quantity'] ?? 1,
                            'assigned_date' => now(),
                            'status' => 'assigned',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // Save Education records (check request then session fallback)
            $educationData = $request->education ?? session('employee_data.education');
            if (!empty($educationData)) {
                $this->saveEducationRecords($employee, $educationData);
            }

            // Save Experience records (check request then session fallback)
            $experienceData = $request->experience ?? session('employee_data.experience');
            if (!empty($experienceData)) {
                $this->saveExperienceRecords($employee, $experienceData);
            }

            // Clear session
            session()->forget('employee_data');
            session()->forget('employee_education_files');
            session()->forget('employee_experience_files');

            // Send Welcome SMS Notification if phone number is provided
            if (!empty($employee->phone)) {
                try {
                    $smsService = app(\App\Services\SmsEthiopiaService::class);
                    $message = "Welcome {$employee->full_name}! You have been successfully registered in the Construct-Pro ERP system. Your Employee Code is: {$employee->employee_code}.";
                    $smsService->sendNotification($employee->phone, $message);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send welcome SMS: ' . $e->getMessage());
                }
            }

            // Send SMS Notification to GM for approval
            try {
                $gmUsers = \App\Models\User::role('gm')->get();
                $smsService = app(\App\Services\SmsEthiopiaService::class);
                $gmMessage = "New Employee {$employee->full_name} registered. Please login to approve.";
                foreach ($gmUsers as $gm) {
                    if ($gm->phone) {
                        $smsService->sendNotification($gm->phone, $gmMessage);
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send GM SMS: ' . $e->getMessage());
            }

            return redirect()->route('employees.create')
                ->with('success', "Registration successful! Employee \"{$employee->full_name}\" registered successfully.");
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Employee registration failed: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return back()->withInput()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }

    private function validateStep(Request $request, $step)
    {
        switch ($step) {
            case 1:
                $request->validate([
                    'employee_code' => 'required|string',
                    'full_name' => 'required|string|max:255',
                    'phone' => 'required|string|max:20',
                    'department' => 'nullable|string|max:100',
                    'role_title' => 'nullable|string|max:255',
                ]);
                break;
            case 2:
                // Get values from session or request
                $employmentType = $request->employment_type ?? session('employee_data.employment_type', 'permanent');
                $dateOfJoining = $request->date_of_joining ?? session('employee_data.date_of_joining', date('Y-m-d'));
                $status = $request->status ?? session('employee_data.status', 'active');
                
                $request->merge([
                    'employment_type' => $employmentType,
                    'date_of_joining' => $dateOfJoining,
                    'status' => $status,
                ]);
                
                $request->validate([
                    'employment_type' => 'nullable|in:permanent,contract,daily',
                    'date_of_joining' => 'nullable|date',
                    'status' => 'nullable|in:active,suspended,terminated',
                ]);
                break;
            case 3:
                // Get values from session or request
                $basicSalary = $request->basic_salary ?? session('employee_data.basic_salary', 0);
                $transportAllowance = $request->transport_allowance ?? session('employee_data.transport_allowance', 0);
                $houseAllowance = $request->house_allowance ?? session('employee_data.house_allowance', 0);
                $positionAllowance = $request->position_allowance ?? session('employee_data.position_allowance', 0);
                $contractType = $request->contract_type ?? session('employee_data.contract_type', 'Full-Time');
                
                $request->merge([
                    'basic_salary' => $basicSalary,
                    'transport_allowance' => $transportAllowance,
                    'house_allowance' => $houseAllowance,
                    'position_allowance' => $positionAllowance,
                    'contract_type' => $contractType,
                ]);
                
                $request->validate([
                    'basic_salary' => 'nullable|numeric|min:0',
                    'transport_allowance' => 'nullable|numeric|min:0',
                    'house_allowance' => 'nullable|numeric|min:0',
                    'position_allowance' => 'nullable|numeric|min:0',
                    'contract_type' => 'nullable|string',
                ]);
                break;
            case 4:
                // Assets step - no required validation, optional
                break;
            case 5:
                // Education step - completely optional
                // Only validate if education data has actual values
                if ($request->has('education') && is_array($request->education)) {
                    $hasEducationData = false;
                    
                    // Check if any education entry has data
                    foreach ($request->education as $edu) {
                        if (!empty($edu['degree_level']) || !empty($edu['field_of_study']) || !empty($edu['institution_name'])) {
                            $hasEducationData = true;
                            break;
                        }
                    }
                    
                    // Only validate if there's actual data
                    if ($hasEducationData) {
                        $request->validate([
                            'education' => 'array',
                            'education.*.degree_level' => 'required_with:education.*.field_of_study,education.*.institution_name|string',
                            'education.*.field_of_study' => 'required_with:education.*.degree_level,education.*.institution_name|string',
                            'education.*.institution_name' => 'required_with:education.*.degree_level,education.*.field_of_study|string',
                            'education.*.certificate_photo' => 'nullable|file|mimes:pdf,jpeg,png,jpg,webp|max:10240',
                        ]);
                    }
                }
                // If no education data, just pass through - it's optional
                break;
            case 6:
                // Experience step - completely optional
                // Only validate if experience data has actual values
                if ($request->has('experience') && is_array($request->experience)) {
                    $hasExperienceData = false;
                    
                    // Check if any experience entry has data
                    foreach ($request->experience as $exp) {
                        if (!empty($exp['job_title']) || !empty($exp['company_name']) || !empty($exp['start_date'])) {
                            $hasExperienceData = true;
                            break;
                        }
                    }
                    
                    // Only validate if there's actual data
                    if ($hasExperienceData) {
                        $request->validate([
                            'experience' => 'array',
                            'experience.*.job_title' => 'required_with:experience.*.company_name,experience.*.start_date|string',
                            'experience.*.company_name' => 'required_with:experience.*.job_title,experience.*.start_date|string',
                            'experience.*.start_date' => 'required_with:experience.*.job_title,experience.*.company_name|date',
                            'experience.*.license_document' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:10240',
                        ]);
                    }
                }
                // If no experience data, just pass through - it's optional
                break;
        }
    }

    /**
     * Store education certificate photos in session temporarily
     */
    private function storeEducationFilesInSession(Request $request)
    {
        try {
            $filesData = session('employee_education_files', []);
            $cloudinary = app(\App\Services\CloudinaryService::class);
            
            if ($request->hasFile('education')) {
                foreach ($request->file('education') as $index => $educationFiles) {
                    if (isset($educationFiles['certificate_photo']) && $educationFiles['certificate_photo'] instanceof \Illuminate\Http\UploadedFile) {
                        $file = $educationFiles['certificate_photo'];
                        if ($file->isValid()) {
                            $filesData[$index] = $cloudinary->upload($file, 'employee_certificates');
                        }
                    }
                }
            }
            
            session(['employee_education_files' => $filesData]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('storeEducationFilesInSession error: ' . $e->getMessage());
        }
    }

    /**
     * Store experience license documents in session temporarily
     */
    private function storeExperienceFilesInSession(Request $request)
    {
        try {
            $filesData = session('employee_experience_files', []);
            $cloudinary = app(\App\Services\CloudinaryService::class);
            
            if ($request->hasFile('experience')) {
                foreach ($request->file('experience') as $index => $experienceFiles) {
                    if (isset($experienceFiles['license_document']) && $experienceFiles['license_document'] instanceof \Illuminate\Http\UploadedFile) {
                        $file = $experienceFiles['license_document'];
                        if ($file->isValid()) {
                            $filesData[$index] = $cloudinary->upload($file, 'employee_licenses');
                        }
                    }
                }
            }
            
            session(['employee_experience_files' => $filesData]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('storeExperienceFilesInSession error: ' . $e->getMessage());
        }
    }

    /**
     * Save education records with file uploads
     */
    private function saveEducationRecords(Employee $employee, $educationData)
    {
        if (empty($educationData)) {
            return; // No education data, skip
        }

        $sessionFiles = session('employee_education_files', []);
        
        foreach ($educationData as $index => $education) {
            // Skip empty entries
            if (empty($education['degree_level']) || empty($education['field_of_study']) || empty($education['institution_name'])) {
                continue;
            }

            $certificatePath = null;
            
            // Check if there's a file in the request
            if (request()->hasFile("education.{$index}.certificate_photo")) {
                $file = request()->file("education.{$index}.certificate_photo");
                $cloudinary = app(\App\Services\CloudinaryService::class);
                $certificatePath = $cloudinary->upload($file, 'employee_certificates');
            } 
            // Check if file was stored in session from previous step
            elseif (isset($sessionFiles[$index])) {
                $tempPath = $sessionFiles[$index];
                if (\Illuminate\Support\Str::startsWith($tempPath, ['http://', 'https://'])) {
                    $certificatePath = $tempPath;
                } else {
                    $newPath = str_replace('temp/education/', 'employee_certificates/', $tempPath);
                    if (\Storage::disk('public')->exists($tempPath)) {
                        \Storage::disk('public')->move($tempPath, $newPath);
                        $certificatePath = $newPath;
                    } else {
                        $certificatePath = $tempPath;
                    }
                }
            }
            
            \App\Models\EmployeeEducation::create([
                'employee_id' => $employee->id,
                'degree_level' => $education['degree_level'],
                'field_of_study' => $education['field_of_study'],
                'institution_name' => $education['institution_name'],
                'location' => $education['location'] ?? null,
                'start_date' => $education['start_date'] ?? null,
                'end_date' => $education['end_date'] ?? null,
                'grade_gpa' => $education['grade_gpa'] ?? null,
                'description' => $education['description'] ?? null,
                'certificate_photo' => $certificatePath,
                'is_verified' => false,
            ]);
        }
    }

    /**
     * Save experience records with file uploads
     */
    private function saveExperienceRecords(Employee $employee, $experienceData)
    {
        if (empty($experienceData)) {
            return; // No experience data, skip
        }

        $sessionFiles = session('employee_experience_files', []);
        
        foreach ($experienceData as $index => $experience) {
            // Skip empty entries
            if (empty($experience['job_title']) || empty($experience['company_name'])) {
                continue;
            }

            $licensePath = null;
            
            // Check if there's a file in the request
            if (request()->hasFile("experience.{$index}.license_document")) {
                $file = request()->file("experience.{$index}.license_document");
                $cloudinary = app(\App\Services\CloudinaryService::class);
                $licensePath = $cloudinary->upload($file, 'employee_licenses');
            }
            // Check if file was stored in session from previous step
            elseif (isset($sessionFiles[$index])) {
                $tempPath = $sessionFiles[$index];
                if (\Illuminate\Support\Str::startsWith($tempPath, ['http://', 'https://'])) {
                    $licensePath = $tempPath;
                } else {
                    $newPath = str_replace('temp/experience/', 'employee_licenses/', $tempPath);
                    if (\Storage::disk('public')->exists($tempPath)) {
                        \Storage::disk('public')->move($tempPath, $newPath);
                        $licensePath = $newPath;
                    } else {
                        $licensePath = $tempPath;
                    }
                }
            }
            
            $isCurrent = isset($experience['is_current']) && $experience['is_current'] == '1';
            
            \App\Models\EmployeeExperience::create([
                'employee_id' => $employee->id,
                'job_title' => $experience['job_title'],
                'company_name' => $experience['company_name'],
                'location' => $experience['location'] ?? null,
                'start_date' => $experience['start_date'] ?? now(),
                'end_date' => $isCurrent ? null : ($experience['end_date'] ?? null),
                'is_current' => $isCurrent,
                'responsibilities' => $experience['responsibilities'] ?? null,
                'reference_name' => $experience['reference_name'] ?? null,
                'reference_phone' => $experience['reference_phone'] ?? null,
                'license_document' => $licensePath,
                'license_number' => $experience['license_number'] ?? null,
                'license_expiry' => $experience['license_expiry'] ?? null,
            ]);
        }
    }

    public function show(Employee $employee)
    {
        Gate::authorize('view', $employee);
        $employee->load([
            'project', 
            'payrolls' => fn($q) => $q->latest()->limit(12),
            'education',
            'experience',
            'activeAssets.product',
            'assignedFixedAssets.parentAsset',
            'fixedAssetAssignments.unit.parentAsset',
            'fixedAssetAssignments.assigner',
            'fixedAssetAssignments.receiver'
        ]);
        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        Gate::authorize('update', $employee);
        $employee->load([
            'project',
            'education',
            'experience',
            'assignedFixedAssets.parentAsset',
        ]);
        $projects     = Project::where('status', '!=', 'cancelled')->get();
        $departments  = \App\Models\Department::where('is_active', true)->get();
        $products     = \App\Models\Product::where('category', 'Fixed Asset')
            ->where('current_location', 'Main Store')
            ->where('asset_status', 'Available')
            ->get();
        
        // Available Centralized Fixed Asset Units (In Store OR currently assigned to this employee)
        $fixedAssetUnits = \App\Models\FixedAssetUnit::with('parentAsset')
            ->where(function($q) use ($employee) {
                $q->where('status', \App\Models\FixedAssetUnit::STATUS_IN_STORE)
                  ->orWhere('assigned_to_employee_id', $employee->id);
            })
            ->orderBy('unit_code')
            ->get();

        return view('hr.employees.edit', compact('employee', 'projects', 'products', 'departments', 'fixedAssetUnits'));
    }

    public function update(Request $request, Employee $employee)
    {
        Gate::authorize('update', $employee);

        $validated = $request->validate([
            'employee_code'        => 'required|string|unique:employees,employee_code,'.$employee->id,
            'full_name'            => 'required|string|max:255',
            'phone'                => 'nullable|string|max:20',
            'email'                => 'nullable|email|max:255',
            'role_title'           => 'nullable|string|max:255',
            'department'           => 'nullable|string|max:100',
            'project_id'           => 'nullable|exists:projects,id',
            'site_assignment'      => 'nullable|string|max:100',
            'employment_type'      => 'required|in:permanent,contract,daily',
            'contract_type'        => 'nullable|string',
            'date_of_joining'      => 'required|date',
            'basic_salary'         => 'required|numeric|min:0',
            'transport_allowance'  => 'nullable|numeric|min:0',
            'house_allowance'      => 'nullable|numeric|min:0',
            'position_allowance'   => 'nullable|numeric|min:0',
            'bank_name'            => 'nullable|string|max:255',
            'account_number'       => 'nullable|string|max:100',
            'status'               => 'required|in:active,suspended,terminated',
            'notes'                => 'nullable|string',
            'device_user_id'       => 'nullable|string|max:100',
            'guarantee_letter'     => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:10240',
            'fixed_asset_units'    => 'nullable|array',
            'education'            => 'nullable|array',
            'experience'           => 'nullable|array',
        ]);

        // Handle guarantee letter upload if provided
        if ($request->hasFile('guarantee_letter')) {
            $letterPath = \App\Services\FileUploadService::upload($request->file('guarantee_letter'), 'guarantee_letters');
            $validated['guarantee_letter'] = $letterPath;
            $validated['guarantee_letter_submitted_at'] = now();
        }

        // If employee was rejected by GM, resubmitting clears rejection and queues for GM review
        if ($employee->gm_approval_status === 'rejected') {
            $validated['gm_approval_status'] = 'pending';
            $validated['is_approved_by_gm'] = false;
            $validated['gm_rejection_reason'] = null;
        }

        // Check if status changed to terminated
        $wasTerminated = $employee->status !== 'terminated' && $validated['status'] === 'terminated';

        // Strip non-model attributes
        $employeeData = \Illuminate\Support\Arr::except($validated, ['fixed_asset_units', 'education', 'experience']);
        $employee->update($employeeData);

        // ── Sync Fixed Asset Units ──────────────────────────────────────────
        $currentAssignedIds = $employee->assignedFixedAssets()->pluck('id')->toArray();
        $newSelectedIds = array_filter(array_map('intval', $request->input('fixed_asset_units', [])));

        // Unassign units that were removed
        $toUnassign = array_diff($currentAssignedIds, $newSelectedIds);
        foreach ($toUnassign as $uId) {
            $unit = \App\Models\FixedAssetUnit::find($uId);
            if ($unit && $unit->assigned_to_employee_id == $employee->id) {
                $unit->returnToStore(auth()->id(), 'Unassigned during employee profile update');
            }
        }

        // Assign newly selected units
        $toAssign = array_diff($newSelectedIds, $currentAssignedIds);
        foreach ($toAssign as $uId) {
            $unit = \App\Models\FixedAssetUnit::find($uId);
            if ($unit && ($unit->isAvailable() || $unit->assigned_to_employee_id == $employee->id)) {
                $unit->assignToEmployee($employee->id, auth()->id(), 'Assigned during employee profile update');
            }
        }

        // ── Sync Education Records ──────────────────────────────────────────
        if ($request->has('education') && is_array($request->education)) {
            $submittedEduIds = [];
            foreach ($request->education as $index => $eduData) {
                if (empty($eduData['degree_level']) && empty($eduData['field_of_study']) && empty($eduData['institution_name'])) {
                    continue;
                }

                $certPath = null;
                if ($request->hasFile("education.{$index}.certificate_photo")) {
                    $certPath = \App\Services\FileUploadService::upload($request->file("education.{$index}.certificate_photo"), 'employee_certificates');
                }

                $eduPayload = [
                    'employee_id'      => $employee->id,
                    'degree_level'     => $eduData['degree_level'] ?? 'Bachelor',
                    'field_of_study'   => $eduData['field_of_study'] ?? '',
                    'institution_name' => $eduData['institution_name'] ?? '',
                    'location'         => $eduData['location'] ?? null,
                    'start_date'       => $eduData['start_date'] ?? null,
                    'end_date'         => $eduData['end_date'] ?? null,
                    'grade_gpa'        => $eduData['grade_gpa'] ?? null,
                    'description'      => $eduData['description'] ?? null,
                ];
                if ($certPath) {
                    $eduPayload['certificate_photo'] = $certPath;
                }

                if (!empty($eduData['id'])) {
                    $eduRecord = \App\Models\EmployeeEducation::where('employee_id', $employee->id)->find($eduData['id']);
                    if ($eduRecord) {
                        $eduRecord->update($eduPayload);
                        $submittedEduIds[] = $eduRecord->id;
                        continue;
                    }
                }

                $newEdu = \App\Models\EmployeeEducation::create($eduPayload);
                $submittedEduIds[] = $newEdu->id;
            }

            // Remove education records deleted in UI
            if (!empty($submittedEduIds)) {
                \App\Models\EmployeeEducation::where('employee_id', $employee->id)->whereNotIn('id', $submittedEduIds)->delete();
            }
        }

        // ── Sync Experience Records ─────────────────────────────────────────
        if ($request->has('experience') && is_array($request->experience)) {
            $submittedExpIds = [];
            foreach ($request->experience as $index => $expData) {
                if (empty($expData['job_title']) && empty($expData['company_name'])) {
                    continue;
                }

                $licenseDocPath = null;
                if ($request->hasFile("experience.{$index}.license_document")) {
                    $licenseDocPath = \App\Services\FileUploadService::upload($request->file("experience.{$index}.license_document"), 'employee_licenses');
                }

                $isCurrent = isset($expData['is_current']) && $expData['is_current'] == '1';

                $expPayload = [
                    'employee_id'      => $employee->id,
                    'job_title'        => $expData['job_title'] ?? '',
                    'company_name'     => $expData['company_name'] ?? '',
                    'location'         => $expData['location'] ?? null,
                    'start_date'       => $expData['start_date'] ?? now(),
                    'end_date'         => $isCurrent ? null : ($expData['end_date'] ?? null),
                    'is_current'       => $isCurrent,
                    'responsibilities' => $expData['responsibilities'] ?? null,
                    'reference_name'   => $expData['reference_name'] ?? null,
                    'reference_phone'  => $expData['reference_phone'] ?? null,
                    'license_number'   => $expData['license_number'] ?? null,
                    'license_expiry'   => $expData['license_expiry'] ?? null,
                ];
                if ($licenseDocPath) {
                    $expPayload['license_document'] = $licenseDocPath;
                }

                if (!empty($expData['id'])) {
                    $expRecord = \App\Models\EmployeeExperience::where('employee_id', $employee->id)->find($expData['id']);
                    if ($expRecord) {
                        $expRecord->update($expPayload);
                        $submittedExpIds[] = $expRecord->id;
                        continue;
                    }
                }

                $newExp = \App\Models\EmployeeExperience::create($expPayload);
                $submittedExpIds[] = $newExp->id;
            }

            // Remove experience records deleted in UI
            if (!empty($submittedExpIds)) {
                \App\Models\EmployeeExperience::where('employee_id', $employee->id)->whereNotIn('id', $submittedExpIds)->delete();
            }
        }

        if ($wasTerminated) {
            // Flag all currently assigned assets for return approval
            \App\Models\EmployeeAsset::where('employee_id', $employee->id)
                ->whereIn('status', ['assigned', 'in_use'])
                ->update(['return_status' => 'pending_approval']);
        }

        if ($employee->wasChanged('phone') && !empty($employee->phone)) {
            try {
                $smsService = app(\App\Services\SmsEthiopiaService::class);
                $message = "Hello {$employee->full_name}, your phone number has been updated in your Construct-Pro ERP profile. If this wasn't you, please contact HR immediately.";
                $smsService->sendNotification($employee->phone, $message);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send phone update SMS: ' . $e->getMessage());
            }
        }

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Employee profile updated successfully!');
    }

    /**
     * Upload guarantee letter for employee
     */
    public function uploadGuaranteeLetter(Request $request, Employee $employee)
    {
        Gate::authorize('update', $employee);

        $request->validate([
            'guarantee_letter' => 'required|file|mimes:pdf,jpeg,png,jpg|max:10240',
        ]);

        // Upload the file to Cloudinary
        $cloudinary = app(\App\Services\CloudinaryService::class);
        $path = $cloudinary->upload($request->file('guarantee_letter'), 'guarantee_letters');

        // Update employee record
        $employee->update([
            'guarantee_letter' => $path,
            'guarantee_letter_submitted_at' => now(),
        ]);

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Guarantee letter uploaded successfully!');
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee)
    {
        Gate::authorize('delete', $employee);
        
        $name = $employee->full_name;
        $code = $employee->employee_code;

        // If employee has a linked user account without global system roles, remove or decouple
        if ($employee->user_id) {
            $user = \App\Models\User::find($employee->user_id);
            if ($user && $user->roles()->count() === 0) {
                $user->delete();
            }
        }

        $employee->delete();
        
        return redirect()->route('employees.index')
            ->with('success', "Employee {$name} ({$code}) has been deleted successfully. If this employee is registered or added again later, fresh GM approval will be strictly required.");
    }
}
