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
