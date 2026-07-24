<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EmployeeController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Employee::class);
        $employees = Employee::with(['project'])->latest()->paginate(20);
        return view('hr.employees.index', compact('employees'));
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
        return view('hr.employees.create', compact('projects', 'products', 'departments'));
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
                'employee_data.education' => $request->education ?? session('employee_data.education'),
                'employee_data.experience' => $request->experience ?? session('employee_data.experience'),
            ]);

            // Handle file uploads in session for steps 5 & 6
            if ($currentStep == 5 && $request->hasFile('education')) {
                $this->storeEducationFilesInSession($request);
            }
            
            if ($currentStep == 6 && $request->hasFile('experience')) {
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
            'employment_type' => 'required|in:permanent,contract,daily',
            'date_of_joining' => 'required|date',
            'basic_salary'    => 'required|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'house_allowance' => 'nullable|numeric|min:0',
            'position_allowance' => 'nullable|numeric|min:0',
            'contract_type' => 'nullable|string',
            'status'          => 'required|in:active,suspended,terminated',
            'bank_name'       => 'nullable|string|max:255',
            'account_number'  => 'nullable|string|max:50',
            'notes'           => 'nullable|string',
            'guarantee_letter' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:10240',
            'assets'          => 'nullable|array',
            'assets.*.product_id' => 'required_with:assets|exists:products,id',
            'assets.*.quantity' => 'nullable|integer|min:1',
            'education'       => 'nullable|array',
            'education.*.degree_level' => 'required_with:education.*.field_of_study,education.*.institution_name|string',
            'education.*.field_of_study' => 'required_with:education.*.degree_level,education.*.institution_name|string',
            'education.*.institution_name' => 'required_with:education.*.degree_level,education.*.field_of_study|string',
            'education.*.certificate_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'experience'      => 'nullable|array',
            'experience.*.job_title' => 'required_with:experience.*.company_name,experience.*.start_date|string',
            'experience.*.company_name' => 'required_with:experience.*.job_title,experience.*.start_date|string',
            'experience.*.start_date' => 'required_with:experience.*.job_title,experience.*.company_name|date',
            'experience.*.license_document' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:10240',
            'device_user_id' => 'nullable|string|max:100',
        ]);

        // Default allowances to 0 if null
        $validated['transport_allowance'] = $validated['transport_allowance'] ?? 0;
        $validated['house_allowance'] = $validated['house_allowance'] ?? 0;
        $validated['position_allowance'] = $validated['position_allowance'] ?? 0;

        // Handle guarantee letter upload
        $guaranteeLetterPath = null;
        if ($request->hasFile('guarantee_letter')) {
            $guaranteeLetterPath = $request->file('guarantee_letter')->store('guarantee_letters', 'public');
            $validated['guarantee_letter'] = $guaranteeLetterPath;
            $validated['guarantee_letter_submitted_at'] = now();
        }

        // Create User account for the new employee
        $userEmail = $validated['email'] ?? strtolower($validated['employee_code']) . '@construct-pro.com';
        
        $user = \App\Models\User::create([
            'name' => $validated['full_name'],
            'email' => $userEmail,
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'is_active' => true,
        ]);
        
        $validated['user_id'] = $user->id;

        $employee = Employee::create($validated);

        // Attach assets if any selected
        if (!empty($request->assets)) {
            foreach ($request->assets as $assetInfo) {
                if (!empty($assetInfo['product_id'])) {
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

        // Save Education records
        if ($request->has('education')) {
            $this->saveEducationRecords($employee, $request->education);
        }

        // Save Experience records
        if ($request->has('experience')) {
            $this->saveExperienceRecords($employee, $request->experience);
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

        return redirect()->route('admin.role-assignment.index')
            ->with('success', "Employee \"{$employee->full_name}\" created successfully! A system user account has been created — please assign a system role below.")
            ->with('highlight_user_id', $user->id);
    }

    public function approve(Employee $employee)
    {
        $employee->update([
            'is_approved_by_gm' => true,
            'gm_approved_at' => now(),
            'gm_approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Employee approved successfully!');
    }

    private function validateStep(Request $request, $step)
    {
        switch ($step) {
            case 1:
                $request->validate([
                    'employee_code' => 'required|string',
                    'full_name' => 'required|string|max:255',
                    'phone' => 'required|string|max:20',
                    'department' => 'required|string|max:100',
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
                    'employment_type' => 'required|in:permanent,contract,daily',
                    'date_of_joining' => 'required|date',
                    'status' => 'required|in:active,suspended,terminated',
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
                    'basic_salary' => 'required|numeric|min:0',
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
                            'education.*.certificate_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
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
        $filesData = session('employee_education_files', []);
        
        foreach ($request->file('education') as $index => $educationFiles) {
            if (isset($educationFiles['certificate_photo'])) {
                $file = $educationFiles['certificate_photo'];
                $fileName = time() . '_' . $index . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('temp/education', $fileName, 'public');
                $filesData[$index] = $path;
            }
        }
        
        session(['employee_education_files' => $filesData]);
    }

    /**
     * Store experience license documents in session temporarily
     */
    private function storeExperienceFilesInSession(Request $request)
    {
        $filesData = session('employee_experience_files', []);
        
        foreach ($request->file('experience') as $index => $experienceFiles) {
            if (isset($experienceFiles['license_document'])) {
                $file = $experienceFiles['license_document'];
                $fileName = time() . '_' . $index . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('temp/experience', $fileName, 'public');
                $filesData[$index] = $path;
            }
        }
        
        session(['employee_experience_files' => $filesData]);
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
                $certificatePath = $file->store('employee_certificates', 'public');
            } 
            // Check if file was stored in session from previous step
            elseif (isset($sessionFiles[$index])) {
                // Move from temp to permanent location
                $tempPath = $sessionFiles[$index];
                $newPath = str_replace('temp/education/', 'employee_certificates/', $tempPath);
                \Storage::disk('public')->move($tempPath, $newPath);
                $certificatePath = $newPath;
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
                $licensePath = $file->store('employee_licenses', 'public');
            }
            // Check if file was stored in session from previous step
            elseif (isset($sessionFiles[$index])) {
                // Move from temp to permanent location
                $tempPath = $sessionFiles[$index];
                $newPath = str_replace('temp/experience/', 'employee_licenses/', $tempPath);
                \Storage::disk('public')->move($tempPath, $newPath);
                $licensePath = $newPath;
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
            'activeAssets.product'
        ]);
        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        Gate::authorize('update', $employee);
        $projects = Project::where('status', '!=', 'cancelled')->get();
        return view('hr.employees.edit', compact('employee', 'projects'));
    }

    public function update(Request $request, Employee $employee)
    {
        Gate::authorize('update', $employee);

        $validated = $request->validate([
            'employee_code'   => 'required|string|unique:employees,employee_code,'.$employee->id,
            'full_name'       => 'required|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:255',
            'role_title'      => 'nullable|string|max:255',
            'department'      => 'nullable|string|max:100',
            'project_id'      => 'nullable|exists:projects,id',
            'employment_type' => 'required|in:permanent,contract,daily',
            'date_of_joining' => 'required|date',
            'basic_salary'    => 'required|numeric|min:0',
            'status'          => 'required|in:active,suspended,terminated',
            'notes'           => 'nullable|string',
            'device_user_id'  => 'nullable|string|max:100',
        ]);

        // Check if status changed to terminated
        $wasTerminated = $employee->status !== 'terminated' && $validated['status'] === 'terminated';

        $employee->update($validated);

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
            ->with('success', 'Employee updated successfully.');
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

        // Upload the file
        $path = $request->file('guarantee_letter')->store('guarantee_letters', 'public');

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
        
        $employee->delete();
        
        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}
