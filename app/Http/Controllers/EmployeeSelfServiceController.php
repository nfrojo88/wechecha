<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\EmployeeContract;
use App\Models\PerformanceReview;
use App\Models\EmployeeAchievement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeSelfServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Self-service dashboard
     */
    public function dashboard()
    {
        $employee = Employee::where('user_id', Auth::id())->firstOrFail();
        $employee->load(['leaveRequests', 'salaryStructure', 'contracts', 'performanceReviews', 'achievements']);

        // Pending leaves
        $pendingLeaves = $employee->leaveRequests()
            ->where('status', 'pending')
            ->count();

        // Approved leaves
        $approvedLeaves = $employee->leaveRequests()
            ->where('status', 'approved')
            ->count();

        // This month attendance
        $thisMonthAttendance = Attendance::where('employee_id', $employee->id)
            ->whereMonth('attendance_date', Carbon::now()->month)
            ->whereYear('attendance_date', Carbon::now()->year)
            ->get();

        $presentDays = $thisMonthAttendance->where('status', 'present')->count();
        $absentDays = $thisMonthAttendance->where('status', 'absent')->count();
        $leaveDays = $thisMonthAttendance->where('status', 'leave')->count();

        // Latest payroll
        $latestPayroll = $employee->payrolls()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->first();

        // Current contract
        $currentContract = $employee->contracts()
            ->where('status', 'active')
            ->orderBy('start_date', 'desc')
            ->first();

        // Latest performance review
        $latestReview = $employee->performanceReviews()
            ->where('status', 'approved')
            ->orderBy('review_period', 'desc')
            ->first();

        // Recent achievements
        $recentAchievements = $employee->achievements()
            ->orderBy('achievement_date', 'desc')
            ->limit(3)
            ->get();

        return view('employee.self-service.dashboard', compact(
            'employee',
            'pendingLeaves',
            'approvedLeaves',
            'presentDays',
            'absentDays',
            'leaveDays',
            'latestPayroll',
            'currentContract',
            'latestReview',
            'recentAchievements'
        ));
    }

    /**
     * View attendance records
     */
    public function viewAttendance(Request $request)
    {
        $employee = Employee::where('user_id', Auth::id())->firstOrFail();

        $query = Attendance::where('employee_id', $employee->id);

        if ($request->filled('month')) {
            $query->whereMonth('attendance_date', $request->month)
                  ->whereYear('attendance_date', $request->year ?? Carbon::now()->year);
        } else {
            $query->whereMonth('attendance_date', Carbon::now()->month)
                  ->whereYear('attendance_date', Carbon::now()->year);
        }

        $attendance = $query->orderBy('attendance_date', 'desc')->paginate(30);

        return view('employee.self-service.attendance', compact('employee', 'attendance'));
    }

    /**
     * View payroll records
     */
    public function viewPayroll(Request $request)
    {
        $employee = Employee::where('user_id', Auth::id())->firstOrFail();

        $payrolls = $employee->payrolls()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(12);

        // Calculate YTD
        $ytdTotal = Payroll::where('employee_id', $employee->id)
            ->where('year', Carbon::now()->year)
            ->sum('net_salary');

        return view('employee.self-service.payroll', compact('employee', 'payrolls', 'ytdTotal'));
    }

    /**
     * View contract details
     */
    public function viewContract()
    {
        $employee = Employee::where('user_id', Auth::id())->firstOrFail();

        $contracts = $employee->contracts()->orderBy('start_date', 'desc')->get();

        return view('employee.self-service.contract', compact('employee', 'contracts'));
    }

    /**
     * View leave history
     */
    public function viewLeaveHistory(Request $request)
    {
        $employee = Employee::where('user_id', Auth::id())->firstOrFail();

        $query = $employee->leaveRequests();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('employee.self-service.leave-history', compact('employee', 'leaves'));
    }

    /**
     * View performance reviews
     */
    public function viewPerformance()
    {
        $employee = Employee::where('user_id', Auth::id())->firstOrFail();

        $reviews = $employee->performanceReviews()
            ->where('status', 'approved')
            ->orderBy('review_period', 'desc')
            ->paginate(10);

        return view('employee.self-service.performance', compact('employee', 'reviews'));
    }

    /**
     * View achievements/recognition
     */
    public function viewAchievements()
    {
        $employee = Employee::where('user_id', Auth::id())->firstOrFail();

        $achievements = $employee->achievements()
            ->orderBy('achievement_date', 'desc')
            ->paginate(15);

        return view('employee.self-service.achievements', compact('employee', 'achievements'));
    }

    /**
     * View leave balance
     */
    public function viewLeaveBalance()
    {
        $employee = Employee::where('user_id', Auth::id())->firstOrFail();

        $balances = $employee->leaveBalances()
            ->where('year', Carbon::now()->year)
            ->with('leaveType')
            ->get();

        return view('employee.self-service.leave-balance', compact('employee', 'balances'));
    }

    /**
     * Download payroll slip
     */
    public function downloadPayrollSlip(Payroll $payroll)
    {
        $employee = Employee::where('user_id', Auth::id())->firstOrFail();

        if ($payroll->employee_id !== $employee->id) {
            abort(403, 'Unauthorized');
        }

        $pdf = \PDF::loadView('payroll.slip', ['payroll' => $payroll]);

        return $pdf->download('payroll-slip-' . $payroll->period . '.pdf');
    }

    /**
     * Download contract
     */
    public function downloadContract(EmployeeContract $contract)
    {
        $employee = Employee::where('user_id', Auth::id())->firstOrFail();

        if ($contract->employee_id !== $employee->id) {
            abort(403, 'Unauthorized');
        }

        if ($contract->contract_file) {
            return \Storage::download($contract->contract_file, 'contract-' . $contract->contract_number . '.pdf');
        }

        abort(404, 'Contract file not found');
    }

    /**
     * Update personal profile
     */
    public function updateProfile(Request $request)
    {
        $employee = Employee::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ]);

        $employee->update($validated);

        return back()->with('success', 'Profile updated successfully');
    }
}
