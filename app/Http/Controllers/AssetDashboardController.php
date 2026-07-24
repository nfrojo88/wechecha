<?php

namespace App\Http\Controllers;

use App\Models\EmployeeAsset;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AssetDashboardController extends Controller
{
    /**
     * Show asset dashboard
     */
    public function index()
    {
        // Get authorization
        Gate::authorize('viewAny', EmployeeAsset::class);

        // Summary statistics
        $totalAssets = EmployeeAsset::count();
        $activeAssets = EmployeeAsset::whereIn('status', ['assigned', 'in_use'])->count();
        $returnedAssets = EmployeeAsset::where('status', 'returned')->count();
        $damagedAssets = EmployeeAsset::where('status', 'damaged')->count();

        // Asset value calculations
        $activeAssetsValue = EmployeeAsset::whereIn('status', ['assigned', 'in_use'])
            ->join('products', 'employee_assets.product_id', '=', 'products.id')
            ->sum('products.unit_cost');

        $damagedAssetsValue = EmployeeAsset::where('status', 'damaged')
            ->join('products', 'employee_assets.product_id', '=', 'products.id')
            ->sum('products.unit_cost');

        // Assets by category
        $assetsByCategory = EmployeeAsset::join('products', 'employee_assets.product_id', '=', 'products.id')
            ->select(
                'products.category',
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(products.unit_cost) as total_value')
            )
            ->groupBy('products.category')
            ->get();

        // Assets by department
        $assetsByDepartment = EmployeeAsset::join('employees', 'employee_assets.employee_id', '=', 'employees.id')
            ->select(
                'employees.department',
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(CASE WHEN employee_assets.status IN ("assigned", "in_use") THEN 1 ELSE 0 END) as active_count')
            )
            ->groupBy('employees.department')
            ->get();

        // Recent activity
        $recentActivity = EmployeeAsset::with(['employee', 'product'])
            ->latest('updated_at')
            ->limit(10)
            ->get();

        // All assets with pagination
        $allAssets = EmployeeAsset::with(['employee', 'product'])
            ->latest('assigned_date')
            ->paginate(50);

        return view('hr.asset-dashboard', compact(
            'totalAssets',
            'activeAssets',
            'returnedAssets',
            'damagedAssets',
            'activeAssetsValue',
            'damagedAssetsValue',
            'assetsByCategory',
            'assetsByDepartment',
            'recentActivity',
            'allAssets'
        ));
    }

    /**
     * Export assets to CSV
     */
    public function export()
    {
        Gate::authorize('viewAny', EmployeeAsset::class);

        $assets = EmployeeAsset::with(['employee', 'product'])
            ->latest('assigned_date')
            ->get();

        $filename = 'asset-report-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv;charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($assets) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel
            fwrite($file, "\xEF\xBB\xBF");

            // Headers
            fputcsv($file, [
                'Employee Code',
                'Employee Name',
                'Department',
                'Asset Name',
                'Asset Type',
                'Asset Category',
                'Unit Price (Br)',
                'Assigned Date',
                'Returned Date',
                'Status',
                'Notes',
                'Days in Use'
            ]);

            // Data
            foreach ($assets as $asset) {
                $daysInUse = $asset->returned_date 
                    ? $asset->assigned_date->diffInDays($asset->returned_date)
                    : $asset->assigned_date->diffInDays(now());

                fputcsv($file, [
                    $asset->employee->employee_code,
                    $asset->employee->full_name,
                    $asset->employee->department,
                    $asset->product->name,
                    $asset->product->type ?? 'General',
                    $asset->product->category ?? 'N/A',
                    $asset->product->unit_cost ?? 0,
                    $asset->assigned_date->format('Y-m-d'),
                    $asset->returned_date ? $asset->returned_date->format('Y-m-d') : 'N/A',
                    ucfirst($asset->status),
                    $asset->notes ?? '',
                    $daysInUse
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get assets by status
     */
    public function byStatus($status)
    {
        Gate::authorize('viewAny', EmployeeAsset::class);

        $validStatuses = ['assigned', 'in_use', 'returned', 'damaged'];
        if (!in_array($status, $validStatuses)) {
            abort(404);
        }

        $assets = EmployeeAsset::where('status', $status)
            ->with(['employee', 'product'])
            ->latest('updated_at')
            ->paginate(30);

        $statusTitle = match($status) {
            'assigned' => 'Assigned Assets',
            'in_use' => 'Assets in Use',
            'returned' => 'Returned Assets',
            'damaged' => 'Damaged Assets',
        };

        return view('hr.asset-by-status', compact('assets', 'status', 'statusTitle'));
    }

    /**
     * Get assets by employee
     */
    public function byEmployee($employeeId)
    {
        Gate::authorize('viewAny', EmployeeAsset::class);

        $employee = Employee::findOrFail($employeeId);
        $assets = $employee->assets()->with('product')->latest('assigned_date')->paginate(20);

        return view('hr.asset-by-employee', compact('employee', 'assets'));
    }

    /**
     * Get assets by department
     */
    public function byDepartment($department)
    {
        Gate::authorize('viewAny', EmployeeAsset::class);

        $assets = EmployeeAsset::whereHas('employee', function($q) use ($department) {
            $q->where('department', $department);
        })
        ->with(['employee', 'product'])
        ->latest('assigned_date')
        ->paginate(30);

        return view('hr.asset-by-department', compact('assets', 'department'));
    }
}
