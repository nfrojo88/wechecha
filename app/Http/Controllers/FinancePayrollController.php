<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;

class FinancePayrollController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ─── Finance Head: Payroll Management ────────────────────────────────────

    /**
     * List payrolls for the selected month/year, auto-fill from employee records.
     */
    public function index(Request $request)
    {
        $month = (int) $request->get('month', date('n'));
        $year  = (int) $request->get('year',  date('Y'));

        // Load existing payrolls for this period
        $payrolls = Payroll::with('employee')
            ->where('month', $month)
            ->where('year',  $year)
            ->orderBy('id')
            ->get();

        $hasTransport = \Illuminate\Support\Facades\Schema::hasColumn('payrolls', 'transport_allowance');
        $hasGmStatus  = \Illuminate\Support\Facades\Schema::hasColumn('payrolls', 'gm_status');

        // Summary totals
        $totals = [
            'basic'     => $payrolls->sum('basic_salary'),
            'transport' => $hasTransport ? $payrolls->sum('transport_allowance') : 0,
            'house'     => $hasTransport ? $payrolls->sum('house_allowance') : 0,
            'position'  => $hasTransport ? $payrolls->sum('position_allowance') : 0,
            'overtime'  => $payrolls->sum('overtime_pay'),
            'pension'   => $hasTransport ? $payrolls->sum('pension') : round($payrolls->sum('basic_salary') * 0.07, 2),
            'tax'       => $payrolls->sum('tax'),
            'deductions'=> $payrolls->sum('deductions'),
            'gross'     => $payrolls->sum(function($p) { return $p->gross_salary ?? ($p->basic_salary + $p->allowances + $p->overtime_pay); }),
            'net'       => $payrolls->sum('net_salary'),
            'count'     => $payrolls->count(),
        ];

        // GM status for this batch
        $gmStatus  = $hasGmStatus ? ($payrolls->first()->gm_status ?? null) : null;
        $submitted = $hasGmStatus ? $payrolls->where('gm_status', 'submitted')->count() : 0;
        $approved  = $hasGmStatus ? $payrolls->where('gm_status', 'approved')->count() : 0;

        return view('finance.payroll.index', compact(
            'payrolls', 'totals', 'month', 'year', 'gmStatus', 'submitted', 'approved'
        ));
    }

    /**
     * Auto-generate payroll entries for all active employees for the period.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year'  => 'required|integer|min:2020|max:2099',
        ]);

        $month = (int) $request->month;
        $year  = (int) $request->year;

        $employees = Employee::where('status', 'active')->get();
        $created = 0;
        $skipped = 0;

        $hasTransport = \Illuminate\Support\Facades\Schema::hasColumn('payrolls', 'transport_allowance');
        $hasGmStatus  = \Illuminate\Support\Facades\Schema::hasColumn('payrolls', 'gm_status');

        foreach ($employees as $emp) {
            $exists = Payroll::where('employee_id', $emp->id)
                             ->where('month', $month)
                             ->where('year',  $year)
                             ->exists();
            if ($exists) { $skipped++; continue; }

            $basic    = $emp->basic_salary     ?? 0;
            $transport= $emp->transport_allowance ?? 0;
            $house    = $emp->house_allowance     ?? 0;
            $position = $emp->position_allowance  ?? 0;

            // Taxable income = basic + allowances - pension(7%)
            $pension  = round($basic * 0.07, 2);
            $taxable  = $basic + $transport + $house + $position - $pension;
            $tax      = Payroll::calculateIncomeTax($taxable);

            $payload = [
                'employee_id'  => $emp->id,
                'month'        => $month,
                'year'         => $year,
                'basic_salary' => $basic,
                'allowances'   => $transport + $house + $position,
                'overtime_pay' => 0,
                'deductions'   => 0,
                'tax'          => round($tax, 2),
                'status'       => 'draft',
                'created_by'   => auth()->id(),
            ];

            if ($hasTransport) {
                $payload['transport_allowance'] = $transport;
                $payload['house_allowance']     = $house;
                $payload['position_allowance']  = $position;
            }

            Payroll::create($payload);
            $created++;
        }

        $msg = "Generated {$created} payroll entries for " . date('F Y', mktime(0,0,0,$month,1,$year)) . ".";
        if ($skipped) $msg .= " {$skipped} already existed and were skipped.";

        return redirect()->route('finance.payroll.index', ['month' => $month, 'year' => $year])
                         ->with('success', $msg);
    }

    /**
     * Submit the entire month's payrolls to GM for approval.
     */
    public function submitToGM(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year'  => 'required|integer|min:2020|max:2099',
        ]);

        $month = (int) $request->month;
        $year  = (int) $request->year;

        $hasGmStatus = \Illuminate\Support\Facades\Schema::hasColumn('payrolls', 'gm_status');

        if (!$hasGmStatus) {
            return redirect()->route('finance.payroll.index', ['month' => $month, 'year' => $year])
                             ->with('error', 'Database migration required before submitting to GM. Please run migrations.');
        }

        $updated = Payroll::where('month', $month)
            ->where('year',  $year)
            ->whereNotIn('gm_status', ['approved', 'rejected'])
            ->update([
                'gm_status'          => 'submitted',
                'submitted_to_gm_at' => now(),
            ]);

        $period = date('F Y', mktime(0,0,0,$month,1,$year));

        return redirect()->route('finance.payroll.index', ['month' => $month, 'year' => $year])
                         ->with('success', "{$updated} payroll entries for {$period} submitted to GM for approval.");
    }

    // ─── GM: Approval Inbox ──────────────────────────────────────────────────

    /**
     * GM sees all submitted payroll batches (grouped by month/year).
     */
    public function gmIndex()
    {
        // Check if database table has gm_status column
        $hasGmStatus = \Illuminate\Support\Facades\Schema::hasColumn('payrolls', 'gm_status');

        if (!$hasGmStatus) {
            $batches = collect();
            $history = collect();
            return view('finance.payroll.gm-approval', compact('batches', 'history'));
        }

        // Get distinct month/year combos that have submitted payrolls
        $batches = Payroll::with('employee')
            ->where('gm_status', 'submitted')
            ->select('month', 'year')
            ->selectRaw('COUNT(*) as employee_count')
            ->selectRaw('SUM(basic_salary) as total_basic')
            ->selectRaw('SUM(net_salary) as total_net')
            ->selectRaw('COALESCE(SUM(basic_salary + allowances + overtime_pay), 0) as total_gross')
            ->selectRaw('MIN(submitted_to_gm_at) as submitted_at')
            ->groupBy('year', 'month')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        // Also previously approved/rejected batches
        $history = Payroll::select('month', 'year', 'gm_status')
            ->selectRaw('COUNT(*) as employee_count')
            ->selectRaw('SUM(net_salary) as total_net')
            ->selectRaw('MIN(gm_approved_at) as decided_at')
            ->whereIn('gm_status', ['approved', 'rejected'])
            ->groupBy('year', 'month', 'gm_status')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        return view('finance.payroll.gm-approval', compact('batches', 'history'));
    }

    /**
     * GM views detail of a specific batch before approving/rejecting.
     */
    public function gmBatchDetail(Request $request)
    {
        $month = (int) $request->get('month', date('n'));
        $year  = (int) $request->get('year',  date('Y'));

        $payrolls = Payroll::with('employee')
            ->where('month', $month)
            ->where('year',  $year)
            ->where('gm_status', 'submitted')
            ->get();

        if ($payrolls->isEmpty()) {
            return redirect()->route('finance.payroll.gm')
                             ->with('error', 'No submitted payrolls found for this period.');
        }

        $hasTransport = \Illuminate\Support\Facades\Schema::hasColumn('payrolls', 'transport_allowance');

        $totals = [
            'basic'     => $payrolls->sum('basic_salary'),
            'transport' => $hasTransport ? $payrolls->sum('transport_allowance') : 0,
            'house'     => $hasTransport ? $payrolls->sum('house_allowance') : 0,
            'position'  => $hasTransport ? $payrolls->sum('position_allowance') : 0,
            'overtime'  => $payrolls->sum('overtime_pay'),
            'pension'   => $hasTransport ? $payrolls->sum('pension') : round($payrolls->sum('basic_salary') * 0.07, 2),
            'tax'       => $payrolls->sum('tax'),
            'gross'     => $payrolls->sum(function($p) { return $p->gross_salary ?? ($p->basic_salary + $p->allowances + $p->overtime_pay); }),
            'net'       => $payrolls->sum('net_salary'),
        ];

        return view('finance.payroll.gm-batch-detail', compact('payrolls', 'totals', 'month', 'year'));
    }

    /**
     * GM approves the whole batch for a month/year.
     */
    public function gmApprove(Request $request)
    {
        $request->validate([
            'month'    => 'required|integer|between:1,12',
            'year'     => 'required|integer|min:2020|max:2099',
            'gm_notes' => 'nullable|string|max:500',
        ]);

        Payroll::where('month', (int) $request->month)
               ->where('year',  (int) $request->year)
               ->where('gm_status', 'submitted')
               ->update([
                   'gm_status'       => 'approved',
                   'gm_notes'        => $request->gm_notes,
                   'gm_approved_by'  => auth()->id(),
                   'gm_approved_at'  => now(),
                   'status'          => 'pending', // ready for payment
               ]);

        $period = date('F Y', mktime(0,0,0,(int)$request->month,1,(int)$request->year));
        return redirect()->route('finance.payroll.gm')
                         ->with('success', "Payroll for {$period} APPROVED. Finance can now process payment.");
    }

    /**
     * GM rejects the batch.
     */
    public function gmReject(Request $request)
    {
        $request->validate([
            'month'    => 'required|integer|between:1,12',
            'year'     => 'required|integer|min:2020|max:2099',
            'gm_notes' => 'required|string|max:500',
        ]);

        Payroll::where('month', (int) $request->month)
               ->where('year',  (int) $request->year)
               ->where('gm_status', 'submitted')
               ->update([
                   'gm_status'      => 'rejected',
                   'gm_notes'       => $request->gm_notes,
                   'gm_approved_by' => auth()->id(),
                   'gm_approved_at' => now(),
               ]);

        $period = date('F Y', mktime(0,0,0,(int)$request->month,1,(int)$request->year));
        return redirect()->route('finance.payroll.gm')
                         ->with('error', "Payroll for {$period} REJECTED. Notes sent to Finance Head.");
    }
}
