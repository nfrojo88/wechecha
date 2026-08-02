<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Store;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(private InventoryService $inventoryService)
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Inventory::class);

        $query = Inventory::with('store', 'product');

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('low_stock')) {
            $query->whereColumn('quantity_on_hand', '<=', 'min_stock');
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $inventory */
        $inventory = $query->paginate(20);
        $inventory->withQueryString();
        $stores    = Store::where('is_active', true)->orderBy('name')->get();
        $products  = Product::where('is_active', true)->orderBy('name')->get();

        return view('inventory.index', compact('inventory', 'stores', 'products'));
    }

    public function show(Inventory $inventory)
    {
        $this->authorize('view', $inventory);
        $inventory->load('store', 'product', 'movements.performer');
        return view('inventory.show', compact('inventory'));
    }

    public function adjust(Request $request, Inventory $inventory)
    {
        $this->authorize('update', $inventory);

        $validated = $request->validate([
            'type'     => ['required', 'in:in,out,adjustment'],
            'quantity' => ['required', 'numeric', 'min:0.001'],
            'unit_cost'=> ['nullable', 'numeric', 'min:0'],
            'remarks'  => ['nullable', 'string', 'max:500'],
        ]);

        try {
            if ($validated['type'] === 'in' || $validated['type'] === 'adjustment') {
                $this->inventoryService->stockIn(
                    $inventory->store_id,
                    $inventory->product_id,
                    $validated['quantity'],
                    $validated['unit_cost'] ?? $inventory->unit_cost ?? 0,
                    'adjustment',
                    auth()->id(),
                    null, null,
                    $validated['remarks'] ?? null
                );
            } else {
                $this->inventoryService->stockOut(
                    $inventory->store_id,
                    $inventory->product_id,
                    $validated['quantity'],
                    'adjustment',
                    auth()->id(),
                    null, null,
                    $validated['remarks'] ?? null
                );
            }

            return redirect()->back()->with('success', 'Inventory adjusted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['quantity' => $e->getMessage()]);
        }
    }

    /**
     * Show the bulk manual stock-level adjustment form.
     */
    public function showBulkAdjust(Request $request)
    {
        $stores   = Store::where('is_active', true)->orderBy('name')->get();
        $storeId  = $request->store_id ?? ($stores->first()->id ?? null);

        $products = Product::orderBy('name')->get();

        // Load existing inventory for the chosen store (keyed by product_id)
        $existingStock = Inventory::where('store_id', $storeId)
            ->get()
            ->keyBy('product_id');

        $selectedStore = $storeId ? Store::find($storeId) : $stores->first();

        return view('inventory.bulk-adjust', compact('stores', 'products', 'existingStock', 'selectedStore', 'storeId'));
    }

    /**
     * Process bulk manual stock-level adjustment (set absolute levels).
     * Supports AJAX (returns JSON) and regular form POST (redirects).
     */
    public function bulkAdjust(Request $request)
    {
        $request->validate([
            'store_id'              => ['required', 'exists:stores,id'],
            'items'                 => ['required', 'array'],
            'items.*.product_id'    => ['required', 'exists:products,id'],
            'items.*.quantity'      => ['required', 'numeric', 'min:0'],
            'items.*.unit_cost'     => ['nullable', 'numeric', 'min:0'],
        ]);

        $storeId = $request->store_id;
        $count   = 0;
        $results = [];

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $storeId, &$count, &$results) {
            foreach ($request->items as $row) {
                $productId = $row['product_id'];
                $newQty    = (float) $row['quantity'];
                $unitCost  = isset($row['unit_cost']) && $row['unit_cost'] !== '' ? (float) $row['unit_cost'] : null;

                $inv = Inventory::firstOrCreate(
                    ['store_id' => $storeId, 'product_id' => $productId],
                    ['quantity_on_hand' => 0, 'quantity_reserved' => 0, 'unit_cost' => 0, 'min_stock' => 0]
                );

                $currentQty = (float) $inv->quantity_on_hand;
                $diff       = $newQty - $currentQty;

                if (abs($diff) < 0.001) {
                    $results[] = ['product_id' => $productId, 'status' => 'skipped'];
                    continue;
                }

                \App\Models\InventoryMovement::create([
                    'inventory_id'   => $inv->id,
                    'type'           => 'adjustment',
                    'quantity'       => $diff,
                    'reference_type' => 'manual_bulk',
                    'reference_id'   => null,
                    'performed_by'   => auth()->id(),
                    'remarks'        => 'Manual stock adjustment — set to ' . $newQty,
                ]);

                $inv->quantity_on_hand = $newQty;
                $inv->last_movement_at = now();
                if ($unitCost !== null) {
                    $inv->unit_cost = $unitCost;
                }
                $inv->save();

                $results[] = ['product_id' => $productId, 'status' => 'saved', 'new_qty' => $newQty];
                $count++;
            }
        });

        // AJAX request → return JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'count'   => $count,
                'results' => $results,
                'message' => $count > 0
                    ? "$count item(s) updated."
                    : 'No changes detected.',
            ]);
        }

        // Regular form POST → redirect
        return redirect()->route('inventory.index', ['store_id' => $storeId])
            ->with('success', "Manual adjustment complete — $count product(s) updated.");
    }

    /**
     * Save a single product's stock level via AJAX — always returns JSON.
     */
    public function saveSingle(Request $request)
    {
        $validated = $request->validate([
            'store_id'   => ['required', 'integer', 'exists:stores,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['required', 'numeric', 'min:0'],
            'unit_cost'  => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $storeId   = (int) $validated['store_id'];
            $productId = (int) $validated['product_id'];
            $newQty    = (float) $validated['quantity'];
            $unitCost  = isset($validated['unit_cost']) && $validated['unit_cost'] !== '' ? (float) $validated['unit_cost'] : null;

            $result = \Illuminate\Support\Facades\DB::transaction(function () use ($storeId, $productId, $newQty, $unitCost) {
                $inv = Inventory::firstOrCreate(
                    ['store_id' => $storeId, 'product_id' => $productId],
                    ['quantity_on_hand' => 0, 'quantity_reserved' => 0, 'unit_cost' => 0, 'min_stock' => 0]
                );

                $oldQty = (float) $inv->quantity_on_hand;
                $diff   = $newQty - $oldQty;

                // Record movement (even zero-diff, so we log it)
                \App\Models\InventoryMovement::create([
                    'inventory_id' => $inv->id,
                    'type'         => 'adjustment',
                    'quantity'     => $diff,
                    'reference_type' => null,
                    'reference_id'   => null,
                    'performed_by' => auth()->id(),
                    'remarks'      => 'Manual stock adjustment — set to ' . $newQty,
                ]);

                $inv->quantity_on_hand = $newQty;
                $inv->last_movement_at = now();
                if ($unitCost !== null) {
                    $inv->unit_cost = $unitCost;
                }
                $inv->save();

                return [
                    'old_qty'  => $oldQty,
                    'new_qty'  => $newQty,
                    'diff'     => $diff,
                    'unit_cost' => $inv->unit_cost,
                ];
            });

            return response()->json([
                'success'    => true,
                'message'    => 'Stock updated successfully.',
                'product_id' => $productId,
                'store_id'   => $storeId,
                'old_qty'    => $result['old_qty'],
                'new_qty'    => $result['new_qty'],
                'diff'       => $result['diff'],
                'unit_cost'  => $result['unit_cost'],
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function movements(Inventory $inventory)
    {
        $this->authorize('view', $inventory);
        $movements = $inventory->movements()
            ->with('performer')
            ->latest()
            ->paginate(20);
        return view('inventory.movements', compact('inventory', 'movements'));
    }
}
