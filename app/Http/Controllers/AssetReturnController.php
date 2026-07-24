<?php

namespace App\Http\Controllers;

use App\Models\EmployeeAsset;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AssetReturnController extends Controller
{
    /**
     * Display a listing of pending asset returns.
     */
    public function index()
    {
        // Add authorization if you have a specific gate for store manager
        // Gate::authorize('viewAny', EmployeeAsset::class);

        $pendingReturns = EmployeeAsset::with(['employee', 'product'])
            ->where('return_status', 'pending_approval')
            ->latest()
            ->paginate(20);

        return view('store.asset-returns.index', compact('pendingReturns'));
    }

    /**
     * Approve the return of an asset.
     */
    public function approve(Request $request, $id)
    {
        // Gate::authorize('update', EmployeeAsset::class);

        $request->validate([
            'return_notes' => 'nullable|string'
        ]);

        $asset = EmployeeAsset::findOrFail($id);

        if ($asset->return_status !== 'pending_approval') {
            return back()->with('error', 'Asset return is not pending approval.');
        }

        // Update the employee asset record
        $asset->update([
            'status' => 'returned',
            'return_status' => 'approved',
            'returned_date' => now(),
            'store_manager_id' => auth()->id(),
            'return_notes' => $request->input('return_notes'),
        ]);

        // Mark product back as Available
        if ($asset->product) {
            $asset->product->update(['asset_status' => 'Available']);
        }

        return back()->with('success', 'Asset return approved successfully.');
    }

    /**
     * Reject the return of an asset.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'return_notes' => 'required|string'
        ]);

        $asset = EmployeeAsset::findOrFail($id);

        if ($asset->return_status !== 'pending_approval') {
            return back()->with('error', 'Asset return is not pending approval.');
        }

        $asset->update([
            'return_status' => 'rejected',
            'store_manager_id' => auth()->id(),
            'return_notes' => $request->input('return_notes'),
        ]);

        return back()->with('success', 'Asset return rejected.');
    }
}
