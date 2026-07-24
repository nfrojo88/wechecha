<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', PurchaseOrder::class);
        $pos = PurchaseOrder::with(['project', 'creator'])->latest()->paginate(15);
        return view('procurement.purchases.index', compact('pos'));
    }

    public function create()
    {
        Gate::authorize('create', PurchaseOrder::class);
        $projects = Project::where('status', '!=', 'cancelled')->get();
        return view('procurement.purchases.create', compact('projects'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', PurchaseOrder::class);
        
        $validated = $request->validate([
            'project_id'       => 'nullable|exists:projects,id',
            'supplier_name'    => 'required|string|max:255',
            'reference_number' => 'required|string|unique:purchase_orders',
            'notes'            => 'nullable|string',
        ]);

        // ── Budget Guard ──────────────────────────────────────────────────
        if (!empty($validated['project_id'])) {
            $project     = Project::findOrFail($validated['project_id']);
            $estimatedAmt = (float) ($request->input('estimated_amount', 0));
            if ($estimatedAmt > 0) {
                $budgetCheck = app(\App\Services\BudgetGuardService::class)->check($project, $estimatedAmt);
                if ($budgetCheck['status'] === 'blocked') {
                    return back()->withInput()->withErrors(['project_id' => $budgetCheck['message']]);
                }
                $validated['budget_status'] = $budgetCheck['status'];
            }
        }

        $validated['created_by'] = auth()->id();
        $validated['status']     = 'draft';
        
        $po = PurchaseOrder::create($validated);
        
        return redirect()->route('purchase-orders.show', $po)->with('success', 'Purchase Order drafted.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('view', $purchaseOrder);
        $purchaseOrder->load(['project', 'creator', 'items.product']);
        $products = \App\Models\Product::where('is_active', true)->get();
        
        return view('procurement.purchases.show', compact('purchaseOrder', 'products'));
    }

    public function issue(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('update', $purchaseOrder);

        $purchaseOrder->update([
            'status'      => 'issued',
            'issued_date' => now(),
        ]);

        return back()->with('success', 'Purchase Order issued successfully.');
    }
}

