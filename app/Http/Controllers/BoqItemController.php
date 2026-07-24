<?php

namespace App\Http\Controllers;

use App\Models\Boq;
use App\Models\BoqItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BoqItemController extends Controller
{
    public function store(Request $request, Boq $boq)
    {
        Gate::authorize('update', $boq); // Must be able to update the BOQ to add items
        
        if ($boq->status === 'approved') {
            return back()->with('error', 'Cannot add items to an approved BOQ.');
        }

        $validated = $request->validate([
            'item_code' => 'nullable|string|max:100',
            'description' => 'required|string',
            'unit' => 'required|string|max:50',
            'tender_quantity' => 'required|numeric|min:0',
            'quantity' => 'nullable|numeric|min:0',
            'unit_rate' => 'required|numeric|min:0',
            'product_id' => 'nullable|exists:products,id',
            'schedule_task_id' => 'nullable|exists:schedule_tasks,id'
        ]);

        $validated['quantity'] = $validated['quantity'] ?? 0;

        $item = $boq->items()->create($validated);
        
        $this->recalculateBoqTotal($boq);

        return back()->with('success', 'Item added to BOQ.');
    }

    public function update(Request $request, BoqItem $item)
    {
        $boq = $item->boq;
        Gate::authorize('update', $boq);
        
        if ($boq->status === 'approved') {
            return back()->with('error', 'Cannot edit items in an approved BOQ.');
        }

        $validated = $request->validate([
            'item_code' => 'nullable|string|max:100',
            'description' => 'required|string',
            'unit' => 'required|string|max:50',
            'tender_quantity' => 'required|numeric|min:0',
            'quantity' => 'nullable|numeric|min:0',
            'unit_rate' => 'required|numeric|min:0',
            'product_id' => 'nullable|exists:products,id',
            'schedule_task_id' => 'nullable|exists:schedule_tasks,id'
        ]);

        $validated['quantity'] = $validated['quantity'] ?? $item->quantity;

        $item->update($validated);
        
        $this->recalculateBoqTotal($boq);

        return back()->with('success', 'BOQ item updated.');
    }

    public function destroy(BoqItem $item)
    {
        $boq = $item->boq;
        Gate::authorize('update', $boq);
        
        if ($boq->status === 'approved') {
            return back()->with('error', 'Cannot delete items from an approved BOQ.');
        }

        $item->delete();
        $this->recalculateBoqTotal($boq);

        return back()->with('success', 'Item removed from BOQ.');
    }
    
    private function recalculateBoqTotal(Boq $boq)
    {
        $total = $boq->items()->sum('amount');
        $boq->update(['total_amount' => $total]);
    }
}
