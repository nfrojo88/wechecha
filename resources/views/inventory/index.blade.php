@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Inventory</h1>
    @can('inventory.edit')
    <a href="{{ route('inventory.bulk-adjust') }}" class="btn btn-warning fw-semibold shadow-sm px-4" style="border-radius:10px;">
        <i class="fa-solid fa-sliders me-2"></i> Manual Stock Adjustment
    </a>
    @endcan
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small">Store</label>
                <select name="store_id" class="form-select form-select-sm">
                    <option value="">All Stores</option>
                    @foreach($stores as $store)
                    <option value="{{ $store->id }}" @selected(request('store_id') == $store->id)>
                        {{ $store->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Product</label>
                <select name="product_id" class="form-select form-select-sm">
                    <option value="">All Products</option>
                    @foreach($products as $p)
                    <option value="{{ $p->id }}" @selected(request('product_id') == $p->id)>
                        {{ $p->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="low_stock" value="1"
                           id="lowStock" @checked(request('low_stock'))>
                    <label class="form-check-label" for="lowStock">Low Stock Only</label>
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Store</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>On Hand</th>
                        <th>Reserved</th>
                        <th>Available</th>
                        <th>Unit Cost</th>
                        <th>Total Value</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventory as $item)
                    @php
                        $isLow = $item->quantity_on_hand <= $item->min_stock && $item->min_stock > 0;
                        $effectiveCost = (float) (
                            $item->unit_cost ?: (
                                \Illuminate\Support\Facades\DB::table('material_prices')
                                    ->where('product_id', $item->product_id)
                                    ->orderByDesc('effective_date')
                                    ->orderByDesc('id')
                                    ->value('price') ?: (
                                        \Illuminate\Support\Facades\DB::table('purchase_order_items')
                                            ->where('product_id', $item->product_id)
                                            ->orderByDesc('id')
                                            ->value('unit_price') ?: ($item->product->unit_price ?? 0)
                                    )
                            )
                        );
                        $totalVal = $item->quantity_on_hand * $effectiveCost;
                    @endphp
                    <tr class="{{ $isLow ? 'table-warning' : '' }}">
                        <td>{{ $item->store->name }}</td>
                        <td>
                            <div class="fw-semibold">{{ $item->product->name }}</div>
                            <div class="text-muted small">{{ $item->product->code }}</div>
                        </td>
                        <td>{{ $item->product->category }}</td>
                        <td>{{ number_format($item->quantity_on_hand, 3) }} <small class="text-muted">{{ $item->product->unit }}</small></td>
                        <td>{{ number_format($item->quantity_reserved, 3) }}</td>
                        <td class="{{ $isLow ? 'text-danger fw-bold' : '' }}">
                            {{ number_format($item->quantity_available, 3) }}
                        </td>
                        <td>{{ $effectiveCost ? number_format($effectiveCost, 2) : '—' }}</td>
                        <td class="fw-bold text-success">{{ $totalVal ? number_format($totalVal, 2) : '—' }}</td>
                        <td>
                            @if($isLow)
                            <span class="badge bg-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i>Low</span>
                            @else
                            <span class="badge bg-success">OK</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('inventory.show', $item) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @can('inventory.edit')
                            <button class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal" data-bs-target="#adjustModal{{ $item->id }}">
                                <i class="fa-solid fa-sliders"></i>
                            </button>
                            @endcan
                        </td>
                    </tr>

                    @can('inventory.edit')
                    {{-- Adjustment Modal --}}
                    <div class="modal fade" id="adjustModal{{ $item->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('inventory.adjust', $item) }}">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Adjust: {{ $item->product->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Type</label>
                                            <select name="type" class="form-select" required>
                                                <option value="in">Stock In (+)</option>
                                                <option value="out">Stock Out (−)</option>
                                                <option value="adjustment">Adjustment</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Quantity ({{ $item->product->unit }})</label>
                                            <input type="number" step="0.001" min="0.001" name="quantity"
                                                   class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Unit Cost</label>
                                            <input type="number" step="0.01" min="0" name="unit_cost"
                                                   class="form-control" value="{{ $item->unit_cost }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Remarks</label>
                                            <textarea name="remarks" rows="2" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Apply Adjustment</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endcan
                    @empty
                    <tr><td colspan="10" class="text-center text-muted py-4">No inventory records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($inventory->hasPages())
    <div class="card-footer bg-transparent">
        {{ $inventory->links() }}
    </div>
    @endif
</div>
@endsection
