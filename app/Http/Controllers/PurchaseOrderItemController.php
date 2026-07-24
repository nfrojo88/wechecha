<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PurchaseOrderItemController extends Controller
{
    public function store(Request $request, PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('update', $purchaseOrder);

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|numeric|min:0.001',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $validated['purchase_order_id'] = $purchaseOrder->id;

        PurchaseOrderItem::create($validated);

        // Recalculate total
        $purchaseOrder->update([
            'total_amount' => $purchaseOrder->items()->sum('total_price'),
        ]);

        return back()->with('success', 'Item added successfully.');
    }

    public function destroy(PurchaseOrderItem $item)
    {
        $po = $item->purchaseOrder;
        Gate::authorize('update', $po);

        $item->delete();

        $po->update([
            'total_amount' => $po->items()->sum('total_price'),
        ]);

        return back()->with('success', 'Item removed.');
    }
}
