<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\Project;
use App\Models\Store;
use App\Models\Product;
use App\Models\MaterialRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseRequestController extends Controller
{
    public function index()
    {
        $prs = PurchaseRequest::with(['project', 'requestedBy'])
            ->latest()->paginate(20);
        return view('procurement.purchase-requests.index', compact('prs'));
    }

    public function create()
    {
        $projects        = Project::where('status', 'active')->get();
        $stores          = Store::where('is_active', true)->get();
        $products        = Product::where('is_active', true)->orderBy('name')->get();
        $materialRequests = MaterialRequest::where('status', 'approved')->get();
        return view('procurement.purchase-requests.create', compact('projects', 'stores', 'products', 'materialRequests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id'          => 'required|exists:projects,id',
            'store_id'            => 'nullable|exists:stores,id',
            'material_request_id' => 'nullable|exists:material_requests,id',
            'priority'            => 'required|in:normal,high,urgent',
            'type'                => 'required|in:normal,emergency,direct',
            'required_date'       => 'nullable|date',
            'justification'       => 'nullable|string',
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.quantity'    => 'required|numeric|min:0.001',
            'items.*.unit'        => 'required|string|max:20',
        ]);

        DB::transaction(function () use ($request) {
            $no = 'PR-' . date('Ymd') . '-' . str_pad(PurchaseRequest::count() + 1, 4, '0', STR_PAD_LEFT);

            $pr = PurchaseRequest::create([
                'pr_no'               => $no,
                'project_id'          => $request->project_id,
                'store_id'            => $request->store_id,
                'requested_by'        => Auth::id(),
                'material_request_id' => $request->material_request_id,
                'priority'            => $request->priority,
                'type'                => $request->type,
                'required_date'       => $request->required_date,
                'justification'       => $request->justification,
                'status'              => 'draft',
            ]);

            foreach ($request->items as $item) {
                $pr->items()->create([
                    'product_id'           => $item['product_id'],
                    'quantity'             => $item['quantity'],
                    'unit'                 => $item['unit'],
                    'specifications'       => $item['specifications'] ?? null,
                    'estimated_unit_cost'  => $item['estimated_unit_cost'] ?? null,
                ]);
            }
        });

        return redirect()->route('purchase-requests.index')->with('success', 'Purchase Request created.');
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load(['project', 'store', 'requestedBy', 'items.product', 'marketResearch.supplier', 'proformaInvoices.supplier']);
        return view('procurement.purchase-requests.show', compact('purchaseRequest'));
    }

    public function submit(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->update(['status' => 'submitted']);
        return back()->with('success', 'Purchase Request submitted for review.');
    }

    public function approve(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->update([
            'status'      => 'under_review',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'Purchase Request approved.');
    }

    public function reject(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate(['rejection_reason' => 'required|string']);
        $purchaseRequest->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);
        return back()->with('success', 'Purchase Request rejected.');
    }
}
