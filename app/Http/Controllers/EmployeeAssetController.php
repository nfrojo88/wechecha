<?php

namespace App\Http\Controllers;

use App\Models\EmployeeAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EmployeeAssetController extends Controller
{
    /**
     * Show return asset form
     */
    public function returnForm(EmployeeAsset $employeeAsset)
    {
        Gate::authorize('update', $employeeAsset->employee);
        $asset = $employeeAsset->load(['employee', 'product']);
        return view('hr.employees.assets.return', compact('asset'));
    }

    /**
     * Process asset return
     */
    public function returnStore(Request $request, EmployeeAsset $employeeAsset)
    {
        Gate::authorize('update', $employeeAsset->employee);

        $validated = $request->validate([
            'returned_date' => 'required|date',
            'condition' => 'required|in:good,fair,damaged',
            'notes' => 'nullable|string|max:500',
            'received_by' => 'nullable|string|max:255',
        ]);

        $employeeAsset->update([
            'status' => 'returned',
            'returned_date' => $validated['returned_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('employees.show', $employeeAsset->employee)
            ->with('success', 'Asset returned successfully. Record updated.');
    }

    /**
     * Show damage report form
     */
    public function damageForm(EmployeeAsset $employeeAsset)
    {
        Gate::authorize('update', $employeeAsset->employee);
        $asset = $employeeAsset->load(['employee', 'product']);
        return view('hr.employees.assets.damage', compact('asset'));
    }

    /**
     * Process damage report
     */
    public function damageStore(Request $request, EmployeeAsset $employeeAsset)
    {
        Gate::authorize('update', $employeeAsset->employee);

        $validated = $request->validate([
            'severity' => 'required|in:minor,moderate,severe',
            'damage_cause' => 'required|in:accidental,misuse,wear_tear,manufacturing,theft,other',
            'damage_description' => 'required|string|max:1000',
            'reported_by' => 'nullable|string|max:255',
            'employee_acknowledged' => 'nullable|boolean',
        ]);

        // Store damage details in notes
        $damageNotes = "SEVERITY: {$validated['severity']}\n";
        $damageNotes .= "CAUSE: {$validated['damage_cause']}\n";
        $damageNotes .= "DESCRIPTION: {$validated['damage_description']}\n";
        $damageNotes .= "REPORTED BY: {$validated['reported_by']}\n";
        $damageNotes .= "ACKNOWLEDGED: " . ($validated['employee_acknowledged'] ? 'Yes' : 'No') . "\n";

        $employeeAsset->update([
            'status' => 'damaged',
            'notes' => $damageNotes,
        ]);

        return redirect()->route('employees.show', $employeeAsset->employee)
            ->with('success', 'Damage report submitted. Asset marked as damaged for review.');
    }

    /**
     * Show asset tracking details
     */
    public function show(EmployeeAsset $employeeAsset)
    {
        Gate::authorize('view', $employeeAsset->employee);
        $asset = $employeeAsset->load(['employee', 'product']);
        return view('hr.employees.assets.show', compact('asset'));
    }

    /**
     * Get all assets for an employee
     */
    public function employeeAssets($employeeId)
    {
        $employee = \App\Models\Employee::findOrFail($employeeId);
        Gate::authorize('view', $employee);
        
        $assets = $employee->assets()->with('product')->get();
        return view('hr.employees.assets.index', compact('employee', 'assets'));
    }
}
