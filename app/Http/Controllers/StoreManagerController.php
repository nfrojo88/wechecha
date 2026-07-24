<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Transfer;
use App\Models\TransferItem;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\DeliveryReceipt;
use App\Models\SlipSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StoreManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store Manager Dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        // KPI Cards
        $kpi = [
            'total_items'          => $this->safe(fn() => Inventory::count()),
            'total_value'          => $this->safe(fn() => Inventory::sum(DB::raw('quantity_on_hand * unit_cost')), 0),
            'low_stock_items'      => $this->safe(fn() => Inventory::whereColumn('quantity_on_hand', '<=', 'min_stock')->count()),
            'pending_transfers'    => $this->safe(fn() => Transfer::where('status', 'draft')->count()),
            'received_today'       => $this->safe(fn() => DeliveryReceipt::whereDate('receipt_date', today())->count()),
            'pending_requests'     => $this->safe(fn() => MaterialRequest::where('status', 'pending')->count()),
        ];

        // All inventory from all stores
        $allInventory = $this->safe(fn() => Inventory::with('product', 'store')
            ->whereHas('store', fn($q) => $q->where('is_active', true))
            ->orderBy('quantity_on_hand', 'desc')
            ->take(15)
            ->get(), collect());

        // Low stock items
        $lowStock = $this->safe(fn() => Inventory::with('product', 'store')
            ->whereColumn('quantity_on_hand', '<=', 'min_stock')
            ->whereHas('store', fn($q) => $q->where('is_active', true))
            ->get(), collect());

        // Transfers to General Service (scheduled)
        $transfersToGeneralService = $this->safe(fn() => Transfer::with(['fromStore', 'toStore', 'requestedBy', 'items.product'])
            ->where('status', 'approved')
            ->latest()
            ->take(10)
            ->get(), collect());

        // Material requests from coordinator
        $materialRequests = $this->safe(fn() => MaterialRequest::with(['project', 'requestedBy', 'items.product'])
            ->where('status', 'pending')
            ->latest()
            ->take(10)
            ->get(), collect());

        // All stores for filter
        $stores = Store::where('is_active', true)->orderBy('name')->get();

        return view('store-manager.dashboard', compact(
            'kpi', 'allInventory', 'lowStock', 
            'transfersToGeneralService', 'materialRequests', 'stores'
        ));
    }

    /**
     * All Inventory from all stores
     */
    public function allInventory(Request $request)
    {
        $query = Inventory::with('product', 'store')
            ->whereHas('store', fn($q) => $q->where('is_active', true));

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        if ($request->filled('search')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('low_stock')) {
            $query->whereColumn('quantity_on_hand', '<=', 'min_stock');
        }

        $inventory = $query->orderBy('quantity_on_hand', 'desc')->paginate(25)->withQueryString();
        $stores = Store::where('is_active', true)->orderBy('name')->get();

        return view('store-manager.inventory.all', compact('inventory', 'stores'));
    }

    /**
     * Create Transfer
     */
    public function createTransfer()
    {
        $stores = Store::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('store-manager.transfers.create', compact('stores', 'products'));
    }

    /**
     * Store Transfer
     */
    public function storeTransfer(Request $request)
    {
        $request->validate([
            'from_store_id'       => 'required|exists:stores,id',
            'to_store_id'         => 'required|exists:stores,id|different:from_store_id',
            'required_date'       => 'nullable|date',
            'reason'              => 'nullable|string|max:500',
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.quantity'    => 'required|numeric|min:0.001',
            'items.*.unit'        => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($request) {
            $no = 'TR-' . date('Ymd') . '-' . str_pad(Transfer::count() + 1, 4, '0', STR_PAD_LEFT);

            $transfer = Transfer::create([
                'transfer_no'   => $no,
                'from_store_id' => $request->from_store_id,
                'to_store_id'   => $request->to_store_id,
                'requested_by'  => Auth::id(),
                'required_date' => $request->required_date,
                'reason'        => $request->reason,
                'status'        => 'draft',
            ]);

            foreach ($request->items as $item) {
                $transfer->items()->create([
                    'product_id'         => $item['product_id'],
                    'requested_quantity' => $item['quantity'],
                    'unit'               => $item['unit'] ?? 'pcs',
                ]);
            }
        });

        return redirect()->route('store-manager.transfers.index')->with('success', 'Transfer created and sent to General Service for scheduling.');
    }

    /**
     * List Transfers
     */
    public function transfersIndex(Request $request)
    {
        $query = Transfer::with(['fromStore', 'toStore', 'requestedBy', 'items.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('store_id')) {
            $query->where('from_store_id', $request->store_id)
                  ->orWhere('to_store_id', $request->store_id);
        }

        $transfers = $query->latest()->paginate(20)->withQueryString();
        $stores = Store::where('is_active', true)->orderBy('name')->get();

        return view('store-manager.transfers.index', compact('transfers', 'stores'));
    }

    /**
     * Show Transfer
     */
    public function showTransfer(Transfer $transfer)
    {
        $transfer->load(['fromStore', 'toStore', 'requestedBy', 'approvedBy', 'items.product']);
        return view('store-manager.transfers.show', compact('transfer'));
    }

    /**
     * Material Requests from Coordinator
     */
    public function materialRequests(Request $request)
    {
        $query = MaterialRequest::with(['project', 'requestedBy', 'items.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(20)->withQueryString();

        return view('store-manager.material-requests.index', compact('requests'));
    }

    /**
     * Process Material Request - Create transfer if available, or send to purchase
     */
    public function processMaterialRequest(MaterialRequest $materialRequest)
    {
        $materialRequest->load('items.product');

        $allAvailable = true;
        $unavailableItems = [];

        foreach ($materialRequest->items as $item) {
            $inventory = Inventory::where('product_id', $item->product_id)
                ->whereHas('store', fn($q) => $q->where('is_active', true))
                ->sum('quantity_on_hand');

            if ($inventory < $item->quantity) {
                $allAvailable = false;
                $unavailableItems[] = $item->product->name ?? 'Product #' . $item->product_id;
            }
        }

        if ($allAvailable) {
            // Create transfer
            DB::transaction(function () use ($materialRequest) {
                $no = 'TR-' . date('Ymd') . '-' . str_pad(Transfer::count() + 1, 4, '0', STR_PAD_LEFT);

                // Find source store with inventory
                $firstItem = $materialRequest->items->first();
                $sourceStore = Inventory::where('product_id', $firstItem->product_id)
                    ->where('quantity_on_hand', '>=', $firstItem->quantity)
                    ->first();

                $transfer = Transfer::create([
                    'transfer_no'   => $no,
                    'from_store_id' => $sourceStore->store_id ?? $materialRequest->requestedBy->store_id,
                    'to_store_id'   => $materialRequest->project->store_id ?? $materialRequest->requestedBy->store_id,
                    'requested_by'  => Auth::id(),
                    'required_date' => now(),
                    'reason'        => 'Material Request #' . $materialRequest->id,
                    'status'        => 'draft',
                ]);

                foreach ($materialRequest->items as $item) {
                    $transfer->items()->create([
                        'product_id'         => $item->product_id,
                        'requested_quantity' => $item->quantity,
                        'unit'               => $item->unit ?? 'pcs',
                    ]);
                }

                $materialRequest->update(['status' => 'processed']);
            });

            return back()->with('success', 'Transfer created successfully for the material request.');
        } else {
            // Send to Purchase Manager
            $materialRequest->update(['status' => 'needs_purchase']);
            
            return back()->with('warning', 'Materials not available (' . implode(', ', $unavailableItems) . '). Request sent to Purchase Manager.');
        }
    }

    /**
     * Product Catalog
     */
    public function productCatalog(Request $request)
    {
        $query = Product::with('inventory.store')->where('is_active', true);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $products = $query->orderBy('name')->paginate(25)->withQueryString();
        $categories = Product::distinct()->pluck('category')->filter()->sort();

        return view('store-manager.products.index', compact('products', 'categories'));
    }

    /**
     * Create Product
     */
    public function createProduct()
    {
        return view('store-manager.products.create');
    }

    /**
     * Store Product
     */
    public function storeProduct(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'code'           => 'required|string|max:100|unique:products,code',
            'category'       => 'nullable|string|max:100',
            'unit'           => 'required|string|max:20',
            'description'    => 'nullable|string',
            'specification'  => 'nullable|string',
            'min_stock_level'=> 'nullable|numeric|min:0',
            'standard_cost'  => 'nullable|numeric|min:0',
            'is_active'      => 'boolean',
        ]);

        Product::create($request->all());

        return redirect()->route('store-manager.products.index')->with('success', 'Product added to catalog.');
    }

    /**
     * List Slips with Unified View and Sequence Validation
     */
    public function slipsIndex(Request $request)
    {
        $query = DeliveryReceipt::with('store', 'items.product', 'createdBy');

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        if ($request->filled('slip_type')) {
            $query->where('slip_type', $request->slip_type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'void') {
                $query->where('is_void', true);
            } else {
                $query->where('status', $request->status)->where('is_void', false);
            }
        }

        if ($request->filled('slip_search')) {
            $query->where('dr_no', 'like', '%' . $request->slip_search . '%');
        }

        // Add sequence validation status for each slip
        $slips = $query->latest('received_date')->paginate(20)->withQueryString();
        
        // Check sequence for each slip
        foreach ($slips as $slip) {
            $slip->sequence_status = $this->validateSlipSequence($slip);
        }

        // Calculate statistics
        $stats = [
            'receive_total' => DeliveryReceipt::where('slip_type', 'receive')->count(),
            'send_total'    => DeliveryReceipt::where('slip_type', 'send')->count(),
            'gaps'          => $this->countSequenceGaps(),
            'void'          => DeliveryReceipt::where('is_void', true)->count(),
        ];

        $stores = Store::where('is_active', true)->orderBy('name')->get();

        return view('store-manager.slips.index', compact('slips', 'stores', 'stats'));
    }

    /**
     * Validate Slip Sequence
     */
    private function validateSlipSequence(DeliveryReceipt $slip)
    {
        if ($slip->is_void) {
            return 'void';
        }

        $lastSlip = DeliveryReceipt::where('store_id', $slip->store_id)
            ->where('slip_type', $slip->slip_type)
            ->where('id', '<', $slip->id)
            ->where('is_void', false)
            ->latest('id')
            ->first();

        if (!$lastSlip) {
            return 'valid'; // First slip
        }

        // Extract sequence numbers
        $currentSeq = intval(substr($slip->dr_no, -4));
        $lastSeq = intval(substr($lastSlip->dr_no, -4));

        if ($currentSeq === $lastSeq + 1) {
            return 'valid';
        } else {
            return 'gap';
        }
    }

    /**
     * Count Sequence Gaps
     */
    private function countSequenceGaps()
    {
        $slips = DeliveryReceipt::where('is_void', false)
            ->orderBy('store_id')
            ->orderBy('slip_type')
            ->orderBy('dr_no')
            ->get();

        $gaps = 0;
        $lastByStoreType = [];

        foreach ($slips as $slip) {
            $key = $slip->store_id . '-' . $slip->slip_type;
            
            if (isset($lastByStoreType[$key])) {
                $lastSeq = intval(substr($lastByStoreType[$key], -4));
                $currentSeq = intval(substr($slip->dr_no, -4));
                if ($currentSeq !== $lastSeq + 1) {
                    $gaps++;
                }
            }
            
            $lastByStoreType[$key] = $slip->dr_no;
        }

        return $gaps;
    }

    /**
     * Mark Slip as Void
     */
    public function voidSlip(DeliveryReceipt $slip)
    {
        $slip->update([
            'is_void' => true,
            'status' => 'void',
        ]);

        return back()->with('success', 'Slip marked as void and flagged for audit.');
    }

    /**
     * Create Slip (Unified - Receive or Send)
     */
    public function createSlip()
    {
        $stores = Store::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('store-manager.slips.create', compact('stores', 'products'));
    }

    /**
     * Store Slip (Unified - Receive or Send)
     */
    public function storeSlip(Request $request)
    {
        $request->validate([
            'slip_type'           => 'required|in:receive,send',
            'store_id'            => 'required|exists:stores,id',
            'slip_no'             => 'nullable|string|max:50',
            'slip_date'           => 'required|date',
            'supplier_name'       => 'nullable|string|max:255',
            'reference_no'        => 'nullable|string|max:100',
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.quantity'    => 'required|numeric|min:0.001',
            'items.*.unit_cost'   => 'nullable|numeric|min:0',
        ]);

        if ($request->slip_type === 'send') {
            $request->validate([
                'to_store_id'    => 'required|exists:stores,id|different:store_id',
            ]);
        }

        DB::transaction(function () use ($request) {
            $slipType = $request->slip_type;
            $storeId = $request->store_id;
            
            // Get slip number from sequence or manual entry
            $slipNo = null;
            
            if (!empty($request->slip_no)) {
                // Manual slip number provided
                $slipNo = $request->slip_no;
            } else {
                // Auto-generate from slip sequence
                $sequence = SlipSequence::where('store_id', $storeId)
                    ->where('slip_type', $slipType)
                    ->where('status', 'active')
                    ->first();

                if (!$sequence) {
                    throw new \Exception("No active slip sequence configured for this store and type. Please configure a sequence first.");
                }

                // Generate next slip number
                $slipNo = $sequence->generateSlipNumber();
            }

            // Create dummy PO if not exists (required by table structure)
            $dummyPo = \App\Models\PurchaseOrder::firstOrCreate(
                ['supplier_id' => 1],
                ['po_no' => 'SYSTEM-' . time(), 'status' => 'delivered']
            );

            $receipt = DeliveryReceipt::create([
                'dr_no'          => $slipNo,
                'slip_type'      => $slipType,
                'store_id'       => $storeId,
                'to_store_id'    => $request->to_store_id ?? null,
                'received_date'  => $request->slip_date,
                'receipt_date'   => $request->slip_date,
                'supplier_name'  => $request->supplier_name,
                'reference_no'   => $request->reference_no,
                'purchase_order_id' => $dummyPo->id,
                'received_by'    => Auth::id(),
                'created_by'     => Auth::id(),
                'status'         => 'draft',
                'is_void'        => false,
                'sequence_status' => 'pending',
            ]);

            foreach ($request->items as $item) {
                $receipt->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity_received' => $item['quantity'],
                    'accepted_quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? 'pcs',
                ]);
            }

            // Only update inventory if it's a receive slip
            if ($slipType === 'receive') {
                foreach ($request->items as $item) {
                    $inventory = Inventory::firstOrCreate(
                        ['store_id' => $storeId, 'product_id' => $item['product_id']],
                        ['quantity_on_hand' => 0, 'unit_cost' => $item['unit_cost'] ?? 0]
                    );
                    $inventory->increment('quantity_on_hand', $item['quantity']);
                    if (!empty($item['unit_cost'])) {
                        $inventory->update(['unit_cost' => $item['unit_cost']]);
                    }
                }
            }
        });

        $type = $request->slip_type === 'receive' ? 'Receive' : 'Send';
        return redirect()->route('store-manager.slips.index')->with('success', "$type slip created successfully with auto-generated sequence number.");
    }

    /**
     * Issued Materials
     */
    public function issuedMaterials(Request $request)
    {
        $query = DeliveryReceipt::with('store', 'items.product')
            ->where('status', 'issued');

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        $issued = $query->latest()->paginate(20)->withQueryString();
        $stores = Store::where('is_active', true)->orderBy('name')->get();

        return view('store-manager.issued.index', compact('issued', 'stores'));
    }

    /**
     * Helper method for safe execution
     */
    private function safe(callable $fn, $default = null)
    {
        try {
            return $fn();
        } catch (\Throwable $e) {
            return $default;
        }
    }
}
