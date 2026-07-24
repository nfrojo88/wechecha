<?php

namespace App\Services;

use App\Mail\LeaveRequestApproved;
use App\Mail\LeaveRequestRejected;
use App\Mail\PerformanceReviewSubmitted;
use App\Mail\ContractApprovalRequired;
use App\Mail\WeeklyManpowerReport;
use App\Mail\PayrollProcessed;
use App\Models\LeaveRequest;
use App\Models\PerformanceReview;
use App\Models\EmployeeContract;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class HRNotificationService
{
    /**
     * Send leave request approved notification
     */
    public static function sendLeaveApproved(LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->employee->user_id) {
            $user = User::find($leaveRequest->employee->user_id);
            if ($user) {
                Mail::to($user->email)->queue(new LeaveRequestApproved($leaveRequest));
            }
        }
    }

    /**
     * Send leave request rejected notification
     */
    public static function sendLeaveRejected(LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->employee->user_id) {
            $user = User::find($leaveRequest->employee->user_id);
            if ($user) {
                Mail::to($user->email)->queue(new LeaveRequestRejected($leaveRequest));
            }
        }
    }

    /**
     * Send performance review submitted notification
     */
    public static function sendPerformanceReviewSubmitted(PerformanceReview $review)
    {
        // Send to HR Manager for approval
        $hrManagers = User::role('hr_manager')->get();
        foreach ($hrManagers as $manager) {
            Mail::to($manager->email)->queue(new PerformanceReviewSubmitted($review));
        }
    }

    /**
     * Send contract approval required notification
     */
    public static function sendContractApprovalRequired(EmployeeContract $contract, $approvalLevel)
    {
        $approvers = match($approvalLevel) {
            1 => User::role('manager')->get(),
            2 => User::role('hr_manager')->get(),
            3 => User::role('finance_manager')->get(),
            default => collect(),
        };

        foreach ($approvers as $approver) {
            Mail::to($approver->email)->queue(new ContractApprovalRequired($contract, $approvalLevel));
        }
    }

    /**
     * Send weekly manpower report
     */
    public static function sendWeeklyManpowerReport($reportData, $weekStarting, $recipientEmails)
    {
        foreach ($recipientEmails as $email) {
            Mail::to($email)->queue(new WeeklyManpowerReport($reportData, $weekStarting));
        }
    }

    /**
     * Send payroll processed notification
     */
    public static function sendPayrollProcessed(Payroll $payroll)
    {
        if ($payroll->employee->user_id) {
            $user = User::find($payroll->employee->user_id);
            if ($user) {
                Mail::to($user->email)->queue(new PayrollProcessed($payroll));
            }
        }
    }

    /**
     * Send bulk leave approval notifications
     */
    public static function sendBulkLeaveApprovals($leaveRequests)
    {
        foreach ($leaveRequests as $request) {
            self::sendLeaveApproved($request);
        }
    }

    /**
     * Send contract expiry alerts
     */
    public static function sendContractExpiryAlerts($expiringContracts)
    {
        $hrManagers = User::role('hr_manager')->get();
        
        foreach ($hrManagers as $manager) {
            $html = view('emails.contract-expiry-alert', [
                'contracts' => $expiringContracts
            ])->render();

            Mail::html($html, function ($message) use ($manager, $expiringContracts) {
                $message->to($manager->email)
                    ->subject('Contract Expiry Alert - ' . count($expiringContracts) . ' contracts expiring soon');
            });
        }
    }

    /**
     * Send attendance report
     */
    public static function sendAttendanceReport($reportData, $recipientEmails)
    {
        foreach ($recipientEmails as $email) {
            $html = view('emails.attendance-report', [
                'reportData' => $reportData
            ])->render();

            Mail::html($html, function ($message) use ($email, $reportData) {
                $message->to($email)
                    ->subject('Attendance Report - ' . date('F d, Y'));
            });
        }
    }

    /**
     * Send advance request notification
     */
    public static function sendAdvanceRequestNotification($advance)
    {
        $hrManagers = User::role('hr_manager')->get();

        foreach ($hrManagers as $manager) {
            $html = view('emails.advance-request', [
                'advance' => $advance,
                'employee' => $advance->employee
            ])->render();

            Mail::html($html, function ($message) use ($manager, $advance) {
                $message->to($manager->email)
                    ->subject('Salary Advance Request - ' . $advance->employee->name);
            });
        }
    }
}
