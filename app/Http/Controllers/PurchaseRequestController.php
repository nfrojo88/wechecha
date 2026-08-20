<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\Project;
use App\Models\Store;
use App\Models\Product;
use App\Models\MaterialRequest;
use App\Models\Supplier;
use App\Models\ChartOfAccount;
use App\Models\Employee;
use App\Models\User;
use App\Models\Inventory;
use App\Services\ProcurementLifecycleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PurchaseRequestController extends Controller
{
    public function __construct(private ProcurementLifecycleService $lifecycle) {}

    // ─── Index / List ────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = PurchaseRequest::with(['project', 'requestedBy'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        if ($request->filled('source')) {
            $query->whereHas('materialRequest', fn($q) => $q->where('source', $request->source));
        }
        if ($request->filled('search')) {
            $query->where('pr_no', 'like', '%' . $request->search . '%');
        }

        $prs      = $query->paginate(20)->withQueryString();
        $projects = Project::whereIn('status', ['active', 'planning', 'in_progress', 'on_hold'])->orderBy('name')->get();
        if ($projects->isEmpty()) {
            $projects = Project::orderBy('name')->get();
        }
        $statuses = PurchaseRequest::statusLabels();

        return view('procurement.purchase-requests.index', compact('prs', 'projects', 'statuses'));
    }

    // ─── Create / Store ──────────────────────────────────────────────────────
    public function create()
    {
        $projects = Project::whereIn('status', ['active', 'planning', 'in_progress', 'on_hold'])->orderBy('name')->get();
        if ($projects->isEmpty()) {
            $projects = Project::orderBy('name')->get();
        }
        $stores           = Store::where('is_active', true)->get();
        $products = Product::orderBy('name')->get()->map(function($product) {
            $latestPriceRecord = \App\Models\MaterialPrice::where('product_id', $product->id)
                ->orderBy('effective_date', 'desc')
                ->first();
            $unitCost = $latestPriceRecord ? (float)$latestPriceRecord->price : (float)($product->unit_price ?? $product->selling_price ?? 0);
            $product->latest_marketing_price = $unitCost;
            return $product;
        });
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
            $no = 'PR-' . date('Ymd') . '-' . str_pad(PurchaseRequest::withTrashed()->count() + 1, 4, '0', STR_PAD_LEFT);

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
                'status'              => PurchaseRequest::STATUS_DRAFT,
                'current_owner_role'  => 'coordinator',
            ]);

            foreach ($request->items as $item) {
                $prod = Product::find($item['product_id']);
                $unit = !empty($item['unit']) ? $item['unit'] : ($prod?->unit ?? 'pcs');
                
                $latestPriceRecord = \App\Models\MaterialPrice::where('product_id', $item['product_id'])
                    ->orderBy('effective_date', 'desc')
                    ->first();
                $estCost = $latestPriceRecord ? (float)$latestPriceRecord->price : (float)($prod?->unit_price ?? $prod?->selling_price ?? 0);
                if (isset($item['estimated_unit_cost']) && $item['estimated_unit_cost'] !== '' && $item['estimated_unit_cost'] > 0) {
                    $estCost = (float) $item['estimated_unit_cost'];
                }

                $pr->items()->create([
                    'product_id'          => $item['product_id'],
                    'quantity'            => $item['quantity'],
                    'unit'                => $unit,
                    'specifications'      => $item['specifications'] ?? null,
                    'estimated_unit_cost' => $estCost,
                ]);
            }
        });

        return redirect()->route('purchase-requests.index')->with('success', 'Purchase Request created.');
    }

    // ─── Show ────────────────────────────────────────────────────────────────
    public function show(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load([
            'project', 'store', 'requestedBy', 'items.product',
            'marketResearch.supplier', 'proformaInvoices.supplier',
            'gmDecisions.decidedBy', 'marketingVariance.addedBy',
            'payment.coaAccount', 'payment.assignedStaff', 'payment.paidByUser',
            'receipt.uploadedBy', 'receipt.verifiedBy',
            'driverBooking.driver', 'driverBooking.bookedBy',
            'workflowLogs.actor',
        ]);

        // Cross-store stock availability for all items
        $stockAvailability = [];
        foreach ($purchaseRequest->items as $item) {
            if ($item->product_id) {
                $stockAvailability[$item->product_id] = Inventory::with('store')
                    ->where('product_id', $item->product_id)
                    ->where('quantity_on_hand', '>', 0)
                    ->get();
            } else {
                $stockAvailability[$item->id] = collect();
            }
        }

        // Data for action forms
        try {
            $coaAccounts = ChartOfAccount::where('is_active', true)
                ->whereIn('type', ['asset', 'expense'])
                ->orderBy('code')
                ->get();
        } catch (\Throwable $e) {
            $coaAccounts = collect();
        }

        try {
            $financeStaff = User::role(['finance', 'finance_head'])->get();
        } catch (\Throwable $e) {
            $financeStaff = User::all();
        }

        try {
            $drivers = Employee::where('status', 'active')
                ->where(function ($q) {
                    $q->whereIn('role_title', ['Driver', 'driver', 'General Service', 'general_service'])
                      ->orWhere('department', 'General Service');
                })
                ->orderBy('full_name')
                ->get();
        } catch (\Throwable $e) {
            $drivers = collect();
        }

        try {
            $suppliers = Supplier::where('status', 'active')
                ->orWhereNull('status')
                ->orderBy('name')
                ->get();
        } catch (\Throwable $e) {
            $suppliers = collect();
        }

        return view('procurement.purchase-requests.show', compact(
            'purchaseRequest', 'stockAvailability', 'coaAccounts',
            'financeStaff', 'drivers', 'suppliers'
        ));
    }

    // ─── Submit (Coordinator submits draft to Store Manager) ────────────────
    public function submit(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->update([
            'status'             => PurchaseRequest::STATUS_PENDING_STORE_REVIEW,
            'current_owner_role' => 'store_manager',
        ]);
        return back()->with('success', 'Purchase Request submitted to Store Manager.');
    }

    // ─── STAGE 3: Procurement Manager Triage ────────────────────────────────
    public function sendToProcurementManager(Request $request, PurchaseRequest $purchaseRequest)
    {
        $this->lifecycle->sendToProcurementManager($purchaseRequest, $request->notes);
        return back()->with('success', 'Routed to Procurement Manager.');
    }

    public function sendBackToStoreManager(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate(['reason' => 'required|string']);
        $this->lifecycle->sendBackToStoreManager($purchaseRequest, $request->reason);
        return back()->with('success', 'Sent back to Store Manager.');
    }

    public function sendToProcurementTeam(Request $request, PurchaseRequest $purchaseRequest)
    {
        $this->lifecycle->sendToProcurementTeam($purchaseRequest, $request->notes);
        return back()->with('success', 'Routed to Procurement Team.');
    }

    // ─── STAGE 4: Procurement Team Sourcing ─────────────────────────────────
    public function submitDirectBuy(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes'  => 'nullable|string',
        ]);
        $this->lifecycle->submitDirectBuy($purchaseRequest, (float)$request->amount, $request->notes);
        return back()->with('success', 'Direct buy submitted. Awaiting marketing review.');
    }

    public function submitProformas(Request $request, PurchaseRequest $purchaseRequest)
    {
        $this->lifecycle->submitProformas($purchaseRequest, $request->notes);
        return back()->with('success', 'Proformas submitted to Procurement Manager.');
    }

    // ─── STAGE 5a: Marketing Variance ───────────────────────────────────────
    public function addMarketingVariance(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'market_price'       => 'required|numeric|min:0',
            'variance_notes'     => 'nullable|string',
        ]);
        $directBuy       = (float)$purchaseRequest->direct_buy_amount;
        $marketPrice     = (float)$request->market_price;
        $varianceAmount  = $marketPrice - $directBuy;
        $variancePct     = $directBuy > 0 ? round(($varianceAmount / $directBuy) * 100, 2) : 0;

        $this->lifecycle->addMarketingVariance($purchaseRequest, [
            'market_price'        => $marketPrice,
            'variance_amount'     => $varianceAmount,
            'variance_percentage' => $variancePct,
            'variance_notes'      => $request->variance_notes,
        ]);
        return back()->with('success', 'Price variance recorded. Sent to GM.');
    }

    // ─── STAGE 5b: Select Proformas and send to GM ──────────────────────────
    public function selectProformas(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate(['proforma_ids' => 'required|array|min:1']);
        $this->lifecycle->sendProformasToGm($purchaseRequest, $request->proforma_ids, $request->notes);
        return back()->with('success', 'Selected proformas sent to GM.');
    }

    // ─── STAGE 6: GM Decision ────────────────────────────────────────────────
    public function gmDecide(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'decision'       => 'required|in:approve,reject,send_back',
            'payment_method' => 'required_if:decision,approve|in:pay_and_buy,buy_by_credit',
            'notes'          => 'nullable|string',
        ]);
        $this->lifecycle->gmDecide(
            $purchaseRequest,
            $request->decision,
            $request->payment_method,
            $request->notes
        );
        return back()->with('success', 'GM decision recorded.');
    }

    // ─── STAGE 7a: Finance Head — Credit Path ───────────────────────────────
    public function financeCreditApprove(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'coa_account_id' => 'required|exists:chart_of_accounts,id',
            'amount'         => 'required|numeric|min:0.01',
            'notes'          => 'nullable|string',
        ]);
        $this->lifecycle->financeCreditApprove(
            $purchaseRequest,
            (int)$request->coa_account_id,
            (float)$request->amount,
            $request->notes
        );
        return back()->with('success', 'Credit authorized. Driver booking notified.');
    }

    // ─── STAGE 7b: Finance Head — Cash Path, Assign Staff ───────────────────
    public function assignPayment(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'coa_account_id'  => 'required|exists:chart_of_accounts,id',
            'amount'          => 'required|numeric|min:0.01',
            'staff_user_id'   => 'required|exists:users,id',
            'notes'           => 'nullable|string',
        ]);
        $this->lifecycle->financeHeadAssignPayment(
            $purchaseRequest,
            (int)$request->coa_account_id,
            (float)$request->amount,
            (int)$request->staff_user_id,
            $request->notes
        );
        return back()->with('success', 'Payment assigned to finance staff.');
    }

    // ─── STAGE 7b: Finance Staff — Execute Payment ──────────────────────────
    public function executePayment(Request $request, PurchaseRequest $purchaseRequest)
    {
        $this->lifecycle->financeStaffPay($purchaseRequest, $request->notes);
        return back()->with('success', 'Payment executed. COA balance updated. Procurement Team notified to upload receipt.');
    }

    // ─── STAGE 8: Upload Receipt ─────────────────────────────────────────────
    public function uploadReceipt(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate(['receipt_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120']);

        $file     = $request->file('receipt_file');
        $path     = \App\Services\FileUploadService::upload($file, 'procurement_receipts');
        $original = $file->getClientOriginalName();

        $this->lifecycle->uploadReceipt($purchaseRequest, $path, $original, $request->notes);
        return back()->with('success', 'Receipt uploaded. Finance Staff notified to verify.');
    }

    // ─── STAGE 8: Verify Receipt ─────────────────────────────────────────────
    public function verifyReceipt(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'verification_status' => 'required|in:verified,rejected',
            'verification_notes'  => 'nullable|string',
        ]);
        $this->lifecycle->verifyReceipt($purchaseRequest, $request->verification_status, $request->verification_notes);
        return back()->with('success', 'Receipt verification recorded.');
    }

    // ─── STAGE 9: Book Driver ────────────────────────────────────────────────
    public function bookDriver(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'driver_employee_id'  => 'required|exists:employees,id',
            'vehicle_number'      => 'nullable|string|max:50',
            'vehicle_description' => 'nullable|string|max:255',
            'scheduled_at'        => 'nullable|date',
            'booking_notes'       => 'nullable|string',
        ]);
        $this->lifecycle->bookDriver(
            $purchaseRequest,
            (int)$request->driver_employee_id,
            $request->vehicle_number,
            $request->vehicle_description,
            $request->scheduled_at,
            $request->booking_notes
        );
        return back()->with('success', 'Driver booked. Store Manager notified for final intake.');
    }

    // ─── STAGE 9 Final: Store Intake ─────────────────────────────────────────
    public function storeIntake(Request $request, PurchaseRequest $purchaseRequest)
    {
        $this->lifecycle->storeIntake($purchaseRequest, $request->notes);
        return back()->with('success', 'Intake complete. Procurement lifecycle closed.');
    }

    // ─── Legacy: approve/reject (kept for backward compat) ──────────────────
    public function approve(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->update([
            'status'      => PurchaseRequest::STATUS_PENDING_PROC_MANAGER,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'Purchase Request approved.');
    }

    public function reject(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate(['rejection_reason' => 'required|string']);
        $purchaseRequest->update([
            'status'           => PurchaseRequest::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
        ]);
        return back()->with('success', 'Purchase Request rejected.');
    }
}
