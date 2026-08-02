<?php

namespace App\Http\Controllers;

use App\Models\EmployeeAsset;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AssetReportController extends Controller
{
    /**
     * Asset Utilization Report
     */
    public function utilization()
    {
        Gate::authorize('viewAny', EmployeeAsset::class);

        $report = EmployeeAsset::select(
            DB::raw('DATE(assigned_date) as assignment_date'),
            DB::raw('COUNT(*) as total_assigned'),
            DB::raw('SUM(CASE WHEN status IN ("assigned", "in_use") THEN 1 ELSE 0 END) as still_active'),
            DB::raw('SUM(CASE WHEN status = "returned" THEN 1 ELSE 0 END) as returned'),
            DB::raw('SUM(CASE WHEN status = "damaged" THEN 1 ELSE 0 END) as damaged')
        )
        ->groupBy(DB::raw('DATE(assigned_date)'))
        ->orderBy('assignment_date', 'desc')
        ->get();

        return view('hr.reports.asset-utilization', compact('report'));
    }

    /**
     * Asset Lifecycle Report
     */
    public function lifecycle()
    {
        Gate::authorize('viewAny', EmployeeAsset::class);

        $assets = EmployeeAsset::with(['employee', 'product'])
            ->select(
                'employee_assets.*',
                DB::raw('DATEDIFF(COALESCE(returned_date, NOW()), assigned_date) as days_in_use')
            )
            ->latest('assigned_date')
            ->paginate(50);

        return view('hr.reports.asset-lifecycle', compact('assets'));
    }

    /**
     * Damage Report
     */
    public function damage()
    {
        Gate::authorize('viewAny', EmployeeAsset::class);

        $damagedAssets = EmployeeAsset::where('status', 'damaged')
            ->with(['employee', 'product'])
            ->latest('updated_at')
            ->paginate(30);

        $damageStats = EmployeeAsset::where('status', 'damaged')
            ->select(
                DB::raw('COUNT(*) as total_damaged'),
                DB::raw('SUM(products.unit_cost) as total_damage_value'),
                DB::raw('AVG(products.unit_cost) as avg_damage_value')
            )
            ->join('products', 'employee_assets.product_id', '=', 'products.id')
            ->first();

        return view('hr.reports.asset-damage', compact('damagedAssets', 'damageStats'));
    }

    /**
     * Employee Asset Allocation Report
     */
    public function employeeAllocation()
    {
        Gate::authorize('viewAny', EmployeeAsset::class);

        $employees = Employee::with(['activeAssets'])
            ->select(
                'employees.*',
                DB::raw('(SELECT COUNT(*) FROM employee_assets WHERE employee_id = employees.id AND status IN ("assigned", "in_use")) as active_asset_count'),
                DB::raw('(SELECT SUM(products.unit_cost) FROM employee_assets JOIN products ON employee_assets.product_id = products.id WHERE employee_id = employees.id AND employee_assets.status IN ("assigned", "in_use")) as total_asset_value')
            )
            ->having(DB::raw('(SELECT COUNT(*) FROM employee_assets WHERE employee_id = employees.id AND status IN ("assigned", "in_use"))'), '>', 0)
            ->orderBy('employees.full_name')
            ->paginate(30);

        return view('hr.reports.employee-allocation', compact('employees'));
    }

    /**
     * Asset Turnover Report
     */
    public function turnover()
    {
        Gate::authorize('viewAny', EmployeeAsset::class);

        $products = \App\Models\Product::select(
            'products.*',
            DB::raw('COUNT(employee_assets.id) as total_assignments'),
            DB::raw('SUM(CASE WHEN employee_assets.status = "returned" THEN 1 ELSE 0 END) as times_returned'),
            DB::raw('SUM(CASE WHEN employee_assets.status = "damaged" THEN 1 ELSE 0 END) as times_damaged'),
            DB::raw('ROUND(SUM(CASE WHEN employee_assets.status IN ("assigned", "in_use") THEN 1 ELSE 0 END) / COUNT(employee_assets.id) * 100, 2) as current_utilization_percent')
        )
        ->leftJoin('employee_assets', 'products.id', '=', 'employee_assets.product_id')
        ->groupBy('products.id')
        ->having(DB::raw('COUNT(employee_assets.id)'), '>', 0)
        ->orderBy('total_assignments', 'desc')
        ->paginate(30);

        return view('hr.reports.asset-turnover', compact('products'));
    }

    /**
     * Export utilization report
     */
    public function exportUtilization()
    {
        Gate::authorize('viewAny', EmployeeAsset::class);

        $filename = 'asset-utilization-report-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv;charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Assignment Date',
                'Total Assigned',
                'Still Active',
                'Returned',
                'Damaged'
            ]);

            $report = EmployeeAsset::select(
                DB::raw('DATE(assigned_date) as assignment_date'),
                DB::raw('COUNT(*) as total_assigned'),
                DB::raw('SUM(CASE WHEN status IN ("assigned", "in_use") THEN 1 ELSE 0 END) as still_active'),
                DB::raw('SUM(CASE WHEN status = "returned" THEN 1 ELSE 0 END) as returned'),
                DB::raw('SUM(CASE WHEN status = "damaged" THEN 1 ELSE 0 END) as damaged')
            )
            ->groupBy(DB::raw('DATE(assigned_date)'))
            ->orderBy('assignment_date', 'desc')
            ->get();

            foreach ($report as $row) {
                fputcsv($file, [
                    $row->assignment_date,
                    $row->total_assigned,
                    $row->still_active,
                    $row->returned,
                    $row->damaged
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export damage report
     */
    public function exportDamage()
    {
        Gate::authorize('viewAny', EmployeeAsset::class);

        $filename = 'asset-damage-report-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv;charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Employee Code',
                'Employee Name',
                'Department',
                'Asset Name',
                'Asset Type',
                'Unit Price (Br)',
                'Assigned Date',
                'Damage Reported',
                'Days in Use',
                'Damage Details'
            ]);

            $damagedAssets = EmployeeAsset::where('status', 'damaged')
                ->with(['employee', 'product'])
                ->latest('updated_at')
                ->get();

            foreach ($damagedAssets as $asset) {
                $daysInUse = $asset->assigned_date->diffInDays(now());
                fputcsv($file, [
                    $asset->employee->employee_code,
                    $asset->employee->full_name,
                    $asset->employee->department,
                    $asset->product->name,
                    $asset->product->type ?? 'General',
                    $asset->product->unit_cost ?? 0,
                    $asset->assigned_date->format('Y-m-d'),
                    $asset->updated_at->format('Y-m-d'),
                    $daysInUse,
                    $asset->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export employee allocation report
     */
    public function exportEmployeeAllocation()
    {
        Gate::authorize('viewAny', EmployeeAsset::class);

        $filename = 'employee-asset-allocation-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv;charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Employee Code',
                'Employee Name',
                'Department',
                'Role/Designation',
                'Active Assets',
                'Total Asset Value (Br)',
                'Date of Joining'
            ]);

            $employees = Employee::with(['activeAssets'])
                ->select(
                    'employees.*',
                    DB::raw('(SELECT COUNT(*) FROM employee_assets WHERE employee_id = employees.id AND status IN ("assigned", "in_use")) as active_asset_count'),
                    DB::raw('(SELECT SUM(products.unit_cost) FROM employee_assets JOIN products ON employee_assets.product_id = products.id WHERE employee_id = employees.id AND employee_assets.status IN ("assigned", "in_use")) as total_asset_value')
                )
                ->having(DB::raw('(SELECT COUNT(*) FROM employee_assets WHERE employee_id = employees.id AND status IN ("assigned", "in_use"))'), '>', 0)
                ->orderBy('employees.full_name')
                ->get();

            foreach ($employees as $employee) {
                fputcsv($file, [
                    $employee->employee_code,
                    $employee->full_name,
                    $employee->department,
                    $employee->role_title ?? 'N/A',
                    $employee->activeAssets()->count(),
                    $employee->total_asset_value ?? 0,
                    $employee->date_of_joining ? $employee->date_of_joining->format('Y-m-d') : 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
