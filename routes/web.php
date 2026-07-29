<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\EmployeeRatingController;
use App\Http\Controllers\Admin\RoleAssignmentController;
use App\Http\Controllers\Admin\AdminTicketController;
use App\Http\Controllers\SupportTicketController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// One-click migration route for all pending migrations
Route::get('/migrate-material-prices', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', [
            '--force' => true,
        ]);
        $output = \Illuminate\Support\Facades\Artisan::output();
        return "<h2 style='font-family:sans-serif;color:green'>✅ Migration Complete!</h2><pre>$output</pre><a href='/erp-plans'>→ Open ERP Plans</a>";
    } catch (\Exception $e) {
        return "<h2 style='font-family:sans-serif;color:red'>❌ Migration Error</h2><pre>" . $e->getMessage() . "</pre>";
    }
});

// ====== TEMPORARY: SMS Test Route - Remove after testing ======
Route::get('/test-sms/{phone}', function ($phone) {
    $smsService = new \App\Services\SmsEthiopiaService();
    $otp = \App\Services\SmsEthiopiaService::generateOTP();
    $result = $smsService->sendOTP($phone, $otp);

    return response()->json([
        'phone_tested' => $phone,
        'otp_generated' => $otp,
        'service_result' => $result,
    ]);
});
// ====== END TEMPORARY ======

// Temporary route to import products.sql
Route::get('/import-products-sql', function () {
    $sqlPath = base_path('products.sql');
    if (!file_exists($sqlPath)) {
        return "products.sql not found at " . $sqlPath;
    }
    
    $sql = file_get_contents($sqlPath);
    $start = strpos($sql, 'INSERT INTO `products`');
    
    if ($start !== false) {
        $insertQuery = substr($sql, $start);
        
        $end = strpos($insertQuery, 'ALTER TABLE');
        if ($end !== false) {
            $insertQuery = substr($insertQuery, 0, $end);
        }
        
        $insertQuery = str_replace('INSERT INTO `products`', 'INSERT IGNORE INTO `products`', $insertQuery);
        
        try {
            \Illuminate\Support\Facades\DB::unprepared($insertQuery);
            return "<h1>Success!</h1><p>Products imported successfully from SQL file.</p><a href='/store-manager/products'>Go back to Material Catalog</a>";
        } catch (\Exception $e) {
            return "<h1>Error</h1><p>" . $e->getMessage() . "</p>";
        }
    }
    
    return "No INSERT statement found in the file.";
});

// Temporary route to import employees.sql
Route::get('/import-employees-sql', function () {
    $sqlPath = base_path('employees.sql');
    if (!file_exists($sqlPath)) {
        return "employees.sql not found at " . $sqlPath;
    }
    
    $sql = file_get_contents($sqlPath);
    
    // Extract just the INSERT INTO statement
    $start = strpos($sql, 'INSERT INTO `employees`');
    if ($start === false) {
        return "No INSERT statement found in the file.";
    }
    
    $insertQuery = substr($sql, $start);
    $end = strpos($insertQuery, 'ALTER TABLE');
    if ($end !== false) {
        $insertQuery = substr($insertQuery, 0, $end);
    }
    
    $insertQuery = str_replace('INSERT INTO `employees`', 'INSERT INTO `employees_temp`', $insertQuery);
    
    try {
        \Illuminate\Support\Facades\Schema::dropIfExists('employees_temp');
        
        // Create the temp table using Laravel Schema to ensure it has a primary key natively
        \Illuminate\Support\Facades\Schema::create('employees_temp', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->string('employee_id_number')->nullable();
            $table->string('user_id')->nullable();
            $table->text('full_name')->nullable();
            $table->text('department')->nullable();
            $table->text('designation')->nullable();
            $table->text('phone_number')->nullable();
            $table->text('base_salary')->nullable();
            $table->text('position')->nullable();
            $table->text('joining_date')->nullable();
            $table->text('salary')->nullable();
            $table->text('status')->nullable();
            $table->text('created_at')->nullable();
            $table->text('employment_date')->nullable();
            $table->text('educational_background')->nullable();
            $table->text('educational_file')->nullable();
            $table->text('experience_years')->nullable();
            $table->text('experience_file')->nullable();
            $table->text('application_letter_file')->nullable();
            $table->text('id_card_file')->nullable();
            $table->text('license_file')->nullable();
            $table->text('phone_number_2')->nullable();
            $table->text('guarantee_letter_file')->nullable();
            $table->text('contract_type')->nullable();
            $table->text('subcontractor_id')->nullable();
            $table->text('site_id')->nullable();
            $table->text('bank_info')->nullable();
            $table->text('rating')->nullable();
            $table->text('transport_allowance')->nullable();
            $table->text('house_allowance')->nullable();
            $table->text('position_allowance')->nullable();
        });
        
        // Run ONLY the insert statement
        \Illuminate\Support\Facades\DB::unprepared($insertQuery);
        
        $oldEmployees = \Illuminate\Support\Facades\DB::table('employees_temp')->get();
        $imported = 0;
        
        foreach($oldEmployees as $emp) {
            if (\App\Models\Employee::where('employee_code', $emp->employee_id_number)->exists()) {
                continue;
            }
            
            // Check if user_id actually exists in the users table
            $userId = $emp->user_id;
            if ($userId && !\App\Models\User::where('id', $userId)->exists()) {
                $userId = null; // Set to null if user doesn't exist to avoid foreign key errors
            }
            
            $bankName = null;
            $accountNumber = null;
            if ($emp->bank_info && $emp->bank_info !== 'null' && $emp->bank_info !== '[]') {
                $bankInfo = json_decode($emp->bank_info, true);
                if (is_array($bankInfo) && count($bankInfo) > 0) {
                    $bankName = $bankInfo[0]['bank_name'] ?? null;
                    $accountNumber = $bankInfo[0]['account_number'] ?? null;
                }
            }
            
            \App\Models\Employee::create([
                'employee_code'   => $emp->employee_id_number,
                'user_id'         => $userId,
                'full_name'       => $emp->full_name,
                'department'      => $emp->department,
                'role_title'      => $emp->designation ?? $emp->position,
                'phone'           => $emp->phone_number,
                'date_of_joining' => $emp->joining_date ?? $emp->employment_date ?? now(),
                'employment_type' => strtolower($emp->contract_type ?? 'permanent'),
                'status'          => $emp->status ?? 'active',
                'basic_salary'    => 0,
                'bank_name'       => $bankName,
                'account_number'  => $accountNumber,
                'guarantee_letter'=> $emp->guarantee_letter_file,
            ]);
            
            $imported++;
        }
        
        \Illuminate\Support\Facades\Schema::dropIfExists('employees_temp');
        
        return "<h1>Success!</h1><p>$imported employees properly mapped and imported into the new system.</p>";
        
    } catch (\Exception $e) {
        return "<h1>Error</h1><p>" . $e->getMessage() . "</p>";
    }
});

// Auth
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Registration Routes (Guest only)
Route::middleware('guest')->prefix('register')->name('register.')->group(function () {
    Route::get('/', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('index');
    Route::post('/send-otp', [App\Http\Controllers\Auth\RegisterController::class, 'sendOtp'])->name('send-otp');
    Route::get('/verify-otp', [App\Http\Controllers\Auth\RegisterController::class, 'showVerifyOtpForm'])->name('verify-otp');
    Route::post('/verify-otp', [App\Http\Controllers\Auth\RegisterController::class, 'verifyOtp']);
    Route::get('/create-password', [App\Http\Controllers\Auth\RegisterController::class, 'showCreatePasswordForm'])->name('create-password');
    Route::post('/create-password', [App\Http\Controllers\Auth\RegisterController::class, 'createPassword']);
    Route::post('/resend-otp', [App\Http\Controllers\Auth\RegisterController::class, 'resendOtp'])->name('resend-otp');
});

// Phone-Based Password Reset Routes
Route::prefix('password')->group(function () {
    Route::get('/reset', [App\Http\Controllers\Auth\PhonePasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/reset', [App\Http\Controllers\Auth\PhonePasswordResetController::class, 'sendResetOtp'])->name('password.email'); // Kept name for compatibility
    Route::get('/verify', [App\Http\Controllers\Auth\PhonePasswordResetController::class, 'showVerifyOtpForm'])->name('password.verify');
    Route::post('/verify', [App\Http\Controllers\Auth\PhonePasswordResetController::class, 'verifyOtp']);
    Route::get('/update', [App\Http\Controllers\Auth\PhonePasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/update', [App\Http\Controllers\Auth\PhonePasswordResetController::class, 'resetPassword'])->name('password.update');
    Route::post('/resend', [App\Http\Controllers\Auth\PhonePasswordResetController::class, 'resendOtp'])->name('password.resend');
});

// Direct Registration Route
Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->middleware('guest')->name('register');

// Protected routes
Route::middleware(['auth'])->group(function () {
    
    // --- Admin Dashboard Enhancements ---
    Route::middleware('role:global_admin|admin')->group(function () {
        Route::get('/admin/activity-logs', [ActivityLogController::class, 'index'])->name('admin.activity-logs');
        
        Route::get('/admin/employee-ratings', [EmployeeRatingController::class, 'index'])->name('admin.employee-ratings.index');
        Route::post('/admin/employee-ratings', [EmployeeRatingController::class, 'store'])->name('admin.employee-ratings.store');
        
        Route::get('/admin/role-assignment', [RoleAssignmentController::class, 'index'])->name('admin.role-assignment.index');
        Route::post('/admin/role-assignment/{user}', [RoleAssignmentController::class, 'assign'])->name('admin.role-assignment.assign');
        Route::post('/admin/role-assignment/{user}/remove', [RoleAssignmentController::class, 'removeRole'])->name('admin.role-assignment.remove');
        
        Route::get('/admin/tickets', [AdminTicketController::class, 'index'])->name('admin.tickets.index');
        Route::get('/admin/tickets/{ticket}', [AdminTicketController::class, 'show'])->name('admin.tickets.show');
        Route::post('/admin/tickets/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('admin.tickets.reply');
        Route::post('/admin/tickets/{ticket}/status', [AdminTicketController::class, 'updateStatus'])->name('admin.tickets.status');
        Route::post('/admin/tickets/{ticket}/assign', [AdminTicketController::class, 'assign'])->name('admin.tickets.assign');
    });

    // Support Tickets (All Employees)
    Route::get('/tickets', [SupportTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [SupportTicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [SupportTicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [SupportTicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [SupportTicketController::class, 'reply'])->name('tickets.reply');
    // ------------------------------------
    // System Actions – GET so we can trigger it directly from a sidebar link
    Route::get('/system/run-migrations', function () {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $output = \Illuminate\Support\Facades\Artisan::output();
            // Reseed roles & permissions in case new ones were added
            try {
                \Illuminate\Support\Facades\Artisan::call('db:seed', [
                    '--class' => 'RolesAndPermissionsSeeder',
                    '--force' => true,
                ]);
            } catch (\Throwable $seederErr) {
                // ignore if seeder class name differs
            }
            // Seed products if table is empty
            try {
                if (\App\Models\Product::count() === 0) {
                    \Illuminate\Support\Facades\Artisan::call('db:seed', [
                        '--class' => 'ProductSeeder',
                        '--force' => true,
                    ]);
                    $output .= ' | Products seeded.';
                }
            } catch (\Throwable $pe) {
                // ignore
            }
            // Seed Chart of Accounts if table is empty or has very few records
            try {
                if (\App\Models\ChartOfAccount::count() < 5) {
                    \Illuminate\Support\Facades\Artisan::call('db:seed', [
                        '--class' => 'ChartOfAccountsSeeder',
                        '--force' => true,
                    ]);
                    $output .= ' | Chart of Accounts seeded.';
                }
            } catch (\Throwable $coaErr) {
                $output .= ' | COA seed error: ' . $coaErr->getMessage();
            }
            return redirect()->back()->with('success', 'Database migrated & roles synced! Output: ' . $output);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Migration failed: ' . $e->getMessage());
        }
    })->name('system.run-migrations');

    // Dedicated route to (re-)seed products
    Route::get('/system/seed-products', function () {
        try {
            \Illuminate\Support\Facades\Artisan::call('db:seed', [
                '--class' => 'ProductSeeder',
                '--force' => true,
            ]);
            return redirect()->route('products.index')->with('success', 'Products seeded successfully!');
        } catch (\Exception $e) {
            return redirect()->route('products.index')->with('error', 'Seeding failed: ' . $e->getMessage());
        }
    })->name('system.seed-products');

    // Also keep a POST alias for backward-compat form submissions
    Route::post('/system/run-migrations', function () {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            return back()->with('success', 'Migrations completed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Migration failed: ' . $e->getMessage());
        }
    });

    // Dynamic Dashboard Redirect
    Route::get('/dashboard', function () {
        $loginController = new \App\Http\Controllers\Auth\LoginController();
        $method = new \ReflectionMethod($loginController, 'redirectTo');
        $method->setAccessible(true);
        $redirectUrl = $method->invoke($loginController);
        return redirect($redirectUrl);
    })->name('dashboard');

    // Role Tester
    Route::get('/dev/roles', [App\Http\Controllers\RoleTesterController::class, 'index'])->name('dev.roles');
    Route::post('/dev/roles/login', [App\Http\Controllers\RoleTesterController::class, 'loginAsRole'])->name('dev.roles.login');

    // ─── Dashboard placeholders (will be replaced per phase) ──────────────────
    Route::get('/dashboard/admin',          [App\Http\Controllers\DashboardController::class, 'admin'])->name('dashboard.admin');
    Route::get('/dashboard/gm',             [App\Http\Controllers\DashboardController::class, 'gm'])->name('dashboard.gm');
    Route::get('/dashboard/planning',       [App\Http\Controllers\DashboardController::class, 'planning'])->name('dashboard.planning');
    Route::get('/dashboard/coordinator',    [App\Http\Controllers\DashboardController::class, 'coordinator'])->name('dashboard.coordinator');
    
    // Coordinator Routes
    Route::get('/coordinator/forecast',     [App\Http\Controllers\CoordinatorController::class, 'forecastDemand'])->name('coordinator.forecast');
    Route::get('/dashboard/site-engineer',  [App\Http\Controllers\DashboardController::class, 'siteEngineer'])->name('dashboard.site-engineer');
    Route::get('/dashboard/foreman',        [App\Http\Controllers\DashboardController::class, 'foreman'])->name('dashboard.foreman');
    Route::get('/dashboard/store-manager',  [App\Http\Controllers\DashboardController::class, 'storeManager'])->name('dashboard.store-manager');
    Route::get('/dashboard/hr',             [App\Http\Controllers\DashboardController::class, 'hr'])->name('dashboard.hr');
    Route::get('/dashboard/finance',        [App\Http\Controllers\DashboardController::class, 'finance'])->name('dashboard.finance');
    Route::get('/dashboard/purchase',       [App\Http\Controllers\DashboardController::class, 'purchase'])->name('dashboard.purchase');
    Route::get('/dashboard/contract-admin', [App\Http\Controllers\DashboardController::class, 'contractAdmin'])->name('dashboard.contract-admin');
    Route::get('/bidding',                  fn() => view('dashboard.admin'))->name('bidding.index');
    Route::get('/subcon',                   fn() => view('dashboard.admin'))->name('subcon.index');
    Route::get('/audit',                    fn() => view('dashboard.admin'))->name('audit.index');

    // ─── Phase 2 Core Masters ─────────────────────────────────────────────────

    // Users (admin only)
    Route::resource('users', UserController::class)->except(['show']);

    // Projects
    Route::resource('projects', ProjectController::class);

    // Stores
    Route::resource('stores', StoreController::class);

    // Asset Returns (Store Manager)
    Route::get('asset-returns', [App\Http\Controllers\AssetReturnController::class, 'index'])->name('asset-returns.index');
    Route::put('asset-returns/{id}/approve', [App\Http\Controllers\AssetReturnController::class, 'approve'])->name('asset-returns.approve');
    Route::put('asset-returns/{id}/reject', [App\Http\Controllers\AssetReturnController::class, 'reject'])->name('asset-returns.reject');

    // Products
    Route::resource('products', ProductController::class);

    // Inventory
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('{inventory}', [InventoryController::class, 'show'])->name('show');
        Route::post('{inventory}/adjust', [InventoryController::class, 'adjust'])->name('adjust');
        Route::get('{inventory}/movements', [InventoryController::class, 'movements'])->name('movements');
    });
    // ─── Phase 3 Planning ───────────────────────────────────────────────────
    
    // Schedules
    Route::resource('schedules', App\Http\Controllers\ScheduleController::class);
    Route::get('schedules/{schedule}/wbs',                     [App\Http\Controllers\ScheduleController::class, 'wbs'])->name('schedules.wbs');
    Route::post('schedules/{schedule}/tasks',                  [App\Http\Controllers\ScheduleController::class, 'storeTask'])->name('schedules.tasks.store');
    Route::put('schedules/{schedule}/tasks/{task}',            [App\Http\Controllers\ScheduleController::class, 'updateTask'])->name('schedules.tasks.update');
    Route::delete('schedules/{schedule}/tasks/{task}',         [App\Http\Controllers\ScheduleController::class, 'destroyTask'])->name('schedules.tasks.destroy');
    Route::post('schedules/{schedule}/baselines',              [App\Http\Controllers\ScheduleController::class, 'storeBaseline'])->name('schedules.baselines.store');
    Route::post('schedules/{schedule}/send-to-coordinator',    [App\Http\Controllers\ScheduleController::class, 'sendToCoordinator'])->name('schedules.send-to-coordinator');

    // BOQ
    Route::resource('boqs', App\Http\Controllers\BoqController::class);
    Route::post('boqs/{boq}/approve', [App\Http\Controllers\BoqController::class, 'approve'])->name('boqs.approve');
    
    // BOQ Items
    Route::post('boqs/{boq}/items', [App\Http\Controllers\BoqItemController::class, 'store'])->name('boq_items.store');
    Route::put('boq-items/{item}', [App\Http\Controllers\BoqItemController::class, 'update'])->name('boq_items.update');
    Route::delete('boq-items/{item}', [App\Http\Controllers\BoqItemController::class, 'destroy'])->name('boq_items.destroy');

    // Takeoff
    Route::get('takeoff', [App\Http\Controllers\TakeoffController::class, 'index'])->name('takeoff.index');
    Route::get('takeoff/create', [App\Http\Controllers\TakeoffController::class, 'create'])->name('takeoff.create');
    Route::post('takeoff', [App\Http\Controllers\TakeoffController::class, 'store'])->name('takeoff.store');
    Route::get('takeoff/{takeoff}', [App\Http\Controllers\TakeoffController::class, 'show'])->name('takeoff.show');
    Route::delete('takeoff/{takeoff}', [App\Http\Controllers\TakeoffController::class, 'destroy'])->name('takeoff.destroy');
    Route::post('takeoff/{takeoff}/sections', [App\Http\Controllers\TakeoffController::class, 'storeSection'])->name('takeoff.sections.store');
    Route::get('takeoff/{takeoff}/convert', [App\Http\Controllers\TakeoffController::class, 'convert'])->name('takeoff.convert');

    
    // Takeoff Edit Requests
    Route::post('takeoff/{takeoff}/request-edit', [App\Http\Controllers\TakeoffController::class, 'requestEdit'])->name('takeoff.request-edit');
    Route::post('takeoff-edit-requests/{editRequest}/approve', [App\Http\Controllers\TakeoffController::class, 'approveEdit'])->name('takeoff.approve-edit');
    Route::post('takeoff-edit-requests/{editRequest}/reject', [App\Http\Controllers\TakeoffController::class, 'rejectEdit'])->name('takeoff.reject-edit');
    Route::post('takeoff-edit-requests/{editRequest}/revoke', [App\Http\Controllers\TakeoffController::class, 'revokeEdit'])->name('takeoff.revoke-edit');

    // Rebar Diameter → Product Mapping (Settings)
    Route::get('settings/rebar-products', [App\Http\Controllers\RebarDiaProductController::class, 'index'])->name('rebar-products.index');
    Route::post('settings/rebar-products', [App\Http\Controllers\RebarDiaProductController::class, 'update'])->name('rebar-products.update');
    Route::post('settings/rebar-products/seed', [App\Http\Controllers\RebarDiaProductController::class, 'seed'])->name('rebar-products.seed');

    // Planning Manager: Assign Team
    Route::get('planning-manager/team-assignment', [App\Http\Controllers\ProjectTeamController::class, 'index'])->name('planning.team.index');
    Route::post('planning-manager/team-assignment/{project}', [App\Http\Controllers\ProjectTeamController::class, 'update'])->name('planning.team.update');

    // ─── Planning Workflow (5-stage approval chain) ────────────────────────────
    Route::prefix('plan-workflow')->name('plan-workflow.')->group(function () {
        // Show workflow status for a project
        Route::get('/projects/{project}', [App\Http\Controllers\PlanWorkflowController::class, 'show'])->name('show');
        // Budget check API (for JS live bar)
        Route::get('/projects/{project}/budget-check', [App\Http\Controllers\PlanWorkflowController::class, 'budgetCheck'])->name('budget-check');
        // GM: Supplement budget
        Route::post('/projects/{project}/supplement', [App\Http\Controllers\PlanWorkflowController::class, 'supplementBudget'])->name('supplement');
        // Planning team: submit
        Route::post('/projects/{project}/submit', [App\Http\Controllers\PlanWorkflowController::class, 'submit'])->name('submit');
        // Approve steps
        Route::post('/{workflow}/approve-planning',    [App\Http\Controllers\PlanWorkflowController::class, 'approvePlanning'])->name('approve-planning');
        Route::post('/{workflow}/approve-coordinator', [App\Http\Controllers\PlanWorkflowController::class, 'approveCoordinator'])->name('approve-coordinator');
        Route::post('/{workflow}/approve-technical',   [App\Http\Controllers\PlanWorkflowController::class, 'approveTechnical'])->name('approve-technical');
        Route::post('/{workflow}/approve-gm',          [App\Http\Controllers\PlanWorkflowController::class, 'approveGm'])->name('approve-gm');
        // Reject
        Route::post('/{workflow}/reject', [App\Http\Controllers\PlanWorkflowController::class, 'reject'])->name('reject');
    });
    Route::get('takeoff/{takeoff}/sections/{section}/boq-items', [App\Http\Controllers\TakeoffController::class, 'getSectionBoqItems'])->name('takeoff.sections.boq-items');
    Route::get('takeoff/{takeoff}/items/create', [App\Http\Controllers\TakeoffController::class, 'createItem'])->name('takeoff.items.create');
    Route::post('takeoff/{takeoff}/items', [App\Http\Controllers\TakeoffController::class, 'storeItem'])->name('takeoff.items.store');
    Route::delete('takeoff/{takeoff}/items/{item}', [App\Http\Controllers\TakeoffController::class, 'destroyItem'])->name('takeoff.items.destroy');
    Route::patch('takeoff/{takeoff}/items/{item}', [App\Http\Controllers\TakeoffController::class, 'updateItem'])->name('takeoff.items.update');
    Route::patch('takeoff/{takeoff}/items/{item}/toggle-header', [App\Http\Controllers\TakeoffController::class, 'toggleHeader'])->name('takeoff.items.toggle-header');
    Route::delete('takeoff/{takeoff}/sections/{section}', [App\Http\Controllers\TakeoffController::class, 'destroySection'])->name('takeoff.sections.destroy');
    Route::get('takeoff/{takeoff}/convert', [App\Http\Controllers\TakeoffController::class, 'convert'])->name('takeoff.convert');
    Route::post('takeoff/{takeoff}/process-conversion', [App\Http\Controllers\TakeoffController::class, 'processConversion'])->name('takeoff.process-conversion');
    Route::post('takeoff/{takeoff}/rebar-cut-optimize', [App\Http\Controllers\TakeoffController::class, 'rebarCutOptimize'])->name('takeoff.rebar-cut-optimize');
    Route::post('takeoff/{takeoff}/rebar-erp-convert', [App\Http\Controllers\TakeoffController::class, 'rebarConvertToErpPlan'])->name('takeoff.rebar-erp-convert');

    // ERP Plans
    Route::resource('erp-plans', App\Http\Controllers\ErpPlanController::class);

    // Standard Works (Conversion Ratios)
    Route::resource('standard-works', App\Http\Controllers\StandardWorkController::class);

    // Manpower Roles (predefined selectable list)
    Route::get('manpower-roles',              [App\Http\Controllers\ManpowerRoleController::class, 'index'])->name('manpower-roles.index');
    Route::post('manpower-roles',             [App\Http\Controllers\ManpowerRoleController::class, 'store'])->name('manpower-roles.store');
    Route::delete('manpower-roles/{manpowerRole}', [App\Http\Controllers\ManpowerRoleController::class, 'destroy'])->name('manpower-roles.destroy');

    // Equipment Master (Fixed Assets)
    Route::resource('equipment', App\Http\Controllers\EquipmentController::class);

    // Weekly Dispatches
    Route::resource('weekly-dispatches', App\Http\Controllers\WeeklyDispatchController::class)->only(['index', 'show']);
    // ─── Phase 4 Procurement ────────────────────────────────────────────────

    Route::resource('suppliers', App\Http\Controllers\SupplierController::class);
    
    Route::resource('transfers', App\Http\Controllers\TransferController::class)->except(['edit', 'update', 'destroy']);
    Route::post('transfers/{transfer}/approve', [App\Http\Controllers\TransferController::class, 'approve'])->name('transfers.approve');
    Route::post('transfers/{transfer}/reject', [App\Http\Controllers\TransferController::class, 'reject'])->name('transfers.reject');
    Route::post('transfers/{transfer}/complete', [App\Http\Controllers\TransferController::class, 'complete'])->name('transfers.complete');

    // Procurement & Purchasing
    Route::resource('purchase-requests', App\Http\Controllers\PurchaseRequestController::class)->except(['edit', 'update', 'destroy']);
    Route::post('purchase-requests/{purchaseRequest}/submit', [App\Http\Controllers\PurchaseRequestController::class, 'submit'])->name('purchase-requests.submit');
    Route::post('purchase-requests/{purchaseRequest}/approve', [App\Http\Controllers\PurchaseRequestController::class, 'approve'])->name('purchase-requests.approve');
    Route::post('purchase-requests/{purchaseRequest}/reject', [App\Http\Controllers\PurchaseRequestController::class, 'reject'])->name('purchase-requests.reject');

    Route::get('price-intelligence', [App\Http\Controllers\ProcurementController::class, 'priceIntelligence'])->name('price-intelligence.index');
    Route::get('material-demand', [App\Http\Controllers\ProcurementController::class, 'materialDemand'])->name('material-demand.index');

    Route::resource('delivery-receipts', App\Http\Controllers\DeliveryReceiptController::class)->only(['index', 'create', 'store', 'show']);
    
    Route::resource('subcon-agreements', App\Http\Controllers\SubconAgreementController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('subcon-agreements/{subconAgreement}/approve', [App\Http\Controllers\SubconAgreementController::class, 'approve'])->name('subcon-agreements.approve');
    Route::post('subcon-agreements/{subconAgreement}/reject', [App\Http\Controllers\SubconAgreementController::class, 'reject'])->name('subcon-agreements.reject');
    Route::post('subcon-agreements/{subconAgreement}/activate', [App\Http\Controllers\SubconAgreementController::class, 'activate'])->name('subcon-agreements.activate');
    Route::get('subcon-agreements/{subconAgreement}/takeoff-items', [App\Http\Controllers\SubconAgreementController::class, 'getTakeoffItems'])->name('subcon-agreements.getTakeoffItems');
    Route::resource('ipcs', App\Http\Controllers\IpcRecordController::class)->only(['index', 'create', 'store', 'show']);

    // Material Requests
    Route::resource('material-requests', App\Http\Controllers\MaterialRequestController::class)
         ->except(['edit', 'update', 'destroy']);
    Route::post('material-requests/{materialRequest}/status',
        [App\Http\Controllers\MaterialRequestController::class, 'updateStatus'])
        ->name('material-requests.updateStatus');
        
    // Material Damage Reports
    Route::resource('material-damage-reports', App\Http\Controllers\MaterialDamageReportController::class)->only(['index', 'create', 'store', 'show']);

    // Tool Transactions
    Route::resource('tool-transactions', App\Http\Controllers\ToolTransactionController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('tool-transactions/{toolTransaction}/checkin', [App\Http\Controllers\ToolTransactionController::class, 'checkin'])->name('tool-transactions.checkin');

    // Material Request Items
    Route::post('material-requests/{materialRequest}/items',
        [App\Http\Controllers\MaterialRequestItemController::class, 'store'])
        ->name('mr-items.store');
    Route::delete('mr-items/{item}',
        [App\Http\Controllers\MaterialRequestItemController::class, 'destroy'])
        ->name('mr-items.destroy');

    // Purchase Orders
    Route::resource('purchase-orders', App\Http\Controllers\PurchaseOrderController::class)
         ->only(['index', 'create', 'store', 'show']);
    Route::post('purchase-orders/{purchaseOrder}/issue',
        [App\Http\Controllers\PurchaseOrderController::class, 'issue'])
        ->name('purchase-orders.issue');

    // Purchase Order Items
    Route::post('purchase-orders/{purchaseOrder}/items',
        [App\Http\Controllers\PurchaseOrderItemController::class, 'store'])
        ->name('po-items.store');
    Route::delete('po-items/{item}',
        [App\Http\Controllers\PurchaseOrderItemController::class, 'destroy'])
        ->name('po-items.destroy');

    // ─── Phase 6 HR ─────────────────────────────────────────────────────────

    Route::resource('departments', App\Http\Controllers\DepartmentController::class)->except(['show', 'destroy']);
    Route::resource('attendance', App\Http\Controllers\AttendanceController::class)->only(['index', 'create', 'store']);
    Route::post('attendance/bulk', [App\Http\Controllers\AttendanceController::class, 'bulkStore'])->name('attendance.bulkStore');
    Route::get('attendance/device-logs', [App\Http\Controllers\AttendanceController::class, 'deviceLogs'])->name('attendance.deviceLogs');
    Route::post('attendance/zkteco-sync', [App\Http\Controllers\AttendanceController::class, 'syncZkteco'])->name('attendance.zkteco-sync');
    Route::get('attendance/zkteco-status', [App\Http\Controllers\AttendanceController::class, 'zktecoStatus'])->name('attendance.zkteco-status');

    Route::put('employees/{employee}/approve', [App\Http\Controllers\EmployeeController::class, 'approve'])->name('employees.approve');
    Route::resource('employees', App\Http\Controllers\EmployeeController::class);
    Route::post('employees/{employee}/upload-guarantee', [App\Http\Controllers\EmployeeController::class, 'uploadGuaranteeLetter'])->name('employees.upload-guarantee');
    Route::resource('contracts', App\Http\Controllers\EmployeeContractController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('manpower-requests', App\Http\Controllers\ManpowerRequestController::class)->only(['index', 'create', 'store', 'show']);

    // ─── Asset Management ────────────────────────────────────────────────────────
    Route::prefix('assets')->name('assets.')->group(function () {
        Route::get('dashboard', [App\Http\Controllers\AssetDashboardController::class, 'index'])->name('dashboard');
        Route::get('export', [App\Http\Controllers\AssetDashboardController::class, 'export'])->name('export');
        Route::get('by-status/{status}', [App\Http\Controllers\AssetDashboardController::class, 'byStatus'])->name('by-status');
        Route::get('by-employee/{employeeId}', [App\Http\Controllers\AssetDashboardController::class, 'byEmployee'])->name('by-employee');
        Route::get('by-department/{department}', [App\Http\Controllers\AssetDashboardController::class, 'byDepartment'])->name('by-department');
    });
    
    Route::prefix('employee-assets')->name('employee-assets.')->group(function () {
        Route::get('{employeeAsset}/return', [App\Http\Controllers\EmployeeAssetController::class, 'returnForm'])->name('return');
        Route::put('{employeeAsset}/return', [App\Http\Controllers\EmployeeAssetController::class, 'returnStore'])->name('return-store');
        Route::get('{employeeAsset}/damage', [App\Http\Controllers\EmployeeAssetController::class, 'damageForm'])->name('damage');
        Route::put('{employeeAsset}/damage', [App\Http\Controllers\EmployeeAssetController::class, 'damageStore'])->name('damage-store');
        Route::get('{employeeAsset}', [App\Http\Controllers\EmployeeAssetController::class, 'show'])->name('show');
    });
    
    // ─── HR Manager Dashboard ────────────────────────────────────────────────────
    Route::middleware('auth')->prefix('hr-manager')->name('hr-manager.')->group(function () {
        Route::get('dashboard', [App\Http\Controllers\HRManagerController::class, 'dashboard'])->name('dashboard');
        Route::get('employees', [App\Http\Controllers\HRManagerController::class, 'employees'])->name('employees');
        Route::get('statistics', [App\Http\Controllers\HRManagerController::class, 'getStatisticsApi'])->name('statistics');
        Route::get('approvals', [App\Http\Controllers\HRManagerController::class, 'getPendingApprovals'])->name('approvals');
    });

    // ─── Asset Reports ──────────────────────────────────────────────────────────
    Route::prefix('asset-reports')->name('asset-reports.')->group(function () {
        Route::get('utilization', [App\Http\Controllers\AssetReportController::class, 'utilization'])->name('utilization');
        Route::get('export-utilization', [App\Http\Controllers\AssetReportController::class, 'exportUtilization'])->name('export-utilization');
        Route::get('damage', [App\Http\Controllers\AssetReportController::class, 'damage'])->name('damage');
        Route::get('export-damage', [App\Http\Controllers\AssetReportController::class, 'exportDamage'])->name('export-damage');
        Route::get('employee-allocation', [App\Http\Controllers\AssetReportController::class, 'employeeAllocation'])->name('employee-allocation');
        Route::get('export-employee-allocation', [App\Http\Controllers\AssetReportController::class, 'exportEmployeeAllocation'])->name('export-employee-allocation');
    });

    // ─── Leave Management ────────────────────────────────────────────────────────
    Route::resource('leave-requests', App\Http\Controllers\LeaveRequestController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('leave-requests/my', [App\Http\Controllers\LeaveRequestController::class, 'myRequests'])->name('leave-requests.my-requests');
    Route::post('leave-requests/{leaveRequest}/approve', [App\Http\Controllers\LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::post('leave-requests/{leaveRequest}/reject', [App\Http\Controllers\LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
    Route::post('leave-requests/bulk-approve', [App\Http\Controllers\LeaveRequestController::class, 'bulkApprove'])->name('leave-requests.bulkApprove');
    Route::get('leave-requests/balance/{employee}', [App\Http\Controllers\LeaveRequestController::class, 'getBalance'])->name('leave-requests.getBalance');
    Route::get('leave-requests/export', [App\Http\Controllers\LeaveRequestController::class, 'exportReport'])->name('leave-requests.export');

    // ─── Manpower Forecast ──────────────────────────────────────────────────────
    Route::resource('manpower-forecast', App\Http\Controllers\ManpowerForecastController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('manpower-forecast/{manpowerForecast}/assign', [App\Http\Controllers\ManpowerForecastController::class, 'assignEmployee'])->name('manpower-forecast.assignEmployee');
    
    // ─── Finance Dashboard ──────────────────────────────────────────────────────
    Route::get('/finance-dashboard', [App\Http\Controllers\FinanceDashboardController::class, 'index'])->name('finance.dashboard');
    Route::get('/finance-dashboard/revenue-data', [App\Http\Controllers\FinanceDashboardController::class, 'revenueVsExpensesData'])->name('finance.dashboard.revenue-data');

    // ─── Assigned Accounts Portal ───────────────────────────────────────────────
    Route::get('/assigned-accounts', [App\Http\Controllers\AssignedAccountController::class, 'index'])->name('assigned-accounts.index');
    Route::get('/assigned-accounts/{id}', [App\Http\Controllers\AssignedAccountController::class, 'show'])->name('assigned-accounts.show');
    Route::post('/assigned-accounts/{id}/pay', [App\Http\Controllers\AssignedAccountController::class, 'pay'])->name('assigned-accounts.pay');

    Route::delete('manpower-assignment/{manpowerAssignment}', [App\Http\Controllers\ManpowerForecastController::class, 'removeAssignment'])->name('manpower-assignment.remove');
    Route::post('manpower-forecast/{manpowerForecast}/submit', [App\Http\Controllers\ManpowerForecastController::class, 'submit'])->name('manpower-forecast.submit');
    Route::post('manpower-forecast/{manpowerForecast}/approve', [App\Http\Controllers\ManpowerForecastController::class, 'approve'])->name('manpower-forecast.approve');
    Route::post('manpower-forecast/{manpowerForecast}/reject', [App\Http\Controllers\ManpowerForecastController::class, 'reject'])->name('manpower-forecast.reject');
    Route::get('manpower-forecast/export', [App\Http\Controllers\ManpowerForecastController::class, 'exportCSV'])->name('manpower-forecast.export');
    Route::get('resource-availability', [App\Http\Controllers\ManpowerForecastController::class, 'getResourceAvailability'])->name('resource-availability.get');

    // ─── Performance Dashboard ──────────────────────────────────────────────────
    Route::get('performance-dashboard', [App\Http\Controllers\PerformanceDashboardController::class, 'index'])->name('performance-dashboard.index');
    Route::get('performance-dashboard/create-review', [App\Http\Controllers\PerformanceDashboardController::class, 'createReview'])->name('performance-dashboard.create-review');
    Route::post('performance-dashboard/review', [App\Http\Controllers\PerformanceDashboardController::class, 'storeReview'])->name('performance-dashboard.store-review');
    Route::get('performance-dashboard/review/{performanceReview}', [App\Http\Controllers\PerformanceDashboardController::class, 'showReview'])->name('performance-dashboard.show-review');
    Route::post('performance-dashboard/review/{performanceReview}/submit', [App\Http\Controllers\PerformanceDashboardController::class, 'submitReview'])->name('performance-dashboard.submit-review');
    Route::post('performance-dashboard/review/{performanceReview}/approve', [App\Http\Controllers\PerformanceDashboardController::class, 'approveReview'])->name('performance-dashboard.approve-review');
    Route::post('performance-dashboard/review/{performanceReview}/reject', [App\Http\Controllers\PerformanceDashboardController::class, 'rejectReview'])->name('performance-dashboard.reject-review');
    Route::get('performance-dashboard/employee/{employee}', [App\Http\Controllers\PerformanceDashboardController::class, 'showEmployee'])->name('performance-dashboard.show-employee');
    Route::get('performance-dashboard/analytics', [App\Http\Controllers\PerformanceDashboardController::class, 'analytics'])->name('performance-dashboard.analytics');
    Route::get('performance-dashboard/export', [App\Http\Controllers\PerformanceDashboardController::class, 'exportReport'])->name('performance-dashboard.export');

    // ─── Enhanced Contract Management ────────────────────────────────────────────
    Route::get('contracts', [App\Http\Controllers\EmployeeContractManagementController::class, 'index'])->name('contracts.index');
    Route::get('contracts/create', [App\Http\Controllers\EmployeeContractManagementController::class, 'create'])->name('contracts.create');
    Route::post('contracts', [App\Http\Controllers\EmployeeContractManagementController::class, 'store'])->name('contracts.store');
    Route::get('contracts/{employeeContract}', [App\Http\Controllers\EmployeeContractManagementController::class, 'show'])->name('contracts.show');
    Route::post('contracts/{employeeContract}/submit', [App\Http\Controllers\EmployeeContractManagementController::class, 'submitForApproval'])->name('contracts.submit');
    Route::post('contract-approval/{contractApproval}/approve', [App\Http\Controllers\EmployeeContractManagementController::class, 'approve'])->name('contracts.approve');
    Route::post('contract-approval/{contractApproval}/reject', [App\Http\Controllers\EmployeeContractManagementController::class, 'reject'])->name('contracts.reject');
    Route::post('contracts/{employeeContract}/milestone', [App\Http\Controllers\EmployeeContractManagementController::class, 'addMilestone'])->name('contracts.milestone');
    Route::post('contracts/{employeeContract}/renewal', [App\Http\Controllers\EmployeeContractManagementController::class, 'requestRenewal'])->name('contracts.renewal-request');
    Route::post('contract-renewal/{contractRenewal}/approve', [App\Http\Controllers\EmployeeContractManagementController::class, 'approveRenewal'])->name('contracts.renewal-approve');
    Route::post('contracts/{employeeContract}/amendment', [App\Http\Controllers\EmployeeContractManagementController::class, 'requestAmendment'])->name('contracts.amendment-request');
    Route::post('contract-amendment/{contractAmendment}/approve', [App\Http\Controllers\EmployeeContractManagementController::class, 'approveAmendment'])->name('contracts.amendment-approve');
    Route::get('contracts/expiring/list', [App\Http\Controllers\EmployeeContractManagementController::class, 'expiringContracts'])->name('contracts.expiring');
    Route::get('contracts/export', [App\Http\Controllers\EmployeeContractManagementController::class, 'exportReport'])->name('contracts.export');

    // ─── Payroll Integration ────────────────────────────────────────────────────
    Route::get('payroll/dashboard', [App\Http\Controllers\PayrollIntegrationController::class, 'dashboard'])->name('payroll.dashboard');
    Route::get('payroll/employee/{employee}', [App\Http\Controllers\PayrollIntegrationController::class, 'employeePayroll'])->name('payroll.employee');
    Route::get('payroll/salary-structures', [App\Http\Controllers\PayrollIntegrationController::class, 'salaryStructures'])->name('payroll.salary-structures');
    Route::get('payroll/salary-structures/create', [App\Http\Controllers\PayrollIntegrationController::class, 'createSalaryStructure'])->name('payroll.salary-structure-create');
    Route::post('payroll/salary-structures', [App\Http\Controllers\PayrollIntegrationController::class, 'storeSalaryStructure'])->name('payroll.salary-structure-store');
    Route::get('payroll/advances', [App\Http\Controllers\PayrollIntegrationController::class, 'advances'])->name('payroll.advances');
    Route::post('payroll/advances/request', [App\Http\Controllers\PayrollIntegrationController::class, 'requestAdvance'])->name('payroll.advance-request');
    Route::post('payroll/advances/{employeeAdvance}/approve', [App\Http\Controllers\PayrollIntegrationController::class, 'approveAdvance'])->name('payroll.advance-approve');
    Route::post('payroll/advances/{employeeAdvance}/disburse', [App\Http\Controllers\PayrollIntegrationController::class, 'disburseAdvance'])->name('payroll.advance-disburse');
    Route::get('payroll/monthly-status', [App\Http\Controllers\PayrollIntegrationController::class, 'monthlyStatus'])->name('payroll.monthly-status');
    Route::get('payroll/analytics', [App\Http\Controllers\PayrollIntegrationController::class, 'analytics'])->name('payroll.analytics');

    // ─── HR Reports ─────────────────────────────────────────────────────────────
    Route::get('reports/attendance', [App\Http\Controllers\HRReportsController::class, 'attendanceReport'])->name('reports.attendance');
    Route::get('reports/turnover', [App\Http\Controllers\HRReportsController::class, 'turnoverReport'])->name('reports.turnover');
    Route::get('reports/cost-analysis', [App\Http\Controllers\HRReportsController::class, 'costAnalysisReport'])->name('reports.cost-analysis');
    Route::get('reports/leave-analysis', [App\Http\Controllers\HRReportsController::class, 'leaveAnalysisReport'])->name('reports.leave-analysis');
    Route::get('reports/employee-cost', [App\Http\Controllers\HRReportsController::class, 'employeeCostReport'])->name('reports.employee-cost');
    Route::get('reports/attendance/export', [App\Http\Controllers\HRReportsController::class, 'exportAttendanceCSV'])->name('reports.attendance.export');

    // ─── Employee Self-Service Portal ───────────────────────────────────────────
    Route::get('employee/dashboard', [App\Http\Controllers\EmployeeSelfServiceController::class, 'dashboard'])->name('employee.dashboard');
    Route::get('employee/attendance', [App\Http\Controllers\EmployeeSelfServiceController::class, 'viewAttendance'])->name('employee.attendance');
    Route::get('employee/payroll', [App\Http\Controllers\EmployeeSelfServiceController::class, 'viewPayroll'])->name('employee.payroll');
    Route::get('employee/contract', [App\Http\Controllers\EmployeeSelfServiceController::class, 'viewContract'])->name('employee.contract');
    Route::get('employee/leave-history', [App\Http\Controllers\EmployeeSelfServiceController::class, 'viewLeaveHistory'])->name('employee.leave-history');
    Route::get('employee/performance', [App\Http\Controllers\EmployeeSelfServiceController::class, 'viewPerformance'])->name('employee.performance');
    Route::get('employee/achievements', [App\Http\Controllers\EmployeeSelfServiceController::class, 'viewAchievements'])->name('employee.achievements');
    Route::get('employee/leave-balance', [App\Http\Controllers\EmployeeSelfServiceController::class, 'viewLeaveBalance'])->name('employee.leave-balance');
    Route::post('employee/profile', [App\Http\Controllers\EmployeeSelfServiceController::class, 'updateProfile'])->name('employee.profile.update');
    Route::get('employee/payroll/{payroll}/download', [App\Http\Controllers\EmployeeSelfServiceController::class, 'downloadPayrollSlip'])->name('employee.payroll.download');
    Route::get('employee/contract/{contract}/download', [App\Http\Controllers\EmployeeSelfServiceController::class, 'downloadContract'])->name('employee.contract.download');

    Route::resource('payrolls',  App\Http\Controllers\PayrollController::class)->only(['index','create','store','show']);
    Route::post('payrolls/{payroll}/mark-paid',
        [App\Http\Controllers\PayrollController::class, 'markPaid'])
        ->name('payrolls.markPaid');

    // ─── Phase 5 Finance ────────────────────────────────────────────────────

    Route::resource('coa', App\Http\Controllers\ChartOfAccountController::class)->except(['show', 'destroy']);
    Route::resource('bank-accounts', App\Http\Controllers\BankAccountController::class)->except(['destroy']);
    
    Route::resource('income', App\Http\Controllers\IncomeController::class)->except(['edit', 'update', 'destroy']);
    Route::post('income/{income}/confirm', [App\Http\Controllers\IncomeController::class, 'confirm'])->name('income.confirm');

    Route::resource('journal-entries', App\Http\Controllers\JournalEntryController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('budgets', App\Http\Controllers\ProjectBudgetController::class)->except(['show', 'destroy']);
    Route::resource('emergency-funds', App\Http\Controllers\EmergencyFundController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('emergency-funds/{emergencyFund}/approve', [App\Http\Controllers\EmergencyFundController::class, 'approve'])->name('emergency-funds.approve');
    Route::post('emergency-funds/{emergencyFund}/reject', [App\Http\Controllers\EmergencyFundController::class, 'reject'])->name('emergency-funds.reject');

    Route::resource('schedules', App\Http\Controllers\ScheduleController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('schedules/{schedule}/approve', [App\Http\Controllers\ScheduleController::class, 'approve'])->name('schedules.approve');
    
    Route::get('dispatches/active-tasks', [App\Http\Controllers\DispatchController::class, 'getActiveTasks'])->name('dispatches.active-tasks');
    Route::resource('dispatches', App\Http\Controllers\DispatchController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('expenses', [App\Http\Controllers\ApprovalHubController::class, 'index'])->name('expenses.index');
    Route::resource('expenses', App\Http\Controllers\ExpenseController::class)->only(['create','store','show']);
    Route::post('expenses/{expense}/approve', [App\Http\Controllers\ExpenseController::class, 'approve'])->name('expenses.approve');
    Route::post('expenses/{expense}/reject',
        [App\Http\Controllers\ExpenseController::class, 'reject'])->name('expenses.reject');

    // Central Approval Hub
    Route::get('approvals', [App\Http\Controllers\ApprovalHubController::class, 'index'])->name('approvals.index');

    // Reports Hub
    Route::get('finance/reports', [App\Http\Controllers\FinanceReportController::class, 'index'])->name('reports.index');
    Route::get('finance/reports/trial-balance', [App\Http\Controllers\FinanceReportController::class, 'trialBalance'])->name('reports.trial-balance');
    Route::get('finance/reports/income-statement', [App\Http\Controllers\FinanceReportController::class, 'incomeStatement'])->name('reports.income-statement');
    Route::get('finance/reports/balance-sheet', [App\Http\Controllers\FinanceReportController::class, 'balanceSheet'])->name('reports.balance-sheet');
    Route::get('finance/reports/cash-flow', [App\Http\Controllers\FinanceReportController::class, 'cashFlow'])->name('reports.cash-flow');
    Route::get('finance/reports/general-ledger', [App\Http\Controllers\FinanceReportController::class, 'generalLedger'])->name('reports.general-ledger');
    Route::get('finance/reports/expense-by-site', [App\Http\Controllers\FinanceReportController::class, 'expenseBySite'])->name('reports.expense-by-site');

    Route::resource('payments', App\Http\Controllers\PaymentController::class)->only(['index','create','store','show']);

    // ─── Phase 8 Operational ────────────────────────────────────────────────
    Route::resource('material-plans', App\Http\Controllers\MaterialPlanController::class)->only(['index', 'create', 'store', 'show']);
    
    Route::resource('material-usages', App\Http\Controllers\MaterialUsageController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('material-usages/{materialUsage}/confirm', [App\Http\Controllers\MaterialUsageController::class, 'confirm'])->name('material-usages.confirm');

    Route::resource('delivery-receipts', App\Http\Controllers\DeliveryReceiptController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('delivery-receipts/{deliveryReceipt}/receive', [App\Http\Controllers\DeliveryReceiptController::class, 'receive'])->name('delivery-receipts.receive');

    Route::resource('transfers', App\Http\Controllers\TransferController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('transfers/{transfer}/approve', [App\Http\Controllers\TransferController::class, 'approve'])->name('transfers.approve');
    
    // ─── Phase 7 Subcontractors ─────────────────────────────────────────────
    Route::resource('subcontractors', App\Http\Controllers\SubcontractorController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('ipcs', App\Http\Controllers\IpcController::class)->only(['index', 'create', 'store', 'show']);

    // ─── Client IPCs (Company → Client Payment Certificates) ─────────────────────
    Route::resource('client-ipcs', App\Http\Controllers\ClientIpcController::class)
         ->only(['index', 'create', 'store', 'show', 'edit', 'update']);
    Route::post('client-ipcs/{clientIpc}/submit',         [App\Http\Controllers\ClientIpcController::class, 'submit'])->name('client-ipcs.submit');
    Route::post('client-ipcs/{clientIpc}/approve',        [App\Http\Controllers\ClientIpcController::class, 'approve'])->name('client-ipcs.approve');
    Route::post('client-ipcs/{clientIpc}/record-payment', [App\Http\Controllers\ClientIpcController::class, 'recordPayment'])->name('client-ipcs.record-payment');
    Route::get('client-ipcs-boq-items',                   [App\Http\Controllers\ClientIpcController::class, 'boqItems'])->name('client-ipcs.boq-items');


    Route::resource('cut-optimizations', App\Http\Controllers\CutOptimizationController::class)->only(['index', 'create', 'store', 'show']);
    
    Route::resource('issues', App\Http\Controllers\IssueController::class)->only(['index', 'create', 'store', 'show']);
    
    Route::resource('waste', App\Http\Controllers\WasteController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('waste/{waste}/verify', [App\Http\Controllers\WasteController::class, 'verify'])->name('waste.verify');
    
    Route::get('weekly-manpower-report', [App\Http\Controllers\WeeklyManpowerReportController::class, 'index'])->name('weekly-manpower.index');
    Route::post('weekly-manpower-report/send-gm', [App\Http\Controllers\WeeklyManpowerReportController::class, 'sendToGM'])->name('weekly-manpower.sendGM');
    Route::get('weekly-manpower-report/export', [App\Http\Controllers\WeeklyManpowerReportController::class, 'exportCSV'])->name('weekly-manpower.export');
    Route::resource('daily-reports', App\Http\Controllers\DailyReportController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('daily-reports/approval', [App\Http\Controllers\DailyReportController::class, 'approvalDashboard'])->name('daily-reports.approval');
    Route::post('daily-reports/{dailyReport}/approve', [App\Http\Controllers\DailyReportController::class, 'approve'])->name('daily-reports.approve');
    Route::post('daily-reports/{dailyReport}/reject', [App\Http\Controllers\DailyReportController::class, 'reject'])->name('daily-reports.reject');
    Route::post('daily-reports/bulk-approve', [App\Http\Controllers\DailyReportController::class, 'bulkApprove'])->name('daily-reports.bulkApprove');
    Route::get('daily-reports/stats/manpower', [App\Http\Controllers\DailyReportController::class, 'getManpowerStats'])->name('daily-reports.manpowerStats');
    Route::resource('weekly-reports', App\Http\Controllers\WeeklyReportController::class)->only(['index', 'create', 'store', 'show']);

    // ─── Phase 9 Communication ──────────────────────────────────────────────
    Route::resource('messages', App\Http\Controllers\MessageController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('messages/{message}/reply', [App\Http\Controllers\MessageController::class, 'reply'])->name('messages.reply');

    // ─── Phase 10 Admin & Equipment ──────────────────────────────────────────
    Route::resource('equipment', App\Http\Controllers\EquipmentController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('equipment/{equipment}/log', [App\Http\Controllers\EquipmentController::class, 'logProductivity'])->name('equipment.logProductivity');
    
    Route::get('settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

    Route::get('audit-logs', [App\Http\Controllers\AuditController::class, 'index'])->name('audit.index');

    // ─── Store Manager Hub ─────────────────────────────────────────────────────
    Route::prefix('store-manager')->name('store-manager.')->group(function () {
        Route::get('/', [App\Http\Controllers\StoreManagerController::class, 'dashboard'])->name('dashboard');
        
        // Slip Sequences (GRN/SIN Configuration)
        Route::resource('slip-sequences', App\Http\Controllers\SlipSequenceController::class);
        Route::post('slip-sequences/{slipSequence}/deactivate', [App\Http\Controllers\SlipSequenceController::class, 'deactivate'])->name('slip-sequences.deactivate');
        Route::post('slip-sequences/{slipSequence}/reactivate', [App\Http\Controllers\SlipSequenceController::class, 'reactivate'])->name('slip-sequences.reactivate');
        Route::post('slip-sequences/{slipSequence}/reset', [App\Http\Controllers\SlipSequenceController::class, 'reset'])->name('slip-sequences.reset');
        Route::get('api/slip-sequences/{storeId}/{slipType}', [App\Http\Controllers\SlipSequenceController::class, 'getNextSlip']);
        
        // Inventory - All stores
        Route::get('inventory/all', [App\Http\Controllers\StoreManagerController::class, 'allInventory'])->name('inventory.all');
        
        // Transfers
        Route::get('transfers', [App\Http\Controllers\StoreManagerController::class, 'transfersIndex'])->name('transfers.index');
        Route::get('transfers/create', [App\Http\Controllers\StoreManagerController::class, 'createTransfer'])->name('transfers.create');
        Route::post('transfers', [App\Http\Controllers\StoreManagerController::class, 'storeTransfer'])->name('transfers.store');
        Route::get('transfers/{transfer}', [App\Http\Controllers\StoreManagerController::class, 'showTransfer'])->name('transfers.show');
        
        // Material Requests from Coordinator
        Route::get('material-requests', [App\Http\Controllers\StoreManagerController::class, 'materialRequests'])->name('material-requests.index');
        Route::post('material-requests/{materialRequest}/process', [App\Http\Controllers\StoreManagerController::class, 'processMaterialRequest'])->name('material-requests.process');
        
        // Product Catalog
        Route::get('products', [App\Http\Controllers\StoreManagerController::class, 'productCatalog'])->name('products.index');
        Route::get('products/create', [App\Http\Controllers\StoreManagerController::class, 'createProduct'])->name('products.create');
        Route::post('products', [App\Http\Controllers\StoreManagerController::class, 'storeProduct'])->name('products.store');
        
        // Receive & Send Slips (Unified)
        Route::get('slips', [App\Http\Controllers\StoreManagerController::class, 'slipsIndex'])->name('slips.index');
        Route::get('slips/create', [App\Http\Controllers\StoreManagerController::class, 'createSlip'])->name('slips.create');
        Route::post('slips', [App\Http\Controllers\StoreManagerController::class, 'storeSlip'])->name('slips.store');
        Route::post('slips/{slip}/void', [App\Http\Controllers\StoreManagerController::class, 'voidSlip'])->name('slips.void');
        
        // Issued Materials
        Route::get('issued-materials', [App\Http\Controllers\StoreManagerController::class, 'issuedMaterials'])->name('issued.index');
    });

    // ─── Planning Manager Hub ───────────────────────────────────────────────────
    Route::prefix('planning-manager')->name('planning-manager.')->group(function () {
        Route::get('emergency-requests', [App\Http\Controllers\PlanningManagerController::class, 'emergencyRequests'])->name('emergency-requests');
        Route::post('emergency-requests/material/{materialRequest}/approve', [App\Http\Controllers\PlanningManagerController::class, 'approveMaterial'])->name('emergency-requests.material.approve');
        Route::post('emergency-requests/manpower/{manpowerRequest}/approve', [App\Http\Controllers\PlanningManagerController::class, 'approveManpower'])->name('emergency-requests.manpower.approve');


        Route::get('resource-report', [App\Http\Controllers\PlanningManagerController::class, 'resourceReport'])->name('resource-report');
        Route::get('weekly-plan-setup', [App\Http\Controllers\PlanningManagerController::class, 'weeklyPlanSetup'])->name('weekly-plan-setup');
        Route::post('weekly-plan-setup', [App\Http\Controllers\PlanningManagerController::class, 'storeWeeklyPlan'])->name('weekly-plan-setup.store');
    });

    // ─── Engineer Work Scheduling Module ────────────────────────────────────────
    Route::prefix('eng-schedule')->name('eng-schedule.')->group(function () {
        // Calendar feed & conflict check (before resource routes to avoid conflicts)
        Route::get('calendar-feed',     [App\Http\Controllers\EngScheduleController::class, 'calendarFeed'])->name('calendar-feed');
        Route::get('engineer-resources',[App\Http\Controllers\EngScheduleController::class, 'engineerResources'])->name('engineer-resources');
        Route::post('conflict-check',   [App\Http\Controllers\EngScheduleController::class, 'conflictCheck'])->name('conflict-check');

        // Engineer personal view
        Route::get('my',                [App\Http\Controllers\EngScheduleController::class, 'mySchedule'])->name('my');

        // Standard resource (index, create, store, show, edit, update, destroy)
        Route::resource('/', App\Http\Controllers\EngScheduleController::class)
             ->parameters(['' => 'engSchedule'])
             ->names([
                 'index'   => 'index',
                 'create'  => 'create',
                 'store'   => 'store',
                 'show'    => 'show',
                 'edit'    => 'edit',
                 'update'  => 'update',
                 'destroy' => 'destroy',
             ]);

        // Extra actions on a specific work order
        Route::patch('{engSchedule}/status',    [App\Http\Controllers\EngScheduleController::class, 'updateStatus'])->name('update-status');
        Route::patch('{engSchedule}/reschedule',[App\Http\Controllers\EngScheduleController::class, 'reschedule'])->name('reschedule');
        Route::post('{engSchedule}/comments',   [App\Http\Controllers\EngScheduleController::class, 'addComment'])->name('add-comment');
    });

    // ─── Equipment Master & Fixed Asset Units ───────────────────────────────────
    Route::prefix('equipment')->name('equipment.')->group(function () {
        Route::get('/',             [App\Http\Controllers\EquipmentController::class, 'index'])->name('index');
        Route::get('/create',       [App\Http\Controllers\EquipmentController::class, 'create'])->name('create');
        Route::post('/',            [App\Http\Controllers\EquipmentController::class, 'store'])->name('store');
        Route::get('/{equipment}',  [App\Http\Controllers\EquipmentController::class, 'show'])->name('show');

        // Fixed Asset Unit CRUD per equipment type
        Route::post('/{equipment}/units',              [App\Http\Controllers\EquipmentController::class, 'storeUnit'])->name('units.store');
        Route::patch('/{equipment}/units/{unit}',      [App\Http\Controllers\EquipmentController::class, 'updateUnit'])->name('units.update');
        Route::delete('/{equipment}/units/{unit}',     [App\Http\Controllers\EquipmentController::class, 'destroyUnit'])->name('units.destroy');

        // Productivity logging
        Route::post('/{equipment}/productivity',       [App\Http\Controllers\EquipmentController::class, 'logProductivity'])->name('productivity.store');
    });

    // ─── Marketing Module ───────────────────────────────────────────────────────
    Route::prefix('marketing')->name('marketing.')->group(function () {
        Route::get('dashboard', [App\Http\Controllers\MarketingController::class, 'dashboard'])->name('dashboard');
        
        // Prices
        Route::get('prices/create', [App\Http\Controllers\MarketingController::class, 'createPrice'])->name('prices.create');
        Route::post('prices/store', [App\Http\Controllers\MarketingController::class, 'storePrice'])->name('prices.store');
        Route::get('prices/history', [App\Http\Controllers\MarketingController::class, 'priceHistory'])->name('prices.history');

        // Reports
        Route::get('reports/inflation', [App\Http\Controllers\MarketingController::class, 'inflationReport'])->name('reports.inflation');
        Route::get('reports/planning-vs-actual', [App\Http\Controllers\MarketingController::class, 'planningVsActual'])->name('reports.planning-vs-actual');
    });
});

