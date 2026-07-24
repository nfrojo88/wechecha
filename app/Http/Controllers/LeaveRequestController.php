<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeaveRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display all leave requests (HR Officer view)
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', LeaveRequest::class);

        $query = LeaveRequest::with(['employee', 'leaveType', 'approvedByUser']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('start_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('end_date', '<=', $request->to_date);
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')->paginate(15);

        $leaveTypes = LeaveType::where('is_active', true)->get();
        $employees = Employee::where('status', 'active')->orderBy('full_name')->get();

        return view('hr-manager.leave-requests.index', compact(
            'leaveRequests',
            'leaveTypes',
            'employees'
        ));
    }

    /**
     * Show employee's own leave requests
     */
    public function myRequests(Request $request)
    {
        $employee = Employee::where('user_id', Auth::id())->firstOrFail();

        $query = LeaveRequest::where('employee_id', $employee->id)
            ->with(['leaveType', 'approvedByUser']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaveRequests = $query->orderBy('start_date', 'desc')->paginate(10);

        return view('hr-manager.leave-requests.my-requests', compact('leaveRequests'));
    }

    /**
     * Create leave request form
     */
    public function create()
    {
        $employee = Employee::where('user_id', Auth::id())->firstOrFail();

        $leaveTypes = LeaveType::where('is_active', true)->get();
        
        // Get current year balances
        $balances = LeaveBalance::where('employee_id', $employee->id)
            ->where('year', Carbon::now()->year)
            ->with('leaveType')
            ->get();

        return view('hr-manager.leave-requests.create', compact('employee', 'leaveTypes', 'balances'));
    }

    /**
     * Store leave request
     */
    public function store(Request $request)
    {
        $employee = Employee::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:10|max:500',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $leaveType = LeaveType::findOrFail($validated['leave_type_id']);
        $daysRequested = Carbon::parse($validated['start_date'])
            ->diffInDays(Carbon::parse($validated['end_date'])) + 1;

        // Check for overlapping leave
        $overlap = LeaveRequest::where('employee_id', $employee->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($q) use ($validated) {
                $q->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                  ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                  ->orWhere(function ($q2) use ($validated) {
                      $q2->where('start_date', '<=', $validated['start_date'])
                         ->where('end_date', '>=', $validated['end_date']);
                  });
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors(['start_date' => 'Overlapping leave already exists']);
        }

        // Check balance
        $balance = LeaveBalance::where('employee_id', $employee->id)
            ->where('leave_type_id', $leaveType->id)
            ->where('year', Carbon::now()->year)
            ->first();

        if (!$balance || !$balance->hasEnoughBalance($daysRequested)) {
            return back()->withErrors(['leave_type_id' => 'Insufficient leave balance']);
        }

        // Handle attachment
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave-attachments');
        }

        // Create leave request
        $leaveRequest = LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'attachment' => $attachmentPath,
            'status' => 'pending',
        ]);

        return redirect()->route('leave-requests.my-requests')
            ->with('success', 'Leave request submitted successfully');
    }

    /**
     * Show leave request details
     */
    public function show(LeaveRequest $leaveRequest)
    {
        $this->authorize('view', $leaveRequest);

        $leaveRequest->load(['employee', 'leaveType', 'approvedByUser']);

        return view('hr-manager.leave-requests.show', compact('leaveRequest'));
    }

    /**
     * Approve leave request
     */
    public function approve(LeaveRequest $leaveRequest)
    {
        $this->authorize('approve', $leaveRequest);

        if (!$leaveRequest->isPending()) {
            return back()->withErrors(['status' => 'Only pending requests can be approved']);
        }

        $daysRequested = $leaveRequest->days_requested;

        // Update balance
        $balance = LeaveBalance::where('employee_id', $leaveRequest->employee_id)
            ->where('leave_type_id', $leaveRequest->leave_type_id)
            ->where('year', $leaveRequest->start_date->year)
            ->first();

        if ($balance) {
            $balance->updateBalance($daysRequested);
        }

        // Update leave request
        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Leave request approved');
    }

    /**
     * Reject leave request
     */
    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $this->authorize('reject', $leaveRequest);

        if (!$leaveRequest->isPending()) {
            return back()->withErrors(['status' => 'Only pending requests can be rejected']);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ]);

        $leaveRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Leave request rejected');
    }

    /**
     * Bulk approve leave requests
     */
    public function bulkApprove(Request $request)
    {
        $this->authorize('approve', LeaveRequest::class);

        $validated = $request->validate([
            'request_ids' => 'required|array|min:1',
            'request_ids.*' => 'integer|exists:leave_requests,id',
        ]);

        $leaveRequests = LeaveRequest::whereIn('id', $validated['request_ids'])
            ->where('status', 'pending')
            ->get();

        $approved = 0;
        foreach ($leaveRequests as $leaveRequest) {
            $daysRequested = $leaveRequest->days_requested;

            $balance = LeaveBalance::where('employee_id', $leaveRequest->employee_id)
                ->where('leave_type_id', $leaveRequest->leave_type_id)
                ->where('year', $leaveRequest->start_date->year)
                ->first();

            if ($balance && $balance->hasEnoughBalance($daysRequested)) {
                $balance->updateBalance($daysRequested);

                $leaveRequest->update([
                    'status' => 'approved',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);

                $approved++;
            }
        }

        return back()->with('success', "Approved $approved leave requests");
    }

    /**
     * Get leave balance for employee
     */
    public function getBalance(Employee $employee)
    {
        $this->authorize('viewAny', LeaveRequest::class);

        $balances = LeaveBalance::where('employee_id', $employee->id)
            ->where('year', Carbon::now()->year)
            ->with('leaveType')
            ->get();

        return response()->json($balances);
    }

    /**
     * Export leave report
     */
    public function exportReport(Request $request)
    {
        $this->authorize('viewAny', LeaveRequest::class);

        $query = LeaveRequest::with(['employee', 'leaveType']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('start_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('end_date', '<=', $request->to_date);
        }

        $leaveRequests = $query->get();

        $fileName = 'leave-requests-' . now()->format('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($leaveRequests) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Employee', 'Leave Type', 'Start Date', 'End Date', 'Days', 'Status', 'Reason']);

            foreach ($leaveRequests as $lr) {
                fputcsv($file, [
                    $lr->employee->name,
                    $lr->leaveType->name,
                    $lr->start_date->format('Y-m-d'),
                    $lr->end_date->format('Y-m-d'),
                    $lr->days_requested,
                    $lr->status,
                    $lr->reason,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
