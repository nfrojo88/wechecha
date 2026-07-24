<?php

namespace App\Http\Controllers;

use App\Models\MaterialRequest;
use App\Models\MaterialRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MaterialRequestItemController extends Controller
{
    public function store(Request $request, MaterialRequest $materialRequest)
    {
        Gate::authorize('update', $materialRequest);

        $validated = $request->validate([
            'product_id'         => 'required|exists:products,id',
            'quantity_requested' => 'required|numeric|min:0.001',
            'notes'              => 'nullable|string|max:500',
        ]);

        $validated['material_request_id'] = $materialRequest->id;
        $validated['quantity_fulfilled']  = 0;

        MaterialRequestItem::create($validated);

        return back()->with('success', 'Item added to request.');
    }

    public function destroy(MaterialRequestItem $item)
    {
        Gate::authorize('update', $item->materialRequest);

        $item->delete();

        return back()->with('success', 'Item removed.');
    }
}
